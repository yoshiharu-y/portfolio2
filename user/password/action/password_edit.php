<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//パスワード変更ボタンが押された場合
if(!empty($_POST["mms_password_check"]))
{
	
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		//エンコード確認
		if(!mb_check_encoding($_POST[$key],'UTF-8'))
		{
			$enc_err=true;
		}
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	//パスワードチェック
	if(!$control->CheckMatch($_POST["new_password"],"alphabet"))
	{
		$err.='<p class="coution">パスワードが英数字以外か、5文字以上20文字以下になっています。</p>';
	}
	
	if($_POST["new_password"]!=$_POST["check_password"])
	{
		$err.='<p class="coution">新しいパスワードと確認用パスワードが違います。</p>';
	}
	
	if($err=="")
	{
		//セッションに一時的にパスワード保存しリダイレクトで飛ばす。compでセッション内パスワードは削除
		$_SESSION["mms_user_pass"]=$_POST["new_password"];
		header( "Location:".$_SERVER['HTTP_REFERER']);
	}
	else
	{
		$row["err_conf"]='<div class="coutionBox">'.$err.'</div>';	
		$row["new_password"]="";
		$row["check_password"]="";
	}
}
else
{
	//初期設定
	$row["err_conf"]="";
	$row["new_password"]="";
	$row["check_password"]="";
}
$row["ses"]=$ses;
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//ユーザーデータ 画像表示に必要な要素のみ抽出
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