<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データ設定
//
//////////////////////////////////////////////////
if(isset($_SESSION["send_message"]))
{
	foreach( $_GET as $key => $value )
	{
		$_GET[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	
	//ユーザー確認
	$sql = "SELECT * FROM user WHERE del_flag=0 AND user_id=".$_SESSION["mms_user"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)!=0)
	{
		$user=true;
	}
	
	//アーティスト確認
	$sql = "SELECT * FROM artist WHERE del_flag=0 AND ar_id=".$_GET["message"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)!=0)
	{
		$artist=true;
	}
	if(!empty($user) && !empty($artist) && $_SESSION["send_message"]!="")
	{
		if(mb_strlen($_SESSION["send_message"])<=70)
		{
			//sql文作成
			$sql = "INSERT INTO ar_comment (
								comment,
								user_id,
								ar_id,
								reg_date) 
								VALUES (?,?,?,?);
								";
			//POSTDATAからMySQLに反映させるデータを入れる入れ物。
			$phs = array(
						$_SESSION["send_message"],
						$_SESSION["mms_user"],
						$_GET["message"],
						date("Y-m-d")
						);
			//インジェクション対策のsqlプリペアード関数
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			$db->ExecuteSQL($sql_prepare);
			unset($_SESSION["send_message"]);
			$_SESSION["ar_message"]="insert";
		}
	}
	else
	{
		$_SESSION["ar_message"]="not_insert";
	}
	$pass=explode("/",$_SERVER["REQUEST_URI"]);
	header( "Location: http://" .$_SERVER["HTTP_HOST"]."/artist/?view=".$_GET["message"]);
}
else
{
	$pass=explode("/",$_SERVER["REQUEST_URI"]);
	header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/artist/?view=".$_GET["message"]);
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