<?php
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新Forward杂费记录");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("
SELECT M.InvoiceNO,F.CompanyId,
F.HoldNO,F.ForwardNO,F.BoxQty,F.mcWG,F.forwardWG,F.Volume,F.HKVolume,F.VolumeKG,F.HKVolumeKG,F.Amount,F.InvoiceDate,
F.ETD,F.Remark,F.PayType,F.ShipType,
F.CFSCharge,F.THCCharge,F.WJCharge,F.SXCharge,F.ENSCharge,F.BXCharge,F.GQCharge,F.DFCharge,F.TDCharge,F.OtherCharge
FROM $DataIn.ch3_forward F
LEFT JOIN $DataIn.ch1_shipmain M ON F.chId=M.Id
WHERE 1 AND F.Id='$Id'
LIMIT 1",$link_id));
$InvoiceNO=$upData["InvoiceNO"];
$CompanyId=$upData["CompanyId"];
$HoldNO=$upData["HoldNO"];
$ForwardNO=$upData["ForwardNO"];
$BoxQty=$upData["BoxQty"];
$mcWG=$upData["mcWG"];
$forwardWG=$upData["forwardWG"];
$Volume=$upData["Volume"];
$HKVolume=$upData["HKVolume"];
$VolumeKG=$upData["VolumeKG"];
$HKVolumeKG=$upData["HKVolumeKG"];
$Amount=$upData["Amount"];
$CFSCharge=$upData["CFSCharge"];
$THCCharge=$upData["THCCharge"];
$WJCharge=$upData["WJCharge"];
$SXCharge=$upData["SXCharge"];
$ENSCharge=$upData["ENSCharge"];
$BXCharge=$upData["BXCharge"];
$GQCharge=$upData["GQCharge"];
$DFCharge=$upData["DFCharge"];
$TDCharge=$upData["TDCharge"];
$OtherCharge=$upData["OtherCharge"];
$ShipType=$upData["ShipType"];

if($ShipType ==1){
	$SeaStyle ="style='display:none;'";
	$AirStyle = "";
}else if($ShipType ==2){
	$SeaStyle = "";
	$AirStyle ="style='display:none;'";
}else{
	$AirStyle ="style='display:none;'";
	$SeaStyle ="style='display:none;'";
}

