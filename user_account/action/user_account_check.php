<?php
/*
ﾕｰｻﾞｰ登録確認画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//ip保存パス
//$filepass="ipadd/plBNtSgnMMbFUChtAMg.txt";
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


//ipチェック
/*if($fp = @fopen($filepass, "r"))
{
	while( ! feof( $fp ) )
	{
	   preg_match("/ip:\[".$_SERVER['REMOTE_ADDR']."\]/",fgets( $fp, 4096 ),$mach);
	   if($mach[0]!="")
	   {
			$ip="exist";  
	   }
	}
	fclose($fp);
}*/

//ログインIDチェック
if(!$control->CheckMatch($_POST["login_id"],"alphabet"))
{
	$err.='<p style="color: red;">ログインIDに英数字以外か、5文字以上20文字以下になっています。</p>';
}

//既にログインidがないかsql検索
$sql = "select * from user where del_flag = 0 and login_id = ? ";
$phs = array($_POST["login_id"]);
//インジェクション対策のsqlプリペアード関数
$sql_prepare = $db->mysql_prepare($sql, $phs);
$result = $db->ExecuteSQL($sql_prepare);
if (mysql_num_rows($result)!=0) 
{
   $err.='<p style="color: red;">ログインIDが既に使われています。</p>';
}


//パスワードチェック
if(!$control->CheckMatch($_POST["password"],"alphabet"))
{
	$err.='<p style="color: red;">パスワードに英数字以外か、5文字以上20文字以下になっています。</p>';
}


//カナチェック
if(!$control->CheckMatch($_POST["kana"],"kana"))
{
	$err.='<p style="color: red;">お名前(カナ)が正しく入力されていません。</p>';
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
$mail_sql = "select * from user where del_flag = 0 and mail = ? ";
$phs = array($_POST["mail"]);
//インジェクション対策のsqlプリペアード関数
$sql_prepare = $db->mysql_prepare($mail_sql, $phs);
$mail_result = $db->ExecuteSQL($sql_prepare);
if (mysql_num_rows($mail_result)!=0) 
{
   $err.='<p style="color: red;">パソコンのメールアドレスが重複しています。</p>';
}


//郵便番号チェック
if(!$control->CheckMatch($_POST["zip"],"number") || mb_strlen($_POST["zip"])!=7)
{
	$err.='<p style="color: red;">郵便番号が間違っています。</p>';
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



if($err=="")
{
	$row["input_comp"]='<input type="submit" name="mms_comp" value="ユーザー登録を完了する" class="send_submit" />';
	$row["err_conf"]="";
	$row["flow"]="";
}
else if($ip=="exist")
{
	$row["input_comp"]="";
	$row["err_conf"]='<p style="color: red;"><b>同一IPアドレスで既に登録されています。</b></p>';
	$row["flow"]="";
	unset($_SESSION["user_account_start"]);
}
else
{
	$row["input_comp"]="";
	$row["err_conf"]='<div class="coutionBox">'.$err.'</div>';	
	$row["flow"]='<p style="color: red;">「新規登録画面へ戻る」を押して修正してください。</p>';
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