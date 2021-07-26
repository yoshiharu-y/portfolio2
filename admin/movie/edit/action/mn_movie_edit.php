<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//動画サイト
$site=array("ニコニコ動画","USTREAM","YouTube");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//初回時
if(!empty($_POST["mn_movie_edit"]))
{
	$sql = "SELECT * FROM mn_movie WHERE seq_num=".$_POST["mn_movie"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
	}
}
//確認画面から
else if(!empty($_POST["mn_movie_edit_return"]))
{
	foreach( $_SESSION["mn_movie_edit"] as $key => $value )
	{
		$row[$key]=$value;	
	}
	$row["err_det"]="";
}
else
{	
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
//動画サイトリスト
foreach($site as $key => $value)
{
	if($key==$row["movie_type"])
	{
		$row["site_form"].='<option value="'.$key.'" selected="selected" >'.$value.'</option>';
	}
	else
	{
		$row["site_form"].='<option value="'.$key.'">'.$value.'</option>';
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