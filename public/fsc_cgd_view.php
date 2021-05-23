<?php 
/*
已更新
电信-joseph
*/
include "../model/modelhead.php";
//解密
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
if($Id!=""){
	$StockResult = mysql_query("SELECT M.PurchaseID,M.DeliveryDate,M.Date,M.Remark,C.PreChar,
	P.FscNo,P.GysPayMode,
	I.Tel,I.Fax,I.Company,
	L.Name AS Linkman,L.Email,
	S.Name,S.Mail,S.ExtNo,
	C.Symbol
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId AND I.Type='2'
	LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=I.CompanyId AND L.Type=I.Type AND L.Defaults='0'
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN $DataPublic.staffmain S ON M.BuyerId=S.Number WHERE M.Id='$Id' ",$link_id);
	if ($myrow = mysql_fetch_array($StockResult)) {
		$Remark=$myrow["Remark"];
		$Remark=$Remark==""?"":"备注：".$Remark;
		$DeliveryDate=$myrow["DeliveryDate"]=="0000-00-00"?"另议":$myrow["DeliveryDate"];
		$PurchaseID=$myrow["PurchaseID"];
		$Provider=$myrow["Company"]."".$myrow["FscNo"];
		$Linkman=$myrow["Linkman"];
		$ExtNo=$myrow["ExtNo"];
		$Tel=$myrow["Tel"];
		$Fax=$myrow["Fax"];
		$Email=$myrow["Email"];
		$GysPayMode=$myrow["GysPayMode"]==1?"现金":"月结";
		$PreChar=$myrow["PreChar"];
		$Symbol=$myrow["Symbol"];
		$PaySTR=$Symbol.$GysPayMode;
		$Buyer=$myrow["Name"];
		$Mail=$myrow["Mail"];
		$Date=$myrow["Date"];
		}
//取得送货楼层
    $FloorResult=mysql_query("SELECT F.SendFloor FROM $DataIn.stuffdata F  
            LEFT JOIN $DataIn.cg1_stocksheet H ON F.StuffId=H.StuffId 
			WHERE H.Mid='$Id' LIMIT 1");
	if ($FloorRow = mysql_fetch_array( $FloorResult)) {
	   $SendFloor=$FloorRow["SendFloor"];
	   include "../model/subprogram/stuff_GetFloor.php";
	}
 //
	$StockFile=$PurchaseID;
include "../admin/subprogram/mycompany_info.php";
?>
<body>
<table style="width:720px;height:1030px;" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td style="height:60px"  colspan="2"><div align="center" class="TitleModel"><p>黑 云 采 购 单</p></div></td>
    </tr>
    <tr>
      	<td valign="middle"  class="A0100" style="width:520px;height:20px;">
        <div style='float:left;line-height:25px;'>请送货至：</div>
        <div style='height:20px;width:32px;background-color:#666;text-align:center;display:inline;float:left;'>
          <Span style='color:#FFF;Font-family:Arial;Font-size:20px;Font-weight:bold;letter-spacing:2px;line-height:20px;'><?php  echo $SendFloor;?></Span></div>
        </td>
   	  <td  class="A0100" width='200px'>采 购：<?php  echo $Buyer;?><br>Email：<?php  echo $Mail;?></td>
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
    	<td>交货日期：<?php  echo $DeliveryDate?></td>
  	</tr>
  	<tr>
    	<td  style="height:20px">&nbsp;&nbsp;传真号码：<?php  echo $Fax?></td>
    	<td>结付方式：<?php  echo $PaySTR?></td>
  	</tr>
    <tr align="left" valign="top">
      	<td colspan=2 class="A1000">
			<table border=0 cellpadding=0 cellspacing=0 style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
			  <tr>
				<td style="width:40px;height:30px" class="A0100" align="center">序号</td>
				<td colspan="2" style="width:415px" class="A0100" align="center">配件需求流水号/配件ID-配件名称/规格</td>
                                <td style="width:65px" class="A0100" align="right">计量单位</td>
				<td style="width:60px" class="A0100" align="right">单价</td>
				<td style="width:60px" class="A0100" align="right">数量</td>
				<td style="width:80px" class="A0100" align="right">金额</td>
			  </tr>
			    <?php 
				//更新配件需求表
				//记录数
				$TotalQty=0;//数量总数
				$TotalAmount=0;//金额总数
				$Result = mysql_query("SELECT S.StockId,S.StuffId,S.Price,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryDate,S.AddRemark,D.StuffCname,D.Spec,U.Name AS Unit 
                                        FROM $DataIn.cg1_stocksheet S 
                                        LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
                                        LEFT JOIN $DataPublic.stuffunit U  ON U.Id=D.Unit 
                                        WHERE  S.MId='$Id' ORDER BY D.StuffCname",$link_id);
				$k=1;
				if($myRow = mysql_fetch_array($Result)){		
					do{
						$StockId=$myRow["StockId"];
                                                $StuffId=$myRow["StuffId"];
						$DeliveryDate=$myRow["DeliveryDate"];
						$Title=$myRow["AddRemark"]==""?"":"title='$myRow[AddRemark]'";
						$Spec=$myRow["Spec"];
                                                $Unit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
						$StuffCname=$myRow["StuffCname"] ."&nbsp;"."$Spec" ;
						
						$Price=$myRow["Price"];
						$Qty=$myRow["Qty"];
						$TotalQty+=$Qty;
						$ThisAmount=sprintf("%.2f",$Qty*$Price);
						$TotalAmount=sprintf("%.2f",$TotalAmount+$ThisAmount);
					if($DeliveryDate=="0000-00-00"){
						$DeliveryDate="&nbsp;";
						}
					?>
			  <tr>
			  	<td align="center" class="A0100"><?php  echo $k;?></td>
				<td class="A0100"><img src="../model/codefun.php?CodeTemp=<?php  echo $StockId?>" <?php  echo $Title?>></td>
				<td class="A0100" ><?php  echo $StuffId . "-" .$StuffCname;?></td>
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
              <td colspan="6" class="A0100" align="right"><?php  echo $PreChar?> <?php  echo $TotalAmount?><br>(大写:<?php  echo $out;?>)</td>
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