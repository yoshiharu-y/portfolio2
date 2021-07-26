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

//アーティストデータをDBから引っ張り出す。
$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["audition_no"]=$_POST["audition_no"];
	
	//アーティストがいない場合の表示データとフォーム
	$no_form='<input type="hidden" name="no_ar" value="true" />';
	$no_ar='<tr><td colspan="6">'.$no_form.'参加しているアーティストはいません</td></tr>';
	
	//データをDBから引っ張り出す。
	$sql = "SELECT * FROM rel_artist_audition INNER JOIN artist ON rel_artist_audition.ar_id=artist.ar_id
	where rel_artist_audition.del_flag = 0 AND rel_artist_audition.aud_seq_num = ".$row["audition_no"]." order by rel_artist_audition.reg_date desc";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["ar_drop"]=$no_ar;
	}
	//[for_artist]～[end_for_artist]で置換する回数
	$row_num=0;
	
	while($sql_data = $db->FetchRow($result_id))
	{
		//添え字が数字の配列を削除
		$data_key=(count($sql_data)/2);
		for($i=0;$i<$data_key;$i++)
		{
			unset($sql_data[$i]);
		}
		
		if($sql_data["sex"]==0)
		{
			$sql_data["sex"]="男性";
		}
		else
		{
			$sql_data["sex"]="女性";
		}
		
		if(!empty($_POST["ar_drop_comp"]))
		{
			if(strpos($_POST["ar_".$sql_data["ar_id"]],"check_") !== false)
			{
				$sql_data["ar_form_check"]='check_'.$sql_data["ar_id"].'" checked="checked';
			}
			else
			{
				$sql_data["ar_form_check"]="check_".$sql_data["ar_id"];
			}
		}
		else
		{
			$sql_data["ar_form_check"]="check_".$sql_data["ar_id"];
		}
		$row["artist_data_".$row_num]=$sql_data;
		$row_num++;
		

	}
	
	if($row_num==0)
	{
		$row["ar_drop"]=$no_aud;
	}

	if(!empty($_POST["ar_drop_comp"]) && $row["ar_drop"]=="")
	{
		$row["err_det"]='<p class="red">削除確認チェックがはいっていません。</p>';
	}
	else
	{
		$row["err_det"]="";
	}

}
	
//////////////////////////////////////////////////
//
//　　　画面表示設定
//
//////////////////////////////////////////////////

//html内容を保持
$control->SetContentData($content);

/*************************************************
            複数個表示する処理:開始
*************************************************/

//オーディション表示
$for = explode("[for_artist]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_artist]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["ar_drop"]=="")
{
	for($i=0; $i < $row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["artist_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["ar_drop"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_artist]".$for[0]."[end_for_artist]";
$forName="list_for_artist";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";


/*************************************************
            複数個表示する処理:終了
*************************************************/

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