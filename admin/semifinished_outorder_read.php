<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
ChangeWtitle("$SubCompany  (外发加工)未备料");
$funFrom="semifinished_outorder";
$nowWebPage=$funFrom."_read";

$Th_Col="操作|55|序号|30|交货日期|60|采购流水号|100|PO|80|下单日期|70|半成品名称|320|单位|40|成本价|60|数量|60|加工单价|60|加工金额|80|入库数量|70|待检数量|70|订单备注|150|送货楼层|70|操作员|55";	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,23,131";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

if($From!="slist"){
    $SearchRows=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
    
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	echo "<option value='' selected>全部</option>";
	
	$typeResult = mysql_query("SELECT G.CompanyId,C.Forshort FROM $DataIn.yw1_scsheet S 
     LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId  
     LEFT JOIN $DataIn.trade_object   C  ON C.CompanyId = G.CompanyId 
	 WHERE  S.Estate>0 AND G.Mid=0 $SearchRows  GROUP BY C.CompanyId ORDER BY Forshort",$link_id);
	if($typeRow = mysql_fetch_array($typeResult)){
		do{
			$_Id=$typeRow["CompanyId"];
			$_Name=$typeRow["Forshort"];
			if($CompanyId==$_Id){
				echo"<option value='$_Id' selected>$_Name</option>";
				$SearchRows.=" AND G.CompanyId='$_Id' ";
				}
			 else{
			 	echo"<option value='$_Id'>$_Name</option>";
				}
			}while($typeRow = mysql_fetch_array($typeResult));
	}  
}
else{
   $SearchRows.=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
}

echo "<input type='hidden' id='OrderAction' name='OrderAction' value='$OrderAction'/>";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


$mySql="SELECT  G.Id,S.StockId,S.mStockId,G.POrderId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,
IF(T.mainType='" .$APP_CONFIG['SEMI_MAINTYPE'] ."',G.CostPrice,G.Price) AS Price, 
IFNULL(Y.OrderPO,M.PurchaseID) AS OrderPO,IFNULL(YM.OrderDate,M.Date) AS Date,M.Remark,D.StuffCname,D.Picture,U.Name AS UnitName,M.BuyerId,G.DeliveryDate,D.SendFloor,S.Operator
FROM $DataIn.yw1_scsheet S 
LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId 
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=Y.OrderNumber 
WHERE  S.Estate>0  AND G.Mid=0  $SearchRows 
GROUP BY G.StockId ORDER BY Date,DeliveryDate";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
        $POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$mStockId=$myRow["mStockId"];
		$Qty=$myRow["Qty"];
		$OrderPO=$myRow["OrderPO"];
		$Remark=$myRow["Remark"];
        $Date =$myRow["Date"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
        $UnitName=$myRow["UnitName"];
        $Price=$myRow["Price"];
        
        include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性       
        $Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";

        $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        include "../model/subprogram/deliverydate_toweek.php";

        $SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
        
        //加工单价
         $outPriceTemp=mysql_query("SELECT S.Price FROM $DataIn.cg1_semifinished G  
         LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId
         LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = S.StuffId 
         LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
         WHERE  G.mStockId='$mStockId' AND T.mainType='" .$APP_CONFIG['WORKORDER_ACTION_MAINTYPE'] ."'",$link_id);
		 $outPrice=mysql_result($outPriceTemp,0,"Price");
		 $outPrice=$outPrice==""?0:$outPrice;
         $Amount  =   sprintf("%.2f", $outPrice* $Qty);
        //入库数量
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId='$StockId'",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;

		//待送货数量
		$checkSql=mysql_query("SELECT SUM(IFNULL(Qty,0)) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StuffId='$StuffId' AND StockId='$StockId'",$link_id);  
		$checkQty=mysql_result($checkSql,0,"Qty");
		$checkQty=$checkQty==""?0:$checkQty; 
		
		$mStockId=$myRow["mStockId"];
		
        $ShowId=$StockId;
        $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
        $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
        $ShowBomDivId  = "Bom_StuffDiv_"  . $ShowId;
        
        
        if ($OrderAction==104){
	         $ajaxFile="slicebom_ajax";
	         $ajaxDir="pt";
        }
        else{
	         $ajaxFile="semifinished_order_ajax";
             $ajaxDir="admin"; 
        }
        //echo $ajaxFile;
        $showPurchaseorder = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$mStockId|$ShowId|1\",\"$ajaxDir\");'  src='../images/showtable.gif' 
	title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";

	    $StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='$ShowBomTableId' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='$ShowBomDivId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";


		$ValueArray=array(
		    array(0=>$DeliveryDate,		1=>"align='center'"),
		    array(0=>$mStockId,		1=>"align='center'"),
			array(0=>$OrderPO,		1=>"align='center'"),
            array(0=>$Date,			    1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		    1=>"align='center'"),
			array(0=>$Price, 	        1=>"align='right'"),
			array(0=>$Qty,		        1=>"align='right'"),
			array(0=>$outPrice, 	        1=>"align='right'"),
			array(0=>$Amount, 		    1=>"align='right'"),
			array(0=>$rkQty, 		    1=>"align='right'"),
			array(0=>$checkQty, 		1=>"align='right'"),
			array(0=>$Remark,			1=>"align='center'"),
			array(0=>$SendFloor,		1=>"align='center'"),
			array(0=>$Operator,		    1=>"align='center'"),
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
