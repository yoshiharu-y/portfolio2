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
$sql = "SELECT * FROM rel_artist_audition where ar_id = ".$_SESSION["mms_unique_artist"]." and aud_seq_num='".$_GET["oidn"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["form_txt"]='<input type="submit" name="mms_aud_comp" value="ｵｰﾃﾞｨｼｮﾝに応募する" />';
}
else
{
	$row["form_txt"]="既に応募されています。";
}

//公開日付と現在の日付を比較しcontact_urlのテキストを設定

if(strtotime($row["start_date"]) > time() || strtotime($row["end_date"]) < time())
{
	$row["form_txt"]="このｵｰﾃﾞｨｼｮﾝへの登録はまだ開始していないか、終了しています。";
}

//オーデション一覧に戻るリンク（docomoの場合session付きになる）
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