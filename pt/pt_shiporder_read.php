<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=600;
ChangeWtitle("$SubCompany  皮套-已生产");
$funFrom="pt_shiporder";
$nowWebPage=$funFrom."_read";

$Th_Col="操作|55|序号|30|PO|80|下单日期|70|半成品名称|350|单位|40|单价|60|数量|60|金额|80|生产数量|70|入库数量|70|订单备注|150|交货日期|80|送货楼层|80|操作员|55";	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	  
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT  G.Id,G.POrderId,G.StockId, G.StuffId,(G.AddQty + G.FactualQty) AS Qty,G.Price, M.PurchaseID,M.Date,M.Remark,D.StuffCname,D.Price,D.Picture,U.Name AS UnitName,M.BuyerId,G.DeliveryDate,D.SendFloor
FROM  $DataIn.cg1_stocksheet  G  
LEFT JOIN $DataIn.cg1_stockmain M  ON  M.Id = G.Mid 
LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = G.StuffId 
LEFT JOIN $DataIn.stufftype  T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
WHERE  1 $SearchRows   AND  G.Mid>0 AND (G.AddQty + G.FactualQty)>0 AND G.rkSign=0 
AND T.mainType = '".$APP_CONFIG['SEMI_MAINTYPE']."' AND G.CompanyId  = '".$APP_CONFIG['PT_SUPPLIER']."' ORDER BY M.Date ";

//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
        $POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$Qty=$myRow["Qty"];
		$PurchaseID=$myRow["PurchaseID"];
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
        $checkProcessSql    = "SELECT  Id FROM $DataIn.cg1_processsheet WHERE StockId = $StockId";
        $checkProcessResult = mysql_fetch_array(mysql_query($checkProcessSql,$link_id));
        if($checkProcessResult){
        	$ProcessRow=mysql_fetch_array(mysql_query("SELECT B.ProcessId AS LastProcessId,PT.Color,PD.ProcessName AS LastProcessName 
				FROM $DataIn.cg1_processsheet B 
			    LEFT JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
			    LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
			    WHERE B.StockId='$StockId'  GROUP BY B.ProcessId ORDER BY PT.SortId DESC  LIMIT 1  ",$link_id));
            $LastProcessId  = $ProcessRow["LastProcessId"];
		     //检查已登记数量
			$CheckthisScQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE   C.StockId='$StockId' AND C.ProcessId='$LastProcessId'",$link_id));
			$thisScQty      = $CheckthisScQty["gxQty"]==""?0:$CheckthisScQty["gxQty"];

        }else{
        	 $scQtyRow  = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.sc1_cjtj  WHERE  StockId='$StockId' ",$link_id));
		     $thisScQty = $scQtyRow["Qty"];
        }


        //入库数量
		$rkTemp=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId='$StockId'",$link_id));
		$rkQty=$rkTemp["Qty"];
		$rkQty=$rkQty==""?0:$rkQty;


        //显示或隐藏bom
        $ShowBomImageId= "Bom_StuffImage_" . $i;
        $ShowBomTableId= "Bom_StuffTable_" . $i;
        $ShowBomDivId  = "Bom_StuffDiv_" . $i;
	     $showPurchaseorder="<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"pt_order_ajax\",\"$StockId|$i|1\",\"pt\");' name='$ShowBomImageId' src='../images/showtable.gif' 
			title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";	
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='$ShowBomTableId' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='$ShowBomDivId' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$PurchaseID,		1=>"align='center'"),
            array(0=>$Date,			    1=>"align='center'"),
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
