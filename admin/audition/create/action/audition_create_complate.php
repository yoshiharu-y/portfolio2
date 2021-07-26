<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../../audition/images/";
//独自リンクタグを摘出する正規表現
$linkexp="/link:\[.*?\]\[.*?\]/s";

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//データの更新は$_SESSION["audition_create"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["audition_create"]に入れている。

if(!empty($_POST["comp_check"])&&!empty($_POST["audition_create_comp"]))
{
	$row["err_det"]="";
	//sql文作成
	//文字が改行されるようにする
	foreach($_SESSION["audition_create"] as $key => $value)
	{
		$_SESSION["audition_create"][$key]=nl2br($_SESSION["audition_create"][$key]);
		//独自リンクタグをhtmlリンクタグにする。
		if($key=="contact_url")
		{
			preg_match_all($linkexp,$_SESSION["audition_create"][$key],$link_array);
			if(count($link_array[0])>0)
			{
				foreach($link_array[0] as $value)
				{
					preg_match_all("/\[.*?\]/",$value,$link_value);
					$url=str_replace("[","",str_replace("]","",$link_value[0][0]));
					$text=str_replace("[","",str_replace("]","",$link_value[0][1]));
					$link='<a href="'.$url.'" target="_blank">'.$text.'</a>';
					$_SESSION["audition_create"][$key]=preg_replace($linkexp,$link,$_SESSION["audition_create"][$key],1);
				}
			}
		
		}
	}
	//ｱｯﾌﾟﾛｰﾄﾞ画像があるか
	if(!empty($_SESSION["file_temp"]["url"]))
	{
		//文字列_seqnum.jpg
		$thum_name=explode("_",$_SESSION["file_temp"]["name"]);
		$thum_type=explode(".",$thum_name[1]);
		$_SESSION["audition_create"]["new_banner_add"]=$thum_name[0]."_aud.".$thum_type[1];
	}
	else
	{
		$_SESSION["audition_create"]["new_banner_add"]=$_SESSION["audition_create"]["banner_add"];
	}
	
	//表示方法設定
	if($_SESSION["audition_create"]["aud_view"]=="hidden_view")
	{
		$_SESSION["audition_create"]["hidden_flag"]=1;
	}
	else
	{
		$_SESSION["audition_create"]["hidden_flag"]=0;
	}
	//オーディションの更新日時を生成
	$_SESSION["audition_create"]["renew_date"]= date("Y-m-d");
	$_SESSION["audition_create"]["reg_date"]= date("Y-m-d");
	//配列のキーを変数名とした変数を作る
	extract($_SESSION["audition_create"]);
	//sql文作成

	$sql = "INSERT INTO audition_det (
					page_title,
					head,
					name,
					banner_add,
					detail,
					qualification,
					contact_url,
					audition_treatment,
					entry_start,
					entry_end,
					start_date,
					end_date,
					hidden_flag,
					reg_date,
					renew_date) 
					VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	//$_SESSION["audition_create"]からMySQLに反映させるデータを入れる入れ物。
	$phs = array(
					$page_title,
					$head,
					$name,
					$new_banner_add,
					$detail,
					$qualification,
					$contact_url,
					$audition_treatment,
					$entry_start_date,
					$entry_end_date,
					$start_date,
					$end_date,
					$hidden_flag,
					$reg_date,
					$renew_date
					);
	//インジェクション対策のsqlプリペアード関数
	$sql_prepare = $db->mysql_prepare($sql, $phs);
	echo $sql_prepare;
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
			$upFileDir=$tmpdir.$_SESSION["audition_create"]["new_banner_add"];
			$m_upFileDir=$tmpdir."m_".$_SESSION["audition_create"]["new_banner_add"];
			rename($_SESSION["file_temp"]["url"],$upFileDir);
			
			//画像サイズ縮小
			//echo $m_upFileDir;
			
			//拡張子調べてインスタンスに入れる。
			if(preg_match("/.jpg/i",$_SESSION["audition_create"]["new_banner_add"]) || preg_match("/.jpeg/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				$image = imagecreatefromjpeg($upFileDir);
			}
			else if(preg_match("/.gif/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				$image = imagecreatefromgif($upFileDir);
			}
			else if(preg_match("/.png/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				$image = imagecreatefrompng($upFileDir);
			}
			
			
			$width = imagesx($image); //横幅（ピクセル）
			$height = imagesy($image); //縦幅（ピクセル）
					
			//横基準の縮小サイズ設定
			if($width>$height)
			{
				$new_width = 100;//作成する画像サイズ
				$rate = $new_width / $width; //圧縮比
				$new_height = $rate * $height;
			}
			//縦の縮小サイズ設定
			else if($width<$height)
			{
				$new_height = 100;//作成する画像サイズ
				$rate = $new_height / $height; //圧縮比
				$new_width = $rate * $width;
			}
			else
			{
				$new_width = 100;//作成する画像サイズ
				$rate = $new_width / $width; //圧縮比
				$new_height = $rate * $height;
			}
						
			$new_image = imagecreatetruecolor($new_width, $new_height);// 空の画像を作成する。
			imagecopyresampled($new_image,$image,0,0,0,0,$new_width,$new_height,$width,$height);
			
			//拡張子調べて縮小画像を保存する。
			if(preg_match("/.jpg/i",$_SESSION["audition_create"]["new_banner_add"]) || preg_match("/.jpeg/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				imagejpeg($new_image,$m_upFileDir,100);
			}
			else if(preg_match("/.gif/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				imagegif($new_image,$m_upFileDir);
			}
			else if(preg_match("/.png/i",$_SESSION["audition_create"]["new_banner_add"]))
			{
				imagepng($new_image,$m_upFileDir);
			}
			imagedestroy($new_image);
			@unlink($_SESSION["file_temp"]["url"]);
			
			
		}
		$row["err_det"]='<p>新規作成が完了しました。</p>';
	}
}
else
{
	$row["err_det"]='<p class="red">新規作成中にエラーが発生しました。再度作成しなおしてください。</p>';
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