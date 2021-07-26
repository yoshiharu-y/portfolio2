<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//ユーザーデータ抽出
$sql = "SELECT * FROM user where user_id =".$_SESSION["mms_user"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../");
}
else
{
	$row = $db->FetchRow($result_id);
	$row["user_id"]=sprintf("%06d",$row["user_id"]);
	$row["point"]=$row["free_point"]+$row["pay_point"];
	//ポイント表示
	$plain_point=$row["point"];
	$row["point"]="";
	for($i=1;$i<=strlen($plain_point);$i++)
	{
		$row["point"].="<span>".substr($plain_point,$i-1,1)."</span>";
	}
	//画像関係
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."../images/e".$row["user_id"]."/";
		$row["user_thum"]=$tmpdir."b_".$row["user_thum"];
	}
	if($row["sex"]==0)
	{
		$row["sex"]="男性";
	}
	else
	{
		$row["sex"]="女性";		
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

//htmlを表示
echo $control->GetContentData();

?>