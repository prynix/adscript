<?php

//echo "haaaaaaaaaaaaai";exit;
//print_r($_POST); exit;
include("extended-config.inc.php");  
include($GLOBALS['admin_folder']."/config.inc.php");

includeClass("User");
includeClass("Form");
includeClass("ImageResizer");

$user=new User("ppc_users");
if(!$user->validateUser())
{
	header("Location:show-message.php?id=1006");
	exit(0);
}

include("swf-function-inc.php");

$url=$_POST['url2'];
$file=$_FILES['banner']['name'];
$file=str_replace('#','',$file);                              //************** For SWF



$maxamount=$_POST['maxamount'];
$adlang=trim($_POST['language']);
$url1=$_POST['url1'];
$adname=$_POST['adname'];
$adult_status=trim($_POST['adult_status']);
//echo "there".$file;
phpSafe($adlang);
phpSafeUrl($url);
phpSafe($file);
phpSafe($maxamount);
phpSafe($adname);
$wap_flag=$_POST['wap_flag'];
phpSafe($wap_flag);
phpSafe($adult_status);
//print_r($_POST);


if($wap_flag==1)
{
	$table='wap_ad_block';
	$wap_string='and wapstatus=1';
	$bannersize=$_POST['bannersize1'];
}
else
{
	$table='ppc_ad_block';
	$wap_string='and wapstatus=0';
	$bannersize=$_POST['bannersize'];

}
phpSafe($bannersize);
$url=str_replace("http://",'',$url);
$url=str_replace("https://",'',$url);
$url=$url1."://".$url;
//print_r($_POST);
//exit;
if($bannersize==""||$url==""||$file==""||$maxamount==""||$adlang==""||$adname=="")
{

	header("Location:show-message.php?id=1001");
	exit(0);
}

if(!is_numeric($maxamount))
{
	header("Location:show-message.php?id=2003");
	exit(0);

}

if($maxamount<$default_admaxamount)
{
	header("Location:show-message.php?id=2004");
	exit(0);

}



if($wap_flag==1)
{
$exten=strtolower(substr($file,-4));
if($exten!=".jpg" && $exten!="jpeg" && $exten!=".gif" && $exten!=".png" ){
	header("Location:show-message.php?id=5014");
	exit(0);
}
}
else
{

$exten=strtolower(substr($file,-4));
if($exten!=".jpg" && $exten!="jpeg" && $exten!=".gif" && $exten!=".png" && $exten!=".swf"){
	header("Location:show-message.php?id=10010");
	exit(0);
}




}

$newexten=str_replace('.',"",$exten);                    //************************ For Flash Ads ******************************

//////////****************time targetting***************************////////////////////

$time_duration_flag=0;
$t_duration=$_POST['duration']; //option 1,2,3 according to the order


 $tm_flg=0;	
if($t_duration==2)
{
	$date_flg=0;
	
$sdate=trim($_POST['popup_container']);    //start date in real time format(eg:12/21/2012)
$edate=trim($_POST['popup_container1']);   //end date in real time format

phpSafe($sdate);
phpSafe($edate);

$sdate=strtotime($sdate);
$edate=strtotime($edate);

if($sdate=='')
{

header("Location:show-message.php?id=10501");
exit(0);
}
if($edate=='')
{

header("Location:show-message.php?id=10502");
exit(0);
}
if($sdate > $edate)
{

header("Location:show-message.php?id=10503");
exit(0);
}
$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
if($edate<$today)
{

header("Location:show-message.php?id=10504");
exit(0);
}
else
{ 
$tm_flg=1;	
}
}
else if($t_duration==3)
{
$tm_flg=1;	
}


//**********************Time******************
if($time_targeting==1)
{


	$beg_time=trim($_POST['beg_time']);
	$end_time=trim($_POST['end_time']); 
	phpSafe($beg_time);
	phpSafe($end_time);
	
	if(($beg_time=="") || ($beg_time<0) || ($beg_time>24))
	{
	 $beg_time=0;
	}
	
	if(($end_time=="") || ($end_time<0) || ($end_time>24))
	{
	 $end_time=0;
	}
	if($end_time==$beg_time)
	
	{
	 $beg_time=0;
	 $end_time=0;
	}
}

