<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//ユーザーの画像保存フォルダがある場所
$usertmpdir  = "../user/images/";

//ユーザーのMP3保存フォルダがある場所
$soundtmpdir  = "mp3/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


//データの存在を確認
$sql = "SELECT * FROM artist where del_flag= 0 and ar_id =".$_SESSION["mms_artist"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0 )
{
	header( "Location: ../../login/");
}
else
{
	$row = $db->FetchRow($result_id);
	
	//サムネイル画像関係
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$row["ar_id"]."/";
		$row["artist_thum"]=$tmpdir."f_".$row["artist_thum"];
		
	}
	
	//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
	$row["news"]=str_replace("<br />", $_POST["escape_text"], $row["news"]);
	$row["news"]=preg_replace("/\t/","", $row["news"]);
	$row["err_det"]="";

	//添え字無しの配列を削除
	$row_key=(count($row)/2);
	for($i=0;$i<$row_key;$i++)
	{
		unset($row[$i]);
	}
}

	

//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//ログイン時、ナビゲーションの画像をログアウトにする。
if(!empty($_SESSION["mms_user"]) || !empty($_SESSION["mms_artist"]))
{
	$content=str_replace("g_navi_01.gif", "g_navi_09.gif", $content);
}



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