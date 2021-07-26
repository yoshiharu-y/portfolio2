<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

$imagestmpdir  = "../images/";
//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

foreach($_POST as $key => $value)
{
	if(strpos($key,"edit") !== false)
	{
		$num=substr($key,5);
	}
}


//データの存在を確認
$sql = "SELECT * FROM ar_sound 
		WHERE ar_id =".$_SESSION["mms_artist"]."
		AND seq_num =".$num;
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0 )
{
	header( "Location: ../../login/");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";

	//添え字無しの配列を削除
	$row_key=(count($row)/2);
	for($i=0;$i<$row_key;$i++)
	{
		unset($row[$i]);
	}
}

//サムネイル画像関係
$artist_sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$_SESSION["mms_artist"]."'";
$artist_result_id = $db->ExecuteSQL($artist_sql);

if(mysql_num_rows($artist_result_id)==0)
{
	header( "Location: ../");
}
else
{
	$artist_row=$db->FetchRow($artist_result_id);
	//サムネイル画像関係
	if($artist_row["artist_thum"]=="no_thum.jpg")
	{
		$artist_row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$imagestmpdir=$imagestmpdir."a".$artist_row["ar_id"]."/";
		$artist_row["artist_thum"]=$imagestmpdir."f_".$artist_row["artist_thum"];
		
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

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>