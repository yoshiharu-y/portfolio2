<?php
/**
 * データベース（MySQL）アクセスのクラス
 * (参考) http://www.bnote.net/php/php/09_db_class.shtml
 */
class DB {
    //////////////////////////////////////////////////
    //プロパティ
    //////////////////////////////////////////////////
    private $link_id;    //リンクID（MySQL接続ハンドル）
    private $result_id;    //結果セットID
    
    //////////////////////////////////////////////////
    //コンストラクタ
    //////////////////////////////////////////////////
    function __construct($server, $user, $password, $database) {
        $this->link_id = mysql_connect($server, $user, $password);
        if ($this->link_id) {
            if ($database != "") {
                $db = mysql_select_db($database, $this->link_id);
                if (!$db) {
                    mysql_close($this->link_id);
                    return false;
                }
                return $this->link_id;
            }
        }
        return false;
    }
    //////////////////////////////////////////////////
    //デストラクタ
    //////////////////////////////////////////////////
	function __destruct(){
        if($this->link_id){
            if(!$this->pconnect){
                mysql_close($this->link_id);
            }
        }
    }
	
    //////////////////////////////////////////////////
    //メソッド
    //////////////////////////////////////////////////
    //SQLの実行
    function ExecuteSQL($sql) {
        if ($sql != "") {
            $this->result_id = mysql_query($sql, $this->link_id);
            return $this->result_id;
        }
    }
    
    //結果セットID取得
    protected function GetResultID($result_id) {
        if (!$result_id) {
            return $this->result_id;
        }
        return $result_id;
    }
    
    //１行取得
    function FetchRow($result_id = 0) {
        $result_id = $this->GetResultID($result_id);
        if ($result_id) {
            $row = mysql_fetch_array($result_id);
            return $row;
        }
        else {
            return false;
        }
    }
    
    //全行取得
    function FetchAll($result_id = 0) {
        $result_id = $this->GetResultID($result_id);
        if ($result_id) {
            while ($row = mysql_fetch_array($result_id)) {
                $rows[] = $row;
            }
            return $rows;
        }
        else {
            return false;
        }
    }
	
	//* SQLインジェクション対策　MySQLプリペアードステートメント関数
	
	function mysql_prepare($query, $phs = array()) {
        $phs = array_map(create_function('$ph',
            'return "\'".mysql_real_escape_string($ph)."\'";'), $phs);
        
        $curpos = 0;
        $curph  = count($phs)-1;
        for ($i = strlen($query) - 1; $i > 0; $i--) {
            if ($query[$i] !== '?') {
                continue;
            }
            if ($curph < 0 || !isset($phs[$curph])) {
                $query = substr_replace($query, 'NULL', $i, 1);
            } else {
                $query = substr_replace($query, $phs[$curph], $i, 1);
            }
            $curph--;
        }
        unset($curpos, $curph, $phs);
        
        return $query;
    }
    
}
?>
