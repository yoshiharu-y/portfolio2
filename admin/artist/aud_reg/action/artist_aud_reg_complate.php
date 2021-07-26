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
	//登録処理
	foreach( $_POST as $key => $value )
	{
		$value= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		if(strpos($value,"check_") !== false)
		{
			//フォームのcheckタグからオーディションナンバー抽出
			$aud_num=substr($value,6);
			$sql = "SELECT * FROM rel_artist_audition where ar_id = ".$row["ar_id"]." AND aud_seq_num=".$aud_num;
			//echo $sql."<br>";
			$result_id = $db->ExecuteSQL($sql);
			//テーブル内にデータが無ければ登録
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
								$aud_num,
								$row["ar_id"],
								0,
								date("Y-m-d")
								);
				//インジェクション対策のsqlプリペアード関数
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				$db->ExecuteSQL($sql_prepare);
				
			}
			else
			{
				$ar_row = $db->FetchRow($result_id);
				if($ar_row["del_flag"]==1)
				{
					$sql="UPDATE rel_artist_audition SET del_flag = 0 WHERE ar_id = ".$row["ar_id"]." AND aud_seq_num =".$aud_num;
					$sql_prepare = $db->mysql_prepare($sql, $phs);
					$db->ExecuteSQL($sql_prepare);
				}	
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