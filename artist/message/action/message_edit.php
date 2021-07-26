<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//ユーザーの画像保存フォルダがある場所
$usertmpdir  = "../../user/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


if(!empty($_POST["res_message"]) || !empty($_POST["mms_message_return"]))
{
	if(!empty($_POST["mms_message_return"]))
	{
		$_POST["res_message"]=$_POST["uid"];
	}
	//ユーザーデータの存在を確認
	$sql = "SELECT * FROM user WHERE del_flag= 0 AND user_id =".$_POST["res_message"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="coution">ユーザーが存在していない可能性があります。</p>';
		$row["user_thum"]=$usertmpdir."s_no_image.jpg";
		$row["user_name"]="";
		$row["message"]="";
		$row["hidden"]='<input type="hidden" name="res_message" value="'.$_POST["res_message"].'" />';
		
	}
	else
	{
		//アーティストデータチェック
		$sql = "SELECT * FROM artist where del_flag= 0 and ar_id =".$_SESSION["mms_artist"];
		$check_id = $db->ExecuteSQL($sql);
		if(mysql_num_rows($check_id)==0 )
		{
			header( "Location: ../../login/");
		}
		
		
		
		$row = $db->FetchRow($result_id);
		//添え字が数字の配列を削除
		$row_key=(count($row)/2);
		for($i=0;$i<$row_key;$i++)
		{
			unset($row[$i]);
		}
		
		$row["err_det"]="";
		$row["hidden"]='<input type="hidden" name="uid" value="'.$row["user_id"].'" />';
		$row["user_name"]=$row["user_name"]."さん";
		
		if($row["user_thum"]=="no_thum.jpg")
		{
			$row["user_thum"]=$usertmpdir."no_image.jpg";
		}
		else
		{
			$row["user_thum"]=$usertmpdir."e".$row["user_id"]."/"."s_".$row["user_thum"];
		}
		if(!empty($_POST["mms_message_return"]))
		{
			//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
			$row["message"]=str_replace("<br />", $_POST["escape_text"], $_POST["message"]);
			$row["message"]=preg_replace("/\t/","", $row["message"]);
		}
		else
		{
			$row["message"]="";
		}

	
		
	}
}
else
{
	header( "Location: ../../login/");
}

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
		$tmpdir=$tmpdir."../images/a".$artist_row["ar_id"]."/";
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