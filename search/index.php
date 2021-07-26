<?php

//////////////////////////////////////////////////
//
//　　　search内のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

session_name("MMSWEBSITE");
session_start();


if(!empty($_POST["search_text"]))
{	
	//検索ページへ
	define("FILENAME","search");
}
else if(empty($_POST["search_text"]))
{
	define("FILENAME","search");
}
else
{	//404
	header( "Location: error.html");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>