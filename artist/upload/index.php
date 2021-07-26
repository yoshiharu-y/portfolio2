<?php

//////////////////////////////////////////////////
//
//　　　user/upload内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

//セッション
session_name("MMSWEBSITE");
session_start();
//表示ファイルの名前設定
if(!empty($_SESSION["mms_artist"]))
{
	
	//編集ページ
	if(!empty($_POST["mms_artist_image"]))
	{
		//更新完了
		define("FILENAME","upload_edit_complate");
	}
	else
	{
		//通常
		$_SESSION["file_temp"]=array();
		$_SESSION["file_temp"]["artist_id"]=$_SESSION["mms_artist"];
		define("FILENAME","upload_edit");
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