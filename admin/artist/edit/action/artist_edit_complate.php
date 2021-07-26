<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../../artist/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//データの更新は$_SESSION["artist_edit"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["artist_edit"]に入れている。

if(!empty($_POST["comp_check"])&&!empty($_POST["artist_edit_comp"]))
{
	$sql = "SELECT * FROM artist where del_flag = 0 and seq_num='".$_SESSION["artist_edit"]["seq_num"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
		//sqlアップデート
		//detail,newsの文字が改行されるようにする
		$_SESSION["artist_edit"]["detail"]=nl2br($_SESSION["artist_edit"]["detail"]);
		$_SESSION["artist_edit"]["news"]=nl2br($_SESSION["artist_edit"]["news"]);
		//ｱｯﾌﾟﾛｰﾄﾞ画像があるか
		if(!empty($_SESSION["file_temp"]["url"]))
		{
			//文字列_seqnum.jpg
			$thum_name=explode("_",$_SESSION["file_temp"]["name"]);
			$thum_type=explode(".",$thum_name[1]);
			$_SESSION["artist_edit"]["artist_new_thum"]=$thum_name[0].".".$thum_type[1];
		}
		else
		{
			$_SESSION["artist_edit"]["artist_new_thum"]=$_SESSION["artist_edit"]["artist_thum"];
		}
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["artist_edit"]);
		//sql文作成
		$sql = "UPDATE artist SET
					artist_name = ?,
					artist_thum = ?,
					sex = ?,
					age = ?,
					birthday = ?,
					mail = ?,
					vote_point = ?,
					detail = ?,
					news = ?
					WHERE seq_num = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$artist_name,
				$artist_new_thum,
				$sex,
				$age,
				$birthday,
				$mail,
				$vote_point,
				$detail,
				$news,
				$seq_num
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
		if(!$result_id)
		{
			$row["err_det"]='<p class="red">更新に失敗しました。再度編集お願いします。</p>';
			exit();
		}
		else
		{
			//画像が更新されたかどうか
			if(!empty($_SESSION["file_temp"]["url"]))
			{
				//画像フォルダがない場合作成
				if(!is_dir($tmpdir."a".$row["ar_id"]))
				{
					umask(0);
					mkdir($tmpdir."a".$row["ar_id"], 0777);
				}
				//フォルダ名　本サーバー
				$artistdir=$tmpdir."a".$row["ar_id"]."/";
				//$artistdir=$tmpdir;
				//以前の画像ファイル削除
				if($_SESSION["artist_edit"]["artist_thum"]!="no_thum.jpg")
				{
					$delFileDir=$artistdir.$_SESSION["artist_edit"]["artist_thum"];
					@unlink($delFileDir);
					$m_delFileDir=$artistdir."m_".$_SESSION["artist_edit"]["artist_thum"];
					@unlink($m_delFileDir);
					$f_delFileDir=$artistdir."f_".$_SESSION["artist_edit"]["artist_thum"];
					@unlink($f_delFileDir);

				}
				$upFileDir=$artistdir.$_SESSION["artist_edit"]["artist_new_thum"];
				$m_upFileDir=$artistdir."m_".$_SESSION["artist_edit"]["artist_new_thum"];
				$f_upFileDir=$artistdir."f_".$_SESSION["artist_edit"]["artist_new_thum"];
				rename($_SESSION["file_temp"]["url"],$upFileDir);
				//画像サイズ縮小
				
				//拡張子調べてインスタンスに入れる。
				if(preg_match("/.jpg/i",$_SESSION["artist_edit"]["artist_new_thum"]) || preg_match("/.jpeg/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					$image = imagecreatefromjpeg($upFileDir);
				}
				else if(preg_match("/.gif/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					$image = imagecreatefromgif($upFileDir);
				}
				else if(preg_match("/.png/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					$image = imagecreatefrompng($upFileDir);
				}
				
				
				$width = imagesx($image); //横幅（ピクセル）
				$height = imagesy($image); //縦幅（ピクセル）
				
				
				/**携帯用＆サムネイルサイズ作成**/
				//横基準の縮小サイズ設定
				if($width>=$height)
				{
					//携帯用＆サムネ
					$m_new_width = 100;//作成する画像サイズ
					$rate = $m_new_width / $width; //圧縮比
					$m_new_height = $rate * $height;
					//PC用
					$f_new_width = 370;//作成する画像サイズ
					$rate = $f_new_width / $width; //圧縮比
					$f_new_height = $rate * $height;
					
				}
				//縦の縮小サイズ設定
				else if($width<=$height)
				{
					//携帯用＆サムネ
					$m_new_height = 100;//作成する画像サイズ
					$rate = $m_new_height / $height; //圧縮比
					$m_new_width = $rate * $width;
					//PC用
					$f_new_height = 285;//作成する画像サイズ
					$rate = $f_new_height / $height; //圧縮比
					$f_new_width = $rate * $width;
				}
				//携帯用＆サムネ
				$m_width=100;
				$m_height=100;
				$m_center_x=($m_width-$m_new_width)/2;
				$m_center_y=($m_height-$m_new_height)/2;
				$m_new_image = imagecreatetruecolor($m_width, $m_height);// 空の画像を作成する。
				imagefill($m_new_image ,0 ,0, 0xFFFFFF);//背景白
				imagecopyresampled($m_new_image,$image,$m_center_x,$m_center_y,0,0,$m_new_width,$m_new_height,$width,$height);
				//PC用
				$f_width=370;
				$f_height=285;
				$f_center_x=($f_width-$f_new_width)/2;
				$f_center_y=($f_height-$f_new_height)/2;
				$f_new_image = imagecreatetruecolor($f_width, $f_height);// 空の画像を作成する。
				imagefill($f_new_image ,0 ,0, 0xFFFFFF);//背景白
				imagecopyresampled($f_new_image,$image,$f_center_x,$f_center_y,0,0,$f_new_width,$f_new_height,$width,$height);
				
				//拡張子調べて縮小画像を保存する。
				if(preg_match("/.jpg/i",$_SESSION["artist_edit"]["artist_new_thum"]) || preg_match("/.jpeg/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					imagejpeg($m_new_image,$m_upFileDir,100);
					imagejpeg($f_new_image,$f_upFileDir,100);
				}
				else if(preg_match("/.gif/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					imagegif($m_new_image,$m_upFileDir);
					imagegif($f_new_image,$f_upFileDir);
				}
				else if(preg_match("/.png/i",$_SESSION["artist_edit"]["artist_new_thum"]))
				{
					imagepng($m_new_image,$m_upFileDir);
					imagepng($f_new_image,$f_upFileDir);
				}
				imagedestroy($m_new_image);
				imagedestroy($f_new_image);
				@unlink($_SESSION["file_temp"]["url"]);
				

				
				
				
				
			}
			$row["err_det"]='<p>更新しました。</p>';
		}
	}
}
else
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
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