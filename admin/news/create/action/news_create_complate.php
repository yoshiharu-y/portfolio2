<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//独自リンクタグを摘出する正規表現
$linkexp="/link:\[.*?\]\[.*?\]/s";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//データの更新は$_SESSION["news_create"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["news_create"]に入れている。

if(!empty($_POST["comp_check"])&&!empty($_POST["news_create_comp"]))
{
	$row["err_det"]="";
	//sql文作成
	
	foreach($_SESSION["news_create"] as $key => $value)
	{
		//文字が改行されるようにする
		$_SESSION["news_create"][$key]=nl2br($_SESSION["news_create"][$key]);
		//独自リンクタグをhtmlリンクタグにする。
		if($key=="news_detail")
		{
			preg_match_all($linkexp,$_SESSION["news_create"][$key],$link_array);
			if(count($link_array[0])>0)
			{
				foreach($link_array[0] as $value)
				{
					preg_match_all("/\[.*?\]/",$value,$link_value);
					$url=str_replace("[","",str_replace("]","",$link_value[0][0]));
					$text=str_replace("[","",str_replace("]","",$link_value[0][1]));
					$link='<a href="'.$url.'" target="_blank">'.$text.'</a>';
					$_SESSION["news_create"][$key]=preg_replace($linkexp,$link,$_SESSION["news_create"][$key],1);
				}
			}
		
		}
	}
	//記事の表示設定のタグ埋め込み
	if($_SESSION["news_create"]["news_view"]=="タイトルのみ")
	{
		$_SESSION["news_create"]["news_title"]="[news_not_link]".$_SESSION["news_create"]["news_title"];
	}
	//記事ナンバー作成
	$rand=date("d");
	for($i = 0; $i < 4; ++$i)
	{
		$rand .= mt_rand(0,9);	
	}
	$_SESSION["news_create"]["news_id"]=sprintf("%06d",$rand);
	
	//記事の日時を生成
	$_SESSION["news_create"]["reg_date"]= date("Y-m-d");
	$_SESSION["news_create"]["renew_date"]= date("Y-m-d");
	//配列のキーを変数名とした変数を作る
	extract($_SESSION["news_create"]);
	//sql文作成

	$sql = "INSERT INTO news (
					news_title,
					news_category,
					news_detail,
					news_id,
					reg_date,
					renew_date) 
					VALUES (?,?,?,?,?,?)";
	//$_SESSION["news_create"]からMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$news_title,
					$news_category,
					$news_detail,
					$news_id,
					$reg_date,
					$renew_date
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	//echo $sql_prepare;
	$result_id = $db->ExecuteSQL($sql_prepare);
	if(!$result_id)
	{
		$row["err_det"]='<p class="red">更新に失敗しました。再度編集お願いします。</p>';
	}
	else
	{
		$row["err_det"]='<p>新規作成が完了しました。</p>';
	}
}
else
{
	$row["err_det"]='<p class="red">新規作成中にエラーが発生しました。再度作成しなおしてください。</p>';
}
//ログイン情報を残し$_SESSIONを初期化
$temp_login=$_SESSION["mms_admin_login"];
$_SESSION=array();
$_SESSION["mms_admin_login"]=$temp_login;
//var_dump($_SESSION);

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