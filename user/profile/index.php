<?php

//////////////////////////////////////////////////
//
//　　　user_edit内のindex.php
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
	if(!empty($_POST["mms_user_prof_edit"]))
	{
		//prof編集ページ
		define("FILENAME","profile_edit");
	}
	if(!empty($_POST["mms_prof_check"]))
	{
		//prof編集確認ページ
		define("FILENAME","profile_edit_check");
	}
	else if(!empty($_POST["mms_prof_comp"]))
	{
		//prof編集完了ページ
		define("FILENAME","profile_edit_complate");
	}
	else if(!empty($_POST["mms_profile_return"]))
	{
		define("FILENAME","profile_edit");
	}
	else if(!empty($_POST["mms_user_pass_edit"]))
	{
		//password編集ページ
		header( "Location: ../password/");
	}
	else if(!empty($_POST["mms_user_img_edit"]))
	{
		//password編集ページ
		header( "Location: ../upload/");
	}
	else
	{
		//prof編集トップページ
		define("FILENAME","profile");
		
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