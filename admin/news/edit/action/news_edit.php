<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//htmlリンクタグを摘出する正規表現
$link_def="/<a href=\".*?\" target=\"_blank\">.*?<\/a>/s";


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//初回時
if(!empty($_POST["news_edit"]))
{
	$sql = "SELECT * FROM news where seq_num='".$_POST["news_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		foreach($row as $key => $value)
		{
			//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
			$row[$key]=str_replace("<br />", $_POST["escape_text"], $row[$key]);
			$row[$key]=preg_replace("/\t/","", $row[$key]);
		}
		//aタグ抽出
		preg_match_all($link_def,$row["news_detail"],$link_array);
		//print_r($link_array);
		//echo count($link_array[0]);
		if(count($link_array[0])>0)
		{
			foreach($link_array[0] as $value)
			{
				//href抽出
				preg_match("/\".*?\"/",$value,$out_url);
				$url=str_replace("\"","",$out_url[0]);
				//echo $url."<br>";
				//テキスト抽出
				preg_match("/>.*?</",$value,$out_text);
				$text=str_replace(">","",str_replace("<","",$out_text[0]));
				//echo $text."<br>";
				$link='link:['.$url.']['.$text.']';
				$row["news_detail"]=preg_replace($link_def,$link,$row["news_detail"],1);
			}
		}
		$row["err_det"]="";
	}
}
//確認画面から
else if(!empty($_POST["news_edit_return"]))
{
	foreach( $_SESSION["news_edit"] as $key => $value )
	{
		$row[$key]=$value;	
	}
	$row["err_det"]="";
}
else
{	
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
	exit();

}

//カテゴリ
$category_list=array("新着情報");

foreach($category_list as $value)
{
	if($row["news_category"]==$value)
	{
		$row["news_category"]='<option value="%t" selected="selected" >%t</option>';
		$row["news_category"]=str_replace("%t",$value,$row["news_category"]);
	}
	else
	{
		$row["news_category"]='<option value="%t" >%t</option>';
		$row["news_category"]=str_replace("%t",$value,$row["news_category"]);
	}
}

//表示方法解析
if(strpos($row["news_title"],"[news_not_link]") !== false)
{
	$row["news_title"]=str_replace("[news_not_link]", "", $row["news_title"]);
	$not_link=' selected="selected" ';
}
else
{
	$link=' selected="selected" ';	
}
//表示方法リストタグ
$row["news_view"]='<option value="タイトルのみ"'.$not_link.'>タイトルのみ</option>
<option value="告知詳細まで表示"'.$link.'>告知詳細まで表示</option>';

	
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