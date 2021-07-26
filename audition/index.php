<?php

//////////////////////////////////////////////////
//
//　　　audition内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

session_name("MMSWEBSITE");
session_start();


if(!empty($_GET["view"]))
{	
	if(!empty($_GET["check"]))
	{
		if(!empty($_SESSION["mms_user"]))
		{
			if(!empty($_POST["mms_aud_point_comp"]))
			{
				//ファイル名指定
				define("FILENAME","audition_point_comp");	
			}
			else
			{
				//ファイル名指定
				define("FILENAME","audition_point_check");
			}
			
		}
		else
		{
			//ログインページへ
			$_SESSION["referer"]="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
			$pass=explode("/",$_SERVER["REQUEST_URI"]);
			header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/login/");
		}
		
	}
	else
	{
		//ファイル名指定
		define("FILENAME","audition");
	}
}
//アーティストマイページで生成されるaud_app_noとurlに付随するarviewが等しい場合
else if(!empty($_SESSION["mms_artist"]) && !empty($_SESSION["aud_app_no"]))
{
	if($_SESSION["aud_app_no"]==$_GET["arview"])
	{
		if(!empty($_POST["artist_entry"]))
		{
			//ファイル名指定
			define("FILENAME","audition_entry");
		}
		else if(!empty($_GET["entry"]))
		{
			//ファイル名指定
			define("FILENAME","audition");
		}
		else
		{
			//ファイル名指定
			define("FILENAME","audition_list");
		}
	}
	else
	{
		unset($_SESSION["aud_app_no"]);
		//ファイル名指定
		define("FILENAME","audition_list");
	}
}
else
{
	unset($_SESSION["aud_app_no"]);
	//ファイル名指定
	define("FILENAME","audition_list");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>