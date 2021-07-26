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

//htmlに表示させるプログラム

//listでボタンを押したオーディションデータをDBから引っ張り出す。
$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません。</p>';
}
else
{
	$rel_data = $db->FetchRow($result_id);	
	foreach( $rel_data as $key => $value)
	{
		$row["rel_".$key]=$value;
		if($key=="end_date")
		{
			if(strtotime($value) <= time())
			{
				$row["rel_audition_status"] = '<span  class="red">開催終了</span>';
			
			}
			else
			{
				$row["rel_audition_status"] = "開催中";
			}
		}
	}
}
//ボタンを押した時
if(!empty($_POST["audition_rel_comp"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
		$row[$key]=$_POST[$key];
	}
	if(!empty($_POST["rel_check"]))
	{
		//イベントナンバーのオーディションを探す
		$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num= ?";
		$phs = array($_POST["aud_rel_num"]);
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$rel_id = $db->ExecuteSQL($sql_prepare);
		if(mysql_num_rows($rel_id)==0)
		{
			$row["err_det"].='<p class="red">入力したイベントナンバーのオーディションが存在しません。</p>';
		}
	
	}
	else
	{
		$row["err_det"].='<p class="red">確認チェックがはいっていません。</p>';
	}
}
//初回
else
{
	$row["err_det"]="";
	$row["aud_rel_num"]="";
	$row["start_artist_rel"]="";
	$row["end_artist_rel"]="";
}
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
	if(strtotime($sql_data["end_date"]) <= time())
	{
		$sql_data["audition_status"] = '<span  class="red">開催終了</span>';
		
	}
	else
	{
		$sql_data["audition_status"] = "開催中";
	}
	//[for_audition]～[end_for_audition]で置換する分だけrowに多次元配列として作成
	$row["audition_data_".$row_num]=$sql_data;
	$row_num++;
}
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//最初のhtml状態を保存する。
$control->SetContentData($content);

/*
html内の[for_audition]～[end_for_audition]を抽出する
*/

//htmlの[for_rank]より前と後を分割して配列に入れる。
$for = explode("[for_audition]",$control->GetContentData());

//htmlの[for_rank]より前を削除
array_shift($for);

//htmlの[end_for_rank]より前と後を分割する。
$for = explode("[end_for_audition]",$for[0]);

//[end_for_rank]の後を削除
array_pop($for);


/*
[for_rank]～[end_for_rank]の間にある[data_s:example]を表示するデータに置換していく
*/

$control->SetContentType("data_s");

for($i=0; $i < $row_num ;$i++)
{
	//データとして抜き出した<タグ>[data_s:example]</タグ>を保存。
	$control->SetContentData($for[0]);
	
	//<タグ>[data_s:example]</タグ>を置換
	foreach( $row["audition_data_".$i] as $key => $value )
	{
		$control->ChangeData($key,$value);
	}
	
	//置換後html_dataとしてデータを保存
	$html_data.=$control->GetContentData();
	
}

//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

/*
初期化されたHTMLデータ内の[for_audition]～[end_for_audition]で囲まれたところを
[change_for:dl_for_audition]に置換
*/

$htmlFor="[for_audition]".$for[0]."[end_for_audition]";
$forName="table_for_audition";
$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));


/*
初期化されたHTMLデータ内で置換されて[change_for:dl_for_rank]となったところを
$html_dataとして保存されたデータに置換する。
*/

$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);

//[for]～[end_for]で囲まれた所以外の[data_s:example]を表示するデータに置換していく
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();