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

//データの更新は$_SESSION["audition_edit"]内に入った物を使う。
//エスケープはcheck.phpの最初で行い、エスケープしたものを$_SESSION["audition_edit"]に入れている。

if(!empty($_POST["comp_check"])&&!empty($_POST["news_edit_comp"]))
{
	$sql = "SELECT * FROM news where seq_num='".$_SESSION["news_edit"]["seq_num"]."'";
	$result_id = $db->ExecuteSQL($sql);
	if(mysql_num_rows($result_id)==0 )
	{
		$row["err_det"]='<p class="red">表示するデータがありません</p>';
	}
	else
	{
		$row = $db->FetchRow($result_id);
		$row["err_det"]="";
		//sqlアップデート
		
		foreach($_SESSION["news_edit"] as $key => $value)
		{
			//文字が改行されるようにする
			$_SESSION["news_edit"][$key]=nl2br($_SESSION["news_edit"][$key]);
			//独自リンクタグをhtmlリンクタグにする。
			if($key=="news_detail")
			{
				preg_match_all($linkexp,$_SESSION["news_edit"][$key],$link_array);
				if(count($link_array[0])>0)
				{
					foreach($link_array[0] as $value)
					{
						preg_match_all("/\[.*?\]/",$value,$link_value);
						$url=str_replace("[","",str_replace("]","",$link_value[0][0]));
						$text=str_replace("[","",str_replace("]","",$link_value[0][1]));
						$link='<a href="'.$url.'" target="_blank">'.$text.'</a>';
						$_SESSION["news_edit"][$key]=preg_replace($linkexp,$link,$_SESSION["news_edit"][$key],1);
					}
				}
				
			}
		}
		//記事の表示設定のタグ埋め込み
		if($_SESSION["news_edit"]["news_view"]=="タイトルのみ")
		{
			$_SESSION["news_edit"]["news_title"]="[news_not_link]".$_SESSION["news_edit"]["news_title"];
		}
		//オーディションの更新日時を生成
		$_SESSION["news_edit"]["renew_date"]= date("Y-m-d");
		//配列のキーを変数名とした変数を作る
		extract($_SESSION["news_edit"]);
		//sql文作成
		$sql = "UPDATE news SET
					news_title = ?,
					news_category = ?,
					news_detail = ?,
					renew_date = ?
					WHERE seq_num = ? limit 1";
		//POSTDATAからMySQLに反映させるデータを入れる入れ物。
		$phs = array(
				$news_title,
				$news_category,
				$news_detail,
				$renew_date,
				$seq_num
				);
		//インジェクション対策のsqlプリペアード関数
		$sql_prepare = $db->mysql_prepare($sql, $phs);
		//echo $sql_prepare;
		$result_id = $db->ExecuteSQL($sql_prepare);
		if(!$result_id)
		{
			$row["err_det"]='<p class="red">更新に失敗しました。再度編集お願いします。</p>';
		}
		else
		{
			$row["err_det"]='<p>更新しました。</p>';
		}
	}
}
else
{
	$row["err_det"]='<p class="red">表示するデータがありません</p>';
}
//ログイン情報を残し$_SESSIONを初期化
$temp_login=$_SESSION["mms_admin_login"];
$_SESSION=array();
$_SESSION["mms_admin_login"]=$temp_login;
//var_dump($_SESSION);

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