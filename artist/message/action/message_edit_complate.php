<?php


@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////

//送信者のアーティストのデータチェック
$sql = "SELECT * FROM artist where del_flag= 0 and ar_id =".$_SESSION["mms_artist"];
$check_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($check_id)==0)
{
	header( "Location: ../../login/");
}


if(!empty($_POST["mms_message_comp"]))
{
	//ユーザーデータの存在を確認
	$sql = "SELECT * FROM user WHERE del_flag= 0 AND user_id =".$_POST["uid"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["err_det"]='<p class="coution">ユーザーが存在していない可能性があります。</p>';
	}
	else
	{
		//htmlタグエスケープ＆文字強制変換
		foreach( $_POST as $key => $value )
		{
			$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
		}
		
		extract($_POST);
		//アップデート文
		$sql = "INSERT INTO user_comment (
								comment,
								user_id,
								ar_id,
								reg_date) 
								VALUES (?,?,?,?);
								";
		//MySQLに反映させるデータを入れる入れ物。
		$phs = array(
						nl2br($message),
						$uid,
						$_SESSION["mms_artist"],
						date("Y-m-d")
						);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$db->ExecuteSQL($sql_prepare);
		$row["err_det"]='<p class="complate">メッセージ送信が完了しました</p>';
	}
}
else
{
	//デフォルト
	header( "Location: ../");
}

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
		$tmpdir=$tmpdir."../images/a".$artist_row["ar_id"]."/";
		$artist_row["artist_thum"]=$tmpdir."f_".$artist_row["artist_thum"];
		
	}
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

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>