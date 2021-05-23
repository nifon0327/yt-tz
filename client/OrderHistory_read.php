<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;				
$tableMenuS=600;
$funFrom="OrderHistory";
$nowWebPage=$funFrom."_read";
$Th_Col="Choose|40|NO.|30|ShipDate|70|PO#|80|Chinese|250|Product Code|160|Unit|35|Price|55|Qty|50|Amont|65";
$sumCols="11,12";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 and M.CompanyId='$myCompanyId' and M.Estate='0' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
}
$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";
include "../admin/subprogram/read_model_5.php";
$subTableWidth=$tableWidth-30;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Date,S.Id,S.Mid,S.POrderId,S.ProductId,S.Qty,S.Price,S.Type,
P.cName,P.eCode,P.TestStandard,U.Name AS Unit,YS.OrderPO,YS.DeliveryDate,YS.ShipType,E.Leadtime  ,YM.ClientOrder,YM.OrderDate 
FROM $DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.yw1_ordersheet YS ON YS.POrderId=S.POrderId
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=YS.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=YS.Id
WHERE 1 AND S.Type='1' and M.CompanyId='$myCompanyId' and M.Estate='0' $SearchRows ORDER BY M.Date DESC,M.CompanyId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$Dir=anmaIn("../download/teststandard",$SinkOrder,$motherSTR);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);			
		$TestStandard=$myRow["TestStandard"];
		$POrderId=$myRow["POrderId"];
		include "../admin/Productimage/getPOrderImage.php";
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$sumAmount=$sumAmount+$Amount;
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];

		$Date=$myRow["Date"];
        $ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<a href=\"../admin/openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
			}
		else{
			$ClientOrder=$i;
			}
		//$Date=CountDays($Date,0);
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];		
		$sumQty=$sumQty+$Qty;
           
		//动态读取
		/*$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,0);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料. $GQty!=$LQty ' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";*/
		//0:内容	1：对齐方式		2:单元格属性		3：截取
			$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$OrderPO),
			array(0=>$TestStandard),
			array(0=>$eCode, 		3=>"..."),
			array(0=>$Unit, 		1=>"align='center'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='right'")
				);
		$checkidValue=$Id;
	   include "../admin/subprogram/read_model_6_yw.php";
		//echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
ChangeWtitle($SubCompany.$DefaultClient."OrderHistory");
?>