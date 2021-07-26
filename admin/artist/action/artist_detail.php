<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../artist/images/";

//動画サイト
$site=array("ニコニコ動画","USTREAM","YouTube");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM artist where del_flag = 0 and ar_id='".$_POST["artist_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_m_thum"]="画像がありません";
		$row["artist_f_thum"]="画像がありません";
		$row["artist_thum"]="画像がありません";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$row["ar_id"]."/";
		$row["artist_m_thum"]='<img src="'.$tmpdir."m_".$row["artist_thum"].'" />';
		$row["artist_f_thum"]='<img src="'.$tmpdir."f_".$row["artist_thum"].'" />';
		$row["artist_thum"]='<img src="'.$tmpdir.$row["artist_thum"].'" />';
		
	}
	if($row["sex"]==0)
	{
		$row["sex"]="男性";
	}
	else
	{
		$row["sex"]="女性";		
	}
	
	/////////////////////////////////////////////////
	//    動画一覧
	/////////////////////////////////////////////////
	
	$sql = "SELECT * FROM ar_movie WHERE ar_id = ".$row["ar_id"]." ";

	$result_id = $db->ExecuteSQL($sql);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_movie"]='<tr><td colspan="7">投稿している動画はありません</td></tr>';
	}
	else
	{
		$row["non_movie"]="";
		$movie_row_num=0;	
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			//動画チェック
			foreach($site as $key => $value)
			{
				if($key==$sql_data["movie_type"])
				{
					$sql_data["site"]=$value;
				}
			}
			//url内にustのembedがある場合
			if(strpos($sql_data["movie_url"],"embed")!== false)
			{
				$urls=explode("embed/",$sql_data["movie_url"]);
				$sql_data["movie_url"]=$urls[0].$urls[1];
			}
				
			//[for_movie]～[end_for_movie]で置換する分だけrowに多次元配列として作成
			$row["movie_data_".$movie_row_num]=$sql_data;
			$movie_row_num++;
		}
	
	}
	
	/////////////////////////////////////////////////
	//    音楽一覧
	/////////////////////////////////////////////////
	
	$sql = "SELECT * FROM ar_sound WHERE ar_id = ".$row["ar_id"]." ORDER BY seq_num desc";
	$result_id = $db->ExecuteSQL($sql);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_sound"]='<tr><td colspan="5">投稿している音楽はありません</td></tr>';
	}
	else
	{
		$row["non_sound"]="";
		$sound_row_num=0;	
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
				
			//[for_sound]～[end_for_sound]で置換する分だけrowに多次元配列として作成
			$row["sound_data_".$sound_row_num]=$sql_data;
			$sound_row_num++;
			
		}
	}
	
	
	/////////////////////////////////////////////////
	//    参加しているオーディション表示
	/////////////////////////////////////////////////
	
	$sql = "SELECT * FROM audition_det INNER JOIN rel_artist_audition ON audition_det.aud_seq_num=rel_artist_audition.aud_seq_num
	WHERE audition_det.del_flag = 0
	AND rel_artist_audition.del_flag = 0
	AND rel_artist_audition.ar_id = ".$row["ar_id"]." ";
	
	$sort="ORDER BY rel_artist_audition.reg_date DESC";

	$result_id = $db->ExecuteSQL($sql.$sort);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_entry"]='<tr><td colspan="6">オーディションに参加していません</td></tr>';
	}
	else
	{
		$row["non_entry"]="";
		$aud_row_num=0;	
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
			$row["audition_data_".$aud_row_num]=$sql_data;
			$aud_row_num++;
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

//動画一覧表示
$for = explode("[for_movie]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_movie]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_movie"]=="")
{
	for($i=0; $i < $movie_row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["movie_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_movie"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_movie]".$for[0]."[end_for_movie]";
$forName="list_for_movie";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

//動画の置換後html内容を取得
$content=$control->GetContentData();

//音楽一覧表示
$for = explode("[for_sound]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_sound]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_sound"]=="")
{
	for($i=0; $i < $sound_row_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["sound_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_sound"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_sound]".$for[0]."[end_for_sound]";
$forName="list_for_sound";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

//メッセージの置換後html内容を取得
$content=$control->GetContentData();


//オーディション表示
$for = explode("[for_audition]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_audition]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_entry"]=="")
{
	for($i=0; $i < $aud_row_num ;$i++)
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
	$html_data=$row["non_entry"];
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