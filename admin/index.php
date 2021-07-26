<?php

//////////////////////////////////////////////////
//
//　　　最上位のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/
session_name("MMSADMIN");
session_start();
if(!empty($_GET["out"]))
{
	//print_r($_GET);
	session_start();
	$_SESSION=array();
	session_destroy();

}
if($_SESSION["mms_admin_login"])
{
	header( "Location: user/");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/top.html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/top.php");

?>