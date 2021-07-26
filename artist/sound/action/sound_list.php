<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

$tmpdir  = "../mp3/";
$imagestmpdir  = "../images/";

//////////////////////////////////////////////////
//
//　　表示させるデータ設定
//
//////////////////////////////////////////////////

//htmlに表示させるプログラム

//データをDBから引っ張り出す。
$sql = "SELECT * FROM ar_sound 
		WHERE ar_id= ".$_SESSION["mms_artist"]."
		ORDER BY ar_sound.seq_num desc";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["non_sound"]='<tr><td colspan="5">アップロードされている音楽はありません</td></tr>';
}
else
{
	$row["non_sound"]="";
	$row["comment"]="";
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
		$sql_data["sound_url"]=$tmpdir."a".$sql_data["ar_id"]."/".$sql_data["sound_url"];
		$row["sound_data_".$row_num]=$sql_data;
		$row_num++;
	}
	if($row_num==5)
	{
		$row["comment"]='<p class="coution">アップロードできる数は5件までです。</p>';
	}
	else
	{
		$row["comment"]="";
	}
	if($_SESSION["up_err"])
	{
		$row["comment"]=$_SESSION["up_err"];
		$row["up_sound_title"]=$_SESSION["up_sound_title"];
		unset($_SESSION["up_sound_title"]);
		unset($_SESSION["up_err"]);
	}
	else
	{
		$row["up_sound_title"]="";
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
//　 　　画面表示設定
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

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();