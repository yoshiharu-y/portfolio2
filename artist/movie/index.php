<?php

//////////////////////////////////////////////////
//
//　　　artist profile内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

session_name("MMSWEBSITE");
session_start();
//表示ファイルの名前設定
if(!empty($_SESSION["mms_artist"]))
{
	//確認画面
	if(!empty($_POST["movie_edit_check"]))
	{
		//画面指定
		define("FILENAME","movie_edit_check");

	}
	//編集画面
	else if(!empty($_POST["movie_edit_return"]))
	{
		//画面指定
		define("FILENAME","movie_edit");
	}
	//編集完了
	else if(!empty($_POST["movie_edit_comp"]))
	{
		//完了チェックがはいっているかどうか
		if(!empty($_POST["comp_check"]))
		{
			//画面指定
			define("FILENAME","movie_edit_complate");
		}
		else
		{	
			//画面指定
			define("FILENAME","movie_edit_check");
		}
	} 
	else 
	{
		//初回時の設定
		$_SESSION["movie_edit"]=array();
		//画面指定
		define("FILENAME","movie_edit");
	}
}
else
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