<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


//htmlに表示させるプログラム

//データをDBから引っ張り出す。
$sql = "SELECT * FROM artist where del_flag = 0 order by seq_num desc";
$result_id = $db->ExecuteSQL($sql);

//[for_artist]～[end_for_artist]で置換する回数
$row_num=0;

while($sql_data = $db->FetchRow($result_id))
{
	//添え字が数字の配列を削除
	$data_key=(count($sql_data)/2);
	for($i=0;$i<$data_key;$i++)
	{
		unset($sql_data[$i]);
	}
	//[for_artist]～[end_for_artist]で置換する分だけrowに多次元配列として作成
	$row["artist_data_".$row_num]=$sql_data;
	$row_num++;
}

//本登録しているアーティストの数
$sql = "SELECT * FROM artist";
$result_id = $db->ExecuteSQL($sql);
$artist_count=mysql_num_rows($result_id);

//仮登録ｱｰテイストの数
$sql = "SELECT * FROM ar_login";
$result_id = $db->ExecuteSQL($sql);
$test_acc_count=mysql_num_rows($result_id);

$row["acc_check"]=$test_acc_count-$artist_count;
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//最初のhtml状態を保存する。
$control->SetContentData($content);

/*
html内の[for_artist]～[end_for_artist]を抽出する
*/

//htmlの[for_rank]より前と後を分割して配列に入れる。
$for = explode("[for_artist]",$control->GetContentData());

//htmlの[for_rank]より前を削除
array_shift($for);

//htmlの[end_for_rank]より前と後を分割する。
$for = explode("[end_for_artist]",$for[0]);

//[end_for_rank]の後を削除
array_pop($for);


/*
[for_rank]～[end_for_rank]の間にある[data_s:example]を表示するデータに置換していく
*/

$control->SetContentType("data_s");

for($i=0; $i < $row_num ;$i++)
{
	//データとして抜き出した<タグ>[data_s:example]</タグ>を保存。
	$control->SetContentData($for[0]);
	
	//<タグ>[data_s:example]</タグ>を置換
	foreach( $row["artist_data_".$i] as $key => $value )
	{
		$control->ChangeData($key,$value);
	}
	
	//置換後html_dataとしてデータを保存
	$html_data.=$control->GetContentData();
	
}

//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

/*
初期化されたHTMLデータ内の[for_artist]～[end_for_artist]で囲まれたところを
[change_for:dl_for_artist]に置換
*/

$htmlFor="[for_artist]".$for[0]."[end_for_artist]";
$forName="table_for_artist";
$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));


/*
初期化されたHTMLデータ内で置換されて[change_for:dl_for_rank]となったところを
$html_dataとして保存されたデータに置換する。
*/

$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);

//[for]～[end_for]で囲まれた所以外の[data_s:example]を表示するデータに置換していく
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>