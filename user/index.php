<?php

//////////////////////////////////////////////////
//
//　　　user_account内のindex.php
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
	if(!empty($_POST["mms_user_profile"]))
	{
		//編集ページ
		header( "Location: profile/");
	}
	else if(!empty($_POST["mms_user_message"]))
	{
		//メッセージページ
		header( "Location: message/");
	}
	else if(!empty($_POST["mms_user_logout"]))
	{
		//ﾛｸﾞｱｳﾄ
		$_SESSION=array();
		session_destroy();
		header( "Location: http://beyata.minibird.jp/");
	}
	else
	{
		//通常
		define("FILENAME","mypage");
	}
	
}
else if(!empty($_SESSION["mms_artist"]))
{
	header( "Location: ../artist/");
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