//////////****************time targetting***************************////////////////////


//-----------------------#----Adserver Ultimate------//date optional// #----------------
if($time_date_targetting==1)
{


	$beg_time=trim($_POST['beg_time']);
	$end_time=trim($_POST['end_time']); 
	phpSafe($beg_time);
	phpSafe($end_time);
	
	if(($beg_time=="") || ($beg_time<0) || ($beg_time>24))
	{
	 $beg_time=0;
	}
	
	if(($end_time=="") || ($end_time<0) || ($end_time>24))
	{
	 $end_time=0;
	}
	if($end_time==$beg_time)
	
	{
	 $beg_time=0;
	 $end_time=0;
	}
}
//--------------#---Adserver Ultimate 5.4----#---------------
$temp_val=$min_click_value;

if($ad_keyword_mode==2){
	$maxclkamount=trim($_POST['maxclkamount']);
	phpSafe($maxclkamount);
	if($maxclkamount=="")
	{

		header("Location:show-message.php?id=1001");
		exit(0);
	}

	if($maxclkamount<$min_click_value)
	{
		header("Location:show-message.php?id=2008");
		exit(0);

	}
	$temp_val=$maxclkamount;

}


$width=$mysql->echo_one("select width from banner_dimension where id=$bannersize");
$height=$mysql->echo_one("select height from banner_dimension where id=$bannersize");
$file_max_size=$mysql->echo_one("select file_size from banner_dimension where id=$bannersize");
$size=getimagesize($_FILES['banner']['tmp_name']);


if((($_FILES['banner']['size'])/1024) > $file_max_size )
	{
		header("Location:show-message.php?id=5016");
		exit(0);
	}
	





if($newexten=="swf")
{
if(($size[0] > $width) || ($size[1] > $height) || ($size[0] < $width) || ($size[1] < $height) )
{	
	header("Location:show-message.php?id=5012");
	exit(0);

}
}

if($newexten=="swf")
{
$name_new=$_FILES['banner']['tmp_name'];
$swf_file_news=Inout_FlashImageRetrieveFun($name_new);

list($result_news, $flg_href_new) = Inout_FlashInfoFun($swf_file_news);    //*************** For Finding HardCoded Links***********************************

if($flg_href_new !=0)
{

header("Location:show-message.php?id=10011");             //*************** If non-replacable Hard Coded Links Exists in the Image *******
exit(0);
}

}





