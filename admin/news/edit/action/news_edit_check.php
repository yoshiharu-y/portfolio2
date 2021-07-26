<?php
/*
common/config.phpを読む
*/

@require_once(dirname(__FILE__)."/../../../../common/lib/config.php");

//独自リンクタグを摘出する正規表現
$linkexp="/link:\[.*?\]\[.*?\]/s";


//////////////////////////////////////////////////
//
//　　　表示させるデータ設定
//
//////////////////////////////////////////////////
//編集画面から
if(!empty($_POST["news_edit_check"]))
{
	//htmlタグエスケープ
	foreach( $_POST as $key => $value )
	{
		$_POST[$key]= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');	
	}
	
	$sql = "SELECT * FROM news where seq_num='".$_POST["news_no"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0)
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
		exit();
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
		$row["err_det"]="";
	}
}
//確認チェックが入っていない場合
else if(!empty($_POST["news_edit_comp"]))
{
	foreach( $_SESSION["news_edit"] as $key => $value )
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

//sqlフィールドに対応する名称をセット
$fieldName=array("news_title"=>"告知タイトル",
				 "news_category"=>"告知カテゴリ",
				 "news_detail"=>"告知詳細",
				 "news_view"=>"表示方法");

//変更箇所を通達するhtml生成
foreach($_POST as $key => $value)
{
	//無記入箇所はsqlデータにする。記入箇所はpostデータ。
	if($_POST[$key]!="")
	{
		if(isset($row[$key]))
		{
			//rowから表示方法省く
			if($key=="news_title")
			{
				//表示方法解析
				if(strpos($row[$key],"[news_not_link]") !== false)
				{
					$row[$key]=str_replace("[news_not_link]", "", $row[$key]);
				}
			}
			if($_POST[$key]!=$row[$key])
			{
				$renew.=$fieldName[$key]."の項目<br />";
			}
		}
		$row[$key]=$_POST[$key];
	}	
}

//添え字が数字の配列を削除
$data_key=(count($row)/2);
for($i=0;$i<$data_key;$i++)
{
	unset($row[$i]);
}
//編集画面で使用する配列としてSESSIONに保存
$_SESSION["news_edit"]=$row;

//確認画面でtextarea文字が改行されるようにする
foreach($row as $key => $value)
{
	$row[$key]=nl2br($row[$key]);
	//独自リンクタグをhtmlリンクタグにする。
	if($key=="news_detail")
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
	$row["comp_btn"]='<input type="submit" name="news_edit_comp" value="編集を完了する" class="formBtn" />';
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