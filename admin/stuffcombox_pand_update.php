<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/stuffcombox_pand.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 子母配件BOM");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";

$P_Row = mysql_fetch_array(mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$Id' LIMIT 1",$link_id));
$mStuffCname=$P_Row["StuffCname"];

$S_Sql="SELECT A.Id,A.Relation,D.StuffId,D.StuffCname,T.TypeName,M.Name,P.Forshort
			FROM $DataIn.stuffcombox_bom A 
			LEFT JOIN $DataIn.stuffdata D ON A.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			LEFT JOIN $DataIn.bps B ON D.StuffId=B.StuffId
			LEFT JOIN $DataPublic.staffmain M ON B.BuyerId=M.Number
			LEFT JOIN $DataIn.providerdata P ON B.CompanyId=P.CompanyId
			WHERE A.mStuffId='$Id'   
			ORDER BY Id";
//echo $S_Sql;
$S_Result = mysql_query($S_Sql,$link_id);

$SelectCode=" <b>$mStuffCname  </b><input name='mStuffCname' type='hidden' id='mStuffCname' value='$mStuffCname'><input name='mStuffId' type='hidden' id='mStuffId' value='$Id'>";
include "../model/subprogram/add_model_pt.php";
//步骤4：需处理
?>
<table border="0" width="<?=$tableWidth?>" cellpadding="0" cellspacing="0"  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#FFFFFF">
	<tr bgcolor='<?=$Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                  <td width="70" height="25" class="A1101" align="center"> 操作</td>
                    <td width="50" class="A1101" align="center">序号</td>
                    <td width="90" class="A1101" align="center">类别</td>
                    <td width="50" class="A1101" align="center">配件ID</td>
                    <td width="360" class="A1101" align="center">配件名称</td>
                    <td width="70" class="A1101" align="center">对应关系</td>
                    <td width="70" class="A1101" align="center">采购</td>
                    <td width="   " class="A1100" align="center">供应商</td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
  <tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td   height="336" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
			<?php
		if($S_Row=mysql_fetch_array($S_Result)) {//如果设定了产品配件关系
			$i=1;
			do{
				$StuffId=$S_Row["StuffId"];
				$Relation=$S_Row["Relation"];
				$StuffCname=$S_Row["StuffCname"];
				$TypeName=$S_Row["TypeName"];
				$Name=$S_Row["Name"];
				$Forshort=$S_Row["Forshort"]==""?"&nbsp;":$S_Row["Forshort"];
				
				echo"<tr>
				<td align='center' class='A0101' width='70' height='25' onmousedown='window.event.cancelBubble=true;'>
				 <a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;
				 <a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;
				 <a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
				</td>
		   			<td align='center' class='A0101' width='50'>$i</td>
		   			<td class='A0101' width='90'>$TypeName</td>
				   <td class='A0101' width='50' align='center'>$StuffId</td>
				   <td class='A0101' width='360'>$StuffCname</td>
				   <td class='A0101' align='center' width='70'>
				   <input name='Qty[]' type='text' id='Qty$i' size='8' value='$Relation' onchange='checkNum(this)' onfocus='toTempValue(this.value)'>
				   </td>
				   <td class='A0101' align='center' width='70'>$Name</td>
				   <td class='A0101' width=' '>$Forshort</td>
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

<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td  class="A0001" height="25" colspan="2"><span class="redB">添加新配件</span></td>
	</tr>

	<tr bgcolor='<?=$Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                    <td width="70" height="25" class="A1101" align="center"><a href="#" onclick="AddnewRow(this.parentNode.parentNode.rowIndex)" title="新增行">+</a></td>
                    <td width="50" class="A1101" align="center">序号</td>
                    <td width="" class="A1101" align="center">配件名称</td>
                    <td width="150" class="A1100" align="center">对应关系</td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>

<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td   height="180" class="A0111">		
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                     <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='newListTable'></table>
		  </div>
       </td>
  		<td width="10" class="A0001">&nbsp;</td>
	</tr>

</table>
<input name="TempValue" type="hidden" id="TempValue"><input name="SIdList" type="hidden" id="SIdList"><input name="newList" type="hidden" id="newList">
<?php
//步骤5：
include "../model/subprogram/add_model_ps.php";
?>