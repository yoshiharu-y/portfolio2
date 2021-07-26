<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//保存フォルダがある場所
$tmpdir  = "../../../artist/mp3/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM ar_sound WHERE seq_num='".$_POST["sound_drop_no"]."'";

$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row["err_det"]="";
	$row = $db->FetchRow($result_id);
	$sql="DELETE FROM ar_sound WHERE seq_num =".$row["seq_num"];
	$result_id=$db->ExecuteSQL($sql);
	if(!$result_id)
	{
		$row["err_det"]='<span class="red">削除中にエラーが発生しました。再度音楽削除お願いします。</span>';
	}
	else
	{
		
		$row["err_det"]='削除が完了しました。';
		unlink($tmpdir."a".$row["ar_id"]."/".$row["sound_url"]);
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