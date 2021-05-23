<?php   
//电信-zxq 2012-08-01
//$DataIn.errorcasedata 
/*if ($DataIn==""){
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
}

ob_start(); //生成页面开始

$eResult = mysql_query("SELECT * FROM $DataIn.errorcasedata WHERE Estate=1 ORDER BY Date DESC",$link_id);
$i=@mysql_num_rows($eResult);
if($eRow = mysql_fetch_array($eResult)){
	do {
	    $Id=$eRow["Id"];
		$Title=$eRow["Title"];
		$Date=$eRow["Date"];
		$FileName=$eRow["Picture"];
		//$f=anmaIn($FileName,$SinkOrder,$motherSTR);
		//$d=anmaIn("download/errorcase/",$SinkOrder,$motherSTR);			
		$Picture="<span onClick='viewMistakeImage(\"$Id\",2,1)' style='CURSOR: pointer;' class='yellowN'>查阅</span>";
		echo "&nbsp;&nbsp;".$i."、".$Title."(".$Picture.")<br>";
		$i--;
		} while($eRow = mysql_fetch_array($eResult));
	}

$desk4_File="desk4.inc";
$content = ob_get_contents();//取得php页面输出的全部内容
$fp = fopen($desk4_File, "w");
fwrite($fp, $content);
fclose($fp);*/
?>