<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//sql照合
$sql = "SELECT * FROM artist where ar_id =".$_SESSION["mms_artist"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../../");
}
else
{
	$row = $db->FetchRow($result_id);
	//画像ファイル保存先
	$userdir=$tmpdir."a".$row["ar_id"]."/";
}


//////////////////////////////////////////////////
//　 サムネイル変更&削除部分
//////////////////////////////////////////////////

foreach($_POST as $key => $value)
{
	
	//画像ファイル抽出
	if($handle = @opendir($userdir))
	{
		//イメージデータを配列に保持
		$img_list=array("");
		while (($file = readdir($handle)) !== false)
		{
			if(strpos($file,"f_")!== false)
			{
				$img_list[]=$file;
			}
		}
		//昇順にソート
		sort($img_list);
		closedir($handle);
	}
	
	
	//サムネイル変更の場合
	if(strpos($key,"image_conf")!== false)
	{
		$change_num=substr($key,-1);
		//f_を抜く
		$change_file=substr($img_list[$change_num],2);
		$sql = "UPDATE artist SET
				artist_thum = ?,
				renew_date = ?
				WHERE ar_id = ? limit 1";
		$phs = array(
				$change_file,
				date("Y-m-d"),
				$row["ar_id"]
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
	}
	
	
	//削除の場合
	if(strpos($key,"image_del")!== false)
	{
		$del_num=substr($key,-1);
		//f_を抜く
		$del_file=substr($img_list[$del_num],2);
		@unlink($userdir.$del_file);
		@unlink($userdir."m_".$del_file);
		@unlink($userdir."f_".$del_file);
		if($del_file==$row["artist_thum"])
		{
			$sql = "UPDATE artist SET
				artist_thum = ?,
				renew_date = ?
				WHERE ar_id = ? limit 1";
			$phs = array(
				"no_thum.jpg",
				date("Y-m-d"),
				$row["ar_id"]
				);
			//インジェクション対策のsqlプリペアード関数
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			//echo $sql_prepare;
			$result_id = $db->ExecuteSQL($sql_prepare);
		}
	}
	
}


//////////////////////////////////////////////////
//　 画像アップロード部分
//////////////////////////////////////////////////

if(!empty($_POST["upld"]) && $row["ar_id"]!="")
{
	
	if($_SESSION["file_temp"]["url"]!="")
	{
		//画像ファイルの先頭につけるナンバー
		$num=1;
		//ファイル名成型
		$thum_name=explode("_",$_SESSION["file_temp"]["name"]);
		$thum_type=explode(".",$thum_name[1]);
		$file_name=$thum_name[0].".".$thum_type[1];
		//画像フォルダがない場合
		if(!is_dir($tmpdir."a".$row["ar_id"]))
		{
			umask(0);
			mkdir($tmpdir."a".$row["ar_id"], 0777);
			$first_image=true;
			$num=1;
			
		}
		else
		{
			//画像ファイルの数を確認
			if($handle = @opendir($userdir))
			{
				//イメージデータを配列に保持
				$img_list=array();
				while (($file = readdir($handle)) !== false)
				{
					//echo $file,"<br>";
					if(strpos($file,"f_")!== false)
					{
						$img_list[]=$file;
					}
				}
				closedir($handle);
			}
			//昇順にソート
			sort($img_list);
			$f_img=$img_list[(count($img_list)-1)];
			$img=substr($img_list[(count($img_list)-1)],3);
			//var_dump($img_list);
			//echo strlen($f_img)."<br>";
			//echo strlen($img)."<br>";
			$num=substr($f_img,2,-(strlen($img)));
			if($num=="" || $num==0)
			{
				$num=1;
			}
			else
			{
				$num++;
			}
			
		}
		//原寸大アップロードファイル名
		$upFileDir=$tmpdir."a".$row["ar_id"]."/".$num.$file_name;
		//携帯用アップロードファイル名
		$m_upFileDir=$tmpdir."a".$row["ar_id"]."/m_".$num.$file_name;
		//PC用アップロードファイル名
		$f_upFileDir=$tmpdir."a".$row["ar_id"]."/f_".$num.$file_name;
		//tmpの画像を移動
		rename($_SESSION["file_temp"]["url"],$upFileDir);
		
		/**携帯用＆サムネイルサイズ作成**/
			
		//拡張子調べてインスタンスに入れる。
		if(preg_match("/.jpg/i",$file_name) || preg_match("/.jpeg/i",$file_name))
		{
			$image = imagecreatefromjpeg($upFileDir);
		}
		else if(preg_match("/.gif/i",$file_name))
		{
			$image = imagecreatefromgif($upFileDir);
		}
		else if(preg_match("/.png/i",$file_name))
		{
			$image = imagecreatefrompng($upFileDir);
		}
		
		
		$width = imagesx($image); //横幅（ピクセル）
		$height = imagesy($image); //縦幅（ピクセル）
		
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
		if(preg_match("/.jpg/i",$file_name) || preg_match("/.jpeg/i",$file_name))
		{
			imagejpeg($m_new_image,$m_upFileDir,100);
			imagejpeg($f_new_image,$f_upFileDir,100);
		}
		else if(preg_match("/.gif/i",$file_name))
		{
			imagegif($m_new_image,$m_upFileDir);
			imagegif($f_new_image,$f_upFileDir);
		}
		else if(preg_match("/.png/i",$file_name))
		{
			imagepng($m_new_image,$m_upFileDir);
			imagepng($f_new_image,$f_upFileDir);
		}
		imagedestroy($m_new_image);
		imagedestroy($f_new_image);
		@unlink($_SESSION["file_temp"]["url"]);
		
		//初めて画像を上げた場合sqlを更新する
		if($first_image)
		{
			$sql = "UPDATE artist SET
				artist_thum = ?,
				renew_date = ?
				WHERE ar_id = ? limit 1";
			$phs = array(
				$file_name,
				date("Y-m-d"),
				$row["ar_id"]
				);
			//インジェクション対策のsqlプリペアード関数
			$sql_prepare = $db->mysql_prepare($sql, $phs);
			//echo $sql_prepare;
			$result_id = $db->ExecuteSQL($sql_prepare);

		}
	}	
}
header( "Location: http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);

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

?>