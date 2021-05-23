<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#EEEEEE;margin:10px auto;}
.in {background:#FFFFFF;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
-->
</style>
<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
echo"<SCRIPT src='../model/pands.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增BOM资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,ProductType,$ProductType";
//步骤3：
$tableWidth=1110;$tableMenuS=600;$ColsNumber=10;
$CustomFun="<span onClick='addPandStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
$SelectCode="成品 <input name='ProductName' type='text' id='ProductName' size='60' onclick='SearchRecord(\"productdata\",\"$funFrom\",1,7,event)' readonly >
<input name='HZ' type='hidden' id='HZ' value='$HzRate'>
<input name='ProductId' type='hidden' id='ProductId'>";
include "../model/subprogram/add_model_pt.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0"  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                    <td width="70" height="25" class="A1101" align="center"> 操作</td>
                    <td width="50" class="A1101" align="center">序号</td>
                    <td width="90" class="A1101" align="center">类别</td>
                    <td width="50" class="A1101" align="center">配件ID</td>
                    <td width="310" class="A1101" align="center">配件名称</td>
                    <td width="120" class="A1101" align="center">对应数量</td>
                    <td width="70" class="A1101" align="center">备品率</td>
                    <td width="70" class="A1101" align="center">采购</td>
                    <td width="80" class="A1101" align="center">供应商</td>
                    <td width="" class="A1100" align="center">关联配件</td>
                   <!-- <td width="" class="A1100" align="center">工序</td>-->
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

			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr bgcolor='<?php  echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="40">&nbsp;</td>
		<td height="40"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:no">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr>
                  <td>售价</td>
                  <td>-USD成本</td>
                  <td>-RMB成本</td>
                  <td>-行政费用</td>
                  <td>=毛利</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td><input name="saleAmount" type="text" id="saleAmount" value="0" size="20" readonly></td>
                  <td><input name="cbUSD" type="text" id="cbUSD" value="0" size="20" readonly></td>
                  <td><input name="cbRMB" type="text" id="cbRMB" value="0" size="20" readonly></td>
                  <td><input name="cbHZ" type="text" id="cbHZ" value="0" size="20" readonly></td>
                  <td><input name="Maori" type="text" id="Maori" value="0" size="20" readonly></td>
                   <td align='center'><input name="graphic" type="button" id="graphic" value="生成流程图" onclick="createBomflow()"></td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>  
    <tr id='flow' style='visibility:hidden;'>
        <image id='flowimage'></image>
    </tr>
</table>
<input name="TempValue" type="hidden" id="TempValue"><input name="SIdList" type="hidden" id="SIdList">

<?php 
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
//步骤5：
include "../model/subprogram/add_model_p.php";
?>
