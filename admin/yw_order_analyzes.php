<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 产品订单拆分");//需处理
$nowWebPage =$funFrom."_analyzes";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=910;$tableMenuS=500;
$ErrorInfoModel=2;
//步骤4：需处理

$result = mysql_query("SELECT S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,P.cName,P.eCode ,S.OrderPO,P.CompanyId  
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId 
WHERE S.Id='$Id'",$link_id);
if ($myrow = mysql_fetch_array($result)) {
	$OrderNumber=$myrow["OrderNumber"];
	$POrderId=$myrow["POrderId"];
	$ProductId=$myrow["ProductId"];
	$cName=$myrow["cName"];
	$eCode=$myrow["eCode"];
	$Qty=$myrow["Qty"];
	$Price=$myrow["Price"];
	$OrderPO=$myrow["OrderPO"];
	$CompanyId=$myrow["CompanyId"];
	
	$SplitResult = mysql_query("SELECT POrderId FROM $DataIn.yw10_ordersplit  WHERE POrderId='$POrderId' AND Estate=0",$link_id);
	if($SplitRows = mysql_fetch_array($SplitResult)){
	         $SaveSTR="NO";
	         $SplitMessage="<div class='redB'>该订单已拆分未审核状态！</div>";
	 }
	 else{
		 $SplitMessage="";
	 }
}

include "../model/subprogram/add_model_t.php";	
//**************************箱子尺寸
echo $SplitMessage;

$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040'",$link_id);
if($BoxRows = mysql_fetch_array($BoxResult)){
   $Relation=$BoxRows["Relation"];
   $RelationArray=explode("/",$Relation);
   if($RelationArray[1]!="")$Relation=$RelationArray[1];
   else $Relation=$RelationArray[0];
  }

//****************************************一张原单拆单不能超过两次

$OrderTimeSql=mysql_query("SELECT COUNT(P.eCode) AS splitTime FROM $DataIn.yw1_ordersheet S
LEFT  JOIN $DataIn.productdata P ON S.ProductId=P.ProductId WHERE S.OrderPO='$OrderPO' AND P.eCode='$eCode'",$link_id);
$splitTime=mysql_result($OrderTimeSql,0,"splitTime");

if($splitTime<=20 || $CompanyId==1056){//除Ontario 可以拆单两次以上
	  
	  //拆单数据
	  $minProductSql = "SELECT POrderId, Qty
	  					FROM $DataIn.yw1_ordersheet
	  					WHERE scFrom =  '1'
	  					AND ProductId =  '$ProductId'
	  					AND Qty = (SELECT MIN( Qty ) FROM yw1_ordersheet WHERE ProductId =  '$ProductId' AND scFrom =  '1' ) Limit 1";
	  $minProductResult = mysql_query($minProductSql);
	  $minProductRow = mysql_fetch_assoc($minProductResult);
	  $minTargetPOrderId = $minProductRow["POrderId"];
	  $minQty = $minProductRow["Qty"];
	  
	  $sListResult = mysql_query("SELECT MIN( K.tStockQty ) AS minTStockQty 
									FROM $DataIn.cg1_stocksheet S
									LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
									LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
									Left Join $DataIn.pands H On H.StuffId = S.StuffId and H.ProductId = '$ProductId' 
									WHERE S.POrderId='$minTargetPOrderId' 
									And ST.mainType = '1'
									And H.Relation = '1'
									ORDER BY S.StockId",$link_id);						
	  $minQtyRow = mysql_fetch_assoc($sListResult);
	  $splitQty = $minQtyRow["minTStockQty"];					
	
	 //
      $Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId";
   $POrderIdResult=mysql_query("SELECT * FROM $DataIn.ck5_llsheet WHERE StockId like'$POrderId%' AND Estate=0");
   if(mysql_num_rows($POrderIdResult)>0){
      $llSign=1;
    ?>
    <table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
       <tr><td height="40" class="A0010">&nbsp;</td>
        <td colspan="8" valign="bottom"><span style="color:#FF0000">订单有领料,需业务主管审核后方可拆单:</span></td><td class="A0001">&nbsp;</td></tr>
	   <tr class="">
		<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
		<td width="80" height="22" class="A1111" align="center">内部单号</td>
		<td class="A1101" align="center">产品名称</td>
		<td width="200" class="A1101" align="center">Product Code</td>
		<td width="60" class="A1101" align="center">售价</td>
		<td width="60" class="A1101" align="center">装箱数</td>
		<td width="60" class="A1101" align="center">原订单数量</td>
		<td width="60" class="A1101" align="center">拆分数量1</td>
		<td width="60" class="A1101" align="center">拆分数量2</td>
		<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>
	   </tr>
	   <tr>
		<td width="10" class="A0010">&nbsp;<input id="Relation" type="hidden" name="Relation" value="<?php    echo $Relation?>"></td>
		<td class="A0111" align="center"><?php    echo $POrderId?></td>
		<td class="A0101"><?php    echo $cName?></td>
		<td class="A0101"><?php    echo $eCode?></td>
		<td class="A0101" align="center"><?php    echo $Price?></td>
		<td class="A0101" align="center"><?php    echo $Relation?></td>
		<td class="A0101"><input name="Qty" type="text" id="Qty" value="<?php    echo $Qty?>" size="6" class="noLine" readonly></td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="6" class="noLine" value="<?php echo $splitQty; ?>" title="输入第一个拆分的数量" onchange="ChangeQty()">(先出)</td>
		<td class="A0101"><input name="Qty2" type="text" id="Qty2" size="6" class="noLine" value="<?php echo($Qty - $splitQty)?>" dataType="Number" Msg="拆分的数量不正确" readonly></td>
		<td width="10" class="A0001">&nbsp;</td>
	   </tr>
	   <tr>
	   <td width="10" class="A0010">&nbsp;</td>
	   <td colspan="2" class="A0000" align="right">拆分原因:</td>
	   <td colspan="6" class="A0000"><textarea id="Remark" name="Remark" cols="80" rows="2" dataType="Require" Msg="未填写原因" ></textarea></td>
	   <td width="10" class="A0001">&nbsp;<input type="hidden" id="llSign" name="llSign" value="<?php    echo $llSign?>">
	<input type="hidden" id="POrderId" name="POrderId" value="<?php    echo $POrderId?>"></td></tr>
	  <tr>
	     <td height="113" class="A0010">&nbsp;</td>
	     <td colspan="8">&nbsp;&nbsp;说明:拆分数量1所在的子单均使用原订单流水号和原需求单的流水号，并继承原单的采购资料；拆分数量2所在的子单如果原需求单已下则使用库存，否则按需求采购</td>
	     <td class="A0001">&nbsp;</td>
      </tr>
    </table>
	 
<?php    
     }
else{
    $llSign=0;
?>
    <table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
	   <tr class="">
		<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
		<td width="80" height="22" class="A1111" align="center">内部单号</td>
		<td class="A1101" align="center">产品名称</td>
		<td width="200" class="A1101" align="center">Product Code</td>
		<td width="60" class="A1101" align="center">售价</td>
		<td width="60" class="A1101" align="center">装箱数</td>
		<td width="60" class="A1101" align="center">原订单数量</td>
		<td width="60" class="A1101" align="center">拆分数量1</td>
		<td width="60" class="A1101" align="center">拆分数量2</td>
		<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>
	  </tr>
	  <tr>
		<td width="10" class="A0010">&nbsp;<input id="Relation" type="hidden" name="Relation" value="<?php    echo $Relation?>"></td>
		<td class="A0111" align="center"><?php    echo $POrderId?></td>
		<td class="A0101"><?php    echo $cName?></td>
		<td class="A0101"><?php    echo $eCode?></td>
		<td class="A0101" align="center"><?php    echo $Price?></td>
		<td class="A0101" align="center"><?php    echo $Relation?></td>
		<td class="A0101"><input name="Qty" type="text" id="Qty" value="<?php    echo $Qty?>" size="6" class="noLine" readonly></td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="6" value="<?php echo $splitQty; ?>" class="noLine" title="输入第一个拆分的数量" onchange="ChangeQty()">(先出)</td>
		<td class="A0101"><input name="Qty2" type="text" id="Qty2" size="6" value="<?php echo($Qty - $splitQty)?>" class="noLine" dataType="Number" Msg="拆分的数量不正确" readonly></td>
		<td width="10" class="A0001">&nbsp;</td>
	  </tr>
	   <tr>
	   <td width="10" class="A0111">&nbsp;</td>
	   <td colspan="2" class="A0101" align="right">拆分原因:</td>
	   <td colspan="6" class="A0101"><textarea id="Remark" name="Remark" cols="80" rows="2" dataType="Require" Msg="未填写原因" ></textarea></td>
	   <td width="10" class="A0001">&nbsp;<input type="hidden" id="llSign" name="llSign" value="<?php    echo $llSign?>">
	<input type="hidden" id="POrderId" name="POrderId" value="<?php    echo $POrderId?>"></td></tr>
	  <tr>
	    <td height="113" class="A0010">&nbsp;<input type="hidden" id="llSign" name="llSign" value="<?php    echo $llSign?>"></td>
	    <td colspan="8">&nbsp;&nbsp;说明:拆分数量1所在的子单均使用原订单流水号和原需求单的流水号，并继承原单的采购资料；拆分数量2所在的子单如果原需求单已下则使用库存，否则按需求采购</td>
	    <td class="A0001">&nbsp;</td>
      </tr>
   </table>
      <?php   
      }
    }
else{
?>
 <table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
 
 <tr><td width="10" class="A0010">&nbsp;</td>
 <td height="80" align="center"><span style="color:#FF0000;font-size:18px;">该订单已拆单5次,不能再拆单</span></td><td width="10" class="A0001">&nbsp;</td></tr>
 
 </table>

<?php   
    }
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function ChangeQty(){
    var Relation=document.form1.Relation.value;
	var QtyTemp=document.form1.Qty.value;
	var Qty1Temp=document.form1.Qty1.value;
	var Qty2Temp=QtyTemp-Qty1Temp;
	var BoxNum=Qty1Temp%Relation;
	if(BoxNum>0){
	document.form1.Qty1.value="";
	alert("请确定输入的数量是整箱数!");return false;};	
	var Result=fucCheckNUM(Qty1Temp,'');
	if(Result==0){
		alert("输入了不正确的数量:"+Qty1Temp+",重新输入!");
		document.form1.Qty2.value="";
		return false;
		}
	else{
		if((Qty1Temp*1>=QtyTemp*1) || (Qty1Temp*1==0)){
			alert("拆分的数量不对!拆分的子单数量不能为0或>=原单的数量");
			document.form1.Qty1.value="";
			document.form1.Qty2.value="";
			return false;
			}
		else{
			document.form1.Qty2.value=Qty2Temp;
			}		
		}
	}
</script>
