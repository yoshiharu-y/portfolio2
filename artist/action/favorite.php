<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データ設定
//
//////////////////////////////////////////////////

if(!empty($_POST["artist_favorite"]))
{
	foreach( $_GET as $key => $value )
	{
		$_GET[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	$sql = "SELECT * FROM favorite WHERE user_id=".$_SESSION["mms_user"]." AND ar_id =".$_GET["view"];
	$result_id = $db->ExecuteSQL($sql);
	
	if(mysql_num_rows($result_id)==0)
	{
		//sql文作成
		$sql = "INSERT INTO favorite (
							user_id,
							ar_id,
							fav_point,
							reg_date) 
							VALUES (?,?,?,?);
							";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
					$_SESSION["mms_user"],
					$_GET["view"],
					1,
					date("Y-m-d")
					);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$db->ExecuteSQL($sql_prepare) or die("データベースエラーが発生しました。");
		$_SESSION["ar_favorite"]="insert";
	}
	header( "Location: http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
}
else
{
	$pass=explode("/",$_SERVER["REQUEST_URI"]);
	header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/artist/error.html");
}

	
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//ログイン時、ナビゲーションの画像をログアウトにする。
if(!empty($_SESSION["mms_user"]) || !empty($_SESSION["mms_artist"]))
{
	$content=str_replace("g_navi_01.gif", "g_navi_09.gif", $content);
}

/*

//html内容を保持
$control->SetContentData($content);

//html内の特殊タグを変換するのに必要なタイプを設定
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();*/

?>