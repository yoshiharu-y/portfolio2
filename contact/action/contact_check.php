<?php
/*
サブオーディション編集確認画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

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
	$err.='<p style="color: red">お名前(カナ)が正しく入力されていません。</p>';
}	


//メールアドレスチェック
if(!$control->CheckMatch($_POST["mail"],"email"))
{
	$err.='<p style="color: red">メールアドレスの形式が違います。</p>';
}
if($_POST["mail"]!=$_POST["c_mail"])
{
	$err.='<p style="color: red">確認メールアドレスとメールアドレスが違います。</p>';
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
	$err='<p style="color: red">空欄になっている部分があります。全て必須項目です。</p>';
}

if($err=="")
{
	$row["input_comp"].='<input type="submit" name="mms_contact_comp" value="送信" class="send_submit" />';
	$row["err_conf"]="";
	$row["flow"]="";
}
else
{
	$row["input_comp"]="";
	$row["err_conf"]='<div class="errorBox">'.$err.'</div>';		
	$row["flow"]='<p style="color: red">「お問い合わせ画面へ戻る」を押して修正してください。</p>';
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