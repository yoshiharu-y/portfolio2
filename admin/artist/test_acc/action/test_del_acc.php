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

if(!empty($_POST["del_check"]))
{
	//データをDBから引っ張り出す。
	$sql = "SELECT * FROM ar_login";
	$result_id = $db->ExecuteSQL($sql);
	while($check_sql = $db->FetchRow($result_id))
	{
		$sql = "SELECT ar_id FROM artist where ar_id=".$check_sql["ar_id"];
		$check_id = $db->ExecuteSQL($sql);
		if(mysql_num_rows($check_id)==0)
		{
			$del_sql = "DELETE FROM ar_login WHERE ar_id=".$check_sql["ar_id"];
			$db->ExecuteSQL($del_sql);
		}
		
	}
	
	
	$row["err_det"]='<p>削除が完了しました。</p>';

}
else
{
	$row["err_det"]='<p class="red">削除チェックが入っていません。</p>';
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