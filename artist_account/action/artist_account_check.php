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
	$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
}

//パスワードチェック
if(!$control->CheckMatch($_POST["password"],"alphabet"))
{
	$err.='<p style="color: red;">パスワードに英数字以外か、5文字以上20文字以下になっています。</p>';
}
if($_POST["password"]==$_SESSION["mms_artist_pass"])
{
	$err.='<p style="color: red;">新規パスワードが仮パスワードと同じです。別のパスワードにしてください。</p>';
}


//メールアドレスチェック
//PC
if(!$control->CheckMatch($_POST["mail"],"email"))
{
	$err.='<p style="color: red;">パソコンメールアドレスの形式が違います。</p>';
}
if($_POST["mail"]!=$_POST["c_mail"])
{
	$err.='<p style="color: red;">パソコンの確認メールアドレスととメールアドレスが違います。</p>';
}
//パソコンメール重複
$mail_sql = "select * from artist where del_flag = 0 and mail = ? ";
$phs = array($_POST["mail"]);
//インジェクション対策のsqlプリペアード関数
$sql_prepare = $db->mysql_prepare($mail_sql, $phs);
$mail_result = $db->ExecuteSQL($sql_prepare);
if (mysql_num_rows($mail_result)!=0) 
{
   $err.='<p style="color: red;">パソコンのメールアドレスは既に使われています。</p>';
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
	$err='<p style="color: red;">空欄になっている部分があります。全て必須項目です。</p>';
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

//プロフィール表示用
$row["view_detail"]=nl2br($row["detail"]);

if($err=="")
{
	$row["input_comp"]='<input type="submit" name="mms_comp" class="send_submit" value="アーティスト登録を完了する" />';
	$row["err_conf"]="";
	$row["flow"]="";
}
else
{
	$row["input_comp"]="";
	$row["err_conf"]='<div class="coutionBox">'.$err.'</div>';	
	$row["flow"]='<p style="color: red;">「アーティスト登録画面へ戻る」を押して修正してください。</p>';
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