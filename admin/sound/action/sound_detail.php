<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

$tmpdir  = "../../artist/mp3/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT ar_sound.seq_num,ar_sound.sound_title,ar_sound.sound_url,ar_sound.ar_id,ar_sound.reg_date,ar_sound.renew_date,artist.artist_name FROM ar_sound INNER JOIN artist ON ar_sound.ar_id=artist.ar_id 
		WHERE ar_sound.ar_id=".$_POST["artist_no"]."
		AND ar_sound.seq_num=".$_POST["sound_no"];
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	$row["sound_url"]=$tmpdir."a".$row["ar_id"]."/".$row["sound_url"];
}


	
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