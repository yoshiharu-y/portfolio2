<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../images/";

//表示するサムネイル個数
$thum_list=5;

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
$sql = "SELECT * FROM artist where ar_id =".$_SESSION["mms_artist"];
$result_id = $db->ExecuteSQL($sql);
if(mysql_num_rows($result_id)==0)
{
	header( "Location: ../../");
}
else
{
	$row = $db->FetchRow($result_id);

	//添え字無しの配列を削除
	$row_key=(count($row)/2);
	for($i=0;$i<$row_key;$i++)
	{
		unset($row[$i]);
	}
	//画像フォルダがない場合
	if(!is_dir($tmpdir."a".$row["ar_id"]))
	{
		//no_imageを指定する。
		for($i=1;$i<=$thum_list;$i++)
		{
			$row["artist_thum_".$i]='<p><img src="'.$tmpdir.'no_image.jpg" alt="サムネイルなし" /></p>';
			$row["thum_form_".$i]="";
		}
	}
	else
	{		
		//画像ファイル抽出
		$artistdir=$tmpdir."a".$row["ar_id"]."/";
		for($i=1;$i<=$thum_list;$i++)
		{
			$row["artist_thum_".$i]="";
			$row["thum_form_".$i]="";
		}
		if($handle = @opendir($artistdir))
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
			//print_r($img_list);
			closedir($handle);
			
		}
		
		//表示する処理
		
		//画像が最大までアップロードされてる場合file_up.phpで表示されるテキスト
		/*アップロード画像は3つだが、作成されている配列内の要素数は4つなので-1*/
		if($thum_list==(count($img_list)-1))
		{
			$_SESSION["file_temp"]["err"]="アップロードできるファイルは3つまでです。";
		}
		
		//サムネイル画像 form設定
		foreach($img_list as $key => $value)
		{
			$row["artist_thum_".$key]='<p><img src="'.$artistdir.$value.'" alt="サムネイル'.$key.'" /></p>';
			if(strpos($value,$row["artist_thum"])!== false)
			{
				$row["thum_form_".$key]="現在のサムネイル画像";	
			}
			else
			{
				$row["thum_form_".$key]='<input type="submit" name="image_conf_'.$key.'" value="サムネイルに設定する" />';
			}
			$row["thum_form_".$key].='<input type="submit" name="image_del_'.$key.'" value="削除" />';
		}
		
		//$fileが入っていない配列を調べてno_imageを指定する。
		for($i=1;$i<=$thum_list;$i++)
		{
			if($row["artist_thum_".$i]=="")
			{
				$row["artist_thum_".$i]='<p><img src="'.$tmpdir.'no_image.jpg" alt="サムネイルなし" /></p>';
			}
		}
	}
	
		//サムネイル画像関係
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_thum"]=$tmpdir."no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."a".$row["ar_id"]."/";
		$row["artist_thum"]=$tmpdir."f_".$row["artist_thum"];
		
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

//htmlを表示
echo $control->GetContentData();

?>