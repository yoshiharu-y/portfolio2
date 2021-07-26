<?php
/*
common/config.phpを読む
*/
@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");
//画像保存フォルダがある場所
$tmpdir  = "../../../user/images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//データの更新は$_SESSION["user_edit"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["user_edit"]に入れている。
if(!empty($_POST["comp_check"])&&!empty($_POST["user_edit_comp"]))
{
	$sql = "SELECT * FROM user where del_flag = 0 and seq_num='".$_SESSION["user_edit"]["seq_num"]."'";
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
		//ｱｯﾌﾟﾛｰﾄﾞ画像があるか
		if(!empty($_SESSION["file_temp"]["url"]))
		{
			//文字列_seqnum.jpg
			$thum_name=explode("_",$_SESSION["file_temp"]["name"]);
			$thum_type=explode(".",$thum_name[1]);
			$_SESSION["user_edit"]["user_new_thum"]=$thum_name[0].".".$thum_type[1];
		}
		else
		{
			$_SESSION["user_edit"]["user_new_thum"]=$_SESSION["user_edit"]["user_thum"];
		}
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["user_edit"]);
		//sql文作成
		$sql = "UPDATE user SET
					login_id = ?,
					user_name = ?,
					name = ?,
					kana = ?,
					age = ?,
					birthday = ?,
					sex = ?,
					zip = ?,
					address1 = ?,
					address2 = ?,
					user_thum = ?,
					free_point = ?,
					pay_point = ?
					WHERE seq_num = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$login_id,
				$user_name,
				$name,
				$kana,
				$age,
				$birthday,
				$sex,
				$zip,
				$address1,
				$address2,
				$user_new_thum,
				$free_point,
				$pay_point,
				$seq_num
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
		if(!$result_id)
		{
			$row["err_det"]='<p class="red">更新に失敗しました。再度編集お願いします。</p>';
		}
		else
		{
			//画像が更新されたかどうか
			if(!empty($_SESSION["file_temp"]["url"]))
			{
				//画像フォルダがない場合作成
				if(!is_dir($tmpdir."e".$row["user_id"]))
				{
					umask(0);
					mkdir($tmpdir."e".$row["user_id"], 0777);
				}
				//フォルダ名　本サーバー
				$userdir=$tmpdir."e".$row["user_id"]."/";
				//$userdir=$tmpdir;
				//以前の画像ファイル削除
				if($_SESSION["user_edit"]["user_thum"]!="no_thum.jpg")
				{
					$delFileDir=$userdir.$_SESSION["user_edit"]["user_thum"];
					@unlink($delFileDir);
					$s_delFileDir=$userdir."s_".$_SESSION["user_edit"]["user_thum"];
					@unlink($s_delFileDir);
					$b_delFileDir=$userdir."b_".$_SESSION["user_edit"]["user_thum"];
					@unlink($b_delFileDir);
				}
				$upFileDir=$userdir.$_SESSION["user_edit"]["user_new_thum"];
				$s_upFileDir=$userdir."s_".$_SESSION["user_edit"]["user_new_thum"];
				$b_upFileDir=$userdir."b_".$_SESSION["user_edit"]["user_new_thum"];
				rename($_SESSION["file_temp"]["url"],$upFileDir);
				//画像サイズ縮小
				
				//拡張子調べてインスタンスに入れる。
				if(preg_match("/.jpg/i",$_SESSION["user_edit"]["user_new_thum"]) || preg_match("/.jpeg/i",$_SESSION["user_edit"]["user_new_thum"]))
				{
					$image = imagecreatefromjpeg($upFileDir);
				}
				else if(preg_match("/.gif/i",$_SESSION["user_edit"]["user_new_thum"]))
				{
					$image = imagecreatefromgif($upFileDir);
				}
				else if(preg_match("/.png/i",$_SESSION["user_edit"]["user_new_thum"]))
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
					$s_new_width = 100;//作成する画像サイズ
					$rate = $s_new_width / $width; //圧縮比
					$s_new_height = $rate * $height;
					//PC用
					$b_new_width = 165;//作成する画像サイズ
					$rate = $b_new_width / $width; //圧縮比
					$b_new_height = $rate * $height;
					
				}
				//縦の縮小サイズ設定
				else if($width<=$height)
				{
					//携帯用＆サムネ
					$s_new_height = 100;//作成する画像サイズ
					$rate = $s_new_height / $height; //圧縮比
					$s_new_width = $rate * $width;
					//PC用
					$b_new_height = 165;//作成する画像サイズ
					$rate = $b_new_height / $height; //圧縮比
					$b_new_width = $rate * $width;
				}
				//携帯用＆サムネ
				$s_width=100;
				$s_height=100;
				$s_center_x=($s_width-$s_new_width)/2;
				$s_center_y=($s_height-$s_new_height)/2;
				$s_new_image = imagecreatetruecolor($s_width, $s_height);// 空の画像を作成する。
				imagefill($s_new_image ,0 ,0, 0xFFFFFF);//背景白
				imagecopyresampled($s_new_image,$image,$s_center_x,$s_center_y,0,0,$s_new_width,$s_new_height,$width,$height);
				
				//PC用
				$b_width=165;
				$b_height=165;
				$b_center_x=($b_width-$b_new_width)/2;
				$b_center_y=($b_height-$b_new_height)/2;
				$b_new_image = imagecreatetruecolor($b_width, $b_height);// 空の画像を作成する。
				imagefill($b_new_image ,0 ,0, 0xFFFFFF);//背景白
				imagecopyresampled($b_new_image,$image,$b_center_x,$b_center_y,0,0,$b_new_width,$b_new_height,$width,$height);
				
				//拡張子調べて縮小画像を保存する。
				if(preg_match("/.jpg/i",$_SESSION["user_edit"]["user_new_thum"]) || preg_match("/.jpeg/i",$_SESSION["user_edit"]["user_new_thum"]))
				{
					imagejpeg($s_new_image,$s_upFileDir,100);
					imagejpeg($b_new_image,$b_upFileDir,100);
				}
				else if(preg_match("/.gif/i",$_SESSION["user_edit"]["user_new_thum"]))
				{
					imagegif($s_new_image,$s_upFileDir);
					imagegif($b_new_image,$b_upFileDir);
				}
				else if(preg_match("/.png/i",$_SESSION["user_edit"]["user_new_thum"]))
				{
					imagepng($s_new_image,$s_upFileDir);
					imagepng($b_new_image,$b_upFileDir);
				}
				imagedestroy($s_new_image);
				imagedestroy($b_new_image);
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