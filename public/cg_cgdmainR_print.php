<?php 
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=700;
ChangeWtitle("$SubCompany 未收货列表");
$sumCols="";			//求和列,需处理
$MergeRows=3;$ChooseOut="N";

$Th_Col="序号|30|下单日期|80|采购单号|70|供应商|100|配件名称|250|未收数量|60|单价|60|采购流水号|90";
//必选，分页默认值
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框

$SearchRows=" AND S.rkSign>0 AND DATE_FORMAT(M.Date,'%Y-%m')='$chooseDate' AND M.CompanyId='$CompanyId'";//未收完货
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$myResult=mysql_query("SELECT M.Date,M.PurchaseID,S.StockId,S.Price,(S.AddQty+S.FactualQty) AS Qty,A.StuffCname,P.Forshort
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
WHERE 1 $SearchRows AND S.Mid>0 ORDER BY S.StuffId DESC",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Date=$myRow["Date"];
		$PurchaseID=$myRow["PurchaseID"];
		$Forshort=$myRow["Forshort"];
		$StuffCname=$myRow["StuffCname"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$StockId=$myRow["StockId"];
		//收货情况				
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$Qty-$rkQty;
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$PurchaseID,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$StuffCname),
			array(0=>$unQty,1=>"align='center'"),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'")
			);
		include "../model/subprogram/read_model_6.php";
		}while($myRow = mysql_fetch_array($myResult));
	}
List_Title($Th_Col,"0",1);
?>