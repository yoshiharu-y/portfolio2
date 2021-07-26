<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//動画サイズ
$width=550;
$height=305;

//動画サイト
$site=array("ニコニコ動画","USTREAM","YouTube");
$site_type=array("nicovideo","ustream","youtube");

//画像保存フォルダがある場所
$tmpdir  = "../images/";


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//編集画面から
if(!empty($_POST["movie_edit_check"]) && isset($_SESSION["movie_edit"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	$sql = "SELECT * FROM ar_movie WHERE ar_id=".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)!=0)
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
	}
}
//確認チェックが入っていない場合
else if(!empty($_POST["movie_edit_comp"]) && isset($_SESSION["movie_edit"]))
{
	foreach($_SESSION["movie_edit"] as $key => $value )
	{
		$row[$key]=$value;
		$_POST[$key]=$value;
	}
}
else
{
	header( "Location: ../");
}

foreach($_POST as $key => $value)
{
	//無記入箇所はsqlデータにする。記入箇所はpostデータ。
	if($_POST[$key]!="")
	{
		$row[$key]=$_POST[$key];
	}
}

//動画チェック
foreach($site as $key => $value)
{
	if($key==$_POST["movie_site"])
	{
		$row["site"]=$value;
		$row["type"]=$site_type[$key];
		$row["movie_type"]=$_POST["movie_site"];
	}
}

if(strpos($_POST["movie_url"],$row["type"])!== false)
{
	$urls=explode("/",$_POST["movie_url"]);
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
	if($row["type"]=="nicovideo")
	{
		//urlの中にsmもしくはnmの文字があるかどうか
		if(strpos($urls_back[0],"sm")!== false || strpos($urls_back[0],"nm")!== false)
		{
			//動画埋め込み例
			$row["movie_detail"]='
			<script type="text/javascript" src="http://www.nicovideo.jp/thumb_watch/'.$urls_back[0].'?w='.$width.'&h='.$height.'"></script>';
			//動画url
			$row["movie_detail"].='
			<noscript><a href="'.$_POST["movie_url"].'">'.$_POST["movie_title"].'</a></noscript>';
		}
		else
		{
			$row["err_det"].='<div class="coutionBox"><p class="coution">動画のURLから動画を読み込めませんでした。再度設定してください。e101</p></div>';
			$row["movie_detail"]="";
		}
	}
	
	//USTREAM動画
	if($row["type"]=="ustream")
	{
		$movie_url=implode("/",$urls);
		if(strpos($urls[3],"embed")!== false)
		{
			if(count($urls)>=5)
			{
				$row["movie_detail"]='
				<iframe src="'.$movie_url.'" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe>';
			}
			else
			{
				$row["err_det"].='<div class="coutionBox"><p class="coution">動画のURLから動画を読み込めませんでした。再度設定してください。e102</p></div>';
				$row["movie_detail"]="";
			}
		}
		else
		{
			$row["err_det"].='<div class="coutionBox"><p class="coution">動画のURLから動画を読み込めませんでした。再度設定してください。e103</p></div>';
			$row["movie_detail"]="";
		}
	}
	
	//YouTube動画
	if($row["type"]=="youtube")
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
				$row["movie_detail"]='
				<iframe src="'.$movie_url.'" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" allowfullscreen></iframe>';
			}
			else
			{
				$row["err_det"].='<div class="coutionBox"><p class="coution">動画のURLから動画を読み込めませんでした。再度設定してください。e104</p></div>';
				$row["movie_detail"]="";
			}
		}
		else
		{
			$row["err_det"].='<div class="coutionBox"><p class="coution">動画のURLから動画を読み込めませんでした。再度設定してください。e105</p></div>';
			$row["movie_detail"]="";
		}
	}
	
	
}
else
{
	$row["err_det"].='<div class="coutionBox"><p class="coution">動画元のサイトと動画のURLが一致しません。</p></div>';
	$row["movie_detail"]="";
}
//編集画面で使用する配列としてSESSIONに保存
//添え字が数字の配列を削除
$data_key=(count($row)/2);
for($i=0;$i<$data_key;$i++)
{
	unset($row[$i]);
}
$_SESSION["movie_edit"]=$row;


//エラーが無い場合の処理
if($row["err_det"]=="")
{
	$row["err_det"]='<div class="coutionBox"><p>以下の内容でデータが変更されます。</p></div>';
	//確認チェックなしの時に出るエラー文章
	if(!empty($_POST["movie_edit_comp"]) && isset($_SESSION["movie_edit"]))
	{
		$row["err_det"]='<div class="coutionBox"><p class="coution">編集を完了させるチェックが入っていません。</p></div>';
	}
	
	$row["comp_check"]='<p><input type="checkbox" id="send" name="comp_check" value="check" />
      <label for="send" class="label">編集を完了する場合はチェックを入れてください</label><p>';
	$row["comp_btn"]='<input type="submit" name="movie_edit_comp" value="編集を完了する" class="send_submit" />';
}
else
{
	$row["comp_check"]="";
	$row["comp_btn"]="";
}


//サムネイル画像関係
$artist_sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$_SESSION["mms_artist"]."'";
$artist_result_id = $db->ExecuteSQL($artist_sql);

if(mysql_num_rows($artist_result_id)==0)
{
	header( "Location: ../");
}
else
{
	$artist_row=$db->FetchRow($artist_result_id);
	//サムネイル画像関係
	if($artist_row["artist_thum"]=="no_thum.jpg")
	{
		$artist_row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$artist_row["ar_id"]."/";
		$artist_row["artist_thum"]=$tmpdir."f_".$artist_row["artist_thum"];
		
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

//html内の特殊タグを変換するのに必要なタイプを設定
$control->SetContentType("data_s");

//定義したタイプ[data_s:]になっている所を変換
foreach( $row as $key => $value )
{
	$control->ChangeData($key,$value);
}

foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>