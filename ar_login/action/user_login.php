<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

if(!empty($_POST["mms_user_login"]))
{
	
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}

	//ユーザーテーブルにデータがあるか検索
	$sql = "select * from user where login_id = ? and password = ?";
	//MySQLに反映させるデータを入れる入れ物。
	$phs = array(
			$_POST["user_login_id"],
			md5($_POST["user_password"])
			);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	$result_id = $db->ExecuteSQL($sql_prepare);
	if (mysql_num_rows($result_id)==0) 
	{
    	$row["login_id"]=$_POST["user_login_id"];
		$row["password"]="";
		$row["login_err"]='<p>ログインIDかパスワードが間違っています。</p>';
	}else
	{
		//ユーザーのdel_flagが1になってないか検索
		$sql = "select * from user where login_id = ? and del_flag = 0";
		$phs = array(
			$_POST["user_login_id"]
			);
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$result_id = $db->ExecuteSQL($sql_prepare);
		if(mysql_num_rows($result_id)==0)
		{
			$row["login_id"]=$_POST["user_login_id"];
			$row["password"]="";
			$row["login_err"]='<p>入力されたログインIDは削除されたか存在しません。</p>';
		}
		else
		{	
			//ユーザーID保存
			$user=$db->FetchRow($result_id);
			$_SESSION["mms_user"]=sprintf("%06d",$user["user_id"]);
			
			//10point付加
			
			$login_date=explode(" ",$user["login_date"]);
			if($login_date[0]!=date("Y-m-d"))
			{
				//10point
				$sql = "UPDATE user SET
									free_point = ?
									WHERE user_id = ? limit 1";
						//POSTDATAからMySQLに反映させるデータを入れる入れ物。
				
				$phs = array(
								10,
								$_SESSION["mms_user"]
								);
				//インジェクション対策のsqlプリペアード関数
				$sql_prepare = $db->mysql_prepare($sql, $phs);
				$db->ExecuteSQL($sql_prepare);
				
			}
			
			//ログイン日記録
			$sql = "UPDATE user SET
								login_date = ?
								WHERE user_id = ? limit 1";
					//POSTDATAからMySQLに反映させるデータを入れる入れ物。
			
			$phs = array(
							date("Y-m-d H:i:s"),
							$_SESSION["mms_user"]
							);
			//インジェクション対策のsqlプリペアード関数
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			$db->ExecuteSQL($sql_prepare);
	
			
			//パス設定
			if(!empty($_SESSION["referer"]))
			{
				$pass=$_SESSION["referer"];
				unset($_SESSION["referer"]);
				header( "Location: ".$pass);
			}
			else
			{
				$pass_array=explode("login/",$_SERVER["REQUEST_URI"]);
				$pass=$pass_array[0]."user/";
				header( "Location: http://" .$_SERVER["HTTP_HOST"].$pass);
			}
		}
	}
	
}
else
{
	$row["user_login_id"]="";
	$row["user_password"]="";
	$row["login_err"]="";
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