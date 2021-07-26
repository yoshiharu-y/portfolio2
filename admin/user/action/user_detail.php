<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../user/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM user where del_flag = 0 and seq_num='".$_POST["user_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_s_thum"]="画像がありません";
		$row["user_b_thum"]="画像がありません";
		$row["user_thum"]="画像がありません";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."e".$row["user_id"]."/";
		$row["user_s_thum"]='<img src="'.$tmpdir."s_".$row["user_thum"].'" />';
		$row["user_b_thum"]='<img src="'.$tmpdir."b_".$row["user_thum"].'" />';
		$row["user_thum"]='<img src="'.$tmpdir.$row["user_thum"].'" />';
	}
	if($row["sex"]==0)
	{
		$row["sex"]="男性";
	}
	else
	{
		$row["sex"]="女性";		
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