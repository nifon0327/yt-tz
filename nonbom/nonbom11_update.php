<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新预付订金记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.nonbom11_djsheet WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$PurchaseID=$upData["PurchaseID"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
$Date=$upData["Date"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>供 应 商：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 380px;" dataType="Require"  msg="未选择供应商" onchange="ChangeCompany()">
			<?php 
			$checkSql = "SELECT C.CompanyId,C.Forshort,D.Symbol 
			FROM $DataIn.nonbom6_cgsheet A
			LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid 
			LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
			LEFT JOIN $DataIn.nonbom12_cwsheet E ON E.cgId=A.Id
			WHERE A.Mid>0 AND C.Estate=1 AND E.cgId IS NULL GROUP BY C.CompanyId ORDER BY C.Forshort";
			$checkResult = mysql_query($checkSql); 
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value=''>选择供应商</option>";
				do{
					$Forshort=$checkRow["Forshort"];
					$Symbol=$checkRow["Symbol"];
					$Forshort="($Symbol) - ".$Forshort;
					$thisCompanyId=$checkRow["CompanyId"];
					if($CompanyId==$thisCompanyId){
						echo"<option value='$thisCompanyId' selected>$Forshort</option>";
						}
					else{
						echo"<option value='$thisCompanyId'>$Forshort</option>";
						}
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		    </select>
		</td>
    </tr>
    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">非BOM采购单号：</td>
	    <td valign="middle" class='A0001'><input name="PurchaseID" type="text" id="PurchaseID" style="width: 380px;" value="<?php echo $PurchaseID;?>" onClick='ViewShipId()' dataType="Require"  msg="未选择" readonly="readonly"></td>
    </tr> 
	<tr>
    	<td height="35" valign="middle" class='A0010' align="right">请款日期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" style="width: 380px;" dataType="Date" value="<?php echo $Date;?>" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>
    
    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">预付金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" style="width: 380px;"  value="<?php echo $Amount;?>" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>
      预付备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" style="width: 380px;" rows="6" id="Remark" dataType="Require" msg="没有输入备注"><?php echo $Remark;?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function ChangeCompany(){
	document.getElementById("PurchaseID").value=""; 
	}
function ViewShipId(){
	var r=Math.random();  
	var CompanyIdTemp=document.getElementById("CompanyId").value; 
	if(CompanyIdTemp!=""){
		var BackData=window.showModalDialog("nonbom11_s1.php?r="+r+"&tSearchPage=nonbom11&fSearchPage=nonbom11&SearchNum=1&CompanyId="+CompanyIdTemp+"&Action=12","BackData","dialogHeight =500px;dialogWidth=1000px;center=yes;scroll=yes");
		if(BackData){
			var CL=BackData.split("^^");
			document.getElementById("PurchaseID").value=CL[1];
			}
		}
	else{
		alert("请先选择供应商!");
		}
	}
</script> 