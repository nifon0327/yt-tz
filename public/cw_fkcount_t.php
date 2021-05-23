<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 应付货款统计报表");
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
	document.form1.action="cw_fkcount_"+P+".php";
	document.form1.submit();
	}
</script>
<form name="form1" method="post" action="">
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr align="center">
		<td height="25" colspan="4">应付货款统计报表</td>
    </tr>

	<tr>
		<td height="25" colspan="2" >统计方式：
		<input name="Type" type="radio" id="Type4" value="t"  checked><LABEL for="Type4">年度报表</LABEL>
		<input name="Type" type="radio"  id="Type2" value="g"  onClick="PageTo(this.value)"><LABEL for="Type2">供应商</LABEL>
      	<input name="Type" type="radio" id="Type1" value="m" onClick="PageTo(this.value)"><LABEL for="Type1">请款月份</LABEL>
      	<input name="Type" type="radio" id="Type3" value="p" onClick="PageTo(this.value)"><LABEL for="Type3">结付月份</LABEL>
      	&nbsp;&nbsp;</td>
   	 <td colspan="2" align="right">统计日期:<?php  echo date("Y年m月d日")?></td>
    </tr>
</table>
<table width="<?php  echo $tableWidth?>" border="0" cellspacing="0"  bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr class='' align="center">
    <td width="35" height="25" class="A1111"> 序  号</td>
    <td width="50"  class="A1101">供应商ID</td>
    <td width="80"  class="A1101">供应商名称</td>
    <td width="80"  class="A1101">年初应付余额</td>
    <td width="80"  class="A1101">1 - <?php  echo $Month?>月应付金额</td>
	<td width="80"  class="A1101">1 - <?php  echo $Month?>月实付金额</td>
	<td width="80"  class="A1101"><?php  echo $Month?>月末应付余额</td>
  </tr>
<?php 
//读取全部供应商
$myResult = mysql_query("SELECT A.CompanyId,A.Forshort,SUM(Amount) AS Amount  
       FROM (
				SELECT S.CompanyId,P.Forshort,SUM(S.Amount*C.Rate) AS Amount  
				FROM $DataIn.cw1_fkoutsheet S
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				where  (S.Estate=3 AND S.Month<'$EndMonth')  OR (S.Estate=0  AND S.Month>='$StartMonth' AND S.Month<'$EndMonth')
				GROUP BY S.CompanyId 
        UNION ALL 
				SELECT M.CompanyId,P.Forshort,0 AS Amount 
				FROM $DataIn.cw1_fkoutmain M
				LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId
				where  M.Date>='$StartDate' AND M.Date<'$EndDate' 
				GROUP BY M.CompanyId 
          )A WHERE A.Amount>0  GROUP BY A.CompanyId ORDER BY Amount DESC 
",$link_id);
$Total1=0;$Total2=0;$Total3=0;$Total4=0;
if ($myRow = mysql_fetch_array($myResult)) {
     $i=1;
     do{
          $CompanyId = $myRow['CompanyId'];
          $Forshort      = $myRow['Forshort'];
          
         //读取未付货款
         $NoPayResult =  mysql_fetch_array(mysql_query("
                        SELECT SUM(S.Amount*C.Rate) AS lastAmount 
						FROM $DataIn.cw1_fkoutsheet S
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
						LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
						WHERE  S.CompanyId='$CompanyId' AND S.Estate=3 AND S.Month<'$StartMonth'
			",$link_id));
          $LastAmount     = $NoPayResult['lastAmount'];
          
         //读取已付货款
         $PayedResult = mysql_fetch_array(mysql_query("
                           SELECT SUM(S.Amount*C.Rate) AS Amount,SUM(IF(S.Month<'$StartMonth',S.Amount*C.Rate,0)) AS lastAmount 
							FROM $DataIn.cw1_fkoutmain M  
							LEFT JOIN $DataIn.cw1_fkoutsheet S ON M.Id=S.Mid 
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
							WHERE M.CompanyId='$CompanyId' AND M.Date>='$StartDate' AND M.Date<'$EndDate'
			",$link_id));
           $PayedAmount = $PayedResult['Amount'];
           $LastAmount+=$PayedResult['lastAmount']; 
           $Total1+=$LastAmount;
           $Total3+=$PayedAmount;
           
          //读取应付货款
           $PayResult = mysql_fetch_array(mysql_query("
                           SELECT SUM(S.Amount*C.Rate) AS Amount 
							FROM  $DataIn.cw1_fkoutsheet S 
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
							WHERE S.CompanyId='$CompanyId' AND S.Estate IN (0,3) AND S.Month>='$StartMonth' AND S.Month<'$EndMonth' 
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