$InvoiceDate=$upData["InvoiceDate"];
$PayType=$upData["PayType"];
$ETD=$upData["ETD"];
$Remark=$upData["Remark"];
$TempSTR="PayTypeSTR".strval($PayType);
$$TempSTR="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="2">
        <tr><td width="180" align="right">Invoice编号</td><td><?php  echo $InvoiceNO?></td>
        <input id="ShipType" name="ShipType" type="hidden" value="<?php  echo $ShipType?>">
        </tr>
		<tr>
            <td align="right">forward公司</td>
            <td>
			<select name="CompanyId" id="CompanyId" style="width:380px;" onchange="getForwardCharge()" dataType="Require"  msg="未填写">
			<?php
			$fResult = mysql_query("SELECT * FROM $DataPublic.freightdata WHERE Estate='1'  AND MType=2   ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
				do{
			 		if($CompanyId==$fRow["CompanyId"]){
						echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
						}
					else{
						echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
						}
					} while ($fRow = mysql_fetch_array($fResult));
				}
			?></select></td>
          </tr>
          <tr>
     		 <td align="right" height="30">费用结付&nbsp;</td>
     		 <td><select name="PayType" Id="PayType" size="1" style="width:380px;">
     		     <option value="0" <?php  echo $PayTypeSTR0?>>自付</option>
      		    <option value="1" <?php  echo $PayTypeSTR1?>>代付</option>
            </select></td>
 		 </tr>
          <tr>
            <td align="right">forward Invoice</td>
            <td><input name="ForwardNO" type="text" id="ForwardNO" value="<?php  echo $ForwardNO?>" style="width:380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">入仓号</td>
            <td><input name="HoldNO" type="text" id="HoldNO" value="<?php  echo $HoldNO?>" style="width:380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td><div align="right">发票日期</div></td>
            <td><input name="InvoiceDate" type="text" id="InvoiceDate" onfocus="WdatePicker()" value="<?php  echo $InvoiceDate?>" style="width:380px;" maxlength="10"  dataType="Require"  msg="未填写" readonly></td>
          </tr>
          <tr>
            <td align="right">研砼称重</td>
            <td><input name="mcWG" type="text" id="mcWG" style="width:380px;" value="<?php  echo $mcWG?>" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">上海称重</td>
            <td><input name="forwardWG" type="text" id="forwardWG" style="width:380px;" value="<?php  echo $forwardWG?>"  onblur="changeCharge()"dataType="Require"  msg="未填写"></td>
          </tr>

          <tr>
            <td align="right">研砼体积</td>
            <td><input name="Volume" type="text" id="Volume" value="<?php  echo $Volume?>" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">上海体积</td>
            <td><input name="HKVolume" type="text" id="HKVolume" value="<?php  echo $HKVolume?>" onblur="changeCharge()" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">研砼体积重</td>
            <td><input name="VolumeKG" type="text" id="VolumeKG" value="<?php  echo $VolumeKG?>" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">上海体积重</td>
            <td><input name="HKVolumeKG" type="text" id="HKVolumeKG" value="<?php  echo $HKVolumeKG?>" onblur="changeCharge()"  style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">件&nbsp;&nbsp;数</td>
            <td><input name="BoxQty" type="text" id="BoxQty" style="width:380px;" value="<?php  echo $BoxQty?>" dataType="Require"  msg="未填写"></td>
          </tr>

          <tr>
            <td align="right">金&nbsp;&nbsp;额</td>
            <td><input name="Amount" type="text" id="Amount" style="width:380px;" value="<?php  echo $Amount?>" dataType="Require"  msg="未填写"><input id="tempDiff" name="tempDiff" type="text" size="8" readonly>差异</td></td>
          </tr>
          <tr>
            <td align="right" valign="top">ETD/ETA</td>
            <td><textarea name="ETD" cols="51" rows="3" id="ETD"><?php  echo $ETD?></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="51" rows="2" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>

          <tr id='AirForwardCharge' <?php echo $AirStyle?>>
            <td colspan="4"><table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	          <tr>
	            <td colspan="2"><div align="center">Forward空运标准收费</div></td>
	          </tr>
	          <tr>
	            <td align="right"  width="175" height="25" >CFS费</td>
	            <td ><input name="CFSCharge1" type="text" id="CFSCharge1" value="<?php echo $CFSCharge?>" style="width:380px;" >
	                  <input id="tempCFSCharge1" name="tempCFSCharge1" type="text" size="6" readonly>
	            </td>
	          </tr>
	          <tr>
	            <td align="right" height="25">THC费</td>
	            <td ><input name="THCCharge1" type="text" id="THCCharge1" value="<?php echo $THCCharge?>" style="width:380px;" >
	                 <input id="tempTHCCharge1" name="tempTHCCharge1" type="text" size="6" readonly>
	            </td>
	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge1" type="text" id="WJCharge1" value="<?php echo $WJCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge1" type="text" id="SXCharge1" value="<?php echo $SXCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge1" type="text" id="ENSCharge1" value="<?php echo $ENSCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">过桥费</td>
	            <td ><input name="GQCharge1" type="text" id="GQCharge1" value="<?php echo $GQCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge1" type="text" id="TDCharge1" value="<?php echo $TDCharge?>" style="width:380px;" ></td>
	          </tr>
	           <tr>
	            <td align="right" height="25">其它费用</td>
	            <td ><input name="OtherCharge1" type="text" id="OtherCharge1" onblur="changeCharge()" value="<?php echo $OtherCharge?>" value="0"  style="width:380px;" ></td>
	          </tr>
	        </table>
         </td>
       </tr>
	   <!- ------------------------------------------Forward海运标准收费 -->
	      <tr id='SeaForwardCharge' <?php echo $SeaStyle?> >
            <td colspan="4"><table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	          <tr>
	            <td colspan="2"><div align="center">Forward海运标准收费</div></td>
	          </tr>
	         <tr>
	            <td align="right"  width="175" height="25">CFS费</td>
	            <td ><input name="CFSCharge2" type="text" id="CFSCharge2" value="<?php echo $CFSCharge?>" style="width:380px;" >
	                <input id="tempCFSCharge2" name="tempCFSCharge2" type="text" size="6" readonly>
	            </td>
	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge2" type="text" id="WJCharge2" value="<?php echo $WJCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge2" type="text" id="SXCharge2" value="<?php echo $SXCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">保险费</td>
	            <td ><input name="BXCharge2" type="text" id="BXCharge2"  value="<?php echo $BXCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge2" type="text" id="ENSCharge2"  value="<?php echo $ENSCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">电放费</td>
	            <td ><input name="DFCharge2" type="text" id="DFCharge2"  value="<?php echo $DFCharge?>" style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge2" type="text" id="TDCharge2"  value="<?php echo $TDCharge?>" style="width:380px;" ></td>
	          </tr>
	           <tr>
	            <td align="right" height="25">其它费用</td>
	            <td ><input name="OtherCharge2" type="text" id="OtherCharge2" onblur="changeCharge()" value="<?php echo $OtherCharge?>" style="width:380px;" ></td>
	          </tr>
            </table>
         </td>
       </tr>


        </table>
   </td>
  </tr>
 </table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>


