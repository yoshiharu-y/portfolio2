<?php
/*
サブオーディション編集確認画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

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

//カナチェック
if(!$control->CheckMatch($_POST["kana"],"kana"))
{
	$err.='<p>お名前(ｶﾅ)が正しく入力されていません。</p>';
}	
	
//郵便番号チェック
if(!$control->CheckMatch($_POST["zip"],"number") || mb_strlen($_POST["zip"])!=7)
{
	$err.='<p>郵便番号が間違っています。</p>';
}	


//POSTを確認表示用に変換 空欄チェック
foreach( $_POST as $key => $value )
{	
	$row[$key]= $value;
	if($value=="")
	{
		$err_brank=1;	
	}
}


//空欄がある場合のエラー
if($err_brank==1)
{
	$err='<p>空欄になっている部分があります。全て必須項目です。</p>';
}

//年齢計算
$row["age"] = (int) ((date('Ymd')-intval($row["year"].$row["month"].$row["day"]))/10000);

//性別の表示
if($_POST["sex"]==0)
{
	$row["sex_type"]="男性";
}
else
{
	$row["sex_type"]="女性";	
}



if($err=="")
{
	$row["input_comp"]='<input type="submit" name="mms_prof_comp" value="編集を完了する" class="send_submit" />';
	$row["err_conf"]="";
	$row["flow"]="";
}
else
{
	$row["input_comp"]="";
	$row["err_conf"]='<div class="coutionBox">'.$err.'</div>';	
	$row["flow"]='<p>「編集画面へ戻る」を押して修正してください。</p>';
}
$row["ses"]=$ses;
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