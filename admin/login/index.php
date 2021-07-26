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
session_name("MMSADMIN");
session_start();
$_SESSION=array();
session_regenerate_id(true);

//表示ファイルの名前設定
if(!empty($_POST["mms_admin_login"]))
{
	//確認画面指定
	define("FILENAME","login");

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