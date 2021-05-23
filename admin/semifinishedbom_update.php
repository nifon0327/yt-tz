<?php
include "../model/modelhead.php";
echo"<SCRIPT src='../model/semifinishedbom.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 半成品配件BOM");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";

$P_Row = mysql_fetch_array(mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffId='$Id' LIMIT 1",$link_id));
$cName=$P_Row["StuffCname"];

$S_Sql="SELECT A.Id,A.Relation,D.StuffId,D.StuffCname,D.CostPrice,D.Price,T.TypeName,M.Name,P.Forshort,C.Rate   
				FROM $DataIn.semifinished_bom A 
				LEFT JOIN $DataIn.stuffdata D ON A.StuffId=D.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType 
				LEFT JOIN $DataIn.bps B ON D.StuffId=B.StuffId
				LEFT JOIN $DataPublic.staffmain M ON B.BuyerId=M.Number
				LEFT JOIN $DataIn.trade_object P ON B.CompanyId=P.CompanyId
				LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
				WHERE A.mStuffId='$Id'  AND A.StuffId>0  	
				ORDER BY MT.SortId,A.Id";
//echo $S_Sql;
$S_Result = mysql_query($S_Sql,$link_id);

$SelectCode=" <b>$Id - $cName  </b><input name='HZ' type='hidden' id='HZ' value='$pcValue'><input name='mStuffIdName' type='hidden' id='mStuffIdName' value='$cName'><input name='mStuffId' type='hidden' id='mStuffId' value='$Id'>";
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
                    <td width="310" class="A1101" align="center">配件名称</td>
                    <td width="70" class="A1101" align="center">对应数量</td>
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
		$totalCostPrice=0; $taxtPrice=0;
		if($S_Row=mysql_fetch_array($S_Result)) {//如果设定了产品配件关系
			$i=1;
			do{
				$StuffId=$S_Row["StuffId"];
				$Relation=$S_Row["Relation"];
				$StuffCname=$S_Row["StuffCname"];
				$TypeName=$S_Row["TypeName"];
				$Name=$S_Row["Name"];
				$Forshort=$S_Row["Forshort"]==""?"&nbsp;":$S_Row["Forshort"];
				$Rate=$S_Row["Rate"]==""?1:$S_Row["Rate"];
				$Price=$Rate * $S_Row["Price"];
				$CostPrice=$Rate * $S_Row["CostPrice"];
				
				$RelArray=explode("/", $Relation);
				$mRelation=count($RelArray)==2?$RelArray[0]/$RelArray[1]:$RelArray[0];
				$taxtPrice+=($Price*$mRelation);
				$totalCostPrice+=($CostPrice*$mRelation);
				
				echo"<tr>
				<td align='center' class='A0101' width='70' height='25' onmousedown='window.event.cancelBubble=true;'>
				 <a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;
				 <a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;
				 <a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
				</td>
		   			<td align='center' class='A0101' width='50'>$i</td>
		   			<td class='A0101' width='90'>$TypeName</td>
				   <td class='A0101' width='50'>$StuffId</td>
				   <td class='A0101' width='310'>$StuffCname</td>
				   <td class='A0101' align='center' width='70'>
				   <input name='Qty[]' type='text' id='Qty$i' size='8' value='$Relation' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb$i' value='1'><input name='sPrice[]' type='hidden' id='sPrice$i' value='$Price'><input name='CostPrice[]' type='hidden' id='CostPrice$i' value='$CostPrice'
				   </td>
				   <td class='A0101' align='center' width='70'>$Name&nbsp;</td>
				   <td class='A0100' width=' '>$Forshort&nbsp;</td>
				  </tr>";
		  		$i++;
				}while ($S_Row=mysql_fetch_array($S_Result));
			}
			$Rows=$i-1;
			$taxtPrice=round($taxtPrice,4);
			$totalCostPrice=round($totalCostPrice,4);
			?>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>  
	
	<tr bgcolor='<?php  echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="30">&nbsp;</td>
		<td height="30"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:no">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr height='30'>
                  
                  <td width='25%' align='center'>成本价:</td>
                  <td width='25%'><input name="cbHZ" type="text" id="cbHZ" value="<?php echo $totalCostPrice;?>" size="15" readonly></td>
                  <td width='25%' align='center'>含税价:</td>
                  <td width='25%' align='center'><input name="taxtPrice" type="text" id="taxtPrice" value="<?php echo $taxtPrice;?>" size="15" readonly></td>   
                </tr>
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