<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "images/";
//フォルダ名
$dir="e".$_SESSION["mms_user"];
//アーティスト画像保存フォルダがある場所
$ar_tmpdir  = "../artist/images/";

//////////////////////////////////////////////////
//    お気に入りアーティスト部のhtml
//////////////////////////////////////////////////

//リスト内で表示する数
$favo_list_num=5;

//html
$favo_html_head='<div class="favoriteArtist">'."\n";
$favo_html_image='<p class="image"><img src="{artist_thum}" width="100%" height="100%" /></p>'."\n";
$favo_html_name='<p class="name">{artist_name}</p>';
$favo_html_footer='</div>'."\n";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//ユーザーデータ ログイン情報のみ抽出
$sql = "SELECT login_date FROM user where user_id =".$_SESSION["mms_user"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../");
}
else
{
	$date = $db->FetchRow($result_id);
	
	$login_date=explode(" ",$date["login_date"]);
	if($login_date[0]!=date("Y-m-d"))
	{
		$sql = "UPDATE user SET
							free_point = ?
							WHERE user_id = ? limit 1";
				//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		
		$phs = array(
						0,
						$_SESSION["mms_user"]
						);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$db->ExecuteSQL($sql_prepare);
	}
	
	$sql = "SELECT * FROM user where user_id =".$_SESSION["mms_user"];
	$result_id = $db->ExecuteSQL($sql);
	$row = $db->FetchRow($result_id);
	
	//////////////////////////////////////////////////
	//　                                            //
	//　  ***************表示関係***************    //
	//　                                            //
	//////////////////////////////////////////////////
	
	
	
	$row["user_id"]=sprintf("%06d",$row["user_id"]);
	$row["point"]=$row["free_point"]+$row["pay_point"];
	
	//ポイントのHTML設定
	$plain_point=$row["point"];
	$row["point"]="";
	for($i=1;$i<=strlen($plain_point);$i++)
	{
		$row["point"].="<span>".substr($plain_point,$i-1,1)."</span>";
	}
	
	//画像関係
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_thum"]="images/no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."e".$row["user_id"]."/";
		$row["user_thum"]=$tmpdir."b_".$row["user_thum"];
	}
	
	//////////////////////////////////////////////////
	//　 ニュース news_フィールド名
	//////////////////////////////////////////////////
	
	$sql = "SELECT * FROM news order by seq_num desc";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["news_news_title"]="現在お知らせはありません";
		$row["news_reg_date"]="";
		
		
	}
	else
	{
		$news_data = $db->FetchRow($result_id);
		//リンク設定
		if(strpos($news_data["news_title"],"[news_not_link]") !== false)
		{
			$news_data["news_title"]=str_replace("[news_not_link]", "", $news_data["news_title"]);
		}
		else
		{
			$news_data["news_title"]='<a href="../news/?det='.$news_data["news_id"].'">'.$news_data["news_title"].'</a>';
		}
		foreach($news_data as $key => $value)
		{
			$row["news_".$key]=$value;
		}
	}

	//////////////////////////////////////////////////
	//　 お気に入り
	//////////////////////////////////////////////////
	
	$sql = "SELECT * FROM favorite INNER JOIN artist ON favorite.ar_id = artist.ar_id 
	WHERE favorite.user_id=".$_SESSION["mms_user"]." 
	AND artist.del_flag= 0  
	ORDER BY favorite.seq_num DESC";
	$result_id = $db->ExecuteSQL($sql);
	
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_favorite"]='<p>お気に入りはありません</p>';
	}
	else
	{
		$favorite_num=0;
		$favo_html_num=0;
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			if($sql_data["artist_thum"]=="no_thum.jpg")
			{
				$artist_thum=$ar_tmpdir."m_no_image.jpg";
			}
			else
			{
				$artist_thum=$ar_tmpdir."a".$sql_data["ar_id"]."/"."m_".$sql_data["artist_thum"];
			}
			
			//htmlのイメージ画像パス設定
			$new_html_image=str_replace("{artist_thum}", $artist_thum, $favo_html_image);
			//htmlのアーティスト名設定
			$new_html_name=str_replace("{artist_name}", $sql_data["artist_name"], $favo_html_name);
			//html成型
			$favo_html.=$favo_html_head.$new_html_image.$new_html_name.$favo_html_footer;
			$favo_html_num++;
			if($favo_list_num==$favo_html_num)
			{
				$sql_data["artist_favo_html"]=$favo_html;
				$row["favorite_data_".$favorite_num]=$sql_data;
				$favo_html_num=0;
				$favo_html="";
				$favorite_num++;
			}
		}
		
		if($favo_html_num!=0)
		{
			
			$sql_data["artist_favo_html"]=$favo_html;
			$row["favorite_data_".$favorite_num]=$sql_data;
			$favorite_num++;
		}

	}
	
	//////////////////////////////////////////////////
	//　 コメント履歴　5件まで
	//////////////////////////////////////////////////
	
	$sql = "SELECT artist.artist_name,user_comment.comment,user_comment.reg_date FROM user INNER JOIN (user_comment INNER JOIN artist ON user_comment.ar_id=artist.ar_id) ON user_comment.user_id=user.user_id WHERE user_comment.user_id=".$_SESSION["mms_user"]." 
	AND artist.del_flag= 0 
	ORDER BY user_comment.reg_date desc limit 0,5";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_message"]='<tr><td>メッセージ履歴はありません</td></tr>';
	}
	else
	{
		//メッセージ詳細へのリンクの値としても使う。emptyで判定してるので1から
		$message_num=1;
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			//nameの文字数制限
			if(mb_strlen($sql_data["artist_name"])>=7)
			{
				$sql_data["artist_name"]=mb_substr($sql_data["artist_name"],0,7)."...";
			}
			//メッセージ(コメント)の文字数制限
			$sql_data["message"]=str_replace("<br />","", $sql_data["comment"]);
			if(mb_strlen($sql_data["message"])>=20)
			{
				$sql_data["message"]=mb_substr($sql_data["message"],0,20)."...";
			}
			//詳細へのリンク
			$sql_data["link_message"]="message/?message=".$message_num;
			$row["message_data_".$message_num]=$sql_data;
			$message_num++;
		}
	}
	
	//////////////////////////////////////////////////
	//　 投票履歴　5件まで
	//////////////////////////////////////////////////
	
	$sql = "SELECT * FROM user INNER JOIN (vote_list INNER JOIN artist ON vote_list.ar_id=artist.ar_id) ON vote_list.user_id=user.user_id WHERE vote_list.user_id=".$_SESSION["mms_user"]." 
	AND artist.del_flag= 0 
	ORDER BY vote_list.vote_date desc limit 0,5";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["non_vote"]='<div class="voteListBox"><p>履歴はありません</p></div>';
	}
	else
	{
		$vote_num=0;
		while($sql_data = $db->FetchRow($result_id))
		{
			//添え字が数字の配列を削除
			$data_key=(count($sql_data)/2);
			for($i=0;$i<$data_key;$i++)
			{
				unset($sql_data[$i]);
			}
			if($sql_data["artist_thum"]=="no_thum.jpg")
			{
				$sql_data["artist_thum"]=$ar_tmpdir."m_no_image.jpg";
			}
			else
			{
				$sql_data["artist_thum"]=$ar_tmpdir."a".$sql_data["ar_id"]."/"."m_".$sql_data["artist_thum"];
			}
			//nameの文字数制限
			if(mb_strlen($sql_data["artist_name"])>=12)
			{
				$sql_data["artist_name"]=mb_substr($sql_data["artist_name"],0,12)."...";
			}
			//投票日付成型
			$vote_date=explode(" ",$sql_data["vote_date"]);
			$sql_data["vote_date"]=$vote_date[0];
			//投票ポイント成型
			$plain_user_vote=$sql_data["user_vote"];
			$sql_data["user_vote"]="";
			for($i=1;$i<=strlen($plain_user_vote);$i++)
			{
				$sql_data["user_vote"].=substr($plain_user_vote,$i-1,1);
			}
			$row["vote_data_".$vote_num]=$sql_data;
			$vote_num++;
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

//お気に入り
$for = explode("[for_favorite]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_favorite]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_favorite"]=="")
{
	for($i=0; $i < $favorite_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["favorite_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_favorite"];
}
//初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_favorite]".$for[0]."[end_for_favorite]";
$forName="list_for_favorite";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";



//お気に入りの置換後html内容を取得
$content=$control->GetContentData();

//メッセージ
$for = explode("[for_message]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_message]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_message"]=="")
{
	for($i=1; $i < $message_num ;$i++)
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
//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_message]".$for[0]."[end_for_message]";
$forName="list_for_message";

$control->SetContentData(str_replace($htmlFor,"[change_for:".$forName."]",$control->GetContentData()));
$control->SetContentType("change_for");
$control->ChangeData($forName,$html_data);
$html_data="";



//メッセージの置換後html内容を取得
$content=$control->GetContentData();

//投票履歴
$for = explode("[for_vote]",$control->GetContentData());
array_shift($for);
$for = explode("[end_for_vote]",$for[0]);
array_pop($for);

$control->SetContentType("data_s");

if($row["non_vote"]=="")
{
	for($i=0; $i < $vote_num ;$i++)
	{
		$control->SetContentData($for[0]);
		foreach( $row["vote_data_".$i] as $key => $value )
		{
			$control->ChangeData($key,$value);
		}
		$html_data.=$control->GetContentData();
	}
}
else
{
	$html_data=$row["non_vote"];
}
//再度初期状態のHTMLデータにする。
$control->SetContentData($content);

$htmlFor="[for_vote]".$for[0]."[end_for_vote]";
$forName="list_for_vote";

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