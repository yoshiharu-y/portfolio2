<?php
/*
common/config.phpを読む
*/

//コンフィグの位置を記述。パスはindex.phpから辿る
@require_once(dirname(__FILE__)."/../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//処理を記述
//データをDBから引っ張り出す。
$sql = "SELECT * FROM news order by seq_num desc";
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	$data["news_title"]="現在お知らせはありません";
	$data["reg_date"]="";
	$row["news_data_0"]=$data;
}
else
{
	//[for_news]～[end_for_news]で置換する回数
	$row_num=0;
	while($sql_data = $db->FetchRow($result_id))
	{
		//添え字が数字の配列を削除
		$data_key=(count($sql_data)/2);
		for($i=0;$i<$data_key;$i++)
		{
			unset($sql_data[$i]);
		}
		//リンク設定
		if(strpos($sql_data["news_title"],"[news_not_link]") !== false)
		{
			$sql_data["news_title"]=str_replace("[news_not_link]", "", $sql_data["news_title"]);
		}
		/*else
		{
			$sql_data["news_title"]='<a href="news/?det='.$sql_data["news_id"].'">'.$sql_data["news_title"].'</a>';
		}*/
		
		//[for_news]～[end_for_news]で置換する分だけrowに多次元配列として作成
		$row["news_data_".$row_num]=$sql_data;
		$row_num++;
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

/*************************************************
            複数個表示する処理:開始
*************************************************/


$for = explode("[for_news]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_news]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

for($i=0; $i < $row_num ;$i++)
{
	$control->SetContentData($for[0]);
	
	foreach( $row["news_data_".$i] as $key => $value )
	{
		$control->ChangeData($key,$value);
	}
	
	$html_data.=$control->GetContentData();
	
}

$control->SetContentData($content);

$htmlFor="[for_news]".$for[0]."[end_for_news]";
$forName="list_for_news";
$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);

/*************************************************
            複数個表示する処理:終了
*************************************************/

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