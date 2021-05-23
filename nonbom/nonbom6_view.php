<?php
//ewen 2013-03-13 OK
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
//解密
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
if($Id!=""){
	$StockResult = mysql_query("
		SELECT A.PurchaseID,A.CompanyId,A.BuyerId,A.Remark,A.Locks,A.Date,A.Operator,
		B.Forshort,S.Tel,
		C.Name,C.Mail,C.ExtNo,
		D.Symbol
	FROM $DataIn.nonbom6_cgmain A 
	LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId =A.CompanyId 
    LEFT JOIN $DataPublic.nonbom3_retailersheet S ON S.CompanyId =B.CompanyId  
	LEFT JOIN $DataPublic.staffmain C ON C.Number=A.BuyerId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=B.Currency
	WHERE A.Id='$Id' ",$link_id);
	if ($myrow = mysql_fetch_array($StockResult)) {
		$Remark=$myrow["Remark"];
		$Remark=$Remark==""?"":"备注：".$Remark;
		$PurchaseID=$myrow["PurchaseID"];
		$Provider=$myrow["Forshort"];
		$Symbol=$myrow["Symbol"];
		//$Linkman=$myrow["Linkman"];
		$Tel=$myrow["Tel"];
		$Buyer=$myrow["Name"];
		$Mail=$myrow["Mail"];
		$Date=$myrow["Date"];
		}
	//取得送货楼层
    $FloorResult=mysql_query("SELECT D.Remark
							 FROM $DataIn.nonbom6_cgsheet A
							 LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
							 LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=B.TypeId
							 LEFT JOIN $DataIn.base_mposition D ON D.Id=C.SendFloor
			WHERE A.Mid='$Id' LIMIT 1");
	if ($FloorRow = mysql_fetch_array( $FloorResult)) {
	   $SendFloor=$FloorRow["Remark"];
		}
 //
$StockFile=$PurchaseID;

$CompanyShortName=$_SESSION["Login_cSign"]==3?"皮套":"研砼";

//读取本公司信息
include "../model/subprogram/mycompany_info.php";
$SFwidth=strlen($SendFloor)*13;
?>
<body>
<table style="width:720px;height:1030px;" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td style="height:60px"  colspan="2" align="center" class="TitleModel"><?php  echo $CompanyShortName;?>非BOM采购单</td>
    </tr>
    <tr>
      	<td valign="middle"  class="A0100" style="width:520px;height:20px;">
        <div style='float:left;line-height:25px;'>请送货至：</div>
        <div style='height:20px;width:<?php echo $SFwidth; ?>;background-color:#666;text-align:center;display:inline;float:left;'>
          <Span style='color:#FFF;Font-family:Arial;Font-size:20px;Font-weight:bold;letter-spacing:2px;line-height:20px;'><?php  echo $SendFloor;?></Span></div>
        </td>
   	  <td  class="A0100" width='200'>采 购：<?php  echo $Buyer;?><br>Email：<?php  echo $Mail;?></td>
    </tr>
	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;供 应 商：<?php  echo $Provider?></td>
    	<td>采购单号：<?php  echo $PurchaseID?></td>
  	</tr>
  	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;接 洽 人：<?php  echo $Linkman?></td>
    	<td>采购日期：<?php  echo $Date?></td>
  	</tr>
  	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;联系电话：<?php  echo $Tel?></td>
    	<td>交货日期：另议</td>
  	</tr>
    <tr align="left" valign="top">
      	<td colspan=2 class="A1000">
			<table border=0 cellpadding=0 cellspacing=0 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
			  <tr>
				<td style="width:40px;height:30px" class="A0100" align="center">序号</td>
				<td colspan="2" style="width:415px" class="A0100" align="center">配件ID-配件名称</td>
                <td style="width:65px" class="A0100" align="right">单位</td>
				<td style="width:60px" class="A0100" align="right">单价</td>
				<td style="width:60px" class="A0100" align="right">数量</td>
				<td style="width:80px" class="A0100" align="right">金额</td>
			  </tr>
			    <?php
				$TotalQty=0;//数量总数
				$TotalAmount=0;//金额总数
				$Result = mysql_query("SELECT A.Id,A.GoodsId,A.Price,A.Qty,B.Unit,(A.Qty*A.Price) AS Amount ,A.Remark,B.GoodsName,B.BarCode
                                        FROM $DataIn.nonbom6_cgsheet A
                                        LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
                                        WHERE A.MId='$Id' ORDER BY B.GoodsName",$link_id);
         		$k=1;
				if($myRow = mysql_fetch_array($Result)){
					do{
						$BarCode=$myRow["BarCode"];
                        $GoodsId=$myRow["GoodsId"];
                        $Unit=$myRow["Unit"];
						$GoodsName=$myRow["GoodsName"];
						$Price=$myRow["Price"];
						$Qty=$myRow["Qty"];
						$TotalQty+=$Qty;
						$ThisAmount=sprintf("%.2f",$myRow["Amount"]);
						$TotalAmount=sprintf("%.2f",$TotalAmount+$ThisAmount);
						if($BarCode!=""){
							$BarCode="<img src='../model/codefun.php?CodeTemp=$BarCode'>";
							}
						else{
							$BarCode="<img src='../model/codefun.php?CodeTemp=$GoodsId'>";
							}
					?>
			  <tr>
			  	<td align="center" class="A0100"><?php  echo $k;?></td>
				<td class="A0100"><?php  echo $BarCode;?></td>
				<td class="A0100" ><?php  echo $GoodsId . "-" .$GoodsName;?></td>
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
            <tr>
              <td style="width:50px" class="A0100">合计</td>
              <td colspan="6" class="A0100" align="right"><?php  echo $TotalAmount?><br>(大写：<?php  echo $Symbol." ".$out;?>)</td>
            </tr>
			<tr>
              <td style="width:30px">&nbsp;</td>
              <td colspan="6"><span style="color:#F00"><?php  echo $Remark;?></span></td>
            </tr>
           </table>
		</td>
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