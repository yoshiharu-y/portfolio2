<?php

//このファイルはaudition_sub_up.jsで読み込んでいます・


@require_once(dirname(__FILE__)."/../../../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　ユーザー画像ファイルアップロード設定
//
//////////////////////////////////////////////////

//ユーザー情報照合
session_name("MMSWEBSITE");
session_start();

//インスタンス生成、引数には$_FILES
$handle = new Upload($_FILES["up_new"]);

//一時アップロードディレクトリ指定
$temp_dir = "images_temp/tmp/";
//一時アップロードファイルが残っていたら削除
if ($dh = @opendir("../".$temp_dir))
{
   
   while (($file = readdir($dh)) !== false) 
   {
		if(is_file("../".$temp_dir."/".$file))
		{
			
			$file_array=explode(".",$file);
			$checkStr="_".$_SESSION["file_temp"]["ar_id"];
			$nameCheck=strpos($file_array[0],$checkStr);			
			if($nameCheck!==false)
			{
				unlink("../".$temp_dir."/".$file);
			}
		}
   }
   closedir($dh);
}

//ランダムファイル名作成
$filename = $control->CreateRandText(20)."_".$_SESSION["file_temp"]["ar_id"];

//アップロードされるminetypeを抽出
$cutName = explode(".",$_FILES["up_new"]["name"]);
$fileType = $cutName[(count($cutName)-1)];

//画像アップロード実行
$handle->allowed = array("image/*");
$handle->file_auto_rename = false;
$handle->file_src_name_body = $filename;
$handle->Process("../".$temp_dir);


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


//セッションにファイルパスを保存
$fileType=strtolower($fileType);
$type=array("bmp","jpg","jpeg","gif","png");
foreach($type as $value)
{
	if($fileType==$value)
	{
		$fileSet=true;
	}
}
if($fileSet)
{
	if($_SESSION["file_temp"]["err"]=="")
	{
		$_SESSION["file_temp"]["url"] = $temp_dir.$filename.".".$fileType;
		$_SESSION["file_temp"]["name"] = $filename.".".$fileType;
		$img_tag='<p id="up_file">アップロードの準備が整いました。</p>';
	}
	else
	{
		$img_tag='<p id="up_file"><span class="coution">'.$_SESSION["file_temp"]["err"].'</span></p>';
	}
}
else
{
	$img_tag='<p id="up_file"><span class="coution">アップロードできないファイル形式です。</span></p>';
}
//htmlを表示
echo $img_tag;

?>