<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 出货资料更新");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_delivery";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤4：
$tableWidth=900;$tableMenuS=550;$spaceSide=15;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId";
//步骤5：//需处理
 ?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td valign="bottom">&nbsp;◆ <?php    echo $Forshort?> 出货单(<?php    echo $Number?>)资料</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
     <tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;送货单号:<?php    echo $InvoiceNO?></td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
</table>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="30" class='A1111' align="center" <?php    echo $Fun_bgcolor?>>序号</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>PO</td>
		<td width="160" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>产品代码</td>
        <td width="240" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>产品名称</td>
		<td width="90" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>送货数量</td>
       <td width="300" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>备注</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
<?php   
$sListResult = mysql_query("
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
    LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price	
     FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$Id' AND S.Type='3'
",$link_id);
$i=1;
$sumQty=0;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
               $OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		       $POrderId=$StockRows["POrderId"];
		       $cName=$StockRows["cName"];
		       $eCode=$StockRows["eCode"];
               $Qty=$StockRows["Qty"];
		       $Price=$StockRows["Price"];
		        $Amount=sprintf("%.2f",$Qty*$Price);	
		        $sumQty=$sumQty+$Qty;
		        $sumAmount=sprintf("%.2f",$sumAmount+$Amount);
                $Stock_Result=mysql_fetch_array(mysql_query("SELECT A.StuffCname,S.OrderQty
		              FROM $DataIn.cg1_stocksheet S
		              LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
                      LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId 
                     WHERE  1 AND A.StuffCname like '%OZAKI%'   AND  S.POrderId='$POrderId'  limit 1",$link_id));
            $StuffCnameArray= explode("+",$Stock_Result["StuffCname"]);
            $StuffCname=$StuffCnameArray[1];
            $StuffQty= $Stock_Result["OrderQty"];
             echo "<tr>
                         <td width=$spaceSide class='A0010' rowspan='2'>&nbsp;<input id='POrderId$i' name='POrderId[]' type='hidden' value='$POrderId'></td>
                        <td align='center' class='A0111' rowspan='2'>$i</td>
	                    <td class='A0101' rowspan='2' >$OrderPO<input id='OrderPO$i' name='OrderPO[]' type='hidden' value='$OrderPO'></td>
                        <td class='A0101'  rowspan='2'>$eCode<input id='eCode$i' name='eCode[]' type='hidden' value='$eCode'></td>
                        <td class='A0101'>$cName<input id='cName$i' name='cName[]' type='hidden' value='$cName'></td>
                        <td class='A0101' align='center'><input id='OrderQty$i' name='OrderQty[]' type='text' size='10' dataType='Number'  msg='未填写或格式不对'></td>
                        <td class='A0101' align='center' ><textarea id='Content$i'  name='Content[]' cols='40' rows='2' ></textarea></td>
                        <td width=$spaceSide class='A0001'>&nbsp;</td>
                     </tr>";

            echo "<tr>
                        <td  class='A0101'  height='30'>$StuffCname<input id='StuffCname$i' name='StuffCname[]' type='hidden' value='$StuffCname'></td>
                        <td class='A0101' align='center'>$StuffQty<input id='StuffQty$i' name='StuffQty[]' type='hidden' value='$StuffQty'></td>
                      <td class='A0101' align='center'><textarea id='StuffContent$i'  name='StuffContent[]' cols='40' rows='2' ></textarea></td>
                      </tr>";


		     $i++;
 		   }while ($StockRows = mysql_fetch_array($sListResult));
        echo "<input id='TempId'  name='TempId'  value='$i' type='hidden'>";
       echo "<tr><td width=$spaceSide class='A0010' >&nbsp;</td>
                         <td align='center' class='A0111'  colspan='2'>备注:</td>
                         <td class='A0101'  colspan='4'><textarea name='Remark' cols='100' rows='3' id='Remark'' ></textarea></td>
                         <td width=$spaceSide class='A0001'>&nbsp;</td>
                   </tr>";
}
?>
</table>
<?php   
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function Indepot(thisE){
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value="";
		return false;
		}
	}
</script>