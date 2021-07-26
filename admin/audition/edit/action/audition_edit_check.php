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
if(!empty($_POST["audition_edit_check"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	$sql = "SELECT * FROM audition_det where del_flag = 0 and aud_seq_num='".$_POST["audition_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
		exit();
	}
	else
	{
		$row = $db->FetchRow($result_id);
		/*foreach($row as $key => $value)
		{
			//改行文字列のみのtextareaのPOSTデータを使い、<br />を改行文字列に変換する。
			$row[$key]=str_replace("<br />", $_POST["escape_text"], $row[$key]);
			$row[$key]=preg_replace("/\t/","", $row[$key]);
		}*/
		$row["err_det"]="";
	}
}
//確認チェックが入っていない場合
else if(!empty($_POST["audition_edit_comp"]))
{
	foreach( $_SESSION["audition_edit"] as $key => $value )
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

//開始日付、終了日付成型
$entry_start=$_POST["entry_start_year"]."-".$_POST["entry_start_month"]."-".$_POST["entry_start_day"]." ".$_POST["entry_start_hour"].":".$_POST["entry_start_mins"].":00";
$entry_end=$_POST["entry_end_year"]."-".$_POST["entry_end_month"]."-".$_POST["entry_end_day"]." ".$_POST["entry_end_hour"].":".$_POST["entry_end_mins"].":00";


$start_date=$_POST["start_year"]."-".$_POST["start_month"]."-".$_POST["start_day"]." ".$_POST["start_hour"].":".$_POST["start_mins"].":00";
$end_date=$_POST["end_year"]."-".$_POST["end_month"]."-".$_POST["end_day"]." ".$_POST["end_hour"].":".$_POST["end_mins"].":00";

//募集開始日付と募集終了日付を比較
if(strtotime($entry_start)>strtotime($entry_end))
{
	$err.='<br />募集開始日付と募集終了日付の設定に不備があります。';
}

//開催日付と終了日付を比較
if(strtotime($start_date)>strtotime($end_date))
{
	$err.='<br />開催日付と終了日付の設定に不備があります。';
}

//募集終了日付と終了日付を比較
if(strtotime($entry_end)>strtotime($end_date))
{
	$err.='<br />募集終了日付が終了日付の日時より後になっています。';
}

//表示方法設定
if($_POST["aud_view"]=="hidden_view")
{
	$row["view"]="詳細のみの表示";
	$hidden_num=1;
}
else
{
	$row["view"]="通常表示";
	$hidden_num=0;
	$_POST["aud_view"]="def";
}

//sqlフィールドに対応する名称をセット
$fieldName=array("page_title"=>"オーディションページブラウザタイトル",
				 "name"=>"オーディションページサイト内タイトル",
				 "head"=>"説明文",
				 "detail"=>"募集内容",
				 "qualification"=>"応募資格",
				 "contact_url"=>"応募方法",
				 "entry_start"=>"募集開始日付",
				 "entry_end"=>"募集終了日付",
				 "start_date"=>"開始日付",
				 "end_date"=>"終了日付",
				 "hidden_flag"=>"オーディション詳細での表示方法",
				 "audition_treatment"=>"合格後の待遇");

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
	//成型を今のファイルで行っているのでフォーム体裁と同じになるように更新情報入力
	if($key=="entry_start_year")
	{
		if($entry_start!=$row["entry_start"])
		{
			$renew.=$fieldName["entry_start"]."の項目<br />";
			$row["entry_start"]=$entry_start;
		}
	}
	if($key=="entry_end_year")
	{
		if($entry_end!=$row["entry_end"])
		{
			$renew.=$fieldName["entry_end"]."の項目<br />";
			$row["entry_end"]=$entry_end;
		}
	}	
	if($key=="start_year")
	{
		if($start_date!=$row["start_date"])
		{
			$renew.=$fieldName["start_date"]."の項目<br />";
			$row["start_date"]=$start_date;
		}
	}
	if($key=="end_year")
	{
		if($end_date!=$row["end_date"])
		{
			$renew.=$fieldName["end_date"]."の項目<br />";
			$row["end_date"]=$end_date;
		}
	}
	if($key=="aud_view")
	{
		if($_POST[$key]=="hidden_view")
		{
			if($row["hidden_flag"]!=1)
			{
				$renew.=$fieldName["hidden_flag"]."の項目<br />";
				$row["hidden_flag"]=1;
			}
		}
		else
		{
			if($row["hidden_flag"]!=0)
			{
				$renew.=$fieldName["hidden_flag"]."の項目<br />";
				$row["hidden_flag"]=0;
			}
		}
	}
}



//添え字が数字の配列を削除
$data_key=(count($row)/2);
for($i=0;$i<$data_key;$i++)
{
	unset($row[$i]);
}

//編集画面で使用する配列としてSESSIONに保存
$_SESSION["audition_edit"]=$row;

//確認画面でtextarea文字が改行されるようにする
foreach($row as $key => $value)
{
	$row[$key]=nl2br($row[$key]);
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
}//画像が更新されたかどうか
if(!empty($_SESSION["file_temp"]["url"]))
{
	//初回時に確認画面に来た時だけ変更文章表示
	if(empty($_POST["audition_edit_comp"]))
	{
		$renew="サムネイル画像の項目<br />".$renew;
	}
	$row["banner_add"]='<p id="up_file"><img src="'.$_SESSION["file_temp"]["url"].'" /></p>';
}
else
{
	if($row["banner_add"]=="no_thum.jpg")
	{
		$row["banner_add"]='<p id="up_file">サムネイルなし</p>';
	}
	else
	{
		$row["banner_add"]='<p id="up_file"><img src="'.$tmpdir.$row["banner_add"].'" /></p>';
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
	$row["comp_btn"]='<input type="submit" name="audition_edit_comp" value="編集を完了する" class="formBtn" />';
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