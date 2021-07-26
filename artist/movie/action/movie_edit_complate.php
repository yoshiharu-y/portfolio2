<?php
/*
ﾏｲﾍﾟｰｼﾞﾌﾟﾛﾌｨｰﾙ編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////

if(!empty($_POST["comp_check"]) && isset($_SESSION["movie_edit"]))
{
	$sql = "SELECT * FROM ar_movie WHERE ar_id=".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$_SESSION["movie_edit"]["reg_date"]= date("Y-m-d");
		$_SESSION["movie_edit"]["renew_date"]= date("Y-m-d");
		extract($_SESSION["movie_edit"]);
		unset($_SESSION["movie_edit"]);
		$sql = "INSERT INTO ar_movie (
					movie_title,
					movie_url,
					movie_type,
					reg_date,
					renew_date,
					ar_id) 
					VALUES (?,?,?,?,?,?)";
		//$_SESSION["movie_create"]からMySQLに反映させるデータを入れる入れ物。
		$phs = array(
						$movie_title,
						$movie_url,
						$movie_type,
						$reg_date,
						$renew_date,
						$_SESSION["mms_artist"]
						);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
	}
	else
	{
		$row = $db->FetchRow($result_id);
		//sqlアップデート
		//動画の更新日時を生成
		$_SESSION["movie_edit"]["renew_date"]= date("Y-m-d");
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["movie_edit"]);
		unset($_SESSION["movie_edit"]);
		//sql文作成
		$sql = "UPDATE ar_movie SET
					movie_title = ?,
					movie_url = ?,
					movie_type = ?,
					renew_date = ?
					WHERE ar_id = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$movie_title,
				$movie_url,
				$movie_type,
				$renew_date,
				$_SESSION["mms_artist"]
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
	}
}
else
{
	header( "Location: ../");
}

//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//サムネイル画像関係
$artist_sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$_SESSION["mms_artist"]."'";
$artist_result_id = $db->ExecuteSQL($artist_sql);

if(mysql_num_rows($artist_result_id)==0)
{
	header( "Location: ../");
}
else
{
	$artist_row=$db->FetchRow($artist_result_id);
	//サムネイル画像関係
	if($artist_row["artist_thum"]=="no_thum.jpg")
	{
		$artist_row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$artist_row["ar_id"]."/";
		$artist_row["artist_thum"]=$tmpdir."f_".$artist_row["artist_thum"];
		
	}
}

//html内容を保持
$control->SetContentData($content);

//html内の特殊タグを変換するのに必要なタイプを設定
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>