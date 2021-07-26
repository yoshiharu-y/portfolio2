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
	//登録処理
	foreach( $_POST as $key => $value )
	{
		$value= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		if(strpos($value,"check_") !== false)
		{
			//フォームのcheckタグからアーティストID抽出
			$ar_id=substr($value,6);
			$sql = "SELECT * FROM rel_artist_audition WHERE ar_id = ".$ar_id." AND aud_seq_num=".$row["aud_seq_num"];
			
			$result_id = $db->ExecuteSQL($sql);
			//テーブル内にデータが無ければ登録
			//var_dump(mysql_num_rows($result_id));
			if(mysql_num_rows($result_id)==1)
			{
				$sql="UPDATE rel_artist_audition SET del_flag = 1 WHERE ar_id = ".$ar_id." AND aud_seq_num =".$row["aud_seq_num"];
				//echo $sql;
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				$db->ExecuteSQL($sql_prepare);
			}
		}
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