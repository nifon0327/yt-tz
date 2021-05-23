<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 应收货款统计");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=940;
$subTableWidth=910;
$i=1;
$Month        = date('n',strtotime("-1 month"));
$StartMonth = date('Y-01');
$EndMonth  = date('Y-m');
$StartDate    = date('Y-01-01');
$EndDate     = date('Y-m-01');
?>
<body>
<script>
function PageTo(P){
	document.form1.action="cw_clientfkcount_"+P+".php";
	document.form1.submit();
}
</script>

<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="50" colspan="2">应收货款统计</td>
    </tr>
    <tr>
		<td height="25"  >统计方式：
		  <input name="Type" type="radio" id="Type2" value="t" checked><LABEL for="Type2">年度报表</LABEL>
         <input name="Type" type="radio" id="Type1" value="read" onClick="PageTo(this.value)"><LABEL for="Type1">已收货款</LABEL>
      &nbsp;&nbsp;</td>
   	 <td   align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>

<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class='' align="center">
    <td width="35" height="25" class="A1111"> 序  号</td>
    <td width="50"  class="A1101">客户ID</td>
    <td width="80"  class="A1101">客户名称</td>
    <td width="80"  class="A1101">年初应收余额</td>
    <td width="80"  class="A1101">1 - <?php  echo $Month?>月应收金额</td>
	<td width="80"  class="A1101">1 - <?php  echo $Month?>月实收金额</td>
	<td width="80"  class="A1101"><?php  echo $Month?>月末应收余额</td>
  </tr>
<?php 
//读取全部客户
$myResult = mysql_query("
      SELECT M.CompanyId,C.Forshort,SUM( S.Price*S.Qty*M.Sign*D.Rate) AS AmountOrderby
					FROM ch1_shipmain M
					LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
					LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
					LEFT JOIN currencydata D ON D.Id = C.Currency
					WHERE M.Estate =0 AND  (M.cwSign IN (1,2)  AND DATE_FORMAT(M.Date,'%Y-%m')<'$EndMonth')  
					             OR (M.cwSign=0  AND DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth' AND DATE_FORMAT(M.Date,'%Y-%m')<'$EndMonth') 
					GROUP BY M.CompanyId ORDER BY AmountOrderby DESC 
",$link_id);
$Total1=0;$Total2=0;$Total3=0;$Total4=0;
if ($myRow = mysql_fetch_array($myResult)) {
     $i=1;
     do{
          $CompanyId = $myRow['CompanyId'];
          $Forshort      = $myRow['Forshort'];
          
         //读取未收货款
         $NoPayResult =  mysql_fetch_array(mysql_query("
                        SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS lastAmount
						FROM ch1_shipmain M
						LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
						LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
						LEFT JOIN currencydata D ON D.Id = C.Currency
						WHERE  M.CompanyId='$CompanyId'  AND M.cwSign IN (1,2)  AND DATE_FORMAT(M.Date,'%Y-%m')<'$StartMonth' 
			",$link_id));
          $LastAmount     = $NoPayResult['lastAmount'];
          
         //读取已付货款
         $PayedResult = mysql_fetch_array(mysql_query("
                           SELECT SUM(A.Amount*D.Rate) AS Amount,
                                        SUM(IF(DATE_FORMAT(A.ShipDate,'%Y-%m')<'$StartMonth',A.Amount*D.Rate,0)) AS lastAmount  
                             FROM (
	                             SELECT M.CompanyId,M.PayDate,SUM(S.Amount)  AS Amount,C.Date AS ShipDate  
		                         FROM cw6_orderinmain M
								LEFT JOIN cw6_orderinsheet S ON S.Mid=M.Id 
								LEFT JOIN ch1_shipmain C ON C.Id = S.chId
								WHERE M.CompanyId='$CompanyId' AND M.PayDate>='$StartDate' AND M.PayDate<'$EndDate' 
								GROUP BY M.Id,DATE_FORMAT(C.Date,'%Y-%m')  
			            )A
			            LEFT JOIN trade_object P ON P.CompanyId = A.CompanyId
					    LEFT JOIN currencydata D ON D.Id = P.Currency
					   ",$link_id));
           $PayedAmount = $PayedResult['Amount'];
           $LastAmount+=$PayedResult['lastAmount']; 
           $Total1+=$LastAmount;
           $Total3+=$PayedAmount;
           
          //读取应收货款
           $PayResult = mysql_fetch_array(mysql_query("
                           SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount
							FROM ch1_shipmain M
							LEFT JOIN ch1_shipsheet S ON S.Mid = M.Id
							LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
							LEFT JOIN currencydata D ON D.Id = C.Currency
							WHERE  M.CompanyId='$CompanyId'  AND DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth' AND DATE_FORMAT(M.Date,'%Y-%m')<'$EndMonth' 
			",$link_id));
           $PayAmount = $PayResult['Amount'];
           $NoPayAmount = $PayAmount +$LastAmount - $PayedAmount;
           
           $Total2+=$PayAmount;
           $Total4+= $NoPayAmount;
           
           $PayAmount = number_format($PayAmount,2);
           $LastAmount     = number_format($LastAmount,2);
           $PayedAmount = number_format($PayedAmount,2);
           $NoPayAmount= number_format($NoPayAmount,2);
           
           $PayAmount=$PayAmount==0?"&nbsp;":$PayAmount;
           $LastAmount=$LastAmount==0?"&nbsp;":$LastAmount;
           $PayedAmount=$PayedAmount ==0?"&nbsp;":$PayedAmount;
           $NoPayAmount=$NoPayAmount==0?"&nbsp;":$NoPayAmount;
           
           echo "<tr align='center'>
                     <td width='35' height='25' class='A0111'>$i</td>
				    <td width='50'  class='A0101' >$CompanyId</td>
				    <td width='80'  class='A0101'>$Forshort</td>
				    <td width='80'  class='A0101' align='right'>$LastAmount</td>
				    <td width='80'  class='A0101' align='right'>$PayAmount</td>
					<td width='80'  class='A0101' align='right'>$PayedAmount</td>
					<td width='80'  class='A0101' align='right'>$NoPayAmount</td>
	              </tr>";
           $i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
           
           $Total1  = number_format($Total1,2);
           $Total2 = number_format($Total2,2);
           $Total3  = number_format($Total3,2);
           $Total4  = number_format($Total4,2);
           
           $Total1=$Total1==0?"&nbsp;":$Total1;
           $Total2=$Total2==0?"&nbsp;":$Total2;
           $Total3=$Total3==0?"&nbsp;":$Total3;
           $Total4=$Total4==0?"&nbsp;":$Total4;
           
	 echo "<tr align='center'>
                     <td width='35' height='25' class='A0111' colspan='3'>合计</td>
				    <td width='80'  class='A0101' align='right'>$Total1</td>
				    <td width='80'  class='A0101' align='right'>$Total2</td>
					<td width='80'  class='A0101' align='right'>$Total3</td>
					<td width='80'  class='A0101' align='right'>$Total4</td>
	              </tr>";
?>
</table>

</form>
</body>
</html>