<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//データの更新は$_SESSION["audition_edit"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["audition_edit"]に入れている。

if(!empty($_POST["comp_check"])&&!empty($_POST["mn_movie_edit_comp"]))
{
	$sql = "SELECT * FROM mn_movie WHERE seq_num=".$_SESSION["mn_movie_edit"]["mn_movie"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
		//sqlアップデート

		//動画の更新日時を生成
		$_SESSION["mn_movie_edit"]["renew_date"]= date("Y-m-d");
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["mn_movie_edit"]);
		//sql文作成
		$sql = "UPDATE mn_movie SET
					movie_title = ?,
					movie_url = ?,
					movie_type = ?,
					renew_date = ?
					WHERE seq_num = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$movie_title,
				$movie_url,
				$movie_type,
				$renew_date,
				$mn_movie
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
		if(!$result_id)
		{
			$row["err_det"]='<p class="red">更新に失敗しました。再度編集お願いします。</p>';
		}
		else
		{
			$row["err_det"]='<p>更新しました。</p>';
		}
	}
}
else
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
//ログイン情報を残し$_SESSIONを初期化
$temp_login=$_SESSION["mms_admin_login"];
$_SESSION=array();
$_SESSION["mms_admin_login"]=$temp_login;
//var_dump($_SESSION);

//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////



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
echo $control->GetContentData();

?>