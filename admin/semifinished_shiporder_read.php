<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;				
$tableMenuS=600;
ChangeWtitle("$SubCompany  (半成品)已生产");
$funFrom="semifinished_shiporder";
$nowWebPage=$funFrom."_read";


$Th_Col="操作|55|序号|30|采购流水号|100|PO|80|下单日期|70|半成品ID|50|半成品名称|280|单位|40|单价|60|数量|60|金额|80|生产数量|70|入库数量|70|订单备注|150|交货日期|80|送货楼层|80|操作员|55";	
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
    $SearchRows=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
    

	
	$typeResult = mysql_query("SELECT W.Id,W.Name FROM $DataIn.yw1_scsheet S 
	 LEFT JOIN $DataIn.workshopdata W ON W.Id=S.WorkShopId 
	 WHERE 1 $SearchRows AND S.Estate=0  GROUP BY W.Id ORDER BY Id",$link_id);
	if($typeRow = mysql_fetch_array($typeResult)){
        echo "<select name='WorkShopId' id='WorkShopId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部</option>";
		do{
			$_Id=$typeRow["Id"];
			$_Name=$typeRow["Name"];
			if($WorkShopId==$_Id){
				echo"<option value='$_Id' selected>$_Name</option>";
				$SearchRows.=" AND S.WorkShopId='$_Id' ";
				}
			 else{
			 	echo"<option value='$_Id'>$_Name</option>";
				}
			}while($typeRow = mysql_fetch_array($typeResult));
	   echo  "</select>&nbsp;";
	 } 
	
	
	
	    $DeliveryWeekList ="";
	    $DeliveryWeekResult = mysql_query("SELECT G.DeliveryWeek
		FROM $DataIn.yw1_scsheet S
		INNER JOIN $DataIn.workshopdata W  ON W.Id = S.WorkShopId
		LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId 
		 WHERE 1 $SearchRows AND S.Estate=0  GROUP BY G.DeliveryWeek ORDER BY G.DeliveryWeek DESC",$link_id);
		if($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult)){
			$DeliveryWeekList .= "<select name='DeliveryWeek' id='DeliveryWeek' onchange='ResetPage(20,5)'>";
			//$DeliveryWeekList.= "<option value='' selected>全部</option>";
			do{
				$DeliveryWeekValue = $DeliveryWeekRow["DeliveryWeek"];
				
				if($DeliveryWeekValue>0){
				    $week=substr($DeliveryWeekValue, 4,2);
			        $weekName="Week " . $week;
		        }else{
			        $weekName ="未设置";
		        }
		        $DeliveryWeek = $DeliveryWeek==""?$DeliveryWeekValue:$DeliveryWeek;
				if($DeliveryWeek==$DeliveryWeekValue){
					$DeliveryWeekList.="<option value='$DeliveryWeekValue' selected>$weekName</option>";
					$SearchRows.=" AND G.DeliveryWeek='$DeliveryWeekValue' ";
					}
				 else{
				 	$DeliveryWeekList.="<option value='$DeliveryWeekValue'>$weekName</option>";
					}
				}while($DeliveryWeekRow = mysql_fetch_array($DeliveryWeekResult));
			$DeliveryWeekList.= "</select>&nbsp;";
		} 
		
		echo $DeliveryWeekList;
	
	 
}
else{
   $SearchRows.=$OrderAction>0?" AND S.ActionId='$OrderAction' ":"";
}


echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);


$mySql="SELECT  G.Id,S.StockId,S.mStockId,G.POrderId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,G.CostPrice AS Price, 
IFNULL(Y.OrderPO,M.PurchaseID) AS OrderPO,IFNULL(YM.OrderDate,M.Date) AS Date,M.Remark,D.StuffCname,D.Picture,U.Name AS UnitName,U.decimals,M.BuyerId,G.DeliveryDate,D.SendFloor
FROM $DataIn.yw1_scsheet S 
LEFT JOIN $DataIn.cg1_stocksheet  G  ON G.StockId=S.mStockId 
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=Y.OrderNumber 
WHERE S.Estate=0 AND S.scFrom = 0  $SearchRows  GROUP BY G.StockId ORDER BY Date,DeliveryDate";

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
        $Amount  =   sprintf("%.2f", $Price* $Qty);
        include "../model/subprogram/stuffimg_model.php";
		include"../model/subprogram/stuff_Property.php";//配件属性       
        $Operator=$myRow["BuyerId"];
		include "../model/subprogram/staffname.php";

        $DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
        include "../model/subprogram/deliverydate_toweek.php";

        $SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";

        //生产数量
        //1.有工序的半成品
        $thisScQty = "";
        $scQtyRow  = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.sc1_cjtj  WHERE  StockId='$StockId'",$link_id));
		$thisScQty = $scQtyRow["Qty"];

        //入库数量
        
		$rkTemp=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId='$mStockId'",$link_id));
		$rkQty=$rkTemp["Qty"];
		$rkQty=$rkQty==""?0:$rkQty;
		
		if($rkQty==$Qty){
			$rkQty ="<span class='greenB'>$rkQty</span>";
		}

       if($thisScQty==$Qty){
			$thisScQty ="<span class='greenB'>$thisScQty</span>";
		}else{
			$thisScQty ="<span class='yellowB'>$thisScQty</span>";
		}
		
		$ShowId=$StockId;
        $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
        $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
        $ShowBomDivId  = "Bom_StuffDiv_"  . $ShowId;
		 $ajaxFile="semifinishedbom_ajax";
         $ajaxDir="admin"; 
	     $showPurchaseorder = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$mStockId|$ShowId|1\",\"$ajaxDir\");'  src='../images/showtable.gif' 
	title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";

	    $StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='$ShowBomTableId' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='$ShowBomDivId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
		    array(0=>$StockId,		1=>"align='center'"),
			array(0=>$OrderPO,		1=>"align='center'"),
            array(0=>$Date,			    1=>"align='center'"),
            array(0=>$StuffId,			    1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,		    1=>"align='center'"),
			array(0=>$Price, 	        1=>"align='right'"),
			array(0=>$Qty,		        1=>"align='right'"),
			array(0=>$Amount, 		    1=>"align='right'"),
			array(0=>$thisScQty,		1=>"align='right'"),
			array(0=>$rkQty, 		    1=>"align='right'"),
			array(0=>$Remark,			1=>"align='center'"),
			array(0=>$DeliveryDate,		1=>"align='center'"),
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
