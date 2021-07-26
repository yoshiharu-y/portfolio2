<?php

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

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
		$tmpdir=$tmpdir."../images/a".$artist_row["ar_id"]."/";
		$artist_row["artist_thum"]=$tmpdir."f_".$artist_row["artist_thum"];
		
	}
}

//complateで整合性確認、不備がある場合はcomplateから戻ってくる

//パスワードの確認
$sql = "SELECT * FROM ar_login where ar_id =".$_SESSION["mms_artist"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0 )
{
	header( "Location: ../");
}
if(!empty($_SESSION["password_err"]))
{
	$row["err_det"]='<div class="coutionBox">'.$_SESSION["password_err"].'</div>';
	unset($_SESSION["password_err"]);
}
else
{
	$row=$db->FetchRow($result_id);
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

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>