<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/stuffcombox_pand.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 子母配件BOM");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=870;$tableMenuS=500;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
$SelectCode="母配件名称 <input name='mStuffCname' type='text' id='mStuffCname' size='52' onclick='searchStuffId(6)' readonly >
<input name='mStuffId' type='hidden' id='mStuffId'>";
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
		<td   height="180" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                     <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'></table>
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