<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//htmlタグエスケープ
foreach( $_GET as $key => $value )
{
	$_GET[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$sql = "SELECT * FROM news where news_id='".$_GET["det"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	header( "Location: ./");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	
	
	//表示方法チェック（タイトルに[news_not_link]がある場合タイトルのみの表示）
	if(strpos($row["news_title"],"[news_not_link]") !== false)
	{
		$row["news_title"]=str_replace("[news_not_link]", "", $row["news_title"]);
		$row["news_view"]="タイトルのみ";
	}
	else
	{
		$row["news_view"]="告知詳細まで表示";	
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