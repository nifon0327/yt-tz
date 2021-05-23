<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch9_expsheet
$DataIn.usertable
$DataPublic.staffmain
$DataPublic.freightdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新快递记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT E.Date,E.CompanyId,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.HandledBy,E.Remark
FROM $DataIn.ch9_expsheet E WHERE 1 AND E.Id='$Id' LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$Type=$upData["Type"];
$TempEstateSTR="TypeSTR".strval($Type); 
$$TempEstateSTR="checked";
$Date=$upData["Date"];
$ExpressNO=$upData["ExpressNO"];
$HandledBy=$upData["HandledBy"];
$BoxQty=$upData["BoxQty"];
$Weight=$upData["Weight"];
$mcWG=$upData["mcWG"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
		<tr>
            <td width="100" scope="col" align="right">寄/到&nbsp;件</td>
            <td scope="col">
			<input name="Type" type="radio" value="0" <?php  echo $TypeSTR0?>>寄件<input type="radio" name="Type" value="1" <?php  echo $TypeSTR1?>>到件
			</td>
		</tr>
		<tr>
		  <td height="18" scope="col" align="right">经&nbsp;手&nbsp;人</td>
		  <td scope="col">
		  <select name="HandledBy" id="HandledBy" style="width: 460px;">
		  <?php
		    /*
			$result = mysql_query("SELECT U.Number,M.Name 
			FROM $DataIn.usertable U 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number WHERE M.Number>10001 and M.Estate='1' ORDER BY M.Number",$link_id);
			*/
			$result = mysql_query("SELECT M.Number,M.Name 
			from $DataPublic.staffmain M  WHERE M.Number>10001 and M.Estate='1' ORDER BY M.Name",$link_id);
			
			if($myrow = mysql_fetch_array($result)){
				do{
					$Number=$myrow["Number"];
					$Name=$myrow["Name"];
					if($Number==$HandledBy){
						echo "<option value='$Number' selected>$Name</option>";
						}
					else{
						echo "<option value='$Number'>$Name</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
				} 
			?>
			</select>
		   </td>
		  </tr>
		<tr>
		  <td align="right" scope="col">快递公司</td>
		  <td scope="col">
		  <select name="CompanyId" id="CompanyId" style="width: 460px;">
		  <?php 
			$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.freightdata WHERE Estate='1' AND MType=1 ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
				do{
			 		if($CompanyId==$fRow["CompanyId"]){
						echo"<option value='$fRow[CompanyId]' selected>$fRow[Forshort]</option>";
						}
					else{
						echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
						}
					} while($fRow = mysql_fetch_array($fResult));
				}
			?></select></td>
		</tr>
		<tr>
		  <td height="24" scope="col" align="right">寄件日期</td>
		  <td scope="col"><input name="SendDate" type="text" id="SendDate" size="85" value="<?php  echo $Date?>" dataType="Require"  msg="未选择" onfocus="WdatePicker()" readonly></td>
		  </tr>
		<tr>
		  <td height="-1" scope="col" align="right">提单号码</td>
		  <td scope="col"><input name="ExpressNO" type="text" id="ExpressNO" value="<?php  echo $ExpressNO?>" size="85" dataType="Require"  msg="未填写"></td>
		  </tr>
		<tr>
		  <td height="3" scope="col" align="right">件&nbsp;&nbsp;&nbsp;&nbsp;数</td>
		  <td scope="col"><input name="BoxQty" type="text" id="BoxQty" size="85" value="<?php  echo $BoxQty?>" dataType="Number" msg="未填写或格式不对"></td>
		  </tr>
		<tr>
		  <td height="1" scope="col" align="right">重&nbsp;&nbsp;&nbsp;&nbsp;量</td>
		  <td scope="col"><input name="Weight" type="text" id="Weight" size="85" value="<?php  echo $Weight?>" dataType="Currency"  msg="未填写或格式不对"></td>
		</tr>
		<tr>
		  <td height="-9" scope="col" align="right">运&nbsp;&nbsp;&nbsp;&nbsp;费</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" size="85" value="<?php  echo $Amount?>" dataType="Currency"  msg="未填写或格式不对"></td>
		</tr>
		<tr>
		  <td height="13" align="right" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="55" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
		</tr>
        </table>
	</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>