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


//確認画面から
if(!empty($_POST["account_edit_return"]))
{
	//checkページから戻った場合	
	foreach($_SESSION["account_edit"] as $key => $value )
	{
		$row[$key]=$value;
	}
	$row["err_det"]="";
}
else
{	
	//初回時
	$sql = "SELECT * FROM artist where del_flag = 0 and ar_id =".$_SESSION["mms_artist"];
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		header( "Location: ../");
	}
	else
	{
		$row = $db->FetchRow($result_id);
		
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


$birthday=explode("-",$row["birthday"]);

//フォームのselectタグ内とラジオボタン設定
//**********年**********
for( $y=(intval(date("Y"))-5) ; $y>=(intval(date("Y"))-70) ; $y--)
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