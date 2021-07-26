<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//動画サイズ
$width=550;
$height=305;

//動画サイト
$site=array("ニコニコ動画","USTREAM","YouTube");
$site_type=array("nicovideo","ustream","youtube");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

$sql = "SELECT * FROM mn_movie WHERE seq_num=".$_POST["mn_movie"];
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
else
{
	$row = $db->FetchRow($result_id);
	$row["err_det"]="";
	$row["movie_detail"]="";
	//動画チェック
	foreach($site as $key => $value)
	{
		if($key==$row["movie_type"])
		{
			$row["site"]=$value;
			$row["type"]=$site_type[$key];
		}
	}
	
	//動画生成
	$urls=explode("/",$row["movie_url"]);
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
	if($row["type"]==$site_type[0])
	{
		//urlの中にsmもしくはnmの文字があるかどうか
		if(strpos($urls_back[0],"sm")!== false || strpos($urls_back[0],"nm")!== false)
		{
			//動画埋め込み例
			$row["movie_detail"]='
			<script type="text/javascript" src="http://www.nicovideo.jp/thumb_watch/'.$urls_back[0].'?w='.$width.'&h='.$height.'"></script>';
			//動画url
			$row["movie_detail"].='
			<noscript><a href="'.$row["movie_url"].'">'.$row["movie_title"].'</a></noscript>';
		}
	}
	
	//USTREAM動画
	if($row["type"]==$site_type[1])
	{
		$movie_url=implode("/",$urls);
		if(strpos($urls[3],"embed")!== false)
		{
			if(count($urls)>=5)
			{
				$row["movie_detail"]='
				<iframe src="'.$movie_url.'" width="'.$width.'" height="'.$height.'" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe>';
			}
		}
	}
	//YouTube動画
	if($row["type"]==$site_type[2])
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
		}
	}
	
	if(!empty($_POST["mn_drop_comp"]))
	{
		$row["err_det"]='<p class="red">確認チェックがはいっていません。</p>';
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