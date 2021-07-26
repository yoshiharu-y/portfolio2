<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//保存フォルダがある場所
$tmpdir  = "../mp3/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

foreach($_POST as $key => $value)
{
	if(strpos($key,"drop") !== false)
	{
		$num=substr($key,5);
	}
}


$sql = "SELECT * FROM ar_sound 
		WHERE ar_id =".$_SESSION["mms_artist"]."
		AND seq_num =".$num;
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../../login/");
	exit();
}
else
{
	$row = $db->FetchRow($result_id);
	$sql="DELETE FROM ar_sound WHERE seq_num =".$row["seq_num"];
	$result_id=$db->ExecuteSQL($sql);
	if(!$result_id)
	{
		$_SESSION["up_err"]='<span class="red">削除中にエラーが発生しました。再度音楽削除お願いします。</span>';
		header( "Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}
	else
	{
		
		$_SESSION["up_err"]='<p>削除が完了しました。</p>';
		unlink($tmpdir."a".$row["ar_id"]."/".$row["sound_url"]);
		header( "Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}

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