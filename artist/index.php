<?php
//////////////////////////////////////////////////
//
//　　　artist内のindex.php
//
//////////////////////////////////////////////////

/*--
静的部、動的部の読み込み
--*/
session_name("MMSWEBSITE");
session_start();

if(!empty($_GET["view"]))
{
	if(!empty($_SESSION["mms_user"]))
	{
		if(!empty($_POST["artist_favorite"]))
		{
			//お気に入り画面指定
			define("FILENAME","favorite");
		}
		else if(!empty($_POST["artist_message"]))
		{
			//メッセージ（コメント）画面指定
			$_SESSION["send_message"]= htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');
			header( "Location: message/?message=".$_GET["view"]);
		}
		else
		{
			//通常時画面指定
			define("FILENAME","view");
		}
		
	}
	else if(!empty($_POST["artist_favorite"]) || !empty($_POST["artist_message"]))
	{
		$_SESSION["referer"]="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$pass=explode("/",$_SERVER["REQUEST_URI"]);
		header( "Location: http://" .$_SERVER["HTTP_HOST"]."/".$pass[1]."/login/");
	}
	else
	{
		//通常時画面指定
		define("FILENAME","view");
	}
}
else if(!empty($_SESSION["mms_artist"]))
{
	//アカウント名編集ページ
	if(!empty($_POST["account_edit"]))
	{
		header( "Location: account/");
		exit();
	}
	//パスワード編集ページ
	else if(!empty($_POST["mms_artist_pass_edit"]))
	{
		header( "Location: password/");
	}
	//プロフィール編集ページ
	else if(!empty($_POST["mms_artist_prof_edit"]))
	{
		header( "Location: profile/");
	}
	//インフォメーション編集ページ
	else if(!empty($_POST["mms_artist_news_edit"]))
	{
		header( "Location: news/");
	}
	//画像編集ページ
	else if(!empty($_POST["mms_artist_img_edit"]))
	{
		header( "Location: upload/");
	}
	//動画編集ページ
	else if(!empty($_POST["mms_artist_movie_edit"]))
	{
		header( "Location: movie/");
	}
	//音楽編集ページ
	else if(!empty($_POST["mms_artist_sound_edit"]))
	{
		header( "Location: sound/");
	}
	//参加可能オーディションページ
	else if(!empty($_POST["mms_artist_app_audition"]))
	{
		for($i=0;$i<6;$i++)
		{
			$url_rand.=rand(1,9);
		}
		$_SESSION["aud_app_no"]=$url_rand;
		header( "Location: ../audition/?arview=".$url_rand);
	}
	else
	{
		//マイページ画面指定
		define("FILENAME","mypage");
	}
}
else
{
	//GETが無い場合のデフォルト
	header( "Location: error.html");
}

//表示htmlファイル指定
$filepass=dirname(__FILE__)."/template/".FILENAME.".html";

//表示htmlファイルをphp変数に格納
$html=@fopen($filepass,"r");
$content = fread($html,filesize($filepass));

//PHPファイル指定
@require_once(dirname(__FILE__)."/action/".FILENAME.".php");

?>