<?php
/*
ﾕｰｻﾞｰ登録入力画面：動的部
*/

@require_once(dirname(__FILE__)."/../../common/lib/config.php");
@require_once(dirname(__FILE__)."/../../common/lib/m_mail_domain.php");

//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//echo $_SERVER['SERVER_NAME']."/mobile/user_account/?qur=Faf1wjxKNiWnwPeJBjS0CcXQZJMjVx";
if($_POST["mms_return"]=="")
{
	//初回時
	$row["login_id"]="";
	$row["password"]="";
	$row["user_name"]="";
	$row["name"]="";
	$row["kana"]="";
	$row["age"]="";
	$row["mail"]="";
	$row["c_mail"]="";
	$row["zip"]="";
	$row["address1"]="";
	$row["err_det"]="";
	$row["address2-1"]="";
	$row["address2-2"]="";
	
	//フォームのselectタグ内とラジオボタン設定
	//年
	for( $y=(intval(date("Y"))-12) ; $y>=(intval(date("Y"))-70) ; $y--)
	{
		$row["year_form"].='<option value="'.$y.'">'.$y.'</option>';
	}
	//月
	for( $m=1 ; $m<=12 ; $m++)
	{
		$row["month_form"].='<option value="'.sprintf("%02d",$m).'">'.$m.'</option>';
	}
	//日
	for( $d=1 ; $d<=31 ; $d++)
	{
		$row["day_form"].='<option value="'.sprintf("%02d",$d).'">'.$d.'</option>';
	}
	//性別
	for( $s=0 ; $s<=1 ; $s++)
	{
		
		if($s==0)
		{
			$row["sex_form"].='<input name="sex" type="radio" value="'.$s.'" checked="checked" />';
			$row["sex_form"].="男性";
		}
		else
		{
			$row["sex_form"].='<input name="sex" type="radio" value="'.$s.'" />';
			$row["sex_form"].="女性";	
		}
	}
}
else
{
	
	//checkページから戻った場合
	foreach( $_POST as $key => $value )
	{
		$row[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}

	//フォームのselectタグ内とラジオボタン設定
	//年
	for( $y=(intval(date("Y"))-12) ; $y>=(intval(date("Y"))-70) ; $y--)
	{
		if($_POST["year"]==$y)
		{
			$row["year_form"].='<option value="'.$y.'" selected="selected" >'.$y.'</option>';
		}
		else
		{
			$row["year_form"].='<option value="'.$y.'">'.$y.'</option>';
		}
	}
	//月
	for( $m=1 ; $m<=12 ; $m++)
	{
		if($_POST["month"]==$m)
		{
			$row["month_form"].='<option value="'.sprintf("%02d",$m).'" selected="selected" >'.$m.'</option>';
		}
		else
		{
			$row["month_form"].='<option value="'.sprintf("%02d",$m).'">'.$m.'</option>';
		}
	}
	//日
	for( $d=1 ; $d<=31 ; $d++)
	{
		if($_POST["day"]==$d)
		{
			$row["day_form"].='<option value="'.sprintf("%02d",$d).'" selected="selected" >'.$d.'</option>';
		}
		else
		{
			$row["day_form"].='<option value="'.sprintf("%02d",$d).'">'.$d.'</option>';
		}
	}
	//性別
	for( $s=0 ; $s<=1 ; $s++)
	{
		if($_POST["sex"]==$s)
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