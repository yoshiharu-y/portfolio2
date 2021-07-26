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

//データの更新は$_SESSION["movie_create"]内に入った物を使う。

if(!empty($_POST["comp_check"])&&!empty($_POST["mn_movie_create_comp"]))
{
	$row["err_det"]="";
	
	//記事の日時を生成
	$_SESSION["mn_movie_create"]["reg_date"]= date("Y-m-d");
	$_SESSION["mn_movie_create"]["renew_date"]= date("Y-m-d");
	//配列のキーを変数名とした変数を作る
	extract($_SESSION["mn_movie_create"]);
	//sql文作成

	$sql = "INSERT INTO mn_movie (
					movie_title,
					movie_url,
					movie_type,
					reg_date,
					renew_date) 
					VALUES (?,?,?,?,?)";
	//$_SESSION["movie_create"]からMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$movie_title,
					$movie_url,
					$movie_site,
					$reg_date,
					$renew_date
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	//echo $sql_prepare;
	$result_id = $db->ExecuteSQL($sql_prepare);
	if(!$result_id)
	{
		$row["err_det"]='<p class="red">投稿に失敗しました。再度動画投稿お願いします。</p>';
	}
	else
	{
		$row["err_det"]='<p>新規動画投稿が完了しました。</p>';
	}
}
else
{
	$row["err_det"]='<p class="red">新規投稿中にエラーが発生しました。再度投稿しなおしてください。</p>';
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