$userid=$user->getUserID();
$username=$user->getUsername($userid);
mysql_query("INSERT INTO `ppc_ads` ( `id` , `uid` , `link`  , `summary` , `maxamount` , `amountused` , `createtime` , `status`,`bannersize`,`adtype`,`updatedtime`,`wapstatus`,`adlang`,`name`,`contenttype`,`adult_status`)
VALUES ('0', '$userid', '$url', '$file', '$maxamount', '0', '".time()."', '-1','$bannersize','1','".time()."','$wap_flag','$adlang','$adname','$newexten','$adult_status')");

$id=$mysql->echo_one("select id from ppc_ads where uid='$userid' $wap_string order by id desc limit 0,1");
mkdir($GLOBALS['banners_folder']."/$id/");

if(copy($_FILES['banner']['tmp_name'],$GLOBALS['banners_folder']."/$id/".$file))
{

//************************ Changes For Flash Ads ***************************************************************************



if($newexten=="swf")
{
$name=$GLOBALS['banners_folder']."/$id/".$file;
$swf_file=Inout_FlashImageRetrieveFun($name);

list($result, $flg_href) = Inout_FlashInfoFun($swf_file);    //*************** For Finding HardCoded Links***********************************

/*
if($flg_href !=0)
{
header("Location:show-message.php?id=10011");             //*************** If non-replacable Hard Coded Links Exists in the Image *******
exit(0);
}
*/

if(count($result) >0)                                    //*************** If HardCoded Links Already Exists ****************************
{
$hard_count=count($result);
mysql_query("update ppc_ads set hardcodelinks='$hard_count' where id='$id'");
}



if(count($result) >0)                                    //*************** If HardCoded Links Already Exists ****************************
{
   if ($swf_file) {
        if (Inout_FlashVersionFun($swf_file) >= 3 && Inout_FlashInfoFun($swf_file))
		 {
		
		   // SWF's requiring player version 6+ which are already compressed should stay compressed
            if (Inout_FlashVersionFun($swf_file) >= 6 && Inout_FlashCompressedFun($swf_file))
			{
                $compress = true;
            } 
			elseif (isset($compress))
			{
                $compress = true;
            } 
			else 
			{
                $compress = false;
            }

           $convert_links = array();
          
for($i=0;$i < count($result);$i++)
{
$convert_links[$i]=$i+1;
}

  list($result, $parameters) = Inout_FlashConvertFun($swf_file, $compress, $convert_links);              //********** For Removing HardCoded Links ***************

                     
$name=$GLOBALS['banners_folder']."/$id/".$file;           
Inout_FlashImageStoreFun($name,$result);                                                                    //********** For Storing Converted Images ***************     


            
        }
    }

}
}
//************************ Changes For Flash Ads ***************************************************************************

else
{



	
	
	

	//  image resizing

	$rimg = new ImageResizer($GLOBALS['banners_folder']."/$id/$file");
	$rimg->resize($width,$height,$GLOBALS['banners_folder']."/$id/$file");

	//  image resizing
	
		
	

		
	
}









}

mysql_query("INSERT INTO `ad_location_mapping` (`adid` , `country` , `region` , `city`)
VALUES ('$id', '00', '00', '00')");
//if($auto_keyword_approve==0)
//{
//	$key_status=-1;
//}
//else
//{
//	$key_status=$auto_keyword_approve;
//}

if($keywords_default!="")
{
	$mainid=$mysql->echo_one("select id from system_keywords where keyword='$keywords_default'");

mysql_query("insert into ppc_keywords values('0','$id','$userid','$keywords_default','$temp_val','1','".time()."','$min_click_value','$mainid');");
}


//****************************************************** CODE FOR TIME TARGETING ***********************************************








 
if($t_duration==2 && $tm_flg==1	)
{
	
mysql_query("insert into time_targeting values('0','$id','$sdate','$edate','0','0','0','0','$date_flg','0','0')");
}
else if($t_duration==3 && $tm_flg==1)
{
	$date_flg=trim($_POST['timeduration']);
	
	$timeduration=trim($_POST['timeduration']);
    
	
     mysql_query("insert into time_targeting values('0','$id','0','0','0','0','0','0','$date_flg','0','0')");
     
}


if($time_targeting==1)
{
//echo "UPDATE `ppc_ads` SET `beg_time`='$beg_time',`end_time`='$end_time' WHERE id='$id'"; exit;
mysql_query("UPDATE `ppc_ads` SET `beg_time`='$beg_time',`end_time`='$end_time' WHERE id='$id'");

}



if($time_date_targetting==1)
{
//echo "UPDATE `ppc_ads` SET `beg_time`='$beg_time',`end_time`='$end_time' WHERE id='$id'"; exit;
mysql_query("UPDATE `ppc_ads` SET `beg_time`='$beg_time',`end_time`='$end_time' WHERE id='$id'");

}



//****************************************************** CODE FOR TIME TARGETING ***********************************************


//admin notification mail
$msg = <<< EOB

Hello,

A new banner ad has been created at $ppc_engine_name.

Ad id		: $id
Created by	: $username


Login to your admin area for approving/deleting this ad.

Regards,
$ppc_engine_name

EOB;

//echo $msg;

if($script_mode!="demo")
xMail($admin_general_notification_email, "$ppc_engine_name - Banner Ad Created", $msg, $admin_general_notification_email, $email_encoding);
//
if($ad_keyword_mode==2)
{
	header("Location:ppc-manage-target-locations.php?id=$id&wap=$wap_flag&pass=true");
}
else
{

	//header("Location:show-success.php?id=5013");
	header("Location:ppc-manage-keywords.php?id=$id&pass=true&wap=$wap_flag");
}
exit(0);
?>