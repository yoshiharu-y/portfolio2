<?php
/*
サブオーディション編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////


//date("Y-m-d");
if(!empty($_POST["mms_comp"]) && !empty($_SESSION["mms_artist_pass"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}

	
	//POSTを確認表示用に変換
	foreach( $_POST as $key => $value )
	{	
		$row[$key]= $value;
	}
	
	//性別の表示
	if($_POST["sex"]==0)
	{
		$row["sex_type"]="男性";
	}
	else
	{
		$row["sex_type"]="女性";	
	}
	
	$_POST["password"]=md5($_POST["password"]);	
	$_POST["ar_id"]=$_SESSION["mms_artist"];
	$_POST["birthday"]=$_POST["year"]."-".$_POST["month"]."-".$_POST["day"];
	//メールアドレス結合
	$_POST["artist_thum"]="no_thum.jpg";
	$_POST["reg_date"]= date("Y-m-d");
	
	extract($_POST);
	//sql文作成
	$sql = "INSERT INTO artist (
						artist_name,
						ar_id,
						age,
						birthday,
						sex,
						mail,
						detail,
						artist_thum,
						reg_date) 
						VALUES (?,?,?,?,?,?,?,?,?);
						";
	//POSTからMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$artist_name,
					$ar_id,
					$age,
					$birthday,
					$sex,
					$mail,
					nl2br($detail),
					$artist_thum,
					$reg_date
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	$db->ExecuteSQL($sql_prepare);
	//echo $sql_prepare;
	//print_r($phs);
	//sql文作成
	$sql = "UPDATE ar_login SET
						password =?
						WHERE ar_id = ? limit 1";
	//POSTからMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$password,
					$ar_id
					);
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	$db->ExecuteSQL($sql_prepare);
	//一時保存のパスワード削除
	unset($_SESSION["mms_artist_pass"]);

}
else
{
	die("エラーが発生しました、お手数ですが再度ログインからやり直して下さい。");	
}

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