function  getForwardCharge(){
    var CompanyId = document.getElementById("CompanyId").value;
    var HKVolume  = parseFloat(document.getElementById("HKVolume").value);
    var HKVolumeKG  = parseFloat(document.getElementById("HKVolumeKG").value);
    var forwardWG  = parseFloat(document.getElementById("forwardWG").value);
    var ShipType  = document.getElementById("ShipType").value;
    var maxHkWG = forwardWG>HKVolumeKG?forwardWG:HKVolumeKG;

    if(ShipType==1){
	    document.getElementById("AirForwardCharge").style.display = "";
	    document.getElementById("SeaForwardCharge").style.display = "none";
    }else if (ShipType ==2){
	    document.getElementById("AirForwardCharge").style.display = "none";
	    document.getElementById("SeaForwardCharge").style.display = "";
    }
    else{
	    return false;
    }

	var url="ch_freightinfo_ajax.php?CompanyId="+CompanyId+"&ShipType="+ShipType;
    var ajax=InitAjax();
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){//
		       var BackData = ajax.responseText;
		       var tempArray = BackData.split("|");
		       switch(ShipType){
			       case "1":
			            document.getElementById("CFSCharge1").value  = parseFloat(tempArray[0])*maxHkWG;
			            document.getElementById("tempCFSCharge1").value  = tempArray[0];
			            document.getElementById("THCCharge1").value  = parseFloat(tempArray[1])*maxHkWG;
			             document.getElementById("tempTHCCharge1").value  = tempArray[1];
			            document.getElementById("WJCharge1").value   = tempArray[2];
			            document.getElementById("SXCharge1").value   = tempArray[3];
			            document.getElementById("ENSCharge1").value  = tempArray[4];
			            document.getElementById("GQCharge1").value   = tempArray[6];
			            document.getElementById("TDCharge1").value   = tempArray[8];
			            document.getElementById("CFSCharge2").value   = 0.00;
			            document.getElementById("WJCharge2").value    = 0.00;
			            document.getElementById("SXCharge2").value    = 0.00;
			            document.getElementById("BXCharge2").value    = 0.00;
			            document.getElementById("ENSCharge2").value   = 0.00;
			            document.getElementById("DFCharge2").value    = 0.00;
			            document.getElementById("TDCharge2").value    = 0.00;
			            document.getElementById("OtherCharge2").value = 0.00;
			            var Amount = parseFloat(tempArray[0])+parseFloat(tempArray[1])+parseFloat(tempArray[2])+parseFloat(tempArray[3])+parseFloat(tempArray[4])+parseFloat(tempArray[6])+parseFloat(tempArray[8]);
			            document.getElementById("Amount").value = Amount.toFixed(2);
			       break;
			       case "2":
			            document.getElementById("CFSCharge2").value  = parseFloat(tempArray[0])*HKVolume;
			            document.getElementById("tempCFSCharge2").value  = tempArray[0];
			            document.getElementById("WJCharge2").value   = tempArray[2];
			            document.getElementById("SXCharge2").value   = tempArray[3];
			            document.getElementById("BXCharge2").value   = tempArray[5];
			            document.getElementById("ENSCharge2").value  = tempArray[4];
			            document.getElementById("DFCharge2").value   = tempArray[7];
			            document.getElementById("TDCharge2").value   = tempArray[8];
			            document.getElementById("CFSCharge1").value  = 0.00;
			            document.getElementById("THCCharge1").value  = 0.00;
			            document.getElementById("WJCharge1").value   = 0.00;
			            document.getElementById("SXCharge1").value   = 0.00;
			            document.getElementById("ENSCharge1").value  = 0.00;
			            document.getElementById("GQCharge1").value   = 0.00;
			            document.getElementById("TDCharge1").value   = 0.00;
			            document.getElementById("OtherCharge1").value = 0.00;
			            var Amount = parseFloat(tempArray[0])+parseFloat(tempArray[2])+parseFloat(tempArray[3])+parseFloat(tempArray[4])+parseFloat(tempArray[5])+parseFloat(tempArray[7])+parseFloat(tempArray[8]);
			            document.getElementById("Amount").value = Amount.toFixed(2);
			       break;
		       }
			}
		}
	ajax.send(null);
}


