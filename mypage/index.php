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
	//ユーザーマイページへ
	header( "Location: ../user/");
}
else if(!empty($_SESSION["mms_artist"]))
{
	//アーティストマイページへ
	header( "Location: ../artist/");
}
else if(!empty($_SESSION["mms_artist"]) && !empty($_SESSION["mms_user"]))
{
	//アーティストマイページへ
	header( "Location: ../artist/");
}
else
{
	//デフォルト
	header( "Location: ../login/");
}

?>