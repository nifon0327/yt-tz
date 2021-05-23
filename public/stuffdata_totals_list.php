<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
//$Th_Col="操作|30|序号|30|项目|80|日期|80|相应单号|150|订单号|120|中文名|235|采购流水号<br>需求流水号|120|数量|80";
$Th_Col="序号|30|项目|80|日期|80|相应单号|150|订单号|120|中文名|235|采购流水号<br>需求流水号|120|数量|80";
include "../model/subprogram/read_model_3.php";
include "../model/stuffcombox_function.php";

echo "配件ID:$StuffId"; 
List_Title($Th_Col,"1",1);
//$StuffId="102163";
$subStuffbox=check_stuffbox_sub('',$StuffId,$DataIn,$link_id);

$UnionSTR="";
if ($Sign==1){
//入库总数
$UnionSTR.="
	
	SELECT 7 AS Sign,'入库' as Item,M.Date as Date,M.BillNumber as No,Y.POrderId as POrderId,P.cName as Name,R.StockId as StockId,R.Qty AS Qty 
	FROM $DataIn.ck1_rksheet R 
	LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id
	LEFT JOIN $DataIn.cg1_stocksheet K ON K.StockId=R.StockId 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=K.POrderId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	WHERE R.StuffId='$StuffId' AND  DATE_FORMAT(M.Date ,'%Y-%m')='$Month'";
	
	//备品转入总数
$UnionSTR.="
UNION ALL
SELECT 8  AS Sign,'备品转入' as Item,Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,Qty AS Qty FROM $DataIn.ck7_bprk WHERE StuffId='$StuffId' AND  Estate=0 AND DATE_FORMAT(Date ,'%Y-%m')='$Month'";

}
else{
	//领料总数
	$UnionSTR.="
	SELECT 9 AS Sign,'领料' as Item,M.Date as Date,'&nbsp;' as No,IFNULL(Y.POrderId, 'Error' ) as POrderId,P.cName as Name,S.StockId as StockId,S.Qty AS Qty 
	FROM $DataIn.ck5_llsheet S 
	LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id
	LEFT JOIN $DataIn.cg1_stocksheet K ON K.StockId=S.StockId 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=K.POrderId
	LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	WHERE S.StuffId='$StuffId' AND DATE_FORMAT(M.Date ,'%Y-%m')='$Month'";
	
	$UnionSTR.="
	UNION ALL
	SELECT 10 AS Sign,'报废' as Item,Date,'&nbsp;' as No,'&nbsp;' as PorderId,'&nbsp;' as Name, '&nbsp;' as StockId,Qty AS Qty FROM $DataIn.ck8_bfsheet WHERE Estate=0 AND  StuffId='$StuffId' AND DATE_FORMAT(Date ,'%Y-%m')='$Month' ";
}

//echo "$UnionSTR";
$UnionSTR="select * from ($UnionSTR) A order by Sign, Date ";
$myResult = mysql_query($UnionSTR,$link_id);



$j=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Sign=$myRow["Sign"];
		
		$Item=$myRow["Item"]==NULL?"&nbsp;":$myRow["Item"];
		//$Date=$myRow["Date"];
		$Date=$myRow["Date"]==NULL?"&nbsp;":$myRow["Date"];
		//$No=$myRow["No"];		
		$No=$myRow["No"]==NULL?"&nbsp;":$myRow["No"];
		$POrderId=$myRow["POrderId"];		
		//$cName=$myRow["Name"];
		$cName=$myRow["Name"]==NULL?"&nbsp;":$myRow["Name"];
		//$StockId=$myRow["StockId"];
		$StockId=$myRow["StockId"]==NULL?"&nbsp;":$myRow["StockId"];
		
		$Qty=$myRow["Qty"];			  
		//if($Sign in(1,2,3,8) && $POrderId==NULL)  //订单、采购、领料的 PorderId为空，说明有问题
                $delclick="";
        if ($subStuffbox){
	           $comboxResult=mysql_fetch_array(mysql_query("SELECT mStockId FROM $DataIn.cg1_stuffcombox WHERE StockId='$StockId' and StuffId='$StuffId' LIMIT 1",$link_id));
	           $POrderId=$comboxResult["mStockId"];
        }
        else{
			if($POrderId=='Error'){
	                    if ($myRow["StockId"]==NULL){
				$POrderId="<div id='PId' name='PId' class='redB'>×</div>";
	                    }else{
	                        $POrderId="<div id='PId' name='PId' class='redB'>×</div>"; 
	                       // $delclick=" onclick='delLlQty(this,$StockId)'";
	                    }
			}
		}
		$ChooseOut="N";	
		$ValueArray=array(
			0=>array(0=>$Item,
					 1=>"align='Left'"),
			1=>array(0=>$Date,
					 1=>"align='center'"),
			2=>array(0=>$No,
					 1=>"align='Left'"),
			3=>array(0=>$POrderId,
					 1=>"align='center'  $delclick"),
			4=>array(0=>$cName,
					 1=>"align='Left'"),
			5=>array(0=>$StockId,
					 1=>"align='center'"),
			6=>array(0=>$Qty,
					 1=>"align='Left'"),	
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);

?>