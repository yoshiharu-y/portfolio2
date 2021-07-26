<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


//htmlに表示させるプログラム
//アーティストがマイページからオーディション参加ボタンを押して来た場合。
if(!empty($_SESSION["aud_app_no"]))
{
	$get=htmlspecialchars($_GET["entry"], ENT_QUOTES, 'UTF-8');
	$sql = "SELECT * FROM audition_det 
			WHERE del_flag = 0
			AND entry_end >= '".date("Y-m-d H:i:s")."' 
			AND end_date >= '".date("Y-m-d H:i:s")."'
			AND hidden_flag = 0 
			AND aud_seq_num =".$get;
	$ar_app='&gt;　<a href="../artist/">アーティストマイページ</a>　';
	$aud_link="./?arview=".$_SESSION["aud_app_no"];
}
else
{
	$get=htmlspecialchars($_GET["view"], ENT_QUOTES, 'UTF-8');
	$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$get."'";
	$ar_app="";
	$aud_link="./";
}

$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: error.html");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["javasc"]="";
	$row["ar_app"]=$ar_app;
	$row["aud_link"]=$aud_link;
	$row["banner_add"]=$tmpdir.$row["banner_add"];
	
	//参加ボタン
	if(!empty($_SESSION["aud_app_no"]))
	{
		//登録時のアラート表示
		if($_SESSION["entry_comp"]=="insert")
		{
			$row["javasc"]='
			<script type="text/javascript">
				<!--
				alert("オーディションに参加しました。");
				// -->
				</script>
			';
			unset($_SESSION["entry_comp"]);
		}
		
		
		$sql="SELECT * FROM rel_artist_audition
			WHERE del_flag = 0
			AND aud_seq_num =".$get." 
			AND ar_id =".$_SESSION["mms_artist"];
		$result_id = $db->ExecuteSQL($sql);
		if(mysql_num_rows($result_id)!=0)
		{
			$row["contact_url"]='<span class="coution">既に参加しています。</span>';
		}
		else
		{
			if(strtotime($row["end_date"])>strtotime(date("Y-m-d H:i:s")))
			{
				$row["contact_url"]='
				<form action="" method="post" enctype="multipart/form-data">
				<p><input type="submit" name="artist_entry" value="オーディションに参加する" /></p>
				</form>
				';
			}
		}
				
	}
	
	//アーティストの検索文
	$sql = "SELECT * FROM artist INNER JOIN rel_artist_audition ON artist.ar_id=rel_artist_audition.ar_id
	WHERE artist.del_flag = 0 
	AND rel_artist_audition.del_flag = 0 
	AND rel_artist_audition.aud_seq_num = ".$get." ";
	
	//////////////////////////////////////////////////
	//　 NEWエントリー
	//////////////////////////////////////////////////
	
	//データをDBから引っ張り出す。
	$sort="ORDER BY rel_artist_audition.reg_date DESC limit 0,7";
	$result_id = $db->ExecuteSQL($sql.$sort);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["entry_artist_new"]="<p>登録しているアーティストはいません。</p>";
	}
	else
	{
		$row["entry_artist_new"]="";
		$new_num=0;	
		while($new_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($new_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($new_data[$i]);
			}
			//リンク
			$new_data["artist_url"]="../artist/?view=".$new_data["ar_id"];
			if(!empty($_SESSION["mms_artist"]) || (strtotime($row["start_date"])>strtotime(date("Y-m-d H:i:s"))))
			{
				$new_data["point_link"]="";
			}
			else
			{
				$point_link="?view=".$new_data["ar_id"]."ADP".$get."&check=".$control->CreateRandText(20);
				$new_data["point_link"]='<p class="vote"><a href="./'.$point_link.'"><img src="../common/images/btn/vote_btn.gif" width="76" height="15" alt="投票する" /></a></p>';
			}
				
			//画像設定
			if($new_data["artist_thum"]=="no_thum.jpg")
			{
				$new_data["artist_thum"]="../common/images/m_noimage.jpg";	
			}
			else
			{
				$new_data["artist_thum"]="../artist/images/a".$new_data["ar_id"]."/"."m_".$new_data["artist_thum"];
			}
			$row["artist_new_".$new_num]=$new_data;
			$new_num++;
		}
	
	}
	
	//////////////////////////////////////////////////
	//　 アーティストランキング
	//////////////////////////////////////////////////
	
	//データをDBから引っ張り出す。
	$sort="ORDER BY rel_artist_audition.ar_point DESC limit 0,7";
	$result_id = $db->ExecuteSQL($sql.$sort);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["entry_artist_rank"]="<p>登録しているアーティストはいません。</p>";
	}
	else
	{
		$row["entry_artist_rank"]="";
		$rank_num=0;	
		while($rank_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($rank_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($rank_data[$i]);
			}
			//リンク
			$rank_data["artist_url"]="../artist/?view=".$rank_data["ar_id"];
			if(!empty($_SESSION["mms_artist"]) || strtotime($row["start_date"])>strtotime(date("Y-m-d H:i:s")))
			{
				$rank_data["point_link"]="";
			}
			else
			{
				$point_link="?view=".$rank_data["ar_id"]."ADP".$get."&check=".$control->CreateRandText(20);
				$rank_data["point_link"]='<p class="vote"><a href="./'.$point_link.'"><img src="../common/images/btn/vote_btn.gif" width="76" height="15" alt="投票する" /></a></p>';
			}
			//画像設定
			if($rank_data["artist_thum"]=="no_thum.jpg")
			{
				$rank_data["artist_thum"]="../common/images/m_noimage.jpg";	
			}
			else
			{
				$rank_data["artist_thum"]="../artist/images/a".$rank_data["ar_id"]."/"."m_".$rank_data["artist_thum"];
			}
			$row["artist_rank_".$rank_num]=$rank_data;
			$rank_num++;
		}
	
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



//html内容を保持
$control->SetContentData($content);

/*************************************************
            複数個表示する処理:開始
*************************************************/

//NEWエントリー
$for = explode("[for_artist_new]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_artist_new]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["entry_artist_new"]=="")
{
	for($i=0; $i < $new_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["artist_new_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["entry_artist_new"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_artist_new]".$for[0]."[end_for_artist_new]";
$forName="list_for_artist_new";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

//お気に入りの置換後html内容を取得
$content=$control->GetContentData();

//アーティストランキング
$for = explode("[for_artist_rank]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_artist_rank]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["entry_artist_rank"]=="")
{
	for($i=0; $i < $rank_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["artist_rank_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["entry_artist_rank"];
}
//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_artist_rank]".$for[0]."[end_for_artist_rank]";
$forName="list_for_artist_rank";

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