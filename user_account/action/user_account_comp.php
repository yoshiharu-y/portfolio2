<?php
/*
サブオーディション編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//仮登録保存パス
$provfile=$control->CreateRandText(20);
$provpass="prov_acco/".$provfile.".txt";

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////


//date("Y-m-d");
if(!empty($_POST["mms_comp"]))
{
	/*//キャンペーンコードによるポイント付加
	if($_GET["qur"]=="Faf1wjxKNiWnwPeJBjS0CcXQZJMjVx")
	{
		$_POST["free_point"] =100;
		$row["getpoint"]='<br /><span style="color: red">'.$_POST["free_point"].'ﾎﾟｲﾝﾄが贈呈されました！</p>';
	}
	else
	{
		$_POST["free_point"] =0;
		$row["getpoint"]="";
	}*/
	$_POST["free_point"] =0;
	//htmlタグエスケープ　POSTDATAを確認表示用に変換
	foreach( $_POST as $key => $value )
	{
		$value=htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		$_POST[$key]= $value;
		$row[$key]= $value;
	}
	
	//性別の表示
	if($_POST["sex"]==0)
	{
		$row["sex_type"]="男性";
	}
	else
	{
		$row["sex_type"]="女性";	
	}
	
	$_POST["password"]=md5($_POST["password"]);
	
	//user_id作成
	$rand=date("d");
	for($i = 0; $i < 4; ++$i)
	{
		$rand .= mt_rand(0,9);
	}
	//ID重複チェック
	$sql = "SELECT * FROM user where user_id =".$rand;
	$result_id = $db->ExecuteSQL($sql);
	while(mysql_num_rows($result_id)!=0)
	{
		//user_id作成
		$rand=date("d");
		for($i = 0; $i < 4; ++$i)
		{
			$rand .= mt_rand(0,9);
		}
		$sql = "SELECT * FROM user where user_id =".$rand;
		$result_id = $db->ExecuteSQL($sql);
	}
	$_POST["user_id"]=sprintf("%06d",$rand);
	$_POST["birthday"]=$_POST["year"]."-".$_POST["month"]."-".$_POST["day"];
	$_POST["address2"]=$_POST["address2-1"].$_POST["address2-2"];
	$_POST["user_thum"]="no_thum.jpg";
	$_POST["reg_date"]= date("Y-m-d");
	$_POST["ip_address"]=$_SERVER['REMOTE_ADDR'];
	
	//url作成
	$acc_comp_url="http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."?c=".$provfile;
	
	//宛先　メールアドレス
	$to =$_POST["mail"];
	//差出人
	$header = "From: ".mb_encode_mimeheader("UNDEFIND")."<".$contact_mail.">";
	//件名
	$subject = "登録確認メール";
		
	//本文
	$body ='
	以下のリンクを押していただくと登録完了となります。
	'.$acc_comp_url.'
	---------------------------------------------------
	
	ログインID：'.$row["login_id"].'
	パスワード：'.$row["password"].'
	ニックネーム：'.$row["user_name"].'
	お名前：'.$row["name"].'
	お名前（カナ）：'.$row["kana"].'
		
	---------------------------------------------------
	';
		
	if(!mb_send_mail($to,$subject,$body,$header))
	{
		$row["mail_state"]='
		<p><span style="color: red">メールが送信されませんでした。<br />
		登録完了となりませんので、お手数ですが再度アカウント作成フォームからお願いいたします。</span></p>';
			
	}else
	{
		$row["mail_state"].="<p>ご記入いただいたメールアドレスにメールを送信致しました。</p>";
		
		//仮登録テキスト
		foreach( $_POST as $key => $value )
		{
			$text_data.=$key.":".$value."<end_data>";
		}
		//テキスト保存
		if($fp = @fopen($provpass, "a"))
		{
			fwrite($fp,$text_data);
			fclose($fp);	
		}
	}
	unset($_SESSION["user_account_start"]);
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

//htmlを表示
echo $control->GetContentData();


?>