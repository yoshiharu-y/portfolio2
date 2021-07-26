<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");
//htmlに表示させるプログラム

//アーティスト画像保存フォルダがある場所
$tmpdir  = "../artist/images/";

//投票ポイント
$point_list=array(2,5,10);
//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


$_GET["view"]= htmlspecialchars($_GET["view"], ENT_QUOTES, 'UTF-8');

$get_data = @explode("ADP",$_GET["view"]);

$sql = "SELECT free_point,pay_point FROM user where user_id =".$_SESSION["mms_user"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	//ログインページへ
	$_SESSION["referer"]="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$pass=explode("/",$_SERVER["REQUEST_URI"]);
	header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/login/");
}
else
{
	//ポイント情報
	$point = $db->FetchRow($result_id);
	
	//ｵｰﾃﾞｨｼｮﾝ、ｱｰﾃｨｽﾄ、ｵｰﾃﾞｨｼｮﾝとｱｰﾃｨｽﾄを連結するテーブルsql
	$sql = "SELECT * FROM audition_det 
	INNER JOIN (artist INNER JOIN rel_artist_audition ON artist.ar_id=rel_artist_audition.ar_id) 
	ON audition_det.aud_seq_num=rel_artist_audition.aud_seq_num 
	WHERE artist.del_flag = 0 
	AND rel_artist_audition.del_flag = 0
	AND rel_artist_audition.aud_seq_num = ".$get_data[1]." 
	AND artist.ar_id =".$get_data[0];
	
	$result_id = $db->ExecuteSQL($sql);
	
	if(mysql_num_rows($result_id)==0)
	{
		//エラーページ
		header( "Location: error.html");
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["form_select"]="";
		$row["user_vote_point"]="";
		//画像設定
		if($row["artist_thum"]=="no_thum.jpg")
		{
			$row["artist_thum"]=$tmpdir."no_image.jpg";	
		}
		else
		{
			//フォルダ名　本サーバー
			$artistdir=$tmpdir."a".$row["ar_id"]."/";
			$row["artist_thum"]=$artistdir."f_".$row["artist_thum"];
		}
		//ポイント残高
		$row["user_point"]=$point["free_point"]+$point["pay_point"];
		//投票ポイント設定
		
		if(!empty($_POST["mms_aud_point"]))
		{
			if($_SESSION["point_ticket"]!="")
			{
				$row["user_vote_point"]=$_POST["point"];
				
				//ボタン設定
				$row["cancel"]='<input type="submit" name="mms_aud_point_cancel" value="投票画面に戻る" class="cancel_submit" />';
				
				$row["comp"]='<input type="submit" name="mms_aud_point_comp" value="投票する" class="send_submit" />';
				
				
				if($row["user_point"]!=0 && intval($row["user_point"])>=intval($_POST["point"]))
				{
					//所持のデータを複雑化（数値の間に文字を挿入
					for($i=0;$i<strlen($row["user_point"]);$i++)
					{
						$complexity.=$control->CreateRandText(1);
						$complexity.=substr($row["user_point"],$i,1);
					}
					$row["check_txt"]="下記のアーティストに".$_POST["point"]."ポイント投票します。<br />良ければ下部にある投票ボタンを押してください。";
					$row["form_txt"]='
					<input type="hidden" name="mms_user_point" value="'.$complexity.'" />
					<input type="hidden" name="mms_vote_point" value="'.$_POST["point"].'" />';
					$row["form_txt"].=$row["cancel"];
					$row["form_txt"].=$row["comp"];
				}
				else
				{
					$row["check_txt"]="ポイント残高が不足しています。";
					$row["form_txt"]='
					<input type="hidden" name="mms_user_point" value="'.$complexity.'" />
					<input type="hidden" name="mms_vote_point" value="'.$_POST["point"].'" />';
					$row["form_txt"].=$row["cancel"];
				}
			}
			else
			{
				$row["check_txt"]='<span style="color: red;">投票処理エラーが発生しました。再度投票画面からやり直しをお願いします。</span>';
				$row["form_txt"]="";
			}
		}
		else
		{
			$_SESSION["point_ticket"]=$control->CreateRandText(10);
			$row["check_txt"]="投票するアーティスト";
			$row["form_select"]='<select name="point">';
			foreach($point_list as $value)
			{
				$row["form_select"].='<option value="'.$value.'">'.$value.'</option>';
			}
			
			$row["form_select"].='</select>';
			$row["form_txt"]='<input type="submit" name="mms_aud_point" value="投票確認へ" class="submit" />';
		}
	}
}

//ユーザーデータ nameとか画像とかのみ抽出
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

//定義したタイプ[data_s:]になっている所を変換
foreach( $user_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();


?>