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

if(!empty($_POST["mms_artist_login"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}

	//sql文作成
	$sql = "select * from ar_login where ar_id = ? and password = ?";
	//POSTDATAからMySQLに反映させるデータを入れる入れ物。
	$phs = array(
			$_POST["artist_login_id"],
			md5($_POST["artist_password"])
			);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	$result_id = $db->ExecuteSQL($sql_prepare);
	if (mysql_num_rows($result_id)==0) 
	{
    	$row["login_id"]=$_POST["artist_login_id"];
		$row["password"]="";
		$row["login_err"]='<p>アーティストログインIDかパスワードが間違っています。</p>';
	}
	else
	{
		//パス設定
		$pass_array=explode("ar_login/",$_SERVER["REQUEST_URI"]);
		$pass="http://" .$_SERVER["HTTP_HOST"].$pass_array[0];
		
		//アーティストテーブルにデータがあるか検索（仮登録の場合存在しない
		$sql = "select * from artist where ar_id = ?";
		//MySQLに反映させるデータを入れる入れ物。
		$phs = array(
			$_POST["artist_login_id"]
			);
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		$result_id = $db->ExecuteSQL($sql_prepare);
		//アーティスト情報登録へ
		if(mysql_num_rows($result_id)==0)
		{
			//ログイン状態保持
			$_SESSION["mms_artist"]=sprintf("%06d",$_POST["artist_login_id"]);
			$_SESSION["mms_artist_pass"]=$_POST["artist_password"];
			header( "Location: ".$pass."artist_account/");
		}
		else
		{
			//アーティストのdel_flagが1になってないか検索
			$sql = "select * from artist where ar_id = ? and del_flag = 0";
			$phs = array(
				$_POST["artist_login_id"]
				);
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			$result_id = $db->ExecuteSQL($sql_prepare);
			if(mysql_num_rows($result_id)==0)
			{
				$row["login_id"]=$_POST["artist_login_id"];
				$row["password"]="";
				$row["login_err"]='<p>入力されたアーティストログインIDは削除されたか存在しません。</p>';
			}
			else
			{
				//ログイン状態保持
				$_SESSION["mms_artist"]=sprintf("%06d",$_POST["artist_login_id"]);
				//パス設定
				if(!empty($_SESSION["referer"]))
				{
					$pass=$_SESSION["referer"];
					unset($_SESSION["referer"]);
					header( "Location: ".$pass);
				}
				else
				{
					header( "Location: ".$pass."artist/");
				}
			}
		}
	}
	
}
else
{
	$row["artist_login_id"]="";
	$row["artist_password"]="";
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