<?php

//////////////////////////////////////////////////
//
//　　　login内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/
//session初期化
session_name("MMSWEBSITE");
session_start();

$referer=$_SESSION["referer"];
$_SESSION=array();
$_SESSION["referer"]=$referer;

if(!empty($_POST["mms_user_login"]))
{
	define("FILENAME","user_login");
}
else if(!empty($_POST["mms_artist_login"]))
{
	define("FILENAME","artist_login");
}
else
{
	define("FILENAME","login");
}


//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>