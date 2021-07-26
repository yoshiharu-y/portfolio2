<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//アーティスト画像保存フォルダがある場所
$ar_tmpdir  = "../../artist/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//htmlタグエスケープ
foreach( $_GET as $key => $value )
{
	$_GET[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
$sql = "SELECT 
artist.artist_name,artist.artist_thum,artist.ar_id,user_comment.comment,user_comment.reg_date 
FROM user INNER JOIN (user_comment INNER JOIN artist ON user_comment.ar_id=artist.ar_id) ON user_comment.user_id=user.user_id WHERE user_comment.user_id=".$_SESSION["mms_user"]." 
AND artist.del_flag= 0 
ORDER BY user_comment.reg_date desc limit 0,5";
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../");
}
else
{
	$message_num=1;
	while($sql_data = $db->FetchRow($result_id))
	{
		if($message_num==$_GET["message"])
		{
			foreach($sql_data as $key => $value)
			{
				$row[$key]=$value;
			}
		}
		$message_num++;
	}
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_thum"]=$ar_tmpdir."no_image.jpg";
	}
	else
	{
		$row["artist_thum"]=$ar_tmpdir."a".$row["ar_id"]."/"."f_".$row["artist_thum"];
	}
}


//ユーザーデータ nameのみ抽出
$user_sql = "SELECT user_id, user_name, user_thum FROM user where user_id =".$_SESSION["mms_user"];
$user_result_id = $db->ExecuteSQL($user_sql);
if(mysql_num_rows($user_result_id)==0)
{
	header( "Location: ../");
}
else
{
	$user_row = $db->FetchRow($user_result_id);
	
	if($user_row["user_thum"]=="no_thum.jpg")
	{
		$user_row["user_thum"]="../images/no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."../images/e".$user_row["user_id"]."/";
		$user_row["user_thum"]=$tmpdir."b_".$user_row["user_thum"];
	}
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

//定義したタイプ[data_s:]になっている所を変換
foreach( $user_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>