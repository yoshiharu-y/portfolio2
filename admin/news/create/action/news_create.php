<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//初回時
if(!empty($_POST["news_create"]))
{
	$row["news_title"]="";
	$row["news_detail"]="";
	$row["err_det"]="";
}
//確認画面から
else if(!empty($_POST["news_create_return"]))
{
	foreach( $_SESSION["news_create"] as $key => $value )
	{
		$row[$key]=$value;	
	}
	$row["err_det"]="";
}
else
{	
	$row["err_det"]='<p class="red">新規作成中にエラーが発生しました。再度作成しなおしてください。</p>';
	$row["news_title"]="";
	$row["news_detail"]="";
	$row["err_det"]="";

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