<?php
//////////////////////////////////////////////////
//
//　　　sound/edit内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSADMIN");
session_start();
if($_SESSION["mms_admin_login"])
{
	if(!empty($_POST["sound_edit_comp"]))
	{
		//完了チェックがはいっているかどうか
		if(!empty($_POST["comp_check"]))
		{
			//画面指定
			define("FILENAME","sound_edit_complate");
		}
		else
		{	
			//画面指定
			define("FILENAME","sound_edit");
		}
	}
	//編集中に戻るボタンを押しPOST値に$_POST["sound_edit"]が入ってしまった場合
	else
	{
		$_SESSION["edit_start"]=true;
		//画面指定
		define("FILENAME","sound_edit");
	}
}
else
{
	//デフォルト
	header( "Location: ../");
	session_destroy();
}
//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");


?>