<?php  
//电信-zxq 2012-08-01
/*
$DataIn.linkmandata
$DataPublic.staffmain
$DataIn.info4_cgmsg
二合一已更新
*/
include "../model/modelhead.php";
?>
<body style="background:#ffffff;">
<?php
$eResult = mysql_query("SELECT  E.Id,E.Title,E.Picture,E.Date FROM $DataIn.errorcasedata E
                        LEFT JOIN $DataIn.casetostuff C ON C.cId=E.Id
						LEFT JOIN $DataIn.stuffprovider S ON S.StuffId=C.StuffId
						LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=S.CompanyId
                        WHERE E.Estate=1 AND E.Type=2 AND  L.Id=$Login_P_Number 
						GROUP BY E.Id ORDER BY E.Date DESC",$link_id);
		/*echo "SELECT  E.Id,E.Title,E.Picture FROM $DataIn.errorcasedata E
                        LEFT JOIN $DataIn.casetostuff C ON C.cId=E.Id
						LEFT JOIN $DataIn.stuffprovider S ON S.StuffId=C.StuffId
						LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=S.CompanyId
                        WHERE E.Estate=1 AND L.Id=$Login_P_Number 
						GROUP BY E.Id ORDER BY E.Date DESC";*/
//$i=@mysql_num_rows($eResult);
$i=1;
if($eRow = mysql_fetch_array($eResult)){
	do {
	    $Id=$eRow["Id"];
		$Title=$eRow["Title"];
		$Date=$eRow["Date"];
		$FileName=$eRow["Picture"];
		//$f=anmaIn($FileName,$SinkOrder,$motherSTR);
		//$d=anmaIn("download/errorcase/",$SinkOrder,$motherSTR);			
		$Picture="<span onClick='viewMistakeImage(\"$Id\",2,1)' style='CURSOR: pointer;' class='yellowN'>查阅</span>";
		echo "<br><div style='font-size:13px;'>&nbsp;&nbsp;".$i."、".$Title. "&nbsp;&nbsp;$Date" . "(".$Picture.")</div>";
		$i++;
		} while($eRow = mysql_fetch_array($eResult));
	}
?>
</body>
</html>