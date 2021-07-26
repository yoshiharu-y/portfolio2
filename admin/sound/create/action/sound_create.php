<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//音楽ファイル制限
$up_count=5;

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//$_SESSION["ar_id_sound"],$_SESSION["sound_title"],$_SESSION["up_err"]はcomplate.phpで吐き出されるSESSION

if(!empty($_SESSION["ar_id_sound"]))
{
	$ar_id=$_SESSION["ar_id_sound"];
}
else
{
	$ar_id=$_POST["ar_id"];
}
//アーティストデータをDBから引っ張り出す。
$sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id='".$ar_id."'";;
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	if($ar_id=="no_up")
	{
		$row["err_det"]='<p class="red">既に音楽が'.$up_count.'件が投稿されています。音楽の投稿は'.$up_count.'件までです。</p>';
		$row["sound_title"]="";
		$row["ar_id"]="no_up";
	}
	else
	{
		$row["err_det"]='<p class="red">アーティストの情報がありません。アーティスト一覧から確認してください。</p>';
		$row["sound_title"]="";
		$row["ar_id"]="";
	}
}
else
{
	$row = $db->FetchRow($result_id);
	//アーティストデータをDBから引っ張り出す。
	$sql = "SELECT * FROM ar_sound WHERE ar_id='".$ar_id."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)>=$up_count)
	{
		$row["err_det"]='<p class="red">既に音楽が'.$up_count.'件が投稿されています。音楽の投稿は'.$up_count.'件までです。</p>';
		$row["sound_title"]="";
		$row["ar_id"]="no_up";
	}
	else
	{
		//初回時
		if(!empty($_POST["sound_create"]))
		{
			$row["sound_title"]="";
			$row["err_det"]="";
		}
		//確認チェックがない場合
		else if(!empty($_POST["sound_create_comp"]))
		{
			$row["sound_title"]=htmlspecialchars($_POST["sound_title"], ENT_QUOTES, 'UTF-8');
			$row["err_det"]='<p class="red">投稿チェックが入っていません、投稿する場合、必ずチェックを入れてください。</p>';
		}
		//アップロードファイルが違う場合
		else if(!empty($_SESSION["ar_id_sound"]))
		{
			$row["sound_title"]=$_SESSION["sound_title"];
			$row["err_det"]='<p class="red">'.$_SESSION["up_err"]."</p>";
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