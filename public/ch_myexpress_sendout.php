<?php 
//电信-zxq 2012-08-01
/*
$DataIn.usertable
$DataPublic.my3_express
$DataPublic.freightdata
$DataPublic.staffmain
$DataPublic.my3_expresstype
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 寄出快递资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_sendout";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
//
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my3_express E WHERE Id='$Id' LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$ShipType=$upData["ShipType"];
$SendDate=$upData["SendDate"];
$BillNumber=$upData["BillNumber"];
$Length=$upData["Length"];
$Width=$upData["Width"];
$Height=$upData["Height"];
$dWeight=$upData["dWeight"];
$cWeight=$upData["cWeight"];
$Amount=$upData["Amount"];
$CFSAmount=$upData["CFSAmount"];
$HandledBy=$upData["HandledBy"];
$Remark=$upData["Remark"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Estate,$Estate,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
		<td width="100" height="25" valign="middle" class='A0010' align="right">快递公司：
      </td>
      <td valign="middle" class='A0001'>
	  	<select name="CompanyId" id="CompanyId" style="width:420px" datatype="Require" msg="未选择">
		<?php 
		$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1' AND Model='1' ORDER BY Id",$link_id);
		if($fRow = mysql_fetch_array($fResult)){
			if($CompanyId==0){
				echo"<option value=''>请选择</option>";
				}
			do{
				if($CompanyId==$fRow["CompanyId"]){
					echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
					}
				else{
					echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
					}
				} while($fRow = mysql_fetch_array($fResult));
			}
		?>
      </select></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">快件类型： </td>
      <td valign="middle" class='A0001'>
	  		<select name="ShipType" id="ShipType" style="width:420px" dataType="Require" msg="未选择">
            <?php 
			//快件类型
			$PD_Sql = "SELECT T.Id,T.Name FROM $DataPublic.my3_expresstype T ORDER BY T.Id";
			$PD_Result = mysql_query($PD_Sql); 
			if( $PD_Myrow = mysql_fetch_array($PD_Result)){
				if(!ShipType==0){
					echo "<option value='' selected>全部</option>";
					}
				do{
					$Id=$PD_Myrow["Id"];
					$Name=$PD_Myrow["Name"];
					if($ShipType==$Id){
						echo "<option value='$Id' selected>$Name</option>";
						}
					else{			
						echo "<option value='$Id'>$Name</option>";
						}
					} while($PD_Myrow = mysql_fetch_array($PD_Result));
				}
			?></select>
        </td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">快递单号：</td>
      <td valign="middle" class='A0001'>      <input name="BillNumber" type="text" id="BillNumber" value="<?php  echo $BillNumber?>" size="77" <?php  echo $BillNumber?> dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" class='A0010' align="right">快件体积：</td>
      <td valign="middle" class='A0001'><input name="Length" type="text" id="Length" value="<?php  echo $Length?>" size="17" dataType="Number" msg="未填写">
      &nbsp;*&nbsp;&nbsp;<input name="Width" type="text" id="Width" value="<?php  echo $Width?>" size="17" dataType="Number" msg="未填写">
      &nbsp;*&nbsp;&nbsp;<input name="Height" type="text" id="Height" value="<?php  echo $Height?>" size="18" dataType="Number" msg="未填写"></td>
    </tr>
    <tr>
    	<td height="25" valign="middle" class='A0010' align="right">体积重量：</td>
	    <td valign="middle" class='A0001'><input name="dWeight" type="text" id="dWeight" value="<?php  echo $dWeight?>" size="77"></td>
        
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">实际重量：</td>
      <td valign="middle" class='A0001'><input name="cWeight" type="text" id="cWeight" value="<?php  echo $cWeight?>" size="77" dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">费&nbsp;&nbsp;&nbsp;&nbsp;用：</td>
      <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" size="77" dataType="Currency" msg="未填写或格不对"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">入 仓 费：</td>
      <td valign="middle" class='A0001'><input name="CFSAmount" type="text" id="CFSAmount" value="<?php  echo $CFSAmount?>" size="77" dataType="Currency" msg="未填写或格式不对"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">寄出日期：</td>
      <td valign="middle" class='A0001'><input name="SendDate" type="text" id="SendDate" onfocus="WdatePicker()" value="<?php  echo $SendDate?>" size="77" readonly dataType="Require" msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" valign="middle" class='A0010' align="right">经 手 人：</td>
      <td valign="middle" class='A0001'><select name="HandledBy" id="HandledBy" style="width:420px" datatype="Require" msg="未选择">
        <?php 
		$fResult = mysql_query("SELECT U.Number,M.Name FROM $DataIn.usertable U LEFT JOIN $DataPublic.staffmain M ON U.Number=M.Number WHERE U.uType='1' AND M.Estate='1' ORDER BY M.Id",$link_id);
		if($fRow = mysql_fetch_array($fResult)){
			do{
				if($HandledBy==$fRow["Number"]){
					echo"<option value='$fRow[Number]' selected>$fRow[Name]</option>";
					}
				else{
					if($fRow[Number]=="10039"){
						echo"<option value='$fRow[Number]' selected>$fRow[Name]</option>";
						}
					else{
						echo"<option value='$fRow[Number]'>$fRow[Name]</option>";
						}
					}
				} while($fRow = mysql_fetch_array($fResult));
			}
		?>
      </select></td>
    </tr>
    <tr>
      <td height="28" valign="top" class='A0010' align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="50" rows="5" id="Remark"><?php  echo $Remark?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>