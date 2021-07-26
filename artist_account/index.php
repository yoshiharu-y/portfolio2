<?php

//////////////////////////////////////////////////
//
//　　　artist_accountのindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/
session_name("MMSWEBSITE");
session_start();

//画面指定

if(!empty($_SESSION["mms_artist"]) && !empty($_SESSION["mms_artist_pass"]))
{

	//表示ファイルの名前設定
	if(!empty($_POST["mms_check"]))
	{
		//確認画面指定
		define("FILENAME","artist_account_check");
		
	}
	else if(!empty($_POST["mms_comp"]))
	{	
		//編集完了ページ
		define("FILENAME","artist_account_comp");
		
	}
	else
	{
		//デフォルト
		define("FILENAME","artist_account_create");
	}
}
else
{
	//define("FILENAME","artist_account");
	header( "Location: ../mypage/");
}
//////////////////////////////////////////////////
//
//　　以下読み込みファイルのパス
//
//////////////////////////////////////////////////

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");



?>