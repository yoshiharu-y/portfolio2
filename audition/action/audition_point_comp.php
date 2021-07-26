<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");
//htmlに表示させるプログラム

//アーティスト画像保存フォルダがある場所
$tmpdir  = "../artist/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


$_GET["view"]= htmlspecialchars($_GET["view"], ENT_QUOTES, 'UTF-8');

$get_data = @explode("ADP",$_GET["view"]);

$point_list=array(10,30,50,100);

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
			
		if(!empty($_SESSION["point_ticket"]))
		{
			
			
			//ユーザーの現在のポイント
			$row["user_point"]=$point["free_point"]+$point["pay_point"];
			
			//ポストされたポイントの整形
			for($i=1;$i<=strlen($_POST["mms_user_point"]);$i++)
			{
				if($i % 2 ==0)
				{
					$post_point.=substr($_POST["mms_user_point"],$i-1,1);
				}
			}
			
			//ユーザーの総ﾎﾟｲﾝﾄが投票時と同じかどうか比較しデータを更新する。
			/*echo $_SESSION["point_ticket"]."<br>";
			echo 'row["user_point"]='.$row["user_point"]."<br>";
			echo 'POST["mms_user_point"]='.$_POST["mms_user_point"]."<br>";
			echo 'POST["mms_vote_point"]='.$_POST["mms_vote_point"]."<br>";
			echo 'post_point='.$post_point."<br>";
			*/
			//投票チケットを削除
			unset($_SESSION["point_ticket"]);
			if(intval($post_point)===intval($row["user_point"]) && $row["user_point"]>=$post_point)
			{
				
				//userテーブル、artistテーブル、rel_artist_auditionテーブルと更新していく
				
				//////////////////////////////////////////////////
				//　userテーブル更新
				//////////////////////////////////////////////////
				
				//ﾕｰｻﾞｰのfree_pointがmms_vote_pointより多い場合はfree_pointを更新対象にする。
				if(intval($point["free_point"])>=intval($_POST["mms_vote_point"]))
				{
					//sql文作成
					$sql = "UPDATE user SET free_point = ? WHERE user_id = ? limit 1";
					$phs = array(($point["free_point"]-$_POST["mms_vote_point"]),$_SESSION["mms_user"]);
				}
				else
				{
					$user_vote=$_POST["mms_vote_point"]-$point["free_point"];
					//sql文作成
					$sql = "UPDATE user SET pay_point = ? WHERE user_id = ? limit 1";
					$phs = array(($point["pay_point"]-$user_vote),$_SESSION["mms_user"]);
				}
				
				//インジェクション対策のsqlプリペアード関数
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				if(!$db->ExecuteSQL($sql_prepare))
				{
					$up_err=true;
					$err_num.=" e101";
				}
				
				//////////////////////////////////////////////////
				//　artistテーブル更新
				//////////////////////////////////////////////////
				
				/*$sql = "UPDATE artist SET vote_point = ? WHERE ar_id = ? limit 1";
				$phs = array(($row["vote_point"]+$_POST["mms_vote_point"]),$get_data[0]);
				//インジェクション対策のsqlプリペアード関数
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				if(!$db->ExecuteSQL($sql_prepare))
				{
					$up_err=true;
					$err_num.=" e102";
				}*/
				
				//////////////////////////////////////////////////
				//　rel_artist_auditionテーブル更新
				//////////////////////////////////////////////////
				
				$sql = "UPDATE rel_artist_audition SET ar_point = ? WHERE ar_id = ? AND aud_seq_num = ? limit 1";
				$phs = array(($row["ar_point"]+$_POST["mms_vote_point"]),$get_data[0],$get_data[1]);
				//インジェクション対策のsqlプリペアード関数
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				if(!$db->ExecuteSQL($sql_prepare))
				{
					$up_err=true;
					$err_num.=" e103";
				}
				
				//////////////////////////////////////////////////
				//　エラーが有る場合の処理
				//////////////////////////////////////////////////
				if($up_err)
				{
					//userテーブル
					$sql = "UPDATE user SET free_point = ?,pay_point = ? WHERE user_id = ? limit 1";
					$phs = array($point["free_point"],$point["pay_point"],$_SESSION["mms_user"]);
					$sql_prepare = $db->mysql_prepare($sql, $phs);
					$db->ExecuteSQL($sql_prepare);
					
					/*//artistテーブル
					$sql = "UPDATE artist SET vote_point = ? WHERE ar_id = ? limit 1";
					$phs = array($row["vote_point"],$get_data[0]);
					$sql_prepare = $db->mysql_prepare($sql, $phs);
					$db->ExecuteSQL($sql_prepare);*/
					
					//rel_artist_auditionテーブル
					$sql = "UPDATE rel_artist_audition SET ar_point = ? WHERE ar_id = ? AND aud_seq_num = ? limit 1";
					$phs = array($row["ar_point"],$get_data[0],$get_data[1]);
					$sql_prepare = $db->mysql_prepare($sql, $phs);
					$db->ExecuteSQL($sql_prepare);
					
					$row["comp_txt"]=
					'<span style="color: red;">投票処理エラーが発生しました。再度投票画面からやり直しをお願いします。投票エラーコード：'.$err_num.'</span>';
					
				}
				else
				{
					//vote_listテーブル
					$sql = "INSERT INTO vote_list (user_id,ar_id,user_vote,vote_date) VALUES (?,?,?,?)";
					$phs = array($_SESSION["mms_user"],$get_data[0],$_POST["mms_vote_point"],date('Y-m-d H:i:s'));
					$sql_prepare = $db->mysql_prepare($sql, $phs);
					$db->ExecuteSQL($sql_prepare);
					
					$row["comp_txt"]="投票が完了しました。";
					$row["ar_point"]=$row["ar_point"]+$_POST["mms_vote_point"];
					$row["user_point"]=$point["free_point"]+$point["pay_point"]-$_POST["mms_vote_point"];
				}
			}
			else
			{
				$err=true;
				$err_num=" e104";
			}
		}
		else
		{
			$err=true;
			$err_num=" e105";
		}
	}
	if($err)
	{
		$row["user_point"]=$point["free_point"]+$point["pay_point"];
		$row["comp_txt"]='<span style="color: red;">投票処理エラーが発生しました。再度投票画面からやり直しをお願いします。投票エラーコード：'.$err_num.'</span>';
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