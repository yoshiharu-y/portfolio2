<?php
/*
ﾏｲﾍﾟｰｼﾞﾌﾟﾛﾌｨｰﾙ編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////

//htmlタグエスケープ
foreach( $_POST as $key => $value )
{
	$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
}


$_POST["birthday"]=$_POST["year"]."-".$_POST["month"]."-".$_POST["day"];
$_POST["renew_date"]= date("Y-m-d");
extract($_POST);
//アップデート文
$sql = "UPDATE user SET
					user_name = ?,
					name = ?,
					kana = ?,
					age = ?,
					birthday = ?,
					sex = ?,
					zip = ?,
					address1 = ?,
					address2 = ?,
					renew_date = ?
					WHERE user_id = ? limit 1";
//MySQLに反映させるデータを入れる入れ物。
$phs = array(
				$user_name,
				$name,
				$kana,
				$age,
				$birthday,
				$sex,
				$zip,
				$address1,
				$address2,
				$renew_date,
				$_SESSION["mms_user"]
				);
//インジェクション対策のsqlプリペアード関数
$sql_prepare = $db->mysql_prepare($sql, $phs);
$db->ExecuteSQL($sql_prepare) or die("エラーが発生しました。");

//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//ユーザーデータ nameのみ抽出
$user_sql = "SELECT user_id, user_name, user_thum FROM user where user_id =".$_SESSION["mms_user"];
$user_result_id = $db->ExecuteSQL($user_sql);
if(mysql_num_rows($user_result_id)==0)
{
	header( "Location: ../");
}
else
{
	$user_row = $db->FetchRow($user_result_id);
	
	if($user_row["user_thum"]=="no_thum.jpg")
	{
		$user_row["user_thum"]="../images/no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."../images/e".$user_row["user_id"]."/";
		$user_row["user_thum"]=$tmpdir."b_".$user_row["user_thum"];
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

//定義したタイプ[data_s:]になっている所を変換
foreach( $user_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>