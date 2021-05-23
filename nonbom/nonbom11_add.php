<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增预付订金");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    <td class="A0011">
       <table width="700" border="0" align="center" cellspacing="0" id="NoteTable">
       <tr>
    	<td width="150" height="35" align="right" valign="middle" scope="col"><p>供 应 商：<br> 
      </td>
	    <td valign="middle" scope="col">
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
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		    </select>
		</td>
    </tr>
    
    <tr>
    	<td height="35" valign="middle" scope="col" align="right">非BOM采购单号：</td>
	    <td valign="middle" scope="col"><input name="PurchaseID" type="text" id="PurchaseID" style="width: 380px;" onClick='ViewShipId()' dataType="Require"  msg="未选择" readonly="readonly"></td>
    </tr> 
	<tr>
    	<td height="35" valign="middle" scope="col"  align="right">请款日期：</td>
	    <td valign="middle" scope="col"><input name="Date" type="text" id="Date" style="width: 380px;" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>
    
    <tr>
    	<td height="35" valign="middle" scope="col" align="right">预付金额：</td>
	    <td valign="middle" scope="col"><input name="Amount" type="text" id="Amount" style="width: 380px;" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
    </tr>
    <tr>
      <td height="47" align="right" valign="top" scope="col"><br>
      预付备注：</td>
      <td valign="middle" scope="col"><textarea name="Remark" style="width: 380px;" rows="6" id="Remark" dataType="Require" msg="没有输入备注"></textarea></td>
    </tr>
    <tr>
		  <td height="35" align="right" valign="top" scope="col">上传凭证：<br>(限JPG格式)</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" style="width:420px" DataType="Filter" Accept="jpg,JPG" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
	    
</table>
</td></tr></table>
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
		var BackData=window.showModalDialog("nonbom11_s1.php?r="+r+"&tSearchPage=nonbom11&fSearchPage=nonbom11&SearchNum=1&CompanyId="+CompanyIdTemp+"&Action=12","BackData","dialogHeight =500px;dialogWidth=1200px;center=yes;scroll=yes");
		if(BackData){
			var CL=BackData.split("^^");
			document.getElementById("PurchaseID").value=CL[1];
			document.getElementById("rmbAmount").value=CL[2];
			}
		}
	else{
		alert("请先选择供应商!");
		}
	}
function CheckAmount(e){
       var Amount=e.value;
       var rmbAmount=document.getElementById("rmbAmount").value;
            if(parseInt(Amount)>parseInt(rmbAmount)){
                alert("申请的订金大于采购单的金额!");
                  document.getElementById("Amount").value=""; 
               }
}
</script> 