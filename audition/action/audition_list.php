<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


//データをDBから引っ張り出す。
//アーティストがマイページからオーディション参加ボタンを押して来た場合。
if(!empty($_SESSION["aud_app_no"]))
{
	$sql = "SELECT * FROM audition_det 
			WHERE del_flag = 0
			AND entry_end >= '".date("Y-m-d H:i:s")."' 
			AND end_date >= '".date("Y-m-d H:i:s")."'
			AND hidden_flag = 0 
			ORDER BY aud_seq_num DESC";
	$row["ar_app"]='&gt;　<a href="../artist/">アーティストマイページ</a>　';
}
else
{
	$sql = "SELECT * FROM audition_det WHERE del_flag = 0 ORDER BY aud_seq_num DESC";
	$row["ar_app"]="";
}
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	$row["non_audition"]='<p>参加できるオーディションはありません</p>';
}
else
{
	$row["non_audition"]="";
	//[for_audition]～[end_for_audition]で置換する回数
	$row_num=0;
	while($sql_data = $db->FetchRow($result_id))
	{
		//添え字が数字の配列を削除
		$data_key=(count($sql_data)/2);
		for($i=0;$i<$data_key;$i++)
		{
			unset($sql_data[$i]);
		}
		if(!empty($_SESSION["aud_app_no"]))
		{
			$sql_data["aud_link"]="?arview=".$_SESSION["aud_app_no"]."&entry=".$sql_data["aud_seq_num"];
		}
		else
		{
			$sql_data["aud_link"]="?view=".$sql_data["aud_seq_num"];
		}
		//[for_audition]～[end_for_audition]で置換する分だけrowに多次元配列として作成
		$row["audition_data_".$row_num]=$sql_data;
		$row_num++;
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

//最初のhtml状態を保存する。
$control->SetContentData($content);

/*************************************************
            複数個表示する処理:開始
*************************************************/

//動画一覧表示
$for = explode("[for_audition]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_audition]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_audition"]=="")
{
	for($i=0; $i < $row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["audition_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_audition"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_audition]".$for[0]."[end_for_audition]";
$forName="list_for_audition";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

/*************************************************
            複数個表示する処理:終了
*************************************************/

$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>