<?php   
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/style/popupframe.css'>";//弹出窗口css

$From=$From==""?"read":$From;
//需处理参数
ChangeWtitle("$SubCompany  新品重置");
$funFrom="pt_neworder";
$nowWebPage=$funFrom."_read";
$tableMenuS=600;
$sumCols="8";
$ColsNumber=11;				

$Th_Col="操作|55|序号|30|采购流水号|100|配件ID|60|采购单|80|下单日期|70|半成品名称|350|单位|40|订单数量|60|单价|60|金额|70|交货日期|80|送货楼层|65|操作员|55";	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,26";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
 
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='PopupDiv' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


$mySql="SELECT  G.Id,G.POrderId,G.StockId AS mStockId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,G.CostPrice AS Price,G.Operator,
IFNULL(Y.OrderPO,M.PurchaseID) AS OrderPO,IFNULL(YM.OrderDate,M.Date) AS Date,M.Remark,D.StuffCname,D.Picture,U.Name AS UnitName,U.decimals,M.BuyerId,G.DeliveryDate,D.SendFloor,D.bomEstate 
FROM  $DataIn.cg1_stocksheet  G  
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.bps  B ON B.StuffId = D.StuffId
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=Y.OrderNumber 
WHERE  1 $SearchRows  AND (G.AddQty + G.FactualQty)>0 AND G.rkSign>0 
AND FIND_IN_SET(T.mainType,getSysConfig(103))>0 AND (
   FIND_IN_SET(B.CompanyId,getSysConfig(106))>0  
   OR EXISTS(SELECT BO.mStuffId FROM semifinished_bom BO 
   LEFT JOIN stuffdata D ON D.StuffId=BO.mStuffId 
   WHERE BO.mStuffId=G.StuffId AND D.TypeId=getSysConfig(104))) 
AND NOT EXISTS(
     SELECT SM.mStockId AS StockId FROM $DataIn.cg1_semifinished SM   
     WHERE  SM.mStockId=G.StockId 
) ORDER BY M.Date ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];
        $POrderId=$myRow["POrderId"];
		$mStockId=$myRow["mStockId"];
		$Qty=$myRow["Qty"];
		$OrderPO=$myRow["OrderPO"];
		$Remark=$myRow["Remark"];
        $Date =$myRow["Date"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
        $UnitName=$myRow["UnitName"];
        $decimals=$myRow["decimals"];
        $Qty=round($Qty,$decimals);
        
        $Price=$myRow["Price"];
        $Amount  = sprintf("%.2f", $Price* $Qty);

        include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
		include"../model/subprogram/stuff_Property.php";//配件属性       
        $Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

        $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        include "../model/subprogram/deliverydate_toweek.php";

        $SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$LockRemark ="";
		$bomEstate  =$myRow["bomEstate"];
		$ColbgColor = "";
        $CheckBomResult = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS BomCount  FROM $DataIn.semifinished_bom S  WHERE S.mStuffId = '$StuffId'",$link_id));
        $BomCount = $CheckBomResult["BomCount"];
        if($BomCount==0){
           $ColbgColor = "bgcolor='#FFB94F' ";
           $LockRemark="半成品BOM资料不存在";
        }else{
           if($bomEstate==1){
               $ColbgColor = "bgcolor='#00FF00' ";
	           $LockRemark="半成品BOM未审核";
           }
        }
     //半成品锁住
     
     $checkLockRow = mysql_fetch_array(mysql_query("SELECT Id  FROM $DataIn.cg1_lockstock WHERE StockId ='$mStockId'",$link_id));
     if($checkLockRow["Id"]>0){
         $ColbgColor = "bgcolor='#FF0000' ";
         
	     $LockRemark="半成品被锁住，不能重置";
     }
   
		$ValueArray=array(
			array(0=>$mStockId,		1=>"align='center'"),
            array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$OrderPO,		1=>"align='center'"),
            array(0=>$Date,			1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		    1=>"align='center'"),
			array(0=>$Qty,		        1=>"align='right'"),
			array(0=>$Price, 	        1=>"align='right'"),
			array(0=>$Amount, 		    1=>"align='right'"),
			array(0=>$DeliveryDate,		1=>"align='center'"),
			array(0=>$SendFloor,		1=>"align='center'"),
			array(0=>$Operator,		    1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
