<?php
/*
サブオーディション編集確認画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


//編集画面から
if(!empty($_POST["account_edit_check"]) && isset($_SESSION["account_edit"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	$sql = "SELECT * FROM artist where del_flag = 0 and ar_id =".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		header( "Location: ../");
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
		
		//画像保存フォルダがある場所
		$tmpdir  = "../images/";
		
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
}
//確認チェックが入っていない場合
else if(!empty($_POST["account_edit_comp"]) && isset($_SESSION["account_edit"]))
{
	foreach($_SESSION["account_edit"] as $key => $value )
	{
		$row[$key]=$value;
		$_POST[$key]=$value;
	}
	$row["err_det"]='<p class="coutionBox">編集を完了させるチェックが入っていません。</p>';
}
else
{
	header( "Location: ../");;
}

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
		$row[$key]=$_POST[$key];
	}
	//成型を今のファイルで行っているのでこの段階で生年月日の確認
	if($key=="day")
	{
		if($birthday!=$row["birthday"])
		{
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
$_SESSION["account_edit"]=$row;

//性別成型
if($row["sex"]==0)
{
	$row["sex_type"]="男性";
}
else
{
	$row["sex_type"]="女性";	
}

//エラーが無い場合の処理
if($row["err_det"]=="")
{
	$row["err_det"]='<p class="coutionBox">以下の内容でデータが変更されます。</p>';
}
$row["comp_check"]='<p><input type="checkbox" id="send" name="comp_check" value="check" />
      <label for="send" class="label">編集を完了する場合はチェックを入れてください</label><p>';
$row["comp_btn"]='<input type="submit" name="account_edit_comp" value="編集を完了する" class="send_submit" />';


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

//定義したタイプ[data_s:]になっている所を変換(突貫工事)
foreach( $artist_row as $key => $value )
{
	$control->ChangeData($key,$value);
}

//htmlを表示
echo $control->GetContentData();

?>