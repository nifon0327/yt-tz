<?php
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增Forward杂费");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=800;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<script language = "JavaScript">
function ViewShipId(){
	var r=Math.random();
	TypeId=document.getElementById("TypeId").value;
	if(TypeId==""){alert("请选择类型");return false;}
    if(TypeId==1){
	      var BackData=window.showModalDialog("ch_shipmain_s1.php?r="+r+"&tSearchPage=ch_shipmain&fSearchPage=ch_shipforward&SearchNum=2&Action=3","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
		}
	else{
	      var BackData=window.showModalDialog("ch_shipout_bill_s1.php?r="+r+"&tSearchPage=ch_shipout_bill_s1&fSearchPage=ch_shipforward&SearchNum=2&Action=4","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	    }
// alert(BackData);
	if(BackData){
	    var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;
		var mcWG1=0;
		var BoxQty1=0;
		var Volume1= 0;
		var VolumeKG1 = 0 ;
		var ShipType = 0 ;
		if(document.getElementById("TempMaxNumber")){
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		    }
		document.form1.InvoiceNO.value="";
		document.form1.chId.value="";
	for(var i=0;i<Rowslength;i++){
		var FieldTemp=Rows[i];
		//alert(FieldTemp);
		var CL=FieldTemp.split("^^");
		   if(Rowslength==1){
		       document.form1.chId.value=CL[0];
		       document.form1.InvoiceNO.value=CL[1];
		       }
		   else{
		       document.form1.chId.value=document.form1.chId.value+"^^"+CL[0];
		       document.form1.InvoiceNO.value=document.form1.InvoiceNO.value+"^^"+CL[1];
		       }//end if(Rowslength==1)
			   mcWG1=parseFloat(mcWG1)+parseFloat(CL[2]);
			   BoxQty1=parseFloat(BoxQty1)+parseFloat(CL[3]);
			   Volume1 = parseFloat(Volume1)+parseFloat(CL[4]);
			   VolumeKG1 = parseFloat(VolumeKG1)+parseFloat(CL[5]);
			   ShipType  = CL[6];
		 }//end for
		 document.form1.mcWG.value=mcWG1.toFixed(2);	//出货重量
		 document.form1.BoxQty.value=BoxQty1.toFixed(2); 	//出货件数
		 document.form1.Volume.value = Volume1.toFixed(2); // 研砼体积
		 document.form1.VolumeKG.value = VolumeKG1.toFixed(2); //研砼体积重
		 document.form1.ShipType.value = ShipType;
	   }//end if(BackData)
	}//end function
function changeType(){
	e=document.getElementById("TypeId");
	TypeId=e.value;
	if(TypeId==1)e.options[1].selected = true;
    if(TypeId==2)e.options[2].selected = true;
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><input name="chId" type="hidden" id="chId">
	<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="2">
         <tr>
            <td width="180" align="right">(Invoice/提货单)类型</td>
            <td>
			<select name="TypeId" id="TypeId" style="width: 380px;" dataType="Require"  msg="未填写" onchange="changeType()">
			<option value="" Selected>全部</option>
			<option value="1" >invoice</option>
			<option value="2" >提货单</option>
			</select></td>
			<input id="ShipType" name="ShipType" type="hidden" value="0">
          </tr>
        <tr>
            <td  align="right">(Invoice/提货单)编号</td>
            <td><input name="InvoiceNO" type="text" id="InvoiceNO" style="width: 380px;" onClick='ViewShipId()' dataType="Require"  msg="未选择"></td>
	      </tr>

        <tr>
            <td align="right">forward公司</td>
            <td>
		<select name="CompanyId" id="CompanyId" style="width: 380px;" onchange="getForwardCharge()" dataType="Require"  msg="未填写">
			<?php
			$fResult = mysql_query("SELECT * FROM $DataPublic.freightdata WHERE Estate='1' AND MType=2  ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
			echo"<option value=''>请选择</option>";
				do{
			 		echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
					} while($fRow = mysql_fetch_array($fResult));
				}
			?></select></td>
          </tr>
           <tr>
    		<td align="right" height="30">费用结付&nbsp;</td>
   			<td><select name="PayType" Id="PayType" size="1" style="width: 380px;" dataType="Require" msg="未选择费用来源">
      			<option value="" selected>请选择</option>
      			<option value="0">自付</option>
      			<option value="1">代付</option>
        	</select></td>
  		</tr>
          <tr>
            <td align="right">forward Invoice</td>
            <td><input name="ForwardNO" type="text" id="ForwardNO" style="width: 380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">入仓号</td>
            <td><input name="HoldNO" type="text" id="HoldNO" style="width: 380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td><div align="right">发票日期</div></td>
            <td><input name="InvoiceDate" type="text" id="InvoiceDate" onfocus="WdatePicker()" style="width: 380px;" maxlength="10" readonly datatype="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">研砼称重</td>
            <td><input name="mcWG" type="text" id="mcWG" value="0.00" style="width: 380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">上海称重</td>
            <td><input name="forwardWG" type="text" id="forwardWG" value="0.00" style="width: 380px;" onblur="changeCharge()" dataType="Require"  msg="未填写"></td>
          </tr>

          <tr>
            <td align="right">研砼体积</td>
            <td><input name="Volume" type="text" id="Volume" value="0.00" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">上海体积</td>
            <td><input name="HKVolume" type="text" id="HKVolume" value="0.00" onblur="changeCharge()" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">研砼体积重</td>
            <td><input name="VolumeKG" type="text" id="VolumeKG" value="0.00" style="width: 380px;"></td>
          </tr>

          <tr>
            <td align="right">上海体积重</td>
            <td><input name="HKVolumeKG" type="text" id="HKVolumeKG"  value="0.00" onblur="changeCharge()"  style="width: 380px;"></td>
          </tr>
          <tr>
            <td align="right">件&nbsp;&nbsp;数</td>
            <td><input name="BoxQty" type="text" id="BoxQty" style="width: 380px;" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">金&nbsp;&nbsp;额</td>
            <td><input name="Amount" type="text" id="Amount" style="width: 380px;" dataType="Require"  msg="未填写"><input id="tempDiff" name="tempDiff" type="text" size="8" readonly>差异</td>
          </tr>
          <tr>
            <td align="right" valign="top">ETD/ETA</td>
            <td><textarea name="ETD" cols="51" rows="2" id="ETD"></textarea></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="51" rows="2" id="Remark"></textarea></td>
          </tr>

          <tr id='AirForwardCharge' style='display:none;'>
            <td colspan="4"><table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	          <tr>
	            <td colspan="2"><div align="center">Forward空运标准收费</div></td>
	          </tr>
	          <tr>
	            <td align="right"  width="175" height="25" >CFS费</td>
	            <td ><input name="CFSCharge1" type="text" id="CFSCharge1"  style="width:380px;" >
	            <input id="tempCFSCharge1" name="tempCFSCharge1" type="text" size="8" readonly></td>

	          </tr>
	          <tr>
	            <td align="right" height="25">THC费</td>
	            <td ><input name="THCCharge1" type="text" id="THCCharge1"  style="width:380px;" >
	            <input id="tempTHCCharge1" name="tempTHCCharge1" type="text" size="8" readonly></td>

	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge1" type="text" id="WJCharge1"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge1" type="text" id="SXCharge1"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge1" type="text" id="ENSCharge1"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">过桥费</td>
	            <td ><input name="GQCharge1" type="text" id="GQCharge1"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge1" type="text" id="TDCharge1"  style="width:380px;" ></td>
	          </tr>
	           <tr>
	            <td align="right" height="25">其它费用</td>
	            <td ><input name="OtherCharge1" type="text" id="OtherCharge1" value="0"  onblur="changeCharge()" style="width:380px;" ></td>
	          </tr>
	        </table>
         </td>
       </tr>
	   <!- ------------------------------------------Forward海运标准收费 -->
	      <tr id='SeaForwardCharge' style='display:none;'>
            <td colspan="4"><table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	          <tr>
	            <td colspan="2"><div align="center">Forward海运标准收费</div></td>
	          </tr>
	         <tr>
	            <td align="right"  width="175" height="25">CFS费</td>
	            <td ><input name="CFSCharge2" type="text" id="CFSCharge2"  style="width:380px;" >
	            <input id="tempCFSCharge2" name="tempCFSCharge2" type="text" size="8" readonly>
	            </td>
	          </tr>
	          <tr>
	            <td align="right" height="25">文件费</td>
	            <td ><input name="WJCharge2" type="text" id="WJCharge2"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">手续费</td>
	            <td ><input name="SXCharge2" type="text" id="SXCharge2"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">保险费</td>
	            <td ><input name="BXCharge2" type="text" id="BXCharge2"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">ENS费</td>
	            <td ><input name="ENSCharge2" type="text" id="ENSCharge2"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">电放费</td>
	            <td ><input name="DFCharge2" type="text" id="DFCharge2"  style="width:380px;" ></td>
	          </tr>
	          <tr>
	            <td align="right" height="25">提单费</td>
	            <td ><input name="TDCharge2" type="text" id="TDCharge2"  style="width:380px;" ></td>
	          </tr>
	           <tr>
	            <td align="right" height="25">其它费用</td>
	            <td ><input name="OtherCharge2" type="text" id="OtherCharge2" value="0.00" onblur="changeCharge()" style="width:380px;" ></td>
	          </tr>
            </table>
         </td>
       </tr>

        </table>
</td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function  getForwardCharge(){
    var CompanyId = document.getElementById("CompanyId").value;
    var ShipType  = document.getElementById("ShipType").value;
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
			            document.getElementById("CFSCharge1").value  = tempArray[0];
			            document.getElementById("tempCFSCharge1").value  = tempArray[0];
			            document.getElementById("THCCharge1").value  = tempArray[1];
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
			            document.getElementById("CFSCharge2").value  = tempArray[0];
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

}
</script>