<?php
//////////////////////////////////////////////////
//
//　　　movie内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSADMIN");
session_start();
if($_SESSION["mms_admin_login"])
{
	//ログイン情報を残し$_SESSIONを初期化
	$temp_login=$_SESSION["mms_admin_login"];
	$_SESSION=array();
	$_SESSION["mms_admin_login"]=$temp_login;
	if(!empty($_POST["det"]))
	{
		//画面指定
		define("FILENAME","movie_detail");
	}
	else if(!empty($_POST["mn_det"]))
	{
		//画面指定
		define("FILENAME","mn_movie_detail");
	}
	else if(!empty($_GET["mn"]))
	{
		//画面指定
		define("FILENAME","mn_movie_list");
	}
	else
	{
		//画面指定
		define("FILENAME","movie_list");
	}
}
else
{
	//デフォルト
	header( "Location: ../");
	session_destroy();
}
//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");


?>