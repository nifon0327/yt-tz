<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.usertable
$DataPublic.staffmain
$DataIn.yw6_salesview
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增业务可查询客户");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;$ColsNumber=5;
$CheckFormURL="thisPage";

//员工资料表
$PD_Sql = "SELECT U.Number,M.Name FROM $DataIn.usertable U,$DataPublic.staffmain M 
WHERE U.Number=M.Number AND U.uType=1 AND M.Estate=1  AND M.BranchId=3  AND M.cSign='$Login_cSign' ORDER BY U.Number";
$PD_Result = mysql_query($PD_Sql); 
while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
	$PD_BuyerId=$PD_Myrow["Number"];
	if($SalesId==""){
		$SalesId=$PD_BuyerId;
		}
	$PD_StuffCname=$PD_Myrow["Name"];					
	if ($SalesId==$PD_BuyerId){
		$OptionStr.="<option value='$PD_BuyerId' selected>$PD_StuffCname</option>";
		}
	else{
		$OptionStr.="<option value='$PD_BuyerId'>$PD_StuffCname</option>";
		}
	}
//下拉选框处理
$SelectCode="<select name='SalesId' id='SalesId' style='width: 150px;' onchange='document.form1.submit();'>$OptionStr</select>&nbsp;";
$SelectCode.="<select id='TypeId' name='TypeId'>
              <option value='' selected>-业务类型-</option>
              <option value='1'>对内</option>
	          <option value='2'>对外</option></select>";

include "../model/subprogram/add_model_t.php";
echo"<input name='sumCols' type='hidden' id='sumCols' value='$sumCols'><input name='MergeRows' type='hidden' id='MergeRows' value='$MergeRows'>";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="6" class='A0011'>&nbsp;</td>
	</tr>
    <tr>
    	<td width="150" rowspan="2" valign="top" class='A0010'><p align="right">可选功能列表：<br> 
      </td>
      <td class='A1111' height="25" width="40" align="center">选项</td>
      <td class='A1101' width="40" align="center">序号</td>
      <td class='A1101' width="60" align="center">客户ID</td>
      <td class='A1101' width="550" align="center">客户简称</td>
      <td class='A0001'width="10">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" class='A0001'>
	  <div style='width:696;height:300;overflow-x:hidden;overflow-y:scroll'>
	  <?php 
	$Result = mysql_query("SELECT Id,CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($Result)) {
		$i=1;
		do{
			$Id=$myrow["Id"];
			$CompanyId=$myrow["CompanyId"];
			$Forshort=$myrow["Forshort"];
			$Result2 = mysql_query("SELECT * FROM $DataIn.yw6_salesview WHERE CompanyId=$CompanyId and SalesId=$SalesId",$link_id);
			if($myrow2 = mysql_fetch_array($Result2)){
				echo"
				<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='25' width='42' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' checked disabled></td>
				<td class='A0101' width='41' align='center'>$i</td>
				<td class='A0101' width='61' align='center'>$CompanyId</td>
				<td class='A0101' width='550'>$Forshort</td>
				<td >&nbsp;</td></tr></table>";
				echo"<script> 
				 chooseRow(ListTable$i,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber)
				 </script>";
				}
			else{
				echo"
				<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='25' width='42' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled></td>
				<td class='A0101' width='41' align='center'>$i</td>
				<td class='A0101' width='61' align='center'>$CompanyId</td>
				<td class='A0101' width='550'>$Forshort</td>
				<td >&nbsp;</td></tr></table>";
				}
			$i++;
			}while ($myrow = mysql_fetch_array($Result));
		}
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
	  ?>
	  </div>
	  </td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script  type=text/javascript>
function CheckForm(){
    if(document.getElementById("TypeId").value==""){alert("请选择业务类型");return false;}
	//解除
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		var NameTemp=e.name;
		var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
		if (e.type=="checkbox" && Name!=-1){
			e.disabled=false;
			} 
		}
	document.form1.action="yw_salesview_save.php";
	document.form1.submit();
	}
</script>
