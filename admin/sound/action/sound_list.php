<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　表示させるデータ設定
//
//////////////////////////////////////////////////

//htmlに表示させるプログラム

//データをDBから引っ張り出す。
$sql = "SELECT * FROM ar_sound ORDER BY ar_sound.seq_num desc";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["non_sound"]='<tr><td colspan="4">アップロードされている音楽はありません</td></tr>';
}
else
{
	$row["non_sound"]="";
	$row_num=0;	
	while($sql_data = $db->FetchRow($result_id))
	{
		//添え字が数字の配列を削除
		$data_key=(count($sql_data)/2);
		for($i=0;$i<$data_key;$i++)
		{
			unset($sql_data[$i]);
		}
		//[for_sound]～[end_for_sound]で置換する分だけrowに多次元配列として作成
		$row["sound_data_".$row_num]=$sql_data;
		$row_num++;
	}

}
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//最初のhtml状態を保存する。
$control->SetContentData($content);

/*************************************************
            複数個表示する処理:開始
*************************************************/

//動画一覧表示
$for = explode("[for_sound]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_sound]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_sound"]=="")
{
	for($i=0; $i < $row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["sound_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_sound"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_sound]".$for[0]."[end_for_sound]";
$forName="list_for_sound";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

/*************************************************
            複数個表示する処理:終了
*************************************************/

//[for]～[end_for]で囲まれた所以外の[data_s:example]を表示するデータに置換していく
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();