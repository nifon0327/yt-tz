<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 生产配件置换");//需处理
$nowWebPage =$funFrom."_change2";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

$tableWidth=850;$tableMenuS=600;
$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>添加配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$upResult = mysql_query("SELECT S.OrderNumber,S.OrderPO,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId WHERE S.Id=$Id",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$Forshort=$upData["Forshort"];
	$OrderPO=$upData["OrderPO"]==""?"&nbsp;":$upData["OrderPO"];
	$OrderNumber=$upData["OrderNumber"];
	$POrderId=$upData["POrderId"];
	$ProductId=$upData["ProductId"];
	$cName=$upData["cName"];
	$eCode=$upData["eCode"]==""?"&nbsp;":$upData["eCode"];
	$Qty=$upData["Qty"];
	$Price=$upData["Price"];	
	}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId,POrderId,$POrderId,ProductId,$ProductId";
//echo $Parameter;
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="4" valign="bottom"><span class="redB">◆订单资料</div></td>
			<td height="22" colspan="3" align="right"><span class="redB">本页操作请谨慎</div></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
			<td width="60" height="22" class="A1111" align="center">客户</td>
			<td width="80" height="22" class="A1101" align="center">PO号</td>
			<td width="80" height="22" class="A1101" align="center">内部单号</td>
			<td class="A1101" align="center">产品名称</td>
			<td width="280" class="A1101" align="center">Product Code</td>
			<td width="75" class="A1101" align="center">售价</td>
			<td width="75" class="A1101" align="center">订单数量</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td class="A0111" align="center"><?php    echo $Forshort?></td>
			<td class="A0101" align="center"><?php    echo $OrderPO?></td>
			<td class="A0101" align="center"><?php    echo $POrderId?></td>
			<td class="A0101"><?php    echo $cName?></td>
			<td class="A0101"><?php    echo $eCode?></td>
			<td class="A0101" align="center"><?php    echo $Price?></td>
			<td class="A0101" align="center"><?php    echo $Qty?>
		    <input name="POrderQty" type="hidden" id="POrderQty" value="<?php    echo $Qty?>"></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="7" valign="bottom"><span class="redB">◆生产类配件</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
	</table>
	<table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A1111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                    
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                   <td width="30" class="A0001" align="center">序号</td>
                    <td width="110" class="A0001" align="center">采购流水号</td>
                    <td width="400" class="A0001" align="center">配件名称</td>
                    <td width="60" class="A0001" align="center">配件价格</td>
                    <td width="60" class="A0001" align="center">需求数量</td>
                    <td width="50" class="A0001" align="center">采购员</td>
                    <td width="" class="" align="center">供应商</td>
                  </tr>         
                </table>
            </div>		
            </td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>               
		
		<tr>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A0010">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
		<?php   
		//需求单列表
		$StockResult = mysql_query("SELECT A.StuffId,A.StockId,A.Price,A.OrderQty,B.StuffCname,C.Forshort,D.Name		
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
			LEFT JOIN $DataIn.staffmain D ON D.Number=A.BuyerId 
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=B.TypeId
			WHERE A.POrderId='$POrderId' AND A.Level=1 and T.mainType  = 3
			ORDER BY A.StockId",$link_id);
		if($StockRows = mysql_fetch_array($StockResult)){
				$StuffId=$StockRows["StuffId"];
				$FactualQty=$StockRows["FactualQty"];
				$StockId=$StockRows["StockId"];
				$StuffCname=$StockRows["StuffCname"];
				$Price=$StockRows["Price"];
				$OrderQty=$StockRows["OrderQty"];
				$StockQty=$StockRows["StockQty"];
				$AddQty=$StockRows["AddQty"];
				$Name=$StockRows["Name"]==""?"&nbsp;":$StockRows["Name"];
				$Forshort=$StockRows["Forshort"]==""?"&nbsp;":$StockRows["Forshort"];

				echo"<tr><td width='30' height='25' class='A0101' align='center'>1</td>
					<td width='110' class='A0101' align='center'>$StockId</td>
					<input name='StockId' type='hidden' id='StockId' value='$StockId' >
					<input name='OldStuffId' type='hidden' id='OldStuffId' value='$StuffId' >
					<td width='400' class='A0101'>$StuffCname</td>
					<td width='60' class='A0101' align='center'>$Price</td>
					<td width='60' class='A0101' align='center'>$OrderQty</td>
					<td width='50' class='A0101' align='center'>$Name</td>
					<td width='' class='A0101'>$Forshort</td>
					</tr>";
			}
		?>
			</table>
		</div>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
	</table> 
    <table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td width="10" class="A0010" height="25">&nbsp;</td>
        <td valign="bottom" ><span class="redB">◆置换新的生产类配件</span></td>
        <td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width='10' class='A0010' >&nbsp;</td>
		<td  align='center' class='A1111'>
		<div style='width:100%;height:100%; overflow-x:hidden;overflow-y:auto'>
			<table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable' >
              <tr>  
                <td width='30' class='A0001' height='30' align='center'>2</td>
                <td width='110'  class='A0001' align='center'><input  type='text' id='ChangeStuffId' name='ChangeStuffId' size="8" readonly></td>
                <td width='400' class='A0001' align='center'>&nbsp;</td>
                <td width='60' class='A0001' align='center'>&nbsp;</td>
                <td width='60' class='A0001' align='center'><input type='text' id='NewRelation' name='NewRelation' size="6" readonly></td>
                <td width='50' class='A0001' align='center'>&nbsp;</td>
                <td width='' class='' align='center'>&nbsp;</td>
              </tr>     
              
              <tr><td colspan="2" align="center" class='A1001'>备注</td><td class='A1000' colspan="5"><textarea id='Remark' name="Remark" cols="50" rows="3"></textarea></td></tr>
                  
			</table>
		</div>		
		</td>
		<td width='10' class='A0001'>&nbsp;</td>
	</tr>

</table>  
<?php   
include "../model/subprogram/add_model_b.php";
?>

<script LANGUAGE='JavaScript'  type="text/JavaScript">
function ViewStuffId(Action){
	var Message="";
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=stuffchange2&fromAction=1&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
    if(BackData==null || BackData==''){ 
		if(document.getElementById('SafariReturnValue')){
		var SafariReturnValue=document.getElementById('SafariReturnValue');
		BackData=SafariReturnValue.value;
		SafariReturnValue.value="";
		}
	}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录:	
		var FieldTemp=Rows[0];		//拆分后的记录
		var FieldArray=FieldTemp.split("^^");//分拆记录中的字段：0配件ID|1配件名称|2配件价格|3可用库存|4采购|5供应商
		//要求输入数量对应关系
				var returnValue =window.showModalDialog("yw_order_relation.php",window,"dialogWidth=400px;dialogHeight=300px");
				if(returnValue==null || returnValue=='' || returnValue==0){  //专为safari设计的
					if(document.getElementById('SafariReturnQty')){
					var SafariReturnQty=document.getElementById('SafariReturnQty');
					returnValue=SafariReturnQty.value;
					SafariReturnQty.value="";
					}
				}	
				if (returnValue){
					var qtyvalue=returnValue;
					var POrderQty=document.form1.POrderQty.value;
					var thisQty=POrderQty*eval(qtyvalue);//订单需求数
					thisQty=thisQty.toFixed(1);
					
					document.getElementById("ChangeStuffId").value = FieldArray[0];
					ListTable.rows[0].cells[2].innerHTML = FieldArray[1];
					ListTable.rows[0].cells[3].innerHTML = FieldArray[2];
					document.getElementById("NewRelation").value = thisQty;
					ListTable.rows[0].cells[5].innerHTML = FieldArray[4];
					ListTable.rows[0].cells[6].innerHTML = FieldArray[5];
					
				}		
	   }
}
	


function CheckForm(){
    if(confirm("请确认置换的配件是否正确?")){
	    document.form1.action="yw_order_updated.php?ActionId=131";
	    document.form1.submit();
    }
}

</script>
