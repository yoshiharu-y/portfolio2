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

if(!empty($_POST["artist_entry"]))
{
	$get=htmlspecialchars($_GET["entry"], ENT_QUOTES, 'UTF-8');
	$sql="SELECT * FROM rel_artist_audition
			WHERE del_flag = 0
			AND aud_seq_num =".$get." 
			AND ar_id =".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$sql = "SELECT * FROM audition_det 
			WHERE del_flag = 0
			AND hidden_flag = 0 
			AND aud_seq_num =".$get;
		$result_id = $db->ExecuteSQL($sql);
		$row = $db->FetchRow($result_id);
		if(strtotime($row["entry_end"])>strtotime(date("Y-m-d H:i:s")))
		{
			//sql文作成
			$sql = "INSERT INTO rel_artist_audition (
								aud_seq_num,
								ar_id,
								ar_point,
								reg_date) 
								VALUES (?,?,?,?);
								";
			//MySQLに反映させるデータを入れる入れ物。
			$phs = array(
							$get,
							$_SESSION["mms_artist"],
							0,
							date("Y-m-d")
							);
			//インジェクション対策のsqlプリペアード関数
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			$db->ExecuteSQL($sql_prepare) or die("データベースエラーが発生しました。");
			$_SESSION["entry_comp"]="insert";
		}
		else
		{
			$_SESSION["entry_comp"]="no_entry";
		}
	}
	header( "Location: http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
}
else
{
	$pass=explode("/",$_SERVER["REQUEST_URI"]);
	header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/audition/error.html");
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