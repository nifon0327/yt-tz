<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//解密
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
if($Id!=""){
	$StockResult = mysql_query("SELECT M.BillNumber,M.Date,M.Remark,C.PreChar,P.Forshort,I.Company,I.Address,I.ZIP,P.GysPayMode,I.Tel,I.Fax,C.Symbol
	FROM $DataIn.gys_shmain M 
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId AND I.Type='8'
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	WHERE M.Id='$Id' ",$link_id);
	if ($myrow = mysql_fetch_array($StockResult)) {
		$BillNumber=$myrow["BillNumber"];
		$Provider=$myrow["Forshort"];
		$proCompany=$myrow["Company"];
		$proAddress=$myrow["Address"];
		$Tel=$myrow["Tel"];
		$Fax=$myrow["Fax"];
         $ZIP=$myrow["ZIP"];
		$GysPayMode=$myrow["GysPayMode"]==1?"现金":"月结";
		$PreChar=$myrow["PreChar"];
		$Symbol=$myrow["Symbol"];
		$PaySTR=$Symbol.$GysPayMode;
		$Date=$myrow["Date"];
		$Remark=$myrow["Remark"];
		}
	$StockFile=$PurchaseID;
$checkNameSql=mysql_query("SELECT F.Id FROM $DataIn.stuffdata F LEFT JOIN $DataIn.cg1_stocksheet H ON F.StuffId=H.StuffId WHERE H.Mid='$Id' AND F.StuffCname LIKE '%FSC%' LIMIT 1",$link_id);
if($checkNameRow=mysql_fetch_array($checkNameSql)){
	$Provider=$Company;
	$FSC=$myrow["FSC"];
	$FSC="<div class='redB'>FSC证书：".$FSC."</div>";
	}

include "../model/subprogram/mycompany_info.php";

$S_Company = $AC==3?"上海市研砼贸易有限公司":$S_Company;
?>
<body>
<?php
	//若有上传自拍摄送货单，则显示下拉选项
	$hasImage = "no";
	$shImagePath = "../download/ckshbill/S$Id.jpg";
	if(file_exists($shImagePath))
	{
		$hasImage = "yes";
		echo "<select id='showType' onchange='changeType()'>";
		echo "<option value='0'>系统单</option>";
		echo "<option value='1'>原单</option>";
		echo "</select>";
	}

?>

<table id = 'system' style="width:720px;height:1080px" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td style="height:50px"  colspan="2"><div align="center" class="TitleModel"><p><?php  echo $Provider?>送货单</p></div></td>
    </tr>
    <tr>
      	<td style="width:600px;height:20px" class="A0100">&nbsp;</td>
   	  <td class="A0100">&nbsp;</td>
    </tr>
	<tr>
    	<td style="height:20px">&nbsp;&nbsp;客&nbsp;&nbsp;&nbsp;&nbsp;户：<?php  echo $S_Company?></td>
    	<td>送货单号：<?php  echo $BillNumber?></td>
  	</tr>
  	<tr>
    	<td style="height:20px">&nbsp;&nbsp;联系电话：<?php  echo $S_Tel?></td>
    	<td>创建日期：<?php  echo $Date?></td>
  	</tr>
  	<tr>
    	<td style="height:20px">&nbsp;&nbsp;传真号码：<?php  echo $S_Fax?></td>
    	<td>结付方式：<?php  echo $PaySTR?></td>
  	</tr>
         <tr>
    	<td  style="height:20px">&nbsp;&nbsp;公司地址：<?php  echo $Address?></td>
    	<td><?php  echo $FSC?></td>
  	</tr>
    <tr align="left" valign="top">
      	<td colspan=2 class="A1000">
			<table border=0 cellpadding=0 cellspacing=0 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
			  <tr>
				<td style="width:40px;height:30px" class="A0100" align="center">序号</td>
				<td colspan="2" class="A0100" align="center">配件需求流水号/配件名称</td>
				<td style="width:80px" class="A0100" align="right">订单总数</td>
				<td style="width:80px" class="A0100" align="right">本次送货数</td>
			  </tr>
			    <?php
				//更新配件需求表
				//记录数
				$TotalQty=0;//数量总数
				$TotalAmount=0;//金额总数
				$Result = mysql_query("SELECT 
				S.StockId,S.Qty,S.StuffId,S.SendSign,(G.AddQty+G.FactualQty) AS cgQty,D.StuffCname,D.SendFloor ,S.Estate
				FROM $DataIn.gys_shsheet S 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
				WHERE 1 AND S.MId='$Id' ORDER BY D.StuffCname",$link_id);
				$k=1;
				if($myRow = mysql_fetch_array($Result)){
					$SumQty=0;
					$SumcgQty=0;	$tempSign=0;
					do{
						$StockId=$myRow["StockId"];
						$Qty=$myRow["Qty"];
						$StuffId=$myRow["StuffId"];
						$SendFloor=$myRow["SendFloor"];
						$StuffCname=$myRow["StuffCname"];
						$cgQty=$myRow["cgQty"];
						$SendSign=$myRow["SendSign"];
						$Estate=$myRow["Estate"];
                        if($Estate==1)$tempSign++;
						$SignString="";
						switch ($SendSign){
							case 1:
							    $SignString="(补货)";
								$cgQty=0;
								break;
							case 2:
							    $SignString="(备品)";
								$cgQty=0;
								break;
							default:
								break;
						}
						$SumQty+=$Qty;
						$SumcgQty+=$cgQty;
					?>
			  <tr>
			  	<td align="center" class="A0100"><?php  echo $k;?></td>
				<td class="A0100"><img src="../model/codefun.php?CodeTemp=<?php  echo $StockId?>"></td>
				<td class="A0100" style="width:500px"><?php  echo $StuffId?><?php  echo $StuffCname.$SignString;?></td>
				<td class="A0100" align="right"><?php  echo $cgQty;?></td>
				<td class="A0100" align="right"><?php  echo $Qty;?></td>
			  </tr>
		  <?php
					$k++;
					}while ($myRow = mysql_fetch_array($Result));
				}//end if
			?>
            <tr>
              <td style="height:25px" class="A0100">合计</td>
              <td colspan="3" class="A0100" align="right"><?php  echo $SumcgQty?></td>
			  <td class="A0100" align="right"><?php  echo $SumQty?></td>
            </tr>
			<tr>

              <td colspan="5" class="A0000" align="left">&nbsp;&nbsp;&nbsp;&nbsp;</td>

            </tr>
			<tr>
                           <td style="height:25px" class="A0100">备注</td>
              <td colspan="4" class="A0100" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $Remark;?></td>

            </tr>
    <tr valign="top">
	  <td colspan="2" class="A0000"  valign="top">收货人:
     <?PHP
     if($tempSign==0){
          if($SendFloor==6){
             echo "陈连枝";
           }
          else echo "许吴祥";
      }
?></td>
      <td colspan="3" align="right" class="A0000"  valign="top">
	  <?php
if($tempSign==0 && $AC==''){
    echo "<div id='imgCss' style='filter:alpha(opacity=100);position:relative;z-index:1001;bottom:50px;left:-430px;'></div>";  //去除送货单印章 by.lwh
         // echo "<div id='imgCss' style='filter:alpha(opacity=100);position:relative;z-index:1001;bottom:50px;left:-430px;'><img  src='../images/officialseal-2.png' width='150px' height='150px'></div>";
}
	 echo $proCompany."<br>电话:".$Tel." 传真:".$Fax."<br>".$proAddress;
	  ?></td>
  </tr>
           </table>
		</td>
    </tr>
    <tr align="left" valign="top"><td style="height:10px" colspan=2></td></tr>
    <tr>
      	<td style="height:50px" colspan="2" align="right" class="A1000"></td>
    </tr>
  </table>
  <?php

	  if($hasImage == "yes")
	  {
	  	  echo "<div id='image' style='display:none;'>";
		  echo "<img  src= '$shImagePath' />";
		  echo "</div>";
	  }

  ?>
</body>
</html>
<?php
	}
else{
	echo "读取数据错误!";
	}
?>
<style>
</style>
<script type = "text/javascript" charset = 'utf-8'>

	function changeType()
	{
		var showType = document.getElementById("showType").value;
		if(showType == 1)
		{
			document.getElementById("image").style.display = "";
			document.getElementById("system").style.display = "none";
		}
		else
		{
			document.getElementById("image").style.display = "none";
			document.getElementById("system").style.display = "";
		}

	}

</script>