<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 拆分出货");//需处理
$nowWebPage =$funFrom."_splitout";	
$toWebPage  =$funFrom."_splitupdated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId,CompanyId,$CompanyId";
//步骤3：

$upData = mysql_fetch_array(mysql_query("SELECT M.OrderNumber,M.CompanyId,M.OrderDate,SP.Id,SP.Qty AS thisQty,SP.ShipType,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,
P.cName,P.eCode,P.TestStandard,SP.Estate,K.tStockQty
	FROM $DataIn.ch1_shipsplit   SP  
     LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE SP.Id=$Id ",$link_id));
$cName=$upData["cName"];
$OrderPO=$upData["OrderPO"];
$thisQty=$upData["thisQty"];
$POrderId=$upData["POrderId"];
$ProductId=$upData["ProductId"];
$tStockQty=$upData["tStockQty"];


$CheckrkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS rkQty 
FROM $DataIn.yw1_orderrk R 
WHERE R.POrderId='$POrderId' AND R.ProductId = '$ProductId' ",$link_id));
$rkQty=$CheckrkQty["rkQty"];
$rkQty = $rkQty>0?"<span class='yellowB'>$rkQty</span>":"&nbsp;";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
  <td class="A0010" align="right" height="30" width="300">产品名称:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo $cName?><input  type="hidden" id="POrderId" name="POrderId" value="<?php echo $POrderId?>"></td>
  </tr>
  <tr>
  <td class="A0010" align="right" height="30" width="300">订单PO:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo $OrderPO?></td>
  </tr>
  <tr>
  <td class="A0010" align="right" height="30">订单数量:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo $thisQty?></td>
  </tr>
  
    <tr>
  <td class="A0010" align="right" height="30">生产数量:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo $rkQty?></td>
  </tr>
  
    <tr>
  <td class="A0010" align="right" height="30">产品库存:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo "<span class='blueB'>$tStockQty</span>"?></td>
  </tr>
  <?php
       //**************************箱子尺寸
$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040'",$link_id);
if($BoxRows = mysql_fetch_array($BoxResult)){
   $Relation=$BoxRows["Relation"];
   $RelationArray=explode("/",$Relation);
   if($RelationArray[1]!="")$Relation=$RelationArray[1];
   else $Relation=$RelationArray[0];
   $Relation.=" / 箱";
  }

  ?>
  <tr>
  <td class="A0010" align="right" height="30">装箱数量:</td>
  <td class="A0001" >&nbsp;&nbsp;<?php    echo $Relation?></td>
  </tr>
 <tr>
  <td class="A0010" align="right" height="30">拆分数量:</td>
  <td class="A0001" >&nbsp;&nbsp;<input  type="text" id="SplitQty" name="SplitQty" size="10" value="0" onblur="CheckQty(this,<?php echo $thisQty?>)" dataType="Require"  msg="未填写"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">出货方式:</td>
    <td class="A0001">&nbsp;&nbsp;<select name="ShipType" Id="ShipType" style="width: 128px;" dataType="Require" msg="未选择">
      <option value="" selected>请选择</option>
				<?PHP 
					$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($TypeRow = mysql_fetch_array($shipTypeResult)){
				  do{
					           echo "<option value='$TypeRow[Id]'>$TypeRow[Name]</option>";
					  } while($TypeRow = mysql_fetch_array($shipTypeResult));
			      }
				?>
        </select></td>
  </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function  CheckQty(e,Qty){
   var SplitQty=e.value;
   var SplitSign=fucCheckNUM(SplitQty);
       if(SplitSign==1){
               if(SplitQty>=Qty){
                   alert("超出范围"); e.value="";
                   return false; 
                 }
           }
     else{
         alert("不是数字"); e.value="";
          return false; 
        }
}
</script>