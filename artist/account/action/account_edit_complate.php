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

if(!empty($_POST["comp_check"]) && isset($_SESSION["account_edit"]))
{
	$sql = "SELECT * FROM artist where del_flag = 0 and ar_id =".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		header( "Location: ../");
	}
	else
	{
		$row = $db->FetchRow($result_id);
		
		//画像保存フォルダがある場所
		$tmpdir  = "../images/";
		
		//サムネイル画像関係
		if($row["artist_thum"]=="no_thum.jpg")
		{
			$row["artist_thum"]=$tmpdir."no_image.jpg";
		}
		else
		{
			//フォルダ名　本サーバー
			$tmpdir=$tmpdir."a".$row["ar_id"]."/";
			$row["artist_thum"]=$tmpdir."f_".$row["artist_thum"];
			
		}
		
		//sqlアップデート
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["account_edit"]);
		unset($_SESSION["account_edit"]);
		//sql文作成
		$sql = "UPDATE artist SET
					artist_name = ?,
					sex = ?,
					age = ?,
					birthday = ?,
					renew_date = ?
					WHERE ar_id = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$artist_name,
				$sex,
				$age,
				$birthday,
				date("Y-m-d"),
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

//ログイン時、ナビゲーションの画像をログアウトにする。
if(!empty($_SESSION["mms_user"]) || !empty($_SESSION["mms_artist"]))
{
	$content=str_replace("g_navi_01.gif", "g_navi_09.gif", $content);
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

//htmlを表示
echo $control->GetContentData();

?>