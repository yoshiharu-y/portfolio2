<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../../audition/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_drop_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row["err_det"]="";
	$row = $db->FetchRow($result_id);
	$sql="UPDATE audition_det SET del_flag = 1 WHERE aud_seq_num =".$row["aud_seq_num"];
	$db->ExecuteSQL($sql);
	@unlink($tmpdir.$row["banner_add"]);
	@unlink($tmpdir."m_".$row["banner_add"]);

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