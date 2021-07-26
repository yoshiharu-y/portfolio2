<?php

// POP3サーバー
$pop3host = "";
// pop3ユーザーメールID
$pop3user = "";
// pop3ｱｰﾃｨｽﾄメールID
$pop3artist = "";
// pop3パスワード
$pop3pass = "";

//携帯サイトﾒｰﾙのｱﾄﾞﾚｽ設定
$mms_mail ="";

//お問い合わせから送られるﾒｰﾙのｱﾄﾞﾚｽ設定
$contact_mail ="";

//アーティスト審査のﾌｫｰﾑから送られるﾒｰﾙのｱﾄﾞﾚｽ設定
$artist_review_mail ="";

/*************************************************

				以下プログラム

*************************************************/

//文字コード定義(phpソースやHTMLのソースに合わせて設定してください)
//以下の定義しないと、文字化けの原因になります。
mb_language("Japanese");
mb_internal_encoding("UTF-8");
ini_set('default_charset','UTF-8');
header("Content-type: text/html; charset=UTF-8");

/*--
db接続やら
--*/
@require_once(dirname(__FILE__)."/class/db.class.php");
@require_once(dirname(__FILE__)."/class/pop3.class.php");
@require_once(dirname(__FILE__)."/class/upload.class.php");
@require_once(dirname(__FILE__)."/class/Control.class.php");

$db = new DB("", "", "", "");
//$db = new DB("localhost", "root", "nH%z7Gej", "mms");
$db->ExecuteSQL('SET CHARACTER SET utf8');

//コントロールクラス
$control = new Control();

?>