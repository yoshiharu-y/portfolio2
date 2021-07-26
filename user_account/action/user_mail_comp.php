<?php
/*
サブオーディション編集完了画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//仮登録保存パス
$provpass="prov_acco/".htmlspecialchars($_GET["c"], ENT_QUOTES, 'UTF-8').".txt";

//ip保存パス
$filepass="ipadd/plBNtSgnMMbFUChtAMg.txt";

//////////////////////////////////////////////////
//
//　　　データをsqlに保存する設定
//
//////////////////////////////////////////////////


//date("Y-m-d");
if($fp = @fopen($provpass, "r"))
{
	//仮登録テキストを開く
	while( ! feof( $fp ) )
	{
		$load=explode("<end_data>",fgets( $fp, 4096 ));
	}
	fclose($fp);
	$data=array();
	foreach($load as $value)
	{
		//データ分解（登録項目名:値）
		$temp=explode(":",$value);
		//入力データにコロンが使われている場合の処理
		if(count($temp)>2)
		{
			for($i=2;$i<count($temp);$i++)
			{
				$temp[1].=":".$temp[$i];
			}
		}
		$data[$temp[0]]=$temp[1];
	}
	
	/*echo "<pre>";
	var_dump($data);
	echo "</pre>";*/
	
	//表示用
	$row["free_point"]=$data["free_point"];
	extract($data);
	//sql文作成
	$sql = "INSERT INTO user (
						login_id,
						password,
						user_name,
						user_id,
						name,
						kana,
						age,
						birthday,
						sex,
						mail,
						zip,
						address1,
						address2,
						user_thum,
						free_point,
						reg_date) 
						VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
						";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
					$login_id,
					$password,
					$user_name,
					$user_id,
					$name,
					$kana,
					$age,
					$birthday,
					$sex,
					$mail,
					$zip,
					$address1,
					$address2,
					$user_thum,
					$free_point,
					$reg_date
					);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$db->ExecuteSQL($sql_prepare) or die("データベースエラーが発生しました。");
		//echo $sql_prepare;
		//print_r($phs);
		//仮登録テキスト削除
		@unlink($provpass);
		//1pを保存する
		if($fp = @fopen($filepass, "a"))
		{
			fwrite($fp,"ip:[".$ip_address."]");
			fclose($fp);	
		}
		$row["comp"]="登録が完了しました。";
		$row["comp_txt"]="登録したＩＤとパスワードでログインする事ができます。";
}
else
{
	$agent=strtolower($_SERVER['HTTP_USER_AGENT']);
	$jump=false;
	//win系
	if(strpos($agent,'windows phone')!==false)
	{
		$jump=true;
	}
	else if(strpos($agent,'win')!==false)
	{
		$jump=true;
	}
	//mac系
	else if(strpos($agent,'mac')!==false)
	{
		if(strpos($agent,'iphone')===false)
		{
			$jump=true;
		}
	}
	//エージェント別処理
	if($jump)
	{
		$pass=explode("?",$_SERVER["REQUEST_URI"]);
		header( "Location: http://" .$_SERVER["HTTP_HOST"].$pass[0]);	
	}
	else
	{
		$row["comp"]="登録に失敗しました。";
		$row["comp_txt"]="データの有効期間が過ぎて登録が行えませんでした。再度PCから登録をお願いします。";
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

//htmlを表示
echo $control->GetContentData();


?>