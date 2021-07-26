<?php
//////////////////////////////////////////////////
//
//　　　artist内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSWEBSITE");
session_start();

if(!empty($_GET["message"]))
{
	if(!empty($_SESSION["mms_user"]) && isset($_SESSION["send_message"]))
	{
		//メッセージ画面指定
		define("FILENAME","message");
	}
	else
	{
		//コメント入力以外からきた場合の指定
		header( "Location: ../");
	}
}
else if(!empty($_SESSION["mms_artist"]))
{
	//入力画面
	if(!empty($_POST["res_message"]) || !empty($_POST["mms_message_return"]))
	{
		//画面指定
		define("FILENAME","message_edit");

	}
	//入力確認
	else if(!empty($_POST["mms_message_check"]))
	{
		//画面指定
		define("FILENAME","message_edit_check");
	}
	//編集完了
	else if(!empty($_POST["mms_message_comp"]))
	{
		//完了チェックがはいっているかどうか
		if(!empty($_POST["comp_check"]))
		{
			
			//画面指定
			define("FILENAME","message_edit_complate");
		}
		else
		{	
			//画面指定
			define("FILENAME","message_edit_check");
		}
	} 
	else
	{
		//マイページ
		header( "Location: ../");
	}
}
else
{
	//GETが無い場合のデフォルト
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