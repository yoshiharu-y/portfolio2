<?php

$link = mysql_connect("112.78.117.76", "yatabe_db", "beyata0817");
$db = mysql_select_db("yatabe_music", $link);
mysql_query('SET CHARACTER SET utf8');

$sql = "UPDATE user SET free_point = 0";
mysql_query($sql);


$sql = "SELECT * FROM audition_det WHERE end_flag = 0 AND del_flag = 0";
$result = mysql_query($sql);

while($row = mysql_fetch_assoc($result))
{
	if(strtotime($row["end_date"]) <= time())
	{
		$rel_sql = "SELECT * FROM rel_artist_audition WHERE aud_seq_num =".$row["aud_seq_num"];
		$rel_result = mysql_query($rel_sql);
		while($rel_row = mysql_fetch_assoc($rel_result))
		{
			$ar_sql = "SELECT * FROM artist WHERE ar_id =".$rel_row["ar_id"];
			$ar_result = mysql_query($ar_sql);
			while($ar_row = mysql_fetch_assoc($ar_result))
			{
				$point=$ar_row["vote_point"]+$rel_row["ar_point"];
				$up_sql = "UPDATE artist SET vote_point = ".$point." WHERE ar_id =".$rel_row["ar_id"];
				mysql_query($up_sql);
			}
		}
		$aud_up_sql = "UPDATE audition_det SET end_flag = 1 WHERE aud_seq_num =".$row["aud_seq_num"];
		mysql_query($aud_up_sql);
	}
}

?>