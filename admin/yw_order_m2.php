<?php   
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
$sumCols="10";	
$From=$From==""?"m2":$From;
ChangeWtitle("$SubCompany 订单更新审核");
$funFrom="yw_order";
$Th_Col="操作|55|序号|30|PO|80|中文名|320|Product Code|150|Unit|40|修改前<br>Price|70|修改后<br>Price|70|修改前<br>Qty|70|修改后<br>Qty|70|更新备注|180|操作员|55";
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
$ActioToS="15,17";
$nowWebPage=$funFrom."_m2";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	
	
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT U.Id,U.OldQty, U.NewQty,U.OldPrice,U.NewPrice,U.Remark,U.Operator,S.POrderId,S.OrderPO,S.ProductId,S.Qty AS OrderQty,S.Price AS OrderPrice,P.cName,P.eCode,UN.Name AS UnitName
FROM $DataIn.yw1_orderupdate U 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = U.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
LEFT JOIN $DataIn.packingunit UN ON UN.Id=P.PackingUnit 
WHERE U.Estate=1";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$POrderId=$myRow["POrderId"];
		$Id=$myRow["Id"];
	    $ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$OrderPO=$myRow["OrderPO"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$OrderQty=$myRow["OrderQty"];
		$OldQty=$myRow["OldQty"];
		$NewQty=$myRow["NewQty"];
		$OrderPrice=sprintf("%.4f",$myRow["OrderPrice"]);
		$OldPrice=sprintf("%.4f",$myRow["OldPrice"]);
		$NewPrice=sprintf("%.4f",$myRow["NewPrice"]);
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$UnitName=$myRow["UnitName"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		if($NewQty!=$OrderQty){
			$NewQty = "<span class='redB'>$NewQty</span>";
		}
		
		if($NewPrice!=$OrderPrice){
			$NewPrice = "<span class='redB'>$NewPrice</span>";
		}
			
	/*加入同一单的配件  // add by zx 2011-08-04 */
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$XtableWidth=$tableWidth-160;
		$XtableWidth=0;
		//$XsubTableWidth=$subTableWidth-160;
		$StuffListTB="
			<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $cName)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
			</tr>
			
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$OrderPO, 1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode),
			array(0=>$UnitName,1=>"align='center'"),
            array(0=>$OrderPrice,1=>"align='center'"),
			array(0=>$NewPrice,   1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='center'"),			
			array(0=>$NewQty,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Operator,1=>"align='center'")
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