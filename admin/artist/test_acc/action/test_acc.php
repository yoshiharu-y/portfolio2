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


//仮id　一番左の数字は0にならない6桁
$prov_id = mt_rand(1,9);
for($i = 0; $i < 5; ++$i)
{
		$prov_id .= mt_rand(0,9);	
}
//idの重複確認
$sql = "SELECT * FROM ar_login where ar_id =".$prov_id;
$result_id = $db->ExecuteSQL($sql);
while(mysql_num_rows($result_id)!=0)
{
	$prov_id = mt_rand(1,9);
	for($i = 0; $i < 5; ++$i)
	{
			$prov_id .= mt_rand(0,9);	
	}
	$sql = "SELECT * FROM ar_login where ar_id =".$prov_id;
	$result_id = $db->ExecuteSQL($sql);

}
$row["ar_id"]=$prov_id;
$row["password"]=$control->CreateRandText(15);
$row["md5_password"]=md5($row["password"]);

//sql文作成
$sql = "INSERT INTO ar_login (
					ar_id,
					password
					) 
					VALUES (?,?);
					";
//MySQLに反映させるデータをまとめる。
$phs = array(
				$row["ar_id"],
				$row["md5_password"]
			);
//インジェクション対策のsqlプリペアード関数
$sql_prepare = $db->mysql_prepare($sql, $phs);
$db->ExecuteSQL($sql_prepare) or die("エラーが発生しました。");

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