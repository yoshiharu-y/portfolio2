<?php
/*
control.class
テキストデータの制御を行なう（変換、ランダムテキスト出力、テキスト保持）
*/

class Control
{
	var $content_data;
	var $content_type;
	var $rand_charset;
	
	function __construct(){
        $this->content_data = "";
		$this->content_type = "";
		$this->rand_charset = 
		"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
    }
	
	function SetContentData($data)
	{
		$this->content_data = $data;
	}
	
	function SetContentType($type)
	{
		$this->content_type = $type;
	}
	
	function GetContentData()
	{
		return $this->content_data;
	}
	
	function GetContentType()
	{
		return $this->content_type;
	}
	
	function ChangeData($detail,$value)
	{
		if(!empty($this->content_type))
		{
			if(strpos($detail,"script")===FALSE)
			{
				$change_str = "[".$this->content_type.":".$detail."]";
		 		$this->content_data = @str_replace($change_str,$value,$this->content_data);
			}			
		}
	}
	
	function CreateRandText($length = 10)
	{
		 $randCount = strlen($this->rand_charset);
		 $randText = "";
		 for($i = 1; $i <= $length ; $i++)
		 {
		 	$randText = $randText.substr($this->rand_charset, rand(0,$randCount) ,1);
		 }
		 return $randText;
	}
	function CheckMatch($text,$type)
	{
		if($type=="kana")
		{
			//カナ
			if (preg_match('/^[ァ-ヾ]+$/u', $text))
			{
				return true;
			}
			else
			{
				return false;	
			}
		}
		
		if($type=="alphabet")
		{
			//半角英数字5～20文字以下
			if (preg_match('/^[a-z\d_]{5,20}$/i', $text))
			{
				return true;
			}
			else
			{
				return false;	
			}
		}
		
		if($type=="email")
		{
			//メール
			if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\+._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',$text))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		if($type=="number")
		{
			//数字
			if(preg_match("/[0-9]+$/",$text))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	
	}
} 
?>