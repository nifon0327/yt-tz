<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addywbjstuff.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新包装BOM表");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=770;$tableMenuS=500;$ColsNumber=8;
$CustomFun="<span onClick='ViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
$checkPid=mysql_query("SELECT P.Id,P.Remark,T.TypeName FROM $DataIn.ywbj_productdata P LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId WHERE P.Id='$Id' ORDER BY T.TypeName LIMIT 1",$link_id);
if($checkPidRow=mysql_fetch_array($checkPid)){
	$Pid=$checkPidRow["Id"];
	$TypeName=$checkPidRow["TypeName"];
	$Remark=$checkPidRow["Remark"];
	if($Remark!=""){
		$TypeName=$TypeName."(".$Remark.")";
		}
	$SelectSTR.="<option value='$Pid'>$TypeName</option>";
	}
$SelectCode="产品<select name='Pid' id='Pid'>$SelectSTR</select>";
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
		<td width="10" class="A0010" height="336">&nbsp;</td>
		<td height="25" colspan="5" class="A0111">
		<div style="width:740;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='740' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php 
			//入库明细列表
			$S_Result = mysql_query("SELECT A.Id,A.Sid,A.Sprice,D.Name FROM $DataIn.ywbj_pands A LEFT JOIN $DataIn.ywbj_stuffdata D ON A.Sid=D.Id WHERE A.Pid=$Id ORDER BY A.Id",$link_id);
			if($S_Row=mysql_fetch_array($S_Result)) {//如果设定了产品配件关系
				$i=1;
				do{
					$Sid=$S_Row["Sid"];
					$Name=$S_Row["Name"];
					$Sprice=$S_Row["Sprice"];
					echo"<tr>
					<td align='center' class='A0101' width='100' onmousedown='window.event.cancelBubble=true;'><a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a></td>
						<td align='center' class='A0101' width='60'>$i</td>
						<td class='A0101' width='60' align='center'>$Sid</td>
					   <td class='A0101' width='400'>&nbsp;$Name</td>
					   <td class='A0101' align='center'><input name='Price[]' type='text' class='noLine' id='Price$i' size='9' value='$Sprice'></td>
					  </tr>";
					$i++;
					}while ($S_Row=mysql_fetch_array($S_Result));
				}
				$Rows=$i-1;
			?>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<input name="TempValue" type="hidden" id="TempValue"><input name="SIdList" type="hidden" id="SIdList">
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>