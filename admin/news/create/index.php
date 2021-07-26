<?php
//////////////////////////////////////////////////
//
//　　　news/create内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSADMIN");
session_start();
if($_SESSION["mms_admin_login"])
{
	if($_SESSION["create_start"])
	{

		if(!empty($_POST["news_create_check"]))
		{
			
			//画面指定
			define("FILENAME","news_create_check");
	
		}
		else if(!empty($_POST["news_create_return"]))
		{
			//画面指定
			define("FILENAME","news_create");
		}
		else if(!empty($_POST["news_create_comp"]))
		{
			//完了チェックがはいっているかどうか
			if(!empty($_POST["comp_check"]))
			{
				//画面指定
				define("FILENAME","news_create_complate");
			}
			else
			{	
				//画面指定
				define("FILENAME","news_create_check");
			}
		}
		//編集中に戻るボタンを押しPOST値に$_POST["news_create"]が入ってしまった場合
		else
		{
			//画面指定
			define("FILENAME","news_create");
		}
	}
	else if(!empty($_POST["news_create"]))
	{
		
		//初回時の設定
		$_SESSION["create_start"]=true;
		$_SESSION["news_create"]=array();
		//画面指定
		define("FILENAME","news_create");	
	}
	else
	{
		//POSTデータや$_SESSION["create_start"]がない場合
		header( "Location: ../");
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