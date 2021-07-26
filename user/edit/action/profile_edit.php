<?php
/*
サブオーディション編集画面：動的部
*/

@require_once(dirname(__FILE__)."/../../../common/lib/config.php");


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////


if(!empty($_POST["mms_profile_return"]))
{

	$sql = "SELECT * FROM user where user_id =".$_SESSION["mms_user"];
	$result_id = $db->ExecuteSQL($sql);
	$row = $db->FetchRow($result_id);
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_thum"]="../images/no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."../images/e".$row["user_id"]."/";
		$row["user_thum"]=$tmpdir."b_".$row["user_thum"];
	}
	
	//checkページから戻った場合
	foreach( $_POST as $key => $value )
	{
		$row[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	
	$birthday=array($row["year"],$row["month"],$row["day"]);
	
}
else
{

	$sql = "SELECT * FROM user where user_id =".$_SESSION["mms_user"];
	$result_id = $db->ExecuteSQL($sql);
	$row = $db->FetchRow($result_id);
	$row_num=0;
	
	
	//添え字無しの配列を削除
	$row_key=(count($row)/2);
	for($i=0;$i<$row_key;$i++)
	{
		unset($row[$i]);
	}	
	
	$birthday=explode("-",$row["birthday"]);
	
	if($row["user_thum"]=="no_thum.jpg")
	{
		$row["user_thum"]="../images/no_image.jpg";
	}
	else
	{
		//フォルダ名　本サーバー
		$tmpdir=$tmpdir."../images/e".$row["user_id"]."/";
		$row["user_thum"]=$tmpdir."b_".$row["user_thum"];
	}
}


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
}

//**********月**********
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
	
$row["ses"]=$ses;	
	

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