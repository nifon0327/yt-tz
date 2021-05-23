<?php
//ewen 2013-03-25
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<link rel='stylesheet' href='../model/SearchDiv.css'>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='./model/checkform.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";
//读取结付主表内容
$StockResult = mysql_query("
	SELECT A.Id,A.BankId,A.CompanyId,A.PayDate,A.PayAmount,A.djAmount,A.Payee,A.Receipt,A.Checksheet,A.Remark,A.Locks,A.Date,A.Operator,
	B.Forshort,B.Linkman,B.Tel,
	C.Name,C.Mail,C.ExtNo,
	D.Symbol
	FROM $DataIn.nonbom12_cwmain A 
	LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId =A.CompanyId 
	LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Operator
	LEFT JOIN $DataPublic.currencydata D ON D.Id=B.Currency
	WHERE A.Id='$d' ",$link_id);
if ($myrow = mysql_fetch_array($StockResult)) {
	$Provider=$myrow["Forshort"];
	$Linkman=$myrow["Linkman"];
	$Tel=$myrow["Tel"];
	$Remark=$myrow["Remark"];
	$Remark=$Remark==""?"":"备注：".$Remark;

	$Buyer=$myrow["Name"];
	$Mail=$myrow["Mail"];
	$Id=$myrow["Id"];
	$PayDate=$myrow["PayDate"];
	$Symbol=$myrow["Symbol"];
	$CompanyShortName=$_SESSION["Login_cSign"]==3?"皮套":"研砼";
	//读取本公司信息
	include "../model/subprogram/mycompany_info.php";
?>
<body>
<table style="width:720px;height:1030px;" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td style="height:60px"  colspan="2" align="center" class="TitleModel"><?php  echo $CompanyShortName;?>非BOM结付单</td>
    </tr>
    <tr>
      	<td valign="middle"  class="A0100" style="width:520px;height:20px;">&nbsp;
        </td>
   	  <td  class="A0100" width='200'>财 务：<?php  echo $Buyer;?><br>Email：<?php  echo $Mail;?></td>
    </tr>
	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;供 应 商：<?php  echo $Provider?></td>
    	<td>结付单号：<?php  echo $Id?></td>
  	</tr>
  	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;接 洽 人：<?php  echo $Linkman?></td>
    	<td>结付日期：<?php  echo $PayDate?></td>
  	</tr>
  	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;联系电话：<?php  echo $Tel?></td>
    	<td>结付货币：<?php echo $Symbol;?></td>
  	</tr>
    <tr align="left" valign="top">
      	<td colspan=2 class="A1000">
			<table border=0 cellpadding=0 cellspacing=0 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
			  <tr>
				<td width="50" align="center" class="A0100" style="width:40px;height:30px">序号</td>
				<td colspan="2" style="width:415px" class="A0100" align="center">配件ID-配件名称</td>
                                <td width="65" align="right" class="A0100" style="width:65px">单位</td>
				<td width="60" align="right" class="A0100" style="width:60px">单价</td>
				<td width="60" align="right" class="A0100" style="width:60px">数量</td>
				<td width="80" align="right" class="A0100" style="width:80px">金额</td>
			  </tr>
			    <?php
				$TotalQty=0;//数量总数
				$TotalAmount=0;//金额总数
				$Result = mysql_query("SELECT A.GoodsId,A.Price,A.Qty,A.Amount,B.Unit,B.GoodsName,B.BarCode
                                        FROM $DataIn.nonbom12_cwsheet A
                                        LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
                                        WHERE A.MId='$Id' ORDER BY B.GoodsName",$link_id);
         		$k=1;
				if($myRow = mysql_fetch_array($Result)){
					do{
						$SId=$myRow["Id"];
                        $GoodsId=$myRow["GoodsId"];
						$GoodsName=$myRow["GoodsName"];
                        $Unit=$myRow["Unit"];
						$BarCode=$myRow["BarCode"];
						$Price=$myRow["Price"];
						$Qty=$myRow["Qty"];
						$TotalQty+=$Qty;
						$ThisAmount=sprintf("%.2f",$myRow["Amount"]);
						$TotalAmount=sprintf("%.2f",$TotalAmount+$ThisAmount);
					?>
			  <tr valign="bottom">
			  	<td align="center" class="A0100"><?php  echo $k;?></td>
				<td width="156" class="A0100"><img src="../model/codefun.php?CodeTemp=<?php  echo $BarCode?>"></td>
				<td width="259" class="A0100" ><?php  echo $GoodsId . "-" .$GoodsName;?></td>
                                <td class="A0100" align="right"><?php  echo $Unit;?></td>
				<td class="A0100" align="right"><?php  echo $Price;?></td>
				<td class="A0100" align="right"><?php  echo $Qty;?></td>

				<td class="A0100" align="right"><?php  echo $ThisAmount;?></td>
			  </tr>
		  <?php
					$k++;
					}while ($myRow = mysql_fetch_array($Result));
				}//end if
				$out=num2rmb($TotalAmount);
			?>
            <tr  valign="bottom">
              <td style="width:50px;height:50px;" class="A0100">合计</td>
              <td colspan="6" class="A0100" align="right"><?php  echo $TotalAmount?><br>(大写：<?php  echo $Symbol." ".$out;?>)</td>
            </tr>
			<tr>
              <td style="width:30px">&nbsp;</td>
              <td colspan="6"><span style="color:#F00"><?php  echo $Remark;?></span></td>
            </tr>
           </table>
           <?php
           if($myrow["Payee"]){
				$Payee="../download/cwnonbom/P".$d.".jpg";
				echo "<img src=".$Payee." width='720' height='300'>";
		  	 	}
		   ?>
		&nbsp; </td>
    </tr>
    <tr align="left" valign="top"><td style="height:10px" colspan=2></td></tr>
    <tr>
      	<td style="height:50px" colspan="2" align="right" class="A1000">
	  <?php
	    if (strlen($S_Tel)<20) $S_Tel=$ExtNo!=""?$S_Tel."-".$ExtNo:$S_Tel;
	    if (strlen($S_Fax)<20) $S_Fax=$ExtNo!=""?$S_Fax."-".$ExtNo:$S_Fax;
	 echo $S_Company."<br>".$S_Address." 邮政编码:".$S_ZIP."<br>电话:".$S_Tel ." 传真:".$S_Fax;
	  ?></td>
    </tr>
  </table>
</body>
</html>
<?php
	}
else{
	echo "读取数据错误!";
	}
?>