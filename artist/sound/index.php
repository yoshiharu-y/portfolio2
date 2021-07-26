<?php

//////////////////////////////////////////////////
//
//　　　artist sound内のindex.php
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
	//アップロード
	if(!empty($_POST["sound_create"]))
	{
		//画面指定
		define("FILENAME","sound_create");
	}
	//エディット
	else if(!empty($_POST["sound_conf"]))
	{
		foreach($_POST as $key => $value)
		{
			if(strpos($key,"edit") !== false)
			{
				//画面指定
				define("FILENAME","sound_edit");
			}
			else if(strpos($key,"drop") !== false)
			{
				//画面指定
				define("FILENAME","sound_drop");
			}
		}
	}
	//エディット完了
	else if(!empty($_POST["mms_sound_comp"]))
	{
		//画面指定
		define("FILENAME","sound_edit_complate");
	}
	//リスト
	else
	{
		//画面指定
		define("FILENAME","sound_list");	
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