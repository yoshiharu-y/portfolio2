<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");
//htmlに表示させるプログラム

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


$_GET["oidn"]= htmlspecialchars($_GET["oidn"], ENT_QUOTES, 'UTF-8');
$_GET["check"]= htmlspecialchars($_GET["check"], ENT_QUOTES, 'UTF-8');

$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_GET["oidn"]."'";
$result_id = $db->ExecuteSQL($sql);
$row = $db->FetchRow($result_id) or die("このｵｰﾃﾞｨｼｮﾝは存在しません。ﾌﾞﾗｳｻﾞで戻ってください。");

//form_txtの成型
$sql = "SELECT * FROM rel_artist_audition where ar_id = ".$_SESSION["mms_unique_artist"]." and aud_seq_num='".$_GET["oidn"]."'";;
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	//sql文作成
	$sql = "INSERT INTO rel_artist_audition (
						aud_seq_num,
						ar_id,
						ar_point,
						reg_date) 
						VALUES (?,?,?,?);
						";
	//MySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$_GET["oidn"],
					$_SESSION["mms_unique_artist"],
					0,
					date("Y-m-d")
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	$db->ExecuteSQL($sql_prepare) or die("データベースエラーが発生しました。");
	$row["comp_txt"]="ｵｰﾃﾞｨｼｮﾝに応募しました。";
}
else
{
	$row["comp_txt"]="ｵｰﾃﾞｨｼｮﾝに既に応募済みです。";
}

//オーデション一覧に戻るリンク（docomoの場合session付きになる）
if(strstr($_SERVER['HTTP_USER_AGENT'],"DoCoMo"))
{
	$row["aud_link"]=$ses."&oidn=".$_GET["oidn"];
}
else
{
	$row["aud_link"]="?oidn=".$_GET["oidn"];
}
$row["return"] = $ses; 

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

//定義したタイプになっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//sql内の[tag:]を変換するのに必要なタイプを設定
$control->SetContentType("tag");

//$controlに保持している[tag:]を変換する
foreach( $row as $value )
{
	$ext = explode("[tag:",$value);
		
		for($n = 0; $n < count($ext); $n++)
		{
			$ext_back = strrpos($ext[$n],"]");
			$tag_det = substr($ext[$n],0,$ext_back);
			$control->ChangeData($tag_det,"<".$tag_det.">\n");
		}
}

//htmlを表示
echo $control->GetContentData();


?>