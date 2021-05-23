<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 供应商待送货记录");//需处理
$nowWebPage =$funFrom."_gyssh";	
$toWebPage  =$funFrom."_gysshsave";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CheckFormURL="thisPage";

$SelectCode="<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)' style='width: 125px;'>";
			$checkGysSql = mysql_query("SELECT S.CompanyId,P.Forshort FROM $DataIn.gys_shmain S,$DataIn.trade_object P WHERE 1 AND S.CompanyId=P.CompanyId GROUP BY S.CompanyId ORDER BY S.CompanyId",$link_id);
			if($checkGysRow=mysql_fetch_array($checkGysSql)){
				do{
					$ProviderTemp=$checkGysRow["CompanyId"];
					$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
					$Forshort=$checkGysRow["Forshort"];
					if ($ProviderTemp==$CompanyId){
						$SelectCode.= "<option value='$ProviderTemp' selected>$Forshort</option>";
						}
					else{
						$SelectCode.=  "<option value='$ProviderTemp'>$Forshort</option>";
						}
					}while($checkGysRow=mysql_fetch_array($checkGysSql));
				}
			$SelectCode.="</select>&nbsp;<select name='BillNumber' id='BillNumber' style='width: 80px;' onchange='ResetPage(this.name)'>";
			$checkNumSql = mysql_query("SELECT S.BillNumber FROM $DataIn.gys_shmain S WHERE 1 AND S.CompanyId='$CompanyId' ORDER BY S.BillNumber",$link_id);
			if($checkNumRow = mysql_fetch_array($checkNumSql)){
				do{
					$theBillNumber=$checkNumRow["BillNumber"];
					$BillNumber=$BillNumber==""?$theBillNumber:$BillNumber;
					if($theBillNumber==$BillNumber){
						$SelectCode.= "<option value='$theBillNumber' selected>$theBillNumber</option>";
						}
					else{
						$SelectCode.= "<option value='$theBillNumber'>$theBillNumber</option>";
						}
					}while($checkNumRow = mysql_fetch_array($checkNumSql));
				} 
			$SelectCode.="</select>";

include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="95" align="center">需求流水号</td>
		<td class="A1101" width="55" align="center">配件ID</td>
		<td class="A1101" width="330" align="center">配件名称</td>
		<td class="A1101" width="60" align="center">采购总数</td>
		<td class="A1101" width="60" align="center">未收数量</td>
		<td class="A1101" width="80" align="center">当前入库</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<?php 
	//供应商生成的送货单资料
	$mySql=mysql_query("SELECT 
		S.StockId,S.Qty,S.StuffId,D.StuffCname,G.BuyerId,(G.AddQty+G.FactualQty) AS cgQty
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		WHERE 1 AND M.BillNumber='$BillNumber' AND S.Locks=1 ORDER BY S.Id",$link_id);
		$i=1;
		if($myRow= mysql_fetch_array($mySql)){
			do{
				$Id=$myRow["Id"];
				$StockId=$myRow["StockId"];
				$StuffId=$myRow["StuffId"];
				$StuffCname=$myRow["StuffCname"];
				$BuyerId=$myRow["BuyerId"];
				$cgQty=$myRow["cgQty"];
				$Qty=$myRow["Qty"];
				//已收货总数
				$rkTemp=mysql_query("SELECT ifunll(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
				LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
				WHERE R.StockId='$StockId'",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$noQty=$cgQty-$rkQty;//全部未送货
				echo"<tr>
				<td class='A0010' height='25'>&nbsp;</td>
				<td class='A0111' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled></td>
				<td class='A0101' align='center'>$i</td>
				<td class='A0101' align='center'>$StockId</td>
				<td class='A0101' align='center'>$StuffId</td>
				<td class='A0101' align='center'>$StuffCname</td>
				<td class='A0101' align='center'>$cgQty</td>
				<td class='A0101' align='center'>$noQty</td>
				<td class='A0101' align='center'>$Qty</td>
				<td class='A0001'>&nbsp;</td>
				</tr>";
				$i++;
				}while($myRow= mysql_fetch_array($mySql));
			}
	?>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
	var Message=""
	var BillNumber=document.form1.BillNumber.value;
	if(BillNumber==""){
		Message="没有输入送货单号.";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		//document.form1.action="ck_rk_save.php";
		//document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>SumQty) || thisValue==0){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}
//删除指定行
function deleteRow(rowIndex){
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}
	
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   
</script>
