<?php

//////////////////////////////////////////////////
//
//　　　最上位のindex.php
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/
session_name("MMSWEBSITE");
session_start();

//画面指定
define("FILENAME","artist_list");

//////////////////////////////////////////////////
//
//　　以下読み込みファイルのパス
//
//////////////////////////////////////////////////

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");



?>