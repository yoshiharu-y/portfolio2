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

if(!empty($_POST["mms_password_comp"]))
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
		$row=$db->FetchRow($result_id);
		//サムネイル画像関係
		if($row["artist_thum"]=="no_thum.jpg")
		{
			$row["artist_thum"]=$tmpdir."no_image.jpg";
		}
		else
		{
			//フォルダ名　本サーバー
			$tmpdir=$tmpdir."../images/a".$row["ar_id"]."/";
			$row["artist_thum"]=$tmpdir."f_".$row["artist_thum"];
			
		}
		
		//htmlタグエスケープ
		foreach( $_POST as $key => $value )
		{
			$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
		}
		
		//パスワードの確認
		if(!$control->CheckMatch($_POST["new_password"],"alphabet"))
		{
			$_SESSION["password_err"]='<p class="coution">パスワードに英数字以外か、5文字以上20文字以下になっています。</p>';
		}
		if($_POST["new_password"]!=$_POST["check_password"])
		{
			$_SESSION["password_err"]='<p class="coution">新しいパスワードとパスワードが違います。</p>';
		}
		if($_POST["password"]==md5($_POST["new_password"]))
		{
			$_SESSION["password_err"]='<p class="coution">新しいパスワードが前のパスワードと同じです。別のパスワードにしてください。</p>';
		}

		
		if(!empty($_SESSION["password_err"]))
		{
			header( "Location: ./");
		}
		
		extract($_POST);
		//アップデート文
		$sql = "UPDATE ar_login SET
							password = ?
							WHERE ar_id = ? limit 1";
		//MySQLに反映させるデータを入れる入れ物。
		$phs = array(
						md5($new_password),
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