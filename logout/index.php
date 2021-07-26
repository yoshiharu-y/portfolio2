



<?php

//////////////////////////////////////////////////
//
//　　　mypage内のindex.php
//
//
//      移動先のみの記述
//
//////////////////////////////////////////////////


/*--
静的部、動的部の読み込み
--*/

//セッション
session_name("MMSWEBSITE");
session_start();
//表示ファイルの名前設定
if(!empty($_SESSION["mms_user"]))
{
	//セッション破壊とクッキー破壊
	$_SESSION = array();
	if(isset($_COOKIE['PHPSESSID'])){
		setcookie('PHPSESSID', '', time() - 1800, '/');
	}
	session_destroy();
	header("Location: http://beyata.minibird.jp/");
}
else if(!empty($_SESSION["mms_artist"]))
{
	//セッション破壊とクッキー破壊
	$_SESSION = array();
	if(isset($_COOKIE['PHPSESSID'])){
		setcookie('PHPSESSID', '', time() - 1800, '/');
	}
	session_destroy();
	header("Location: http://beyata.minibird.jp/");
}
else if(!empty($_SESSION["mms_artist"]) && !empty($_SESSION["mms_user"]))
{
	//セッション破壊とクッキー破壊
	$_SESSION = array();
	if(isset($_COOKIE['PHPSESSID'])){
		setcookie('PHPSESSID', '', time() - 1800, '/');
	}
	session_destroy();
	//強制でトップページへ
	header( "Location: http://beyata.minibird.jp/");
}
else
{
	//デフォルト
	header( "Location: http://beyata.minibird.jp/");
}

?>