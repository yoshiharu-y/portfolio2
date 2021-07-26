<?php
/*
pop3への接続、命令、テキストデータ取得

user	POPサーバへ接続するユーザ名の入力
pass	POPサーバへ接続するユーザのパスワードを入力
stat	メール数と全体のデータサイズを表示
list	1通あたりのメールサイズを表示
retr	指定した番号のメールを表示
top	指定したメールのヘッダー指定した行数の本文を表示
uidl	メールのidを表示（よくわかりませんｗ）
dele	指定したメールへ削除フラグを付与
rset	deleコマンドの取り消し
noop	何もしない・・・
apop	apop認証時に使用
quit	POPサーバへの接続終了
*/

class POP3
{
	var $pop3_host; //ホスト保持
	var $pop3_user; //アカウント名保持
	var $pop3_pass; //パスワード保持
	var $pop3_connect; //接続状態保持
	
	function __construct($host,$user,$pass)
	{
		$this->pop3_host = $host;
		$this->pop3_user = $user;
		$this->pop3_pass = $pass;
    }
	
	//pop3接続
	function ConnectPOP3()
	{
		$sock = @fsockopen($this->pop3_host, 110, $errno, $errstr, 10) or false;
		@fgets($sock, 512);
		if($sock)
		{
			$this->pop3_connect=$sock;
			fputs($sock, "user ".$this->pop3_user."\r\n");
			$buf = fgets($sock, 512);
			fputs($sock, "pass ".$this->pop3_pass."\r\n");
			$buf = fgets($sock, 512);
			if(substr($buf, 0, 3) != '+OK')
			{
				return false;
			}
		}
		return $buf;
	}
	
	//pop3接続解除
	function DisConnectPOP3()
	{
		@fputs($this->pop3_connect, "quit \r\n");
		@fgets($this->pop3_connect,512);
		if(!@fclose($this->pop3_connect))
		{
			return false;
		}
		return true;
	}
	
	//pop3命令
	function SendCommand($command)
	{
		fputs($this->pop3_connect, $command."\r\n");
		return @fgets($this->pop3_connect,512);
	}
	
	//pop3メール件数とサイズ取得(return array
	function GetMailList()
	{
		fputs($this->pop3_connect, "stat\r\n");
		$data= fgets($this->pop3_connect,512);
		sscanf($data, '+OK %d %d', $num, $size);
		if ($num == "0")
		{
			return NULL;
		}
		return array($num,"num"=>$num,$size,"size"=>$size);
	}
	
	//pop3メールデータを取得(改行位置での分割return array
	function GetMailData($num)
	{
		$binary_flag=false;
		$get_enc=false;
		fputs($this->pop3_connect, "retr ".$num."\r\n");
		$line= fgets($this->pop3_connect,512);
		while (!preg_match("/^\.\r\n/",$line))
		{//EOFの.まで読む
			$line = fgets($this->pop3_connect,512);
			$line=str_replace(" ", "",$line);
			//画像バイナリがあるかどうか
			if (preg_match("/Content-Transfer-Encoding:base64/", $line))
  			{
				$get_enc=true;
			}
			
			//画像バイナリ開始位置
			if($line=="\r\n" && $get_enc)
			{
				$dump.="\r\n[binary:start]";
				$binary_flag=true;
				
			}
			
			//画像バイナリの終了位置
			if ((strpos($line,"=")!==false) && $binary_flag)
  			{
				$binary_flag=false;
				$get_enc=false;
				$line=str_replace("\r\n", "",$line)."[binary:end]\r\n";
				
			}
			
			if(!$binary_flag)
			{
				$dump.=$line;
			}
			else
			{
				
				$dump.=str_replace("\r\n", "",$line);
			}
			
		}
		//改行で分割
		$data=explode("\r\n",$dump);
		//半角スペースと[binary:start]と[binary:end]を削除
		foreach($data as $key => $value)
		{
			$value=preg_replace('/\s\s+/', ' ', $value);
			$data[$key]=$value;
		}
		return $data;
	}
} 
?>