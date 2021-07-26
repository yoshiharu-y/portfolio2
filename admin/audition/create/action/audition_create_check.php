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
//編集画面から
if(!empty($_POST["audition_create_check"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	$row["err_det"]="";
	
}
//確認チェックが入っていない場合
else if(!empty($_POST["audition_create_comp"]))
{
	foreach( $_SESSION["audition_create"] as $key => $value )
	{
		$_POST[$key]=$value;
	}
	$row["err_det"]='<p class="red">編集を完了させるチェックが入っていません。</p>';
}
else
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
	exit();	
}

//開始日付、終了日付成型
$_POST["entry_start_date"]=$_POST["entry_start_year"]."-".$_POST["entry_start_month"]."-".$_POST["entry_start_day"]." ".$_POST["entry_start_hour"].":".$_POST["entry_start_mins"].":00";
$_POST["entry_end_date"]=$_POST["entry_end_year"]."-".$_POST["entry_end_month"]."-".$_POST["entry_end_day"]." ".$_POST["entry_end_hour"].":".$_POST["entry_end_mins"].":00";

$_POST["start_date"]=$_POST["start_year"]."-".$_POST["start_month"]."-".$_POST["start_day"]." ".$_POST["start_hour"].":".$_POST["start_mins"].":00";
$_POST["end_date"]=$_POST["end_year"]."-".$_POST["end_month"]."-".$_POST["end_day"]." ".$_POST["end_hour"].":".$_POST["end_mins"].":00";

//募集開始日付と募集終了日付を比較
if(strtotime($_POST["entry_start_date"])>strtotime($_POST["entry_end_date"]))
{
	$err.='<br />募集開始日付と募集終了日付の設定に不備があります。';
}


//開催日付と終了日付を比較
if(strtotime($_POST["start_date"])>strtotime($_POST["end_date"]))
{
	$err.='<br />開催日付と終了日付の設定に不備があります。';
}

//募集終了日付と終了日付を比較
if(strtotime($_POST["entry_end_date"])>strtotime($_POST["end_date"]))
{
	$err.='<br />募集終了日付が終了日付の日時より後になっています。';
}

//表示方法設定
if($_POST["aud_view"]=="hidden_view")
{
	$row["view"]="詳細のみの表示";
}
else
{
	$row["view"]="通常表示";
}

//無記入箇所チェック。
foreach($_POST as $key => $value)
{
	if($value=="")
	{
		$err.='<br />未記入の項目があります。全て必須項目です。';
	}
	//表示用配列の作成
	$row[$key]=nl2br($_POST[$key]);
	//独自リンクタグをhtmlリンクタグにする。
	if($key=="contact_url")
	{
		preg_match_all($linkexp,$row[$key],$link_array);
		if(count($link_array[0])>0)
		{
			foreach($link_array[0] as $value)
			{
				preg_match_all("/\[.*?\]/",$value,$link_value);
				//print_r($link_value);
				$url=str_replace("[","",str_replace("]","",$link_value[0][0]));
				$text=str_replace("[","",str_replace("]","",$link_value[0][1]));
				$link='<a href="'.$url.'" target="_blank">'.$text.'</a>';
				$row[$key]=preg_replace($linkexp,$link,$row[$key],1);
			}
		}
		
	}
}

//編集画面で使用する配列としてSESSIONに保存
$_SESSION["audition_create"]=$_POST;

//画像が更新されたかどうか
if(!empty($_SESSION["file_temp"]["url"]))
{
	$row["banner_add"]='<p id="up_file"><img src="'.$_SESSION["file_temp"]["url"].'" /></p>';
}
else
{
	$row["banner_add"]='<p id="up_file">未設定</p>';
}
//エラー箇所文章成型
if($err!="")
{
	$err='<p class="red">正しく入力されていない箇所があります。以下の通りです。<b>'.$err.'</b></p>';
}

//エラーが無い場合の処理
if($err=="")
{
	$row["err_det"].=$renew;
	$row["comp_check"]='<p><input type="checkbox" id="send" name="comp_check" value="check" />
      <label for="send" style="vertical-align:top">新規作成を完了する場合はチェックを入れてください</label><p>';
	$row["comp_btn"]='<input type="submit" name="audition_create_comp" value="新規作成を完了する" class="formBtn" />';
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