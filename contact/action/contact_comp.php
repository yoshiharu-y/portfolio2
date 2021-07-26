<?php
/*
サブオーディション編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////

//date("Y-m-d");
if(!empty($_POST["mms_contact_comp"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}

	//POSTDATAを確認表示用に変換
	foreach( $_POST as $key => $value )
	{	
		$row[$key]= $value;
	}
	
	//$conatact_mail="";
	//宛先(cofing.php 内の$contact_mail
	$to =$contact_mail;
	//差出人
	$header = "From: ".mb_encode_mimeheader($_POST["name"])."<".$_POST["mail"].">";
	//件名
	$subject = "お問い合わせメール";
	
	//本文
	$body ='
	お問い合わせフォームからメールが届きました。
	
	お問い合わせ内容
	---------------------------------------------------

	'.$_POST["contact"].'
	
	---------------------------------------------------
	
	お名前：'.$_POST["name"].'
	お名前（カナ）：'.$_POST["kana"].'
	メールアドレス：'.$_POST["mail"].'
	
	---------------------------------------------------
	';
	
	if(!mb_send_mail($to,$subject,$body,$header))
	{
		$row["mail_conf"]='
		<span style="color: red">エラーが発生し、メールが送信されませんでした。<br />
		お手数ですが、時間を空けて再度お問い合わせフォームから連絡をお願いいたします。</span><br />';
		
	}else
	{
		$row["mail_conf"]='お問い合わせを受け付けました。';
	};
}else
{
	die("エラーが発生しました");	
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

//POSTDATA内の[tag:]を変換するのに必要なタイプを設定
$control->SetContentType("tag");
	
//$controlに保持している[tag:]を変換する
foreach( $row as $value )
{
	$ext = explode("[tag:",$value);
	
		for($n = 0; $n < count($ext); $n++)
		{
			$ext_back = strrpos($ext[$n],"]");
			$tag_det = substr($ext[$n],0,$ext_back);
			$control->ChangeData($tag_det,"<".$tag_det.">\n");
		}
}

//htmlを表示
echo $control->GetContentData();


//編集完了：セッション初期化
$_SESSION=array();
session_destroy();	

?>