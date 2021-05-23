<?php
//电信-zxq 2012-08-01
//步骤1 $DataPublic.freightdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增中港报关费");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<script language = "JavaScript">
function ViewShipId(){
	var r=Math.random();
	TypeId=document.getElementById("TypeId").value;
	if(TypeId==""){alert("请选择类型");return false;}
    if(TypeId==1){
	      var BackData=window.showModalDialog("ch_shipmain_s1.php?r="+r+"&tSearchPage=ch_shipmain&fSearchPage=ch_freight_declaration&SearchNum=2&Action=4","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
		}
	else{
	      var BackData=window.showModalDialog("ch_shipout_bill_s1.php?r="+r+"&tSearchPage=ch_shipout_bill_s1&fSearchPage=ch_freight_declaration&SearchNum=2&Action=4","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	    }
	if(BackData){
	    var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;
		var mcWG1=0;
		var BoxQty1=0;
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
			   mcWG1=parseInt(mcWG1)+parseInt(CL[2]);
			   BoxQty1=parseInt(BoxQty1)+parseInt(CL[3])
		 }//end for
		 document.form1.mcWG.value=mcWG1;	//出货重量
		 document.form1.BoxQty.value=BoxQty1; 	//出货件数
	   }//end if(BackData)
}//end function
function changeType(){
	e=document.getElementById("TypeId");
	TypeId=e.value;
	if(TypeId==1)e.options[1].selected = true;
    if(TypeId==2)e.options[2].selected = true;
	//document.form1.action="";
	//document.form1.submit();
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><input name="chId" type="hidden" id="chId">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="2">

        <tr>
            <td width="150" align="right">(Invoice/提货单)类型</td>
            <td>
			<select name="TypeId" id="TypeId" style="width: 460px;" dataType="Require"  msg="未填写" onchange="changeType()">
			<option value="" Selected>全部</option>
			<option value="1" >invoice</option>
			<option value="2" >提货单</option>
			</select></td>
          </tr>
        <tr>
            <td width="150" align="right">(Invoice/提货单)编号</td>
            <td><input name="InvoiceNO" type="text" id="InvoiceNO" size="85" onClick='ViewShipId()' dataType="Require"  msg="未选择"></td>
	      </tr>

        <tr>
            <td align="right">货运公司</td>
            <td>
			<select name="CompanyId" id="CompanyId" style="width: 460px;" dataType="Require"  msg="未填写">
			<?php
			$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1' AND MType=1 ORDER BY Id",$link_id);
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
   			<td><select name="PayType" Id="PayType" size="1" style="width: 460px;" dataType="Require" msg="未选择费用来源">
      			<option value="" selected>请选择</option>
      			<option value="0">自付</option>
      			<option value="1">代付</option>
        	</select></td>
  		</tr>
          <tr>
            <td align="right">目 的 地</td>
            <td><input name="Termini" type="text" id="Termini" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">提单号码</td>
            <td><input name="ExpressNO" type="text" id="ExpressNO" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">研砼称重</td>
            <td><input name="mcWG" type="text" id="mcWG" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数</td>
            <td><input name="BoxQty" type="text" id="BoxQty" size="85" maxlength="10" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;价</td>
            <td><input name="Price" type="text" id="Price" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">入 仓 费</td>
            <td><input name="depotCharge" type="text" id="depotCharge" size="85"></td>
          </tr>
		   <tr>
            <td align="right">报 关 费</td>
            <td><input name="declarationCharge" type="text" id="declarationCharge" size="85"></td>
          </tr>
		   <tr>
            <td align="right">商 检 费</td>
            <td><input name="checkCharge" type="text" id="checkCharge" size="85"></td>
          </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="55" rows="4" id="Remark"></textarea></td>
          </tr>
        </table>
   </td>
  </tr>
 </table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>