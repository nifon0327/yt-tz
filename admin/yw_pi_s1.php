<?php   
//电信-zxq 2012-08-01
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|售价|60|订单数量|60|金额|60|订单日期|70";
$ColsNumber=9;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
$SearchSTR=0;		//不允许搜索
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
if (($Jid==1004) || ($Jid==1059)){
	$SearchRows1=" and S.Estate>0 AND M.CompanyId in (1004,1059)";
}
else {
	$SearchRows1=" and S.Estate>0 AND M.CompanyId='$Jid'";
}

$SearchRows1=" AND M.CompanyId='$Jid' and (S.Estate>0  OR M.OrderDate>='2014-01-01') ";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="
	SELECT M.OrderNumber,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,S.Estate  
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.yw3_pisheet I ON I.oId=S.Id
	WHERE 1 AND I.oId IS NULL $SearchRows1	
";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];		
		$Estate=$myRow["Estate"];
		$OrderSignColor=$Estate==0?" bgcolor='#FF0000' ":"";
		
		$checkidValue=$Id."^^".$OrderPO."^^".$cName."^^".$eCode."^^".$Price."^^".$Qty;
		$Locks=1;
		$ValueArray=array(
			array(0=>$OrderPO,
					 1=>"align='center'"),
			array(0=>$POrderId,
					 1=>"align='center'"),
			array(0=>$ProductId,
					 1=>"align='center'"),
			array(0=>$cName,
					 3=>"..."),
			array(0=>$eCode,
					 3=>"..."),
			array(0=>$Price,
					 1=>"align='center'"),
			array(0=>$Qty,					
					 1=>"align='center'"),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$OrderDate,
					 1=>"align='center'")
			);
		
		include "../model/subprogram/read_model_6.php";
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