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
$sql = "SELECT * FROM user where user_id =".$_SESSION["mms_user"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../../");
}
else
{
	$row = $db->FetchRow($result_id);
	//画像ファイル保存先
	$userdir=$tmpdir."e".$row["user_id"]."/";
}


//////////////////////////////////////////////////
//　 サムネイル変更&削除部分
//////////////////////////////////////////////////

foreach($_POST as $key => $value)
{
	//表示しているサムネイル個数
	$thum_list=3;
	
	//画像ファイル抽出
	if($handle = @opendir($userdir))
	{
		//イメージデータを配列に保持
		$img_list=array("");
		while (($file = readdir($handle)) !== false)
		{
			if(strpos($file,"b_")!== false)
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
		//b_を抜く
		$change_file=substr($img_list[$change_num],2);
		$sql = "UPDATE user SET
				user_thum = ?,
				renew_date = ?
				WHERE user_id = ? limit 1";
		$phs = array(
				$change_file,
				date("Y-m-d"),
				$row["user_id"]
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
		//b_を抜く
		$del_file=substr($img_list[$del_num],2);
		@unlink($userdir.$del_file);
		@unlink($userdir."s_".$del_file);
		@unlink($userdir."b_".$del_file);
		if($del_file==$row["user_thum"])
		{
			$sql = "UPDATE user SET
				user_thum = ?,
				renew_date = ?
				WHERE user_id = ? limit 1";
			$phs = array(
				"no_thum.jpg",
				date("Y-m-d"),
				$row["user_id"]
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

if(!empty($_POST["upld"]) && $row["user_id"]!="")
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
		if(!is_dir($tmpdir."e".$row["user_id"]))
		{
			umask(0);
			mkdir($tmpdir."e".$row["user_id"], 0777);
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
					if(strpos($file,"b_")!== false)
					{
						$img_list[]=$file;
					}
				}
				closedir($handle);
			}
			//昇順にソート
			sort($img_list);
			$b_img=$img_list[(count($img_list)-1)];
			$img=substr($img_list[(count($img_list)-1)],3);
			echo strlen($b_img)."<br>";
			echo strlen($img)."<br>";
			$num=substr($b_img,2,-(strlen($img)));
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
		$upFileDir=$tmpdir."e".$row["user_id"]."/".$num.$file_name;
		//携帯用アップロードファイル名
		$s_upFileDir=$tmpdir."e".$row["user_id"]."/s_".$num.$file_name;
		//PC用アップロードファイル名
		$b_upFileDir=$tmpdir."e".$row["user_id"]."/b_".$num.$file_name;
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
		if(preg_match("/.jpg/i",$file_name) || preg_match("/.jpeg/i",$file_name))
		{
			imagejpeg($s_new_image,$s_upFileDir,100);
			imagejpeg($b_new_image,$b_upFileDir,100);
		}
		else if(preg_match("/.gif/i",$file_name))
		{
			imagegif($s_new_image,$s_upFileDir);
			imagegif($b_new_image,$b_upFileDir);
		}
		else if(preg_match("/.png/i",$file_name))
		{
			imagepng($s_new_image,$s_upFileDir);
			imagepng($b_new_image,$b_upFileDir);
		}
		imagedestroy($s_new_image);
		imagedestroy($b_new_image);
		@unlink($_SESSION["file_temp"]["url"]);
		
		//初めて画像を上げた場合sqlを更新する
		if($first_image)
		{
			$sql = "UPDATE user SET
				user_thum = ?,
				renew_date = ?
				WHERE user_id = ? limit 1";
			$phs = array(
				$file_name,
				date("Y-m-d"),
				$row["user_id"]
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