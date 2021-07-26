<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");
//音楽ファイル保存場所
$tmpdir  = "../../../artist/mp3/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//音楽が規定数以上アップロードされている場合ar_idにはno_upが入っておりsound_createで警告がでる。
if($_POST["artist_no"]=="no_up")
{
	$_SESSION["ar_id_sound"]="no_up";
	header( "Location: ".$_SERVER["HTTP_REFERER"]);
	exit();
}

//データの更新は$_SESSION["sound_create"]内に入った物を使う。

if(!empty($_POST["comp_check"])&&!empty($_POST["sound_edit_comp"]) && $_SESSION["edit_start"])
{
	$row["err_det"]="";
	
	//入力文字をエスケープ
	$_POST["sound_title"]=htmlspecialchars($_POST["sound_title"], ENT_QUOTES, 'UTF-8');
	$_FILES["up_sound"]['name']=htmlspecialchars($_FILES["up_sound"]['name'], ENT_QUOTES, 'UTF-8');
	
	//データ確認
	$sql = "SELECT * FROM ar_sound 
			WHERE ar_id=".$_POST["artist_no"]." 
			AND seq_num=".$_POST["sound_no"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$_SESSION["ar_id_sound"]=$_POST["artist_no"];
		$_SESSION["sound_title"]=$_POST["sound_title"];
		$_SESSION["up_err"]='音楽データが見つかりませんでした。アーティスト音楽一覧から確認してください。';
		header( "Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}
	$row = $db->FetchRow($result_id);
	
	//タイトルが空の場合
	if($_POST["sound_title"]=="")
	{
		$_SESSION["ar_id_sound"]=$_POST["artist_no"];
		$_SESSION["sound_title"]=$_POST["sound_title"];
		$_SESSION["up_err"]='タイトルが空欄になっています。';
		header( "Location: ".$_SERVER["HTTP_REFERER"]);
		exit();
	}
	else
	{
		$row["sound_title"]=$_POST["sound_title"];
	}
	
	
	if($_FILES["up_sound"]['name']!="")
	{
		//音楽データアップロードに必要な変数設定
		$upfile=$_FILES["up_sound"]["tmp_name"];
		$upfile_name=$control->CreateRandText(20);
		$file_det=explode(".",$_FILES["up_sound"]['name']);//ファイル名と拡張子を分離
		$file_det_back=array_reverse($file_det);//配列を降順にし、0番目が拡張子になる
		$upfile_type=$file_det_back[0];
		$upfile_new=$upfile_name.".".$upfile_type;
		
		//ファイルサイズが規定値以上だった場合
		if($_FILES["up_sound"]['size']>=6000000)
		{
			$_SESSION["ar_id_sound"]=$_POST["artist_no"];
			$_SESSION["sound_title"]=$_POST["sound_title"];
			$_SESSION["up_err"]='ファイルサイズが大きすぎます。';
			header( "Location: ".$_SERVER["HTTP_REFERER"]);
			exit();
		}
		
		//mp3ではなかった場合
		if($upfile_type!="mp3")
		{
			$_SESSION["ar_id_sound"]=$_POST["artist_no"];
			$_SESSION["sound_title"]=$_POST["sound_title"];
			$_SESSION["up_err"]='アップロードできるファイルはmp3形式のみです。';
			header( "Location: ".$_SERVER["HTTP_REFERER"]);
			exit();
		}
		
		//mineタイプを調べる
		$mime_err="";
		$mime = shell_exec('file -bi '.escapeshellcmd($upfile));
		$mime = trim($mime);
		$mime = preg_replace("/ [^ ]*/", "", $mime);
		//mineタイプが異なる場合
		if(strpos($mime,"audio") === false)
		{
			$_SESSION["ar_id_sound"]=$_POST["artist_no"];
			$_SESSION["sound_title"]=$_POST["sound_title"];
			$_SESSION["up_err"]='音楽データと拡張子が一致しませんでした。音楽データの確認をお願いします。';
			header( "Location: ".$_SERVER["HTTP_REFERER"]);
			exit();
		}
		$row["sound_url"]=$upfile_new;
	}
	//sqlへinsert
	$sql = "UPDATE ar_sound SET
					sound_title = ?,
					sound_url = ?,
					renew_date = ? 
					WHERE ar_id = ? 
					AND seq_num = ? limit 1";
	//$_SESSION["sound_create"]からMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$row["sound_title"],
					$row["sound_url"],
					date("Y-m-d"),
					$_POST["artist_no"],
					$_POST["sound_no"]
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	//echo $sql_prepare;
	$result_id = $db->ExecuteSQL($sql_prepare);
	
	if(!$result_id)
	{
		$row["err_det"]='<p class="red">投稿に失敗しました。再度音楽投稿お願いします。</p>';
	}
	else
	{
		if($_FILES["up_sound"]['name']!="")
		{
			unlink($tmpdir."a".$row["ar_id"]."/".$row["sound_url"]);
			$ar_sound_dir=$tmpdir."a".$row["ar_id"];
			
			//音楽データをバイナリで取得
			$fp = fopen($upfile, "r");
			$contents = fread($fp, filesize($upfile));
			fclose($fp);
			$contents_d=chunk_split(base64_encode($contents));
			
			//バイナリデータをアーティストのsoundフォルダに保存
			$up_filepass=$ar_sound_dir."/".$upfile_new;
			$fp = fopen($up_filepass, "w");
			$tmp=base64_decode($contents_d);
			fputs($fp, $tmp);
			fclose($fp);
		}
		$row["err_det"]='<p>新規音楽投稿が完了しました。</p>';
	}
}
else
{
	header( "Location: ../");
}
//ログイン情報を残し$_SESSIONを初期化
$temp_login=$_SESSION["mms_admin_login"];
$_SESSION=array();
$_SESSION["mms_admin_login"]=$temp_login;
//var_dump($_SESSION);

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