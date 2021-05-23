<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/slice_cutdie.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 片材配件BOM");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=800;$tableMenuS=500;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewCut(4)' $onClickCSS>添加刀模</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";

$P_Row = mysql_fetch_array(mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$Id' LIMIT 1",$link_id));
$cName=$P_Row["StuffCname"];

$S_Sql="SELECT  A.CutId,C.CutName,C.CutSize,C.cutSign   
			FROM $DataIn.slice_cutdie A 
			LEFT JOIN $DataIn.pt_cut_data C ON C.Id=A.CutId  
			WHERE A.StuffId='$Id'";
$S_Result = mysql_query($S_Sql,$link_id);

$SelectCode=" <b>$cName  </b><input name='HZ' type='hidden' id='HZ' value='$pcValue'><input name='gStuffIdName' type='hidden' id='gStuffIdName' value='$cName'><input name='gStuffId' type='hidden' id='gStuffId' value='$Id'>";
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
                  <td width="100" height="25" class="A1101" align="center"> 操作</td>
                    <td width="70" class="A1101" align="center">序号</td>
                    <td width="420" class="A1101" align="center">刀模编号</td>
                    <td width="   " class="A1100" align="center">刀模尺寸</td>
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
				$CutId=$S_Row["CutId"];
				$CutName=$S_Row["CutName"];
				$CutSize=$S_Row["CutSize"];
				
				echo"<tr>
				<td align='center' class='A0101' width='100' height='25' onmousedown='window.event.cancelBubble=true;'>
				 <a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;
				 <a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;
				 <a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
				</td>
		   			<td align='center' class='A0101' width='70'>$i</td>
				   <td class='A0101' width='420'>$CutName<input name='CutId[]' type='hidden' id='CutId$i' value='$CutId'></td>
				   <td class='A0101' width=' '>$CutSize</td>
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
include "../model/subprogram/add_model_ps.php";
?>