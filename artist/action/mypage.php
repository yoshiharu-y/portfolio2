<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "images/";

//ユーザーの画像保存フォルダがある場所
$usertmpdir  = "../user/images/";

//ユーザーのMP3保存フォルダがある場所
$soundtmpdir  = "mp3/";

//動画サイズ
$width=550;
$height=305;

//動画サイト
$site_type=array("nicovideo","ustream","youtube");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$artist_sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$_SESSION["mms_artist"]."'";
$artist_result_id = $db->ExecuteSQL($artist_sql);

if(mysql_num_rows($artist_result_id)==0)
{
	header( "Location: ../login/");
}
else
{
	$row = $db->FetchRow($artist_result_id);
	
	//アーティストインフォメーション
	if($row["news"]=="")
	{
		$row["news"]="現在はありません。";
	}
	//ポイントのHTML設定
	$plain_point=$row["vote_point"];
	$row["vote_point"]="";
	for($i=1;$i<=strlen($plain_point);$i++)
	{
		$row["vote_point"].="<span>".substr($plain_point,$i-1,1)."</span>";
	}
	
	//サムネイル画像関係
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$row["ar_id"]."/";
		$row["artist_thum"]=$tmpdir."f_".$row["artist_thum"];
		
	}
	
	//////////////////////////////////////////////////
	//　 動画表示
	//////////////////////////////////////////////////
	
	$movie_sql = "SELECT * FROM ar_movie WHERE ar_id='".$_SESSION["mms_artist"]."' 
				ORDER BY renew_date DESC";			
	$movie_result_id = $db->ExecuteSQL($movie_sql);
	if(mysql_num_rows($movie_result_id)==0)
	{
		$row["movie_html"]='<img src="images/no_video.gif" width="550" height="305" />';
	}
	else
	{
		$movie_row = $db->FetchRow($movie_result_id);
		//動画チェック
		foreach($site_type as $key => $value)
		{
			if($key==$movie_row["movie_type"])
			{
				$movie_row["type"]=$site_type[$key];
			}
		}
		
		//動画生成
	$urls=explode("/",$movie_row["movie_url"]);
	//ust,youtubeのアドレス成型
	if((strpos($urls[3],"watch")!== false) || (strpos($urls[3],"recorded")!== false))
	{
		$array_num=0;
		foreach($urls as $value)
		{
			if(($array_num==3 && strpos($value,"watch")!== false)||($array_num==3 && strpos($value,"recorded")!== false))
			{
				$urls[$array_num]="embed";
				$urls[$array_num+1]=$value;
				$array_num=$array_num+2;	
			}
			else
			{
				$urls[$array_num]=$value;
				$array_num++;
			}
		}
	}
	$urls_back=array_reverse($urls);
	
	//ニコニコ動画
	if($movie_row["type"]==$site_type[0])
	{
		//urlの中にsmもしくはnmの文字があるかどうか
		if(strpos($urls_back[0],"sm")!== false || strpos($urls_back[0],"nm")!== false)
		{
			//動画埋め込み例
			$row["movie_html"]='
			<script type="text/javascript" src="http://www.nicovideo.jp/thumb_watch/'.$urls_back[0].'?w='.$width.'&h='.$height.'"></script>';
			//動画url
			$row["movie_html"].='
			<noscript><a href="'.$movie_row["movie_url"].'">'.$movie_row["movie_title"].'</a></noscript>';
		}
	}
	
	//USTREAM動画
	if($movie_row["type"]==$site_type[1])
	{
		$movie_url=implode("/",$urls);
		if(strpos($urls[3],"embed")!== false)
		{
			if(count($urls)>=5)
			{
				$row["movie_html"]='
				<iframe src="'.$movie_url.'" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe>';
			}
		}
	}
	//YouTube動画
	if($movie_row["type"]==$site_type[2])
	{
		if(strpos($urls[3],"embed")!== false)
		{
			$movie_add=explode("=",$urls_back[0]);
			if($movie_add[1]!="")
			{
				if(strpos($movie_add[1],"&")!== false)
				{
					$movie_add=explode("&",$movie_add[1]);
					$urls[count($urls)-1]=$movie_add[0];
				}
				else
				{
					$urls[count($urls)-1]=$movie_add[1];
				}
				
			}
			$movie_url=implode("/",$urls);
			if(count($urls)==5)
			{
				$row["movie_html"]='
				<iframe src="'.$movie_url.'" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" allowfullscreen></iframe>';
			}
		}
	}
	}
	
	//////////////////////////////////////////////////
	//　 コメント表示　5件まで
	//////////////////////////////////////////////////
	
	$comment_sql = "SELECT * FROM ar_comment INNER JOIN user ON ar_comment.user_id = user.user_id 
	WHERE ar_comment.ar_id = ".$_SESSION["mms_artist"]."
	AND user.del_flag = 0
	ORDER BY ar_comment.seq_num desc limit 0,5";
	
	$comment_result_id = $db->ExecuteSQL($comment_sql);
	if(mysql_num_rows($comment_result_id)==0)
	{
		$row["non_message"]="現在はありません。";
	}
	else
	{
		$message_num=0;
		while($sql_data = $db->FetchRow($comment_result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			if($sql_data["user_thum"]=="no_thum.jpg")
			{
				$sql_data["user_thum"]=$usertmpdir."no_image.jpg";
			}
			else
			{
				$sql_data["user_thum"]=$usertmpdir."e".$sql_data["user_id"]."/"."s_".$sql_data["user_thum"];
			}
			$row["message_data_".$message_num]=$sql_data;
			$message_num++;
		}
	}
	
	//////////////////////////////////////////////////
	//　 サウンド表示
	//////////////////////////////////////////////////

	//音楽ファイル関係
	$sound_sql = "SELECT * FROM ar_sound WHERE ar_id=".$_SESSION["mms_artist"]." ORDER BY seq_num desc limit 0,1";
	$sound_result_id = $db->ExecuteSQL($sound_sql);
	
	$sound_result_id = $db->ExecuteSQL($sound_sql);
	if(mysql_num_rows($sound_result_id)==0)
	{
		$row["non_sound"]="<p>現在はありません</p>";
	}
	else
	{
		$sound_num=0;
		while($sql_data = $db->FetchRow($sound_result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			$sql_data["sound_html"]=$soundtmpdir."a".$sql_data["ar_id"]."/".$sql_data["sound_url"];
			$row["sound_data_".$sound_num]=$sql_data;
			$sound_num++;
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

//メッセージ
$for = explode("[for_message]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_message]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_message"]=="")
{
	for($i=0; $i < $message_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["message_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_message"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_message]".$for[0]."[end_for_message]";
$forName="list_for_message";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

//メッセージの置換後html内容を取得
$content=$control->GetContentData();




//サウンド
$for = explode("[for_sound]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_sound]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_sound"]=="")
{
	for($i=0; $i < $sound_num ;$i++)
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
//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_sound]".$for[0]."[end_for_sound]";
$forName="list_for_sound";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";

//メッセージの置換後html内容を取得
$content=$control->GetContentData();




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