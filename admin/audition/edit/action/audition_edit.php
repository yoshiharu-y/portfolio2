<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//画像保存フォルダがある場所
$tmpdir  = "../../../audition/images/";

//htmlリンクタグを摘出する正規表現
$link_def="/<a href=\".*?\" target=\"_blank\">.*?<\/a>/s";
//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////

//初回時
if(!empty($_POST["audition_edit"]))
{
	$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		foreach($row as $key => $value)
		{
			//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
			$row[$key]=str_replace("<br />", $_POST["escape_text"], $row[$key]);
			$row[$key]=preg_replace("/\t/","", $row[$key]);
		}
		//aタグ抽出
		preg_match_all($link_def,$row["contact_url"],$link_array);
		//print_r($link_array);
		if(count($link_array[0])>0)
		{
			foreach($link_array[0] as $value)
			{
				//href抽出
				preg_match("/\".*?\"/",$value,$out_url);
				$url=str_replace("\"","",$out_url[0]);
				//echo $url."<br>";
				//テキスト抽出
				preg_match("/>.*?</",$value,$out_text);
				$text=str_replace(">","",str_replace("<","",$out_text[0]));
				//echo $text."<br>";
				$link='link:['.$url.']['.$text.']';
				$row["contact_url"]=preg_replace($link_def,$link,$row["contact_url"],1);
			}
		}
		$row["err_det"]="";
	}
}
//確認画面から
else if(!empty($_POST["audition_edit_return"]))
{
	foreach( $_SESSION["audition_edit"] as $key => $value )
	{
		$row[$key]=$value;	
	}
	$row["err_det"]="";
}
else
{	
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
	exit();
}
//フォームのオプションタグ生成
$select_option='<option value="%t" selected="selected" >%t</option>';
$option='<option value="%t">%t</option>';//日付と時間に分ける


//日付と時間に分ける
$entry_start_date=explode(" ",$row["entry_start"]);
$entry_end_date=explode(" ",$row["entry_end"]);
$start_date=explode(" ",$row["start_date"]);
$end_date=explode(" ",$row["end_date"]);

//日付をフォームに適用させるために分割
$entry_start_day=explode("-",$entry_start_date[0]);
$entry_end_day=explode("-",$entry_end_date[0]);
$start_day=explode("-",$start_date[0]);
$end_day=explode("-",$end_date[0]);

//時間をフォームに適用させるために分割
$entry_start_time=explode(":",$entry_start_date[1]);
$entry_end_time=explode(":",$entry_end_date[1]);
$start_time=explode(":",$start_date[1]);
$end_time=explode(":",$end_date[1]);

//フォームのselectタグ設定（開始と終了時間
//**********年**********
for( $y=(intval(date("Y"))+2) ; $y>=(intval(date("Y"))) ; $y--)
{
	if($entry_start_day[0]==$y)
	{
		$row["entry_start_year_form"].=str_replace("%t",$y,$select_option);
	}
	else
	{
		$row["entry_start_year_form"].=str_replace("%t",$y,$option);
	}
	
	if($entry_end_day[0]==$y)
	{
		$row["entry_end_year_form"].=str_replace("%t",$y,$select_option);
	}
	else
	{
		$row["entry_end_year_form"].=str_replace("%t",$y,$option);
	}
	
	if($start_day[0]==$y)
	{
		$row["start_year_form"].=str_replace("%t",$y,$select_option);
	}
	else
	{
		$row["start_year_form"].=str_replace("%t",$y,$option);
	}
	
	if($end_day[0]==$y)
	{
		$row["end_year_form"].=str_replace("%t",$y,$select_option);
	}
	else
	{
		$row["end_year_form"].=str_replace("%t",$y,$option);
	}
}

//**********月**********
for( $m=1 ; $m<=12 ; $m++)
{
	if($entry_start_day[1]==$m)
	{
		$row["entry_start_month_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["entry_start_month_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	if($entry_end_day[1]==$m)
	{
		$row["entry_end_month_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["entry_end_month_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	
	if($start_day[1]==$m)
	{
		$row["start_month_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["start_month_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	if($end_day[1]==$m)
	{
		$row["end_month_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["end_month_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
}
//**********日**********
for( $d=1 ; $d<=31 ; $d++)
{
	if($entry_start_day[2]==$d)
	{
		$row["entry_start_day_form"].=str_replace("%t",sprintf("%02d",$d),$select_option);
	}
	else
	{
		$row["entry_start_day_form"].=str_replace("%t",sprintf("%02d",$d),$option);
	}
	if($entry_end_day[2]==$d)
	{
		$row["entry_end_day_form"].=str_replace("%t",sprintf("%02d",$d),$select_option);
	}
	else
	{
		$row["entry_end_day_form"].=str_replace("%t",sprintf("%02d",$d),$option);
	}
	
	if($start_day[2]==$d)
	{
		$row["start_day_form"].=str_replace("%t",sprintf("%02d",$d),$select_option);
	}
	else
	{
		$row["start_day_form"].=str_replace("%t",sprintf("%02d",$d),$option);
	}
	if($end_day[2]==$d)
	{
		$row["end_day_form"].=str_replace("%t",sprintf("%02d",$d),$select_option);
	}
	else
	{
		$row["end_day_form"].=str_replace("%t",sprintf("%02d",$d),$option);
	}
}
//**********時**********
for( $h=1 ; $h<=24 ; $h++)
{
	if($entry_start_time[0]==$h)
	{
		$row["entry_start_hour_form"].=str_replace("%t",sprintf("%02d",$h),$select_option);
	}
	else
	{
		$row["entry_start_hour_form"].=str_replace("%t",sprintf("%02d",$h),$option);
	}
	if($entry_end_time[0]==$h)
	{
		$row["entry_end_hour_form"].=str_replace("%t",sprintf("%02d",$h),$select_option);
	}
	else
	{
		$row["entry_end_hour_form"].=str_replace("%t",sprintf("%02d",$h),$option);
	}
	
	if($start_time[0]==$h)
	{
		$row["start_hour_form"].=str_replace("%t",sprintf("%02d",$h),$select_option);
	}
	else
	{
		$row["start_hour_form"].=str_replace("%t",sprintf("%02d",$h),$option);
	}
	if($end_time[0]==$h)
	{
		$row["end_hour_form"].=str_replace("%t",sprintf("%02d",$h),$select_option);
	}
	else
	{
		$row["end_hour_form"].=str_replace("%t",sprintf("%02d",$h),$option);
	}
}
//**********分**********
for( $m=1 ; $m<=60 ; $m++)
{
	if($entry_start_time[1]==$m)
	{
		$row["entry_start_mins_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["entry_start_mins_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	if($entry_end_time[1]==$m)
	{
		$row["entry_end_mins_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["entry_end_mins_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	
	if($start_time[1]==$m)
	{
		$row["start_mins_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["start_mins_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
	if($end_time[1]==$m)
	{
		$row["end_mins_form"].=str_replace("%t",sprintf("%02d",$m),$select_option);
	}
	else
	{
		$row["end_mins_form"].=str_replace("%t",sprintf("%02d",$m),$option);
	}
}

//表示方法チェックボックス
if($row["hidden_flag"]==0)
{
	$row["aud_view"]="hidden_view";
}
else
{
	$row["aud_view"]='hidden_view" checked="checked';
}
//画像
if(!empty($_SESSION["file_temp"]["url"]))
{
	$row["audition_banner_add"]='<p id="up_file"><img src="'.$_SESSION["file_temp"]["url"].'" /></p>';
}
else
{
	//サムネイル
	if($row["banner_add"]=="no_thum.jpg")
	{
		$row["audition_banner_add"]='<p id="up_file">サムネイルなし</p>';
	}
	else
	{
		$row["audition_banner_add"]='<p id="up_file"><img src="'.$tmpdir.$row["banner_add"].'" /></p>';
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