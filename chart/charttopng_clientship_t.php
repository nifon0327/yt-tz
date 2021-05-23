<?php   
//独立已更新**********电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 客户出货分析");
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td>
<form name="form1" id="checkFrom" enctype="multipart/form-data" action="charttopng_clientship_a.php?CompanyId=<?php    echo $CompanyId?>" method="post" target="mainFrame">
<?php   
/*$clientResult = mysql_query("SELECT M.CompanyId,D.Forshort,SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount            
            FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		    LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
	        WHERE 1  AND D.cSign='7' AND DATE_FORMAT(M.Date,'%Y')>'2008' AND D.Estate=1 
			GROUP BY M.CompanyId ORDER BY Amount DESC",$link_id);
if($clientRow = mysql_fetch_array($clientResult)) {
echo "<select name='CID' id='CID' onChange='document.form1.submit()'>";
	do{			
		$thisCompanyId=$clientRow["CompanyId"];
		$Forshort=$clientRow["Forshort"];
        if($thisCompanyId==$CompanyId){
                   echo"<option value='$thisCompanyId|$Forshort' selected>$Forshort</option>";
                 }
         else{
		          echo"<option value='$thisCompanyId|$Forshort'>$Forshort</option>";
                 }
		}while ($clientRow = mysql_fetch_array($clientResult));
	
	}
echo "</select>&nbsp;";*/

 //月份
	  $ChooseMonth=$ChooseMonth==""?12:$ChooseMonth;
	  $MonthType="Month".$ChooseMonth;
	  $$MonthType="Selected";
	  echo "<select id='ChooseMonth' name='ChooseMonth' onChange='document.form1.submit()'>";
	  echo "<option value='6' $Month12>最近6个月</option>
	        <option value='12' $Month12>最近12个月</option>
	        <option value='24' $Month24>最近24个月</option>
			<option value='36' $Month36>最近36个月</option>
			</select>";
			
?>
</form>
</td></tr>
</table>
</body>
</html>