<?php

//////////////////////////////////////////////////
//
//　　　password内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

session_name("MMSWEBSITE");
session_start();

//表示ファイルの名前設定
if(!empty($_SESSION["mms_user"]))
{
	if(!empty($_SESSION["mms_user_pass"]))
	{
		//更新ページ
		define("FILENAME","password_complate");
	}
	else
	{
		//prof編集ページ
		define("FILENAME","password_edit");
	}
}else
{
	//デフォルト
	header( "Location: ../../login/");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";
//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>