<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.freightdata
$DataIn.ch4_freight
$DatdIn.ch1_shipmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新中港运费记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
if($TempTypeId==1){
        $TempTable="ch1_shipmain";
		$UpSql="SELECT  M.Date,M.InvoiceNO,F.Id,F.Termini,F.ExpressNO,F.BoxQty,
		F.mcWG,F.Price,F.Amount,F.depotCharge,F.Remark,F.declarationCharge,F.checkCharge,
		F.PayType,F.carryCharge,F.xyCharge,F.wfqgCharge,F.ccCharge,F.djCharge,
		F.stopcarCharge,F.expressCharge,F.otherCharge,F.CarType,F.Volume
        FROM $DataIn.ch4_freight_declaration  F
        LEFT JOIN $DataIn.$TempTable M ON F.chId=M.Id
        WHERE 1 AND F.Id='$Id' LIMIT 1";
		$TypeName="Invoice";
		}
    else{ 
	    $TempTable="ch1_deliverymain";
		$UpSql="SELECT  M.DeliveryDate AS Date,M.DeliveryNumber AS InvoiceNO,F.Id,F.Termini,F.ExpressNO,F.PayType,
		F.BoxQty,F.mcWG,F.Price,F.Amount,F.depotCharge,F.Remark,F.declarationCharge,F.checkCharge,F.carryCharge,
		F.xyCharge,F.wfqgCharge,F.ccCharge,F.djCharge,F.stopcarCharge,F.expressCharge,F.otherCharge,F.CarType,F.Volume
        FROM $DataIn.ch4_freight_declaration  F
        LEFT JOIN $DataIn.$TempTable M ON F.chId=M.Id
        WHERE 1 AND F.Id='$Id' LIMIT 1";
		$TypeName="提货单";
	    }
//echo $UpSql;
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query($UpSql,$link_id));
$InvoiceNO=$upData["InvoiceNO"];
$CompanyId=$upData["CompanyId"];
$PayType=$upData["PayType"];
$Termini=$upData["Termini"];
$ExpressNO=$upData["ExpressNO"];
$BoxQty=$upData["BoxQty"];
$mcWG=$upData["mcWG"];
$Price=$upData["Price"];
$Amount=$upData["Amount"];
$depotCharge=$upData["depotCharge"];
$Remark=$upData["Remark"];
$declarationCharge=$upData["declarationCharge"];
$checkCharge=$upData["checkCharge"];
$InvoiceDate=$upData["InvoiceDate"];
$ETD=$upData["ETD"];
$Remark=$upData["Remark"];
$TempSTR="PayTypeSTR".strval($PayType); 
$$TempSTR="selected";	
$CarType=$upData["CarType"];
$Volume=$upData["Volume"];
$carryCharge=$upData["carryCharge"];
$xyCharge=$upData["xyCharge"];
$wfqgCharge=$upData["wfqgCharge"];
$ccCharge=$upData["ccCharge"];
$djCharge=$upData["djCharge"];
$stopcarCharge=$upData["stopcarCharge"];
$expressCharge=$upData["expressCharge"];
$otherCharge=$upData["otherCharge"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><input name="chId" type="hidden" id="chId">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="2">
		<tr>
            <td width="150" align="right">(Invoice/提货单)类型</td>
            <td><?php  echo $TypeName?></td>
		</tr>
        <tr>
            <td width="150" align="right">(Invoice/提货单)编号</td>
            <td><?php  echo $InvoiceNO?></td>
		</tr>
        <tr>
            <td align="right">货运公司</td>
            <td>
			<select name="CompanyId" id="CompanyId" style="width: 460px;">
			<?php 
			$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1'  AND MType=1 ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
				do{
			 		if($CompanyId==$fRow[CompanyId]){
						echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
						}
					else{
						echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
						}
					} while($fRow = mysql_fetch_array($fResult));
				}
			?></select></td>
          </tr>
          <!--<tr>
     		 <td align="right" height="30">费用结付&nbsp;</td>
     		 <td><select name="PayType" Id="PayType" size="1" style="width: 460px;">
     		     <option value="0" <?php echo $PayTypeSTR0?>>自付</option>
      		    <option value="1" <?php  echo $PayTypeSTR1?>>代付</option>
            </select></td>
 		 </tr>
          <tr>
            <td align="right">目 的 地</td>
            <td><input name="Termini" type="text" id="Termini" style="width: 460px;" value="<?php  echo $Termini?>" dataType="Require"  msg="未填写"></td>
          </tr>-->
          <tr>
            <td align="right">提单号码</td>
            <td><input name="ExpressNO" type="text" id="ExpressNO" style="width: 460px;" value="<?php  echo $ExpressNO?>" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量</td>
            <td><input name="mcWG" type="text" id="mcWG" style="width: 460px;" value="<?php  echo $mcWG?>" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td><div align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数</div></td>
            <td><input name="BoxQty" type="text" id="BoxQty" style="width: 460px;" value="<?php  echo $BoxQty?>" maxlength="10" dataType="Require"  msg="未填写"></td>
          </tr>
          <!--<tr>
            <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价</td>
            <td><input name="Price" type="text" id="Price" style="width: 460px;" value="<?php  echo $Price?>" dataType="Require"  msg="未填写" onblur="setAmount()"></td>
          </tr>-->
          
            <tr>
            <td align="right">体&nbsp;&nbsp;&nbsp;&nbsp;积</td>
            <td><input name="Volume" type="text" id="Volume" style="width: 460px;" value="<?php  echo $Volume?>" dataType="Require"  msg="未填写"></td>
          </tr>
          
          <tr>
            <td align="right">车&nbsp;&nbsp;&nbsp;&nbsp;型</td>
            <td><input name="CarType" type="text" id="CarType" style="width: 460px;"  value="<?php  echo $CarType?>" dataType="Require"  msg="未填写"></td>
          </tr>
          
          <tr>
            <td align="right">运&nbsp;&nbsp;&nbsp;&nbsp;费</td>
            <td><input name="Amount" type="text" id="Amount" style="width: 460px;" value="<?php  echo $Amount?>"  dataType="Require"  msg="未填写" onblur="setPrice()" ></td>
          </tr>

          <tr>
            <td align="right">入 仓 费</td>
            <td><input name="depotCharge" type="text" id="depotCharge" style="width: 460px;" value="<?php echo $depotCharge?>"></td>
          </tr>         
		   <tr>
            <td align="right">报 关 费</td>
            <td><input name="declarationCharge" type="text" id="declarationCharge" style="width: 460px;" value="<?php echo $declarationCharge?>"></td>
          </tr>
		   <tr>
            <td align="right">商 检 费</td>
            <td><input name="checkCharge" type="text" id="checkCharge" style="width: 460px;" value="<?php echo $checkCharge?>"></td>
          </tr>     
           <tr>
            <td align="right">搬 运 费</td>
            <td><input name="carryCharge" type="text" id="carryCharge" style="width: 460px;" value="<?php echo $carryCharge?>"></td>
          </tr>
		   <tr>
            <td align="right">续 页 费</td>
            <td><input name="xyCharge" type="text" id="xyCharge" style="width: 460px;" value="<?php echo $xyCharge?>"></td>
          </tr>
            <tr>
            <td align="right">无缝清关</td>
            <td><input name="wfqgCharge" type="text" id="wfqgCharge" style="width: 460px;" value="<?php echo $wfqgCharge?>"></td>
          </tr>
            <tr>
            <td align="right">仓 储 费</td>
            <td><input name="ccCharge" type="text" id="ccCharge" style="width: 460px;" value="<?php echo $ccCharge?>"></td>
          </tr>
            <tr>
            <td align="right">登 记 费</td>
            <td><input name="djCharge" type="text" id="djCharge" style="width: 460px;" value="<?php echo $djCharge?>"></td>
          </tr>
            <tr>
            <td align="right">停 车 费</td>
            <td><input name="stopcarCharge" type="text" id="stopcarCharge" style="width: 460px;" value="<?php echo $stopcarCharge ?>"></td>
          </tr>
            <tr>
            <td align="right">快 递 费</td>
            <td><input name="expressCharge" type="text" id="expressCharge" style="width: 460px;" value="<?php echo $expressCharge ?>"></td>
          </tr>
            <tr>
            <td align="right">其他费用</td>
            <td><input name="otherCharge" type="text" id="otherCharge" style="width: 460px;" value="<?php echo $otherCharge?>"></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="62" rows="4" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>          
        </table>
   </td>
  </tr>
 </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script language = "JavaScript"> 
	
function setAmount(){
	document.form1.Amount.value=(1.00*document.form1.mcWG.value*document.form1.Price.value).toFixed(2);		 
}

function setPrice(){
	if (document.form1.Price.value=="" && document.form1.mcWG.value!=""){
		document.form1.Price.value=(document.form1.Amount.value/document.form1.mcWG.value).toFixed(4);
	}
}
</script> 