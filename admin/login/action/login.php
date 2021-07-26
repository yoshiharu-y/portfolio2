<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

$login="root";
$password="696d29e0940a4957748fe3fc9efd22a3";

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


if($login==$_POST["login_id"])
{
	if($password==md5(md5($_POST["password"])))
	{
		$_SESSION["mms_admin_login"]=true;
		header( "Location: ../user/");
	}
	else
	{
		$row["login_id"]=$_POST["login_id"];
		$row["password"]=$_POST["password"];
		$row["login_err"]='<p class="red">パスワードが違います</p>';
	}
}
else
{
	$row["login_id"]=$_POST["login_id"];
	$row["password"]=$_POST["password"];
	$row["login_err"]='<p class="red">ログインIDが違います</p>';
}
/*if (mysql_num_rows($result)==0) 
{
   	$row["login_id"]=$_POST["login_id"];
	$row["password"]="";
	$row["login_err"]='<p style="color: red;font-size:x-small;">ﾛｸﾞｲﾝIDかﾊﾟｽﾜｰﾄﾞが間違っています。</p>';
}else
{
	//ユーザーページへの移動
	$user=$db->FetchRow($result);
	$_SESSION["mms_unique_user"]=sprintf("%06d",$user["user_id"]);
	//print_r($_SESSION);
	header( "Location: ../user_mypage/".$ses);
}*/
	
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