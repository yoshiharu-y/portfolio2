<?php
/*
common/config.phpを読む
*/@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");//画像保存フォルダがある場所
$tmpdir  = "../../../audition/images/";//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//htmlに表示させるプログラム
$get=$_GET["sub"];$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$get."'";
$result_id = $db->ExecuteSQL($sql);
$row = $db->FetchRow($result_id) or die("存在しません");$row["banner_add"]=$tmpdir.$row["banner_add"];//////////////////////////////////////////////////
//
//　　　画面表示設定
//
////////////////////////////////////////////////////html内容を保持
$control->SetContentData($content);//html内の特殊タグを変換するのに必要なタイプを設定
$control->SetContentType("data_s");//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}//htmlを表示
echo $control->GetContentData();?>