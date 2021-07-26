<?php
//////////////////////////////////////////////////
//
//　　　audition/edit内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSADMIN");
session_start();
if($_SESSION["mms_admin_login"])
{
	if($_SESSION["edit_start"])
	{
		if(!empty($_POST["audition_edit_check"]))
		{
			//画面指定
			define("FILENAME","audition_edit_check");
	
		}
		else if(!empty($_POST["audition_edit_return"]))
		{
			//画面指定
			define("FILENAME","audition_edit");
		}
		else if(!empty($_POST["audition_edit_comp"]))
		{
			//完了チェックがはいっているかどうか
			if(!empty($_POST["comp_check"]))
			{
				//画面指定
				define("FILENAME","audition_edit_complate");
			}
			else
			{	
				//画面指定
				define("FILENAME","audition_edit_check");
			}
		}
		//編集中に戻るボタンを押しPOST値に$_POST["audition_edit"]が入ってしまった場合
		else
		{
			//画面指定
			define("FILENAME","audition_edit");
		}
	}
	else if(!empty($_POST["audition_edit"]))
	{
		//初回時の設定
		$_SESSION["edit_start"]=true;
		$_SESSION["audition_edit"]=array();
		$_SESSION["file_temp"]=array();
		$_SESSION["file_temp"]["aud_seq_num"]=$_POST["audition_no"];
		//画面指定
		define("FILENAME","audition_edit");	
	}
	else
	{
		//POSTデータや$_SESSION["edit_start"]がない場合
		header( "Location: ../");
	}
	/*echo "<pre>";
	print_r($_SESSION);
	print_r($_POST);
	echo "</pre>";*/

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