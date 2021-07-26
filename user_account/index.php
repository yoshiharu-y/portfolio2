<?php

//////////////////////////////////////////////////
//
//　　　user_account内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/
session_name("MMSWEBSITE");
session_start();

if(!empty($_GET["c"]))
{
	//メールからのリンク：登録完了ページ
	define("FILENAME","user_mail_comp");
}
else if($_SESSION["user_account_start"])
{	
	//表示ファイルの名前設定
	if(!empty($_POST["mms_check"]))
	{
		//確認画面指定
		define("FILENAME","user_account_check");
		
	}
	else if(!empty($_POST["mms_return"]))
	{
		//編集画面指定
		define("FILENAME","user_account_create");
		
	}
	else if(!empty($_POST["mms_comp"]))
	{	
		//仮登録完了ページ
		define("FILENAME","user_account_comp");
		
	}
	else
	{
		//デフォルト
		define("FILENAME","user_account_create");
	}
}
else
{
	//デフォルト
	//初回時の設定
	$_SESSION["user_account_start"]=true;
	define("FILENAME","user_account_create");

}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>