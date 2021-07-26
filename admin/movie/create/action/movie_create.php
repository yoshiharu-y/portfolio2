<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//動画サイト
$site=array("ニコニコ動画","USTREAM","YouTube");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//idを入力させるフォームがあるため、エスケープ
$_POST["artist_no"]=htmlspecialchars($_POST["artist_no"], ENT_QUOTES, 'UTF-8');

//アーティストデータをDBから引っ張り出す。
$sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$_POST["artist_no"]."'";
$result_id = $db->ExecuteSQL($sql);

if(mysql_num_rows($result_id)==0)
{
	if($_SESSION["no_up"]=="no_up")
	{
		$row["err_det"]='<p class="red">既に動画が投稿されています。動画の投稿は1件までです。</p>';
		$row["movie_title"]="";
		$row["movie_url"]="";
		$row["ar_id"]="no_up";
		unset($_SESSION["no_up"]);
	}
	else
	{
		$row["err_det"]='<p class="red">アーティストの情報がありません。アーティスト一覧から確認してください。</p>';
		$row["movie_title"]="";
		$row["movie_url"]="";
		$row["ar_id"]="";
	}
}
else
{
	$row = $db->FetchRow($result_id);
	//アーティストデータをDBから引っ張り出す。
	$sql = "SELECT * FROM ar_movie WHERE ar_id='".$_POST["artist_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)!=0)
	{
		$row["err_det"]='<p class="red">既に動画が投稿されています。動画の投稿は1件までです。</p>';
		$row["movie_title"]="";
		$row["movie_url"]="";
		$row["ar_id"]="no_up";
	}
	else
	{
		//初回時
		if(!empty($_POST["movie_create"]))
		{
			$row["movie_title"]="";
			$row["movie_url"]="";
			$row["err_det"]="";
			
			//動画サイトリスト
			foreach($site as $key => $value)
			{
				$row["site_form"].='<option value="'.$key.'">'.$value.'</option>';
			}
		}
		//確認画面から
		else if(!empty($_POST["movie_create_return"]))
		{
			foreach( $_SESSION["movie_create"] as $key => $value )
			{
				$row[$key]=$value;	
			}
			$row["err_det"]="";
			
			//動画サイトリスト
			foreach($site as $key => $value)
			{
				if($key==$row["movie_site"])
				{
					$row["site_form"].='<option value="'.$key.'" selected="selected" >'.$value.'</option>';
				}
				else
				{
					$row["site_form"].='<option value="'.$key.'">'.$value.'</option>';
				}
			}
		}
		else
		{	
			$row["err_det"]='<p class="red">新規作成中にエラーが発生しました。再度作成しなおしてください。</p>';
			$row["movie_title"]="";
			$row["movie_url"]="";
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