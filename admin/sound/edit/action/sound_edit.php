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

if(!empty($_SESSION["ar_id_sound"]))
{
	$ar_id=$_SESSION["ar_id_sound"];
}
else
{
	$ar_id=$_POST["artist_no"];
}

//アーティストデータをDBから引っ張り出す。
$sql = "SELECT * FROM artist WHERE del_flag = 0 AND ar_id=".$ar_id;
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	$row["err_det"]='<p class="red">アーティストの情報がありません。アーティスト一覧から確認してください。</p>';
	$row["sound_title"]="";
	$row["ar_id"]="";
	$row["seq_num"]="";
}
else
{
	//アーティスト音楽データをDBから引っ張り出す。
	$sql = "SELECT * FROM ar_sound 
			WHERE ar_id=".$ar_id." 
			AND seq_num=".$_POST["sound_no"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["err_det"]='<p class="red">音楽データが見つかりませんでした。アーティスト音楽一覧から確認してください。</p>';
		$row["sound_title"]="";
		$row["ar_id"]="";
		$row["seq_num"]="";
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["artist_no"]=$row["ar_no"];
		//初回時
		if(!empty($_POST["sound_edit"]))
		{
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