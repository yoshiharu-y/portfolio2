<?php

//////////////////////////////////////////////////
//
//　　　user/message内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

//セッション
session_name("MMSWEBSITE");
session_start();
//表示ファイルの名前設定
if(!empty($_SESSION["mms_user"]))
{
	//編集ページ
	if(!empty($_GET["message"]))
	{
		//通常
		define("FILENAME","message_detail");
	}
	else
	{
		//通常
		header( "Location: ../");
	}
	
}
else
{
	//デフォルト
	header( "Location: ../login/");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>