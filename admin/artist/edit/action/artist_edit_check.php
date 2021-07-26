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
//編集画面から
if(!empty($_POST["artist_edit_check"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	$sql = "SELECT * FROM artist where del_flag = 0 and seq_num='".$_POST["artist_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
		exit();
	}
	else
	{
		$row = $db->FetchRow($result_id);
		//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
		$row["detail"]=str_replace("<br />", $_POST["escape_text"], $row["detail"]);
		$row["detail"]=preg_replace("/\t/","", $row["detail"]);
		$row["news"]=str_replace("<br />", $_POST["escape_text"], $row["news"]);
		$row["news"]=preg_replace("/\t/","", $row["news"]);
		$row["err_det"]="";
	}
}
//確認チェックが入っていない場合
else if(!empty($_POST["artist_edit_comp"]))
{
	foreach( $_SESSION["artist_edit"] as $key => $value )
	{
		$row[$key]=$value;
		//POST配列で内容確認しているのでPOSTに入れる。
		$_POST[$key]=$value;
	}
	$row["err_det"]='<p class="red">編集を完了させるチェックが入っていません。</p>';
}
else
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
	exit();	
}
//メールアドレスチェック
if(!$control->CheckMatch($_POST["mail"],"email"))
{
	$err.='<br />メールアドレスの形式が違います。';
}

//ポイントチェック
if(!$control->CheckMatch($_POST["vote_point"],"number"))
{
	$err.='<br />取得ポイントが正しく入力されていません。';
}
else if(strlen($_POST["vote_point"])!=1 && substr($_POST["vote_point"],0,1)=="0")
{
	$err.='<br />取得ポイントが正しく入力されていません。';
}
//sqlフィールドに対応する名称をセット
$fieldName=array("artist_name"=>"ニックネーム",
				 "birthday"=>"生年月日",
				 "sex"=>"性別",
				 "mail"=>"メールアドレス",
				 "vote_point"=>"課金ポイント",
				 "detail"=>"自己紹介");
//生年月日成型
$birthday=$_POST["year"]."-".$_POST["month"]."-".$_POST["day"];
//年齢計算
$age = (int) ((date('Ymd')-intval($_POST["year"].$_POST["month"].$_POST["day"]))/10000);
//変更箇所を通達するhtml生成
foreach($_POST as $key => $value)
{
	//無記入箇所はsqlデータにする。記入箇所はpostデータ。
	if($_POST[$key]!="")
	{
		if(isset($row[$key]))
		{
			if($_POST[$key]!=$row[$key])
			{
				$renew.=$fieldName[$key]."の項目<br />";
			}
		}
		$row[$key]=$_POST[$key];
	}
	//成型を今のファイルで行っているのでこの段階で生年月日の確認
	if($key=="day")
	{
		if($birthday!=$row["birthday"])
		{
			$renew.=$fieldName["birthday"]."の項目<br />";
			$row["birthday"]=$birthday;
			$row["age"]=$age;
		}
	}
	
}
//編集画面で使用する配列としてSESSIONに保存
//添え字が数字の配列を削除
$data_key=(count($row)/2);
for($i=0;$i<$data_key;$i++)
{
	unset($row[$i]);
}
$_SESSION["artist_edit"]=$row;

//確認画面でtextarea文字が改行されるようにする
$row["detail"]=nl2br($row["detail"]);
$row["news"]=nl2br($row["news"]);

//性別成型
if($row["sex"]==0)
{
	$row["sex_txt"]="男性";
}
else
{
	$row["sex_txt"]="女性";	
}
//画像が更新されたかどうか
if(!empty($_SESSION["file_temp"]["url"]))
{
	//初回時に確認画面に来た時だけ変更文章表示
	if(empty($_POST["artist_edit_comp"]))
	{
		$renew="サムネイル画像の項目<br />".$renew;
	}
	$row["artist_thum"]='<p id="up_file"><img src="'.$_SESSION["file_temp"]["url"].'" /></p>';
}
else
{
	if($row["artist_thum"]=="no_thum.jpg")
	{
		$row["artist_thum"]='<p id="up_file">サムネイルなし</p>';
	}
	else
	{
		//フォルダ名　本サーバー
		$artistdir=$tmpdir."a".$row["ar_id"]."/";
		//$artistdir=$tmpdir;
		$row["artist_thum"]='<p id="up_file"><img src="'.$artistdir.$row["artist_thum"].'" /></p>';
	}
}
//エラー箇所文章成型
if($err!="")
{
	$err='<p class="red">正しく入力されていない箇所があります。以下の通りです。<b>'.$err.'</b></p>';
}

//更新箇所通達
if($renew!="")
{
	$renew='<p><b>'.$renew.'</b>以上のデータが変更されます。</p>';
}

//エラーが無い場合の処理
if($err=="")
{
	$row["err_det"].=$renew;
	$row["comp_check"]='<p><input type="checkbox" id="send" name="comp_check" value="check" />
      <label for="send">編集を完了する場合はチェックを入れてください</label><p>';
	$row["comp_btn"]='<input type="submit" name="artist_edit_comp" value="編集を完了する" class="formBtn" />';
}
else
{
	 $row["err_det"].=$err;
	 $row["comp_check"]="";
	 $row["comp_btn"]="";
}
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