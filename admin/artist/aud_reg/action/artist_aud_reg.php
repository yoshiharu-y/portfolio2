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
$sql = "SELECT * FROM artist where del_flag = 0 and seq_num='".$_POST["artist_reg_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["artist_no"]=$_POST["artist_reg_no"];
	if($row["sex"]==0)
	{
		$row["sex"]="男性";
	}
	else
	{
		$row["sex"]="女性";
	}
	
	//参加オーディションがない場合の表示データとフォーム
	$no_form='<input type="hidden" name="no_aud" value="true" />';
	$no_aud='<tr><td colspan="5">'.$no_form.'参加できるオーディションがありません</td></tr>';
	
	//データをDBから引っ張り出す。
	$sql = "SELECT * FROM audition_det where del_flag = 0 order by aud_seq_num desc";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["reg_audition"]=$no_aud;
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
		$sql = "SELECT * FROM rel_artist_audition WHERE del_flag = 0 
		AND aud_seq_num=".$sql_data["aud_seq_num"]."
		AND ar_id=".$row["ar_id"];
		$reg_id = $db->ExecuteSQL($sql);
		if(mysql_num_rows($reg_id)==0)
		{
			if(strtotime($sql_data["end_date"]) >= time())
			{
				if(!empty($_POST["aud_reg_comp"]))
				{
					if(strpos($_POST["aud_".$sql_data["aud_seq_num"]],"check_") !== false)
					{
						$sql_data["aud_form_check"]='check_'.$sql_data["aud_seq_num"].'" checked="checked';
					}
					else
					{
						$sql_data["aud_form_check"]="check_".$sql_data["aud_seq_num"];
					}
				}
				else
				{
					$sql_data["aud_form_check"]="check_".$sql_data["aud_seq_num"];
				}
				$row["audition_data_".$row_num]=$sql_data;
				$row_num++;
			}
		}
		
	}
	
	if($row_num==0)
	{
		$row["reg_audition"]=$no_aud;
	}
	
	if(!empty($_POST["aud_reg_comp"]) && $row["reg_audition"]=="")
	{
		$row["err_det"]='<p class="red">参加確認チェックがはいっていません。</p>';
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
$for = explode("[for_audition]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_audition]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["reg_audition"]=="")
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
	$html_data=$row["reg_audition"];
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