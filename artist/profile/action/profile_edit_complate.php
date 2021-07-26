<?php


@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//ユーザーの画像保存フォルダがある場所
$usertmpdir  = "../user/images/";

//ユーザーのMP3保存フォルダがある場所
$soundtmpdir  = "mp3/";

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////

if(!empty($_POST["mms_prof_comp"]))
{
	//データの存在を確認
	$sql = "SELECT * FROM artist where del_flag= 0 and ar_id =".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		header( "Location: ../../login/");
	}
	else
	{
		$row = $db->FetchRow($result_id);
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
		
		//htmlタグエスケープ＆文字強制変換
		foreach( $_POST as $key => $value )
		{
			$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
		}
		
		extract($_POST);
		//アップデート文
		$sql = "UPDATE artist SET
							detail = ?,
							renew_date = ?
							WHERE ar_id = ? limit 1";
		//MySQLに反映させるデータを入れる入れ物。
		$phs = array(
						nl2br($detail),
						date("Y-m-d"),
						$_SESSION["mms_artist"]
						);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$db->ExecuteSQL($sql_prepare);
	}
}
else
{
	//デフォルト
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