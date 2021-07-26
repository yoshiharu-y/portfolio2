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
//初回時
if(!empty($_POST["user_edit"]))
{
	$sql = "SELECT * FROM user where del_flag = 0 and seq_num='".$_POST["user_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
		exit();
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
	}
}
//確認画面から
else if(!empty($_POST["user_edit_return"]))
{
	foreach( $_SESSION["user_edit"] as $key => $value )
	{
		$row[$key]=$value;	
	}
	$row["err_det"]="";
}
else
{	
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
	exit();}
$birthday=explode("-",$row["birthday"]);

//フォームのselectタグ内とラジオボタン設定
//**********年**********
for( $y=(intval(date("Y"))-12) ; $y>=(intval(date("Y"))-70) ; $y--)
{
	if($birthday[0]==$y)
	{
		$row["year_form"].='<option value="'.$y.'" selected="selected" >'.$y.'</option>';
	}
	else
	{
		$row["year_form"].='<option value="'.$y.'">'.$y.'</option>';
	}
}//**********月**********
for( $m=1 ; $m<=12 ; $m++)
{
	if($birthday[1]==$m)
	{
		$row["month_form"].='<option value="'.sprintf("%02d",$m).'" selected="selected" >'.$m.'</option>';
	}
	else
	{
		$row["month_form"].='<option value="'.sprintf("%02d",$m).'">'.$m.'</option>';
	}
}
		
//**********日**********
for( $d=1 ; $d<=31 ; $d++)
{
	if($birthday[2]==$d)
	{
		$row["day_form"].='<option value="'.sprintf("%02d",$d).'" selected="selected" >'.$d.'</option>';
	}
	else
	{
		$row["day_form"].='<option value="'.sprintf("%02d",$d).'">'.$d.'</option>';
	}
}
	
//**********性別**********
for( $s=0 ; $s<=1 ; $s++)
{
	if($row["sex"]==$s)
	{
		$row["sex_form"].='<input name="sex" type="radio" value="'.$s.'" checked="checked" />';
	}
	else
	{
		$row["sex_form"].='<input name="sex" type="radio" value="'.$s.'" />';
	}
	if($s==0)
	{
		$row["sex_form"].="男性";
	}
	else
	{
		$row["sex_form"].="女性";	
	}
}
if(!empty($_SESSION["file_temp"]["url"]))
{
	$row["user_thum_add"]='<p id="up_file"><img src="'.$_SESSION["file_temp"]["url"].'" /></p>';
}
else
{
	//サムネイル
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_thum_add"]='<p id="up_file">サムネイルなし</p>';
	}
	else
	{
		//フォルダ名　本サーバー
		$userdir=$tmpdir."e".$row["user_id"]."/";
		//$userdir=$tmpdir;
		$row["user_thum_add"]='<p id="up_file"><img src="'.$userdir.$row["user_thum"].'" /></p>';
	}
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
}//htmlを表示
echo $control->GetContentData();

?>