<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../audition/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	if($row["banner_add"]=="no_thum.jpg")
	{
		$row["m_banner_add"]="画像がありません";
		$row["banner_add"]="画像がありません";
		
	}
	else
	{
		$row["m_banner_add"]='<img src="'.$tmpdir."m_".$row["banner_add"].'" />';
		$row["banner_add"]='<img src="'.$tmpdir.$row["banner_add"].'" />';
		
	}
	//表示方法設定
	if($row["hidden_flag"]==1)
	{
		$row["view"]="詳細のみの表示";
	}
	else
	{
		$row["view"]="通常表示";
	}
	
	//オーディションに参加しているアーティストを表示
	$row_list="artist.ar_id,artist.seq_num,artist.artist_name,rel_artist_audition.ar_point,rel_artist_audition.ar_point,rel_artist_audition.reg_date";
	
	$sql = "SELECT ".$row_list." FROM artist INNER JOIN rel_artist_audition ON artist.ar_id=rel_artist_audition.ar_id
	WHERE artist.del_flag = 0
	AND rel_artist_audition.del_flag = 0
	AND rel_artist_audition.aud_seq_num = ".$row["aud_seq_num"]." ";
	
	$sort="ORDER BY rel_artist_audition.ar_point DESC";
	
	$result_id = $db->ExecuteSQL($sql.$sort);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["entry"]='<tr><td colspan="6"></th>オーディションに参加しているアーティストはいません</td></tr>';
	}
	else
	{
		$row["entry"]="";
		$row_num=0;	
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			
			//順位
			$rank_sql="SELECT * FROM rel_artist_audition WHERE aud_seq_num=".$row["aud_seq_num"]."
			ORDER BY ar_point DESC";
			$rank_id = $db->ExecuteSQL($rank_sql);
			
			$rank_num=1;
			while($rank = $db->FetchRow($rank_id))
			{
				//同率順位を適応する処理
				$ranklist[$rank_num]["ar_point"]=$rank["ar_point"];
				$ranklist[$rank_num]["ar_id"]=$rank["ar_id"];
				
				if($ranklist[$rank_num-1]["ar_point"]==$rank["ar_point"])
				{
					$ranklist[$rank_num]["rank"]=$ranklist[$rank_num-1]["rank"];
				}
				else
				{
					$ranklist[$rank_num]["rank"]=$rank_num;
				}
				
				$rank_num++;
			}
			
			foreach($ranklist as $key => $value)
			{
				if($value["ar_id"]==$sql_data["ar_id"])
				{
					$sql_data["aud_rank"]=$value["rank"];
				}
			
			}
			
			//[for_artist]～[end_for_artist]で置換する分だけrowに多次元配列として作成
			$row["artist_data_".$row_num]=$sql_data;
			$row_num++;
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

/*************************************************
            複数個表示する処理:開始
*************************************************/

//アーティストを表示
$for = explode("[for_artist]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_artist]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["entry"]=="")
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
	$html_data=$row["entry"];
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