<?php
/*
common/config.phpを読む
*/

//コンフィグの位置を記述。パスはindex.phpから辿る
@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../artist/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
$escape=htmlspecialchars($_POST["search_text"], ENT_QUOTES, 'UTF-8');
$sql = "SELECT * FROM artist where del_flag = 0 and artist_name='".$escape."'";

$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["non_artist"]='<p>検索結果に該当するアーティストはいません</p>';
}
else
{
	$row["non_artist"]="";
	$row_num=0;	
	while($sql_data = $db->FetchRow($result_id))
	{
		//添え字が数字の配列を削除
		$data_key=(count($sql_data)/2);
		for($i=0;$i<$data_key;$i++)
		{
			unset($sql_data[$i]);
		}
		$sql_data["artist_thum"]=$tmpdir."a".$sql_data["ar_id"]."/"."m_".$sql_data["artist_thum"];
		$sql_data["artist_url"]="../artist/?view=".$sql_data["ar_id"];
		//[for_artist]～[end_for_artist]で置換する分だけrowに多次元配列として作成
		$row["artist_data_".$row_num]=$sql_data;
		$row_num++;
	}

}


//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////


//html内容を保持
$control->SetContentData($content);

/*************************************************
            複数個表示する処理:開始
*************************************************/

//アーティストを表示
$for = explode("[for_artist]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_artist]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_artist"]=="")
{
	for($i=0; $i < $row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["artist_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_artist"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_artist]".$for[0]."[end_for_artist]";
$forName="list_for_artist";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";


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