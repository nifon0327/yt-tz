<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addywbjstuff.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增报价BOM表");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
//步骤3：
$tableWidth=770;$tableMenuS=500;$ColsNumber=7;
$CustomFun="<span onClick='ViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";

$checkPid=mysql_query("SELECT P.Id,P.Remark,T.TypeName FROM $DataIn.ywbj_productdata P LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId WHERE 1 AND P.CompanyId='$CompanyId' AND P.Id NOT IN (SELECT Pid FROM $DataIn.ywbj_pands GROUP BY Pid ORDER BY Pid) AND P.Estate=1 ORDER BY T.TypeName",$link_id);
if($checkPidRow=mysql_fetch_array($checkPid)){
	$i=1;
	do{
		$Pid=$checkPidRow["Id"];
		$TypeName=$checkPidRow["TypeName"];
		$Remark=$checkPidRow["Remark"];
		if($Remark!=""){
			$Remark="(".$Remark.")";
			}
		$SelectSTR.="<option value='$Pid'>$i - $TypeName$Remark</option>";
		$i++;
		}while ($checkPidRow=mysql_fetch_array($checkPid));
	}
$SelectCode="<select name='Pid' id='Pid'><option selected>请选择产品</option>$SelectSTR</select>";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
        <td width="100" class="A1111" align="center">操作</td>
        <td width="60" class="A1101" align="center">序号</td>
		<td width="60" class="A1101" align="center">配件ID</td>
        <td width="400" class="A1101" align="center">配件名称</td>
		<td width="120" class="A1101" align="center">价格</td>
		<td width="12" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td class="A0010" height="336">&nbsp;</td>
		<td height="25" colspan="5" class="A0111">
			<div style="width:735;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width="730" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php 
			//入库明细列表
			?>
			</table>
			</div>
		</td>
		<td class="A0001">&nbsp;</td>
	</tr>
</table>
<input name="TempValue" type="hidden" id="TempValue"><input name="SIdList" type="hidden" id="SIdList">
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>