function  changeCharge(){

	var tempdiff =0;
    var mcWG   = parseFloat(document.getElementById("mcWG").value);
    var forwardWG = parseFloat(document.getElementById("forwardWG").value);
    var Volume  = parseFloat(document.getElementById("Volume").value);
	var HKVolume  = parseFloat(document.getElementById("HKVolume").value);
    var HKVolumeKG  = parseFloat(document.getElementById("HKVolumeKG").value);
    var VolumeKG  = parseFloat(document.getElementById("VolumeKG").value);
    var ShipType  = document.getElementById("ShipType").value;
    var maxHkWG = forwardWG>HKVolumeKG?forwardWG:HKVolumeKG;


	if(maxHkWG>0 && ShipType ==1){
	    var CFSCharge1 = document.getElementById("tempCFSCharge1").value;
	    var THCCharge1 = document.getElementById("tempTHCCharge1").value;
		document.getElementById("CFSCharge1").value = (parseFloat(CFSCharge1)*maxHkWG).toFixed(2);
		document.getElementById("THCCharge1").value = (parseFloat(THCCharge1)*maxHkWG).toFixed(2);
		if(forwardWG>HKVolumeKG){

		if(forwardWG>mcWG) tempdiff = (forwardWG/mcWG * 100).toFixed(2);
		    else  tempdiff = (mcWG/forwardWG*100).toFixed(2);

		}else{

			if(HKVolumeKG>VolumeKG) tempdiff = (HKVolumeKG/VolumeKG * 100).toFixed(2);
		    else  tempdiff = (VolumeKG/HKVolumeKG*100).toFixed(2);
		}
		document.getElementById("tempDiff").value = tempdiff + "%";

	 }


	 if(HKVolume>0 && ShipType ==2){
	    var CFSCharge2 = document.getElementById("tempCFSCharge2").value;
		document.getElementById("CFSCharge2").value  = (parseFloat(CFSCharge2)*HKVolume).toFixed(2);

		if(HKVolume>Volume) tempdiff = (HKVolume/Volume * 100).toFixed(2);
		else  tempdiff = (Volume/HKVolume*100).toFixed(2);
		document.getElementById("tempDiff").value = tempdiff + "%";
	 }
	 totalAmount(ShipType);
}


function  totalAmount(ShipType){
	switch(ShipType){

		case "1":
			 var CFSCharge   = document.getElementById("CFSCharge1").value;
             var THCCharge   = document.getElementById("THCCharge1").value;
             var WJCharge   = document.getElementById("WJCharge1").value;
             var SXCharge   = document.getElementById("SXCharge1").value;
             var ENSCharge   = document.getElementById("ENSCharge1").value;
             var GQCharge   = document.getElementById("GQCharge1").value;
             var TDCharge   = document.getElementById("TDCharge1").value;
	         var OtherCharge = document.getElementById("OtherCharge1").value;
	         var Amount = parseFloat(CFSCharge)+parseFloat(THCCharge)+parseFloat(WJCharge)+parseFloat(SXCharge)+parseFloat(ENSCharge)+parseFloat(GQCharge)+parseFloat(TDCharge)+parseFloat(OtherCharge);
			 document.getElementById("Amount").value = Amount.toFixed(2);

		break;
		case "2":
			 var CFSCharge   = document.getElementById("CFSCharge2").value;
             var WJCharge   = document.getElementById("WJCharge2").value;
             var SXCharge   = document.getElementById("SXCharge2").value;
             var BXCharge   = document.getElementById("BXCharge2").value;
             var ENSCharge   = document.getElementById("ENSCharge2").value;
             var DFCharge   = document.getElementById("DFCharge2").value;
             var TDCharge   = document.getElementById("TDCharge2").value;
	         var OtherCharge = document.getElementById("OtherCharge2").value;
	         var Amount = parseFloat(CFSCharge)+parseFloat(WJCharge)+parseFloat(SXCharge)+parseFloat(BXCharge)+parseFloat(ENSCharge)+parseFloat(DFCharge)+parseFloat(TDCharge)+parseFloat(OtherCharge);
			 document.getElementById("Amount").value = Amount.toFixed(2);

		break;

	}
</script>