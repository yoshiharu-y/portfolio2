<?php

//////////////////////////////////////////////////
//
//　　　contact内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

/*if(!empty($_GET["co"]))
{*/
	//表示ファイルの名前設定
	if(!empty($_POST["mms_contact"]))
	{
		//確認画面指定
		define("FILENAME","contact_check");
		
	}
	else if(!empty($_POST["mms_contact_comp"]))
	{	
		//編集完了ページ
		define("FILENAME","contact_comp");
		
	}
	else
	{
		//デフォルト
		define("FILENAME","contact_create");
	}
/*}
else
{
	
	//デフォルト
	//define("FILENAME","contact_create");
	define("FILENAME","contact");

}*/

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>