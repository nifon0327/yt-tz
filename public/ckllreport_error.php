<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 领料分析");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
$Th_Col="序号|40|错误提示|200|配件ID|50|配件名称|200|需领料数|55|本次领料|55|采购单流水号|90|所属产品名称|300|业务单流水号|90|操作|80";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
 //echo "<input type='button' name='Desk_Old_Ver' id='Desk_Old_Ver' value='修正出了货但没领料问题'  onclick='ToOld();' /> ";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

//1、领了料没业务订单号的，或没有采购流水号
$mySql="select * from (";
$mySql.="select '1' as sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,S.StockId as StockId,P.cName,Y.PorderId as PorderId
FROM $DataIn.ck5_llsheet S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE Y.PorderId is NULL or G.StockID is NULL  ";

//2、出了货但没领料的!
$mySql.="  UNION ALL 
select '2' as sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,G.StockId as StockId,P.cName,C.PorderId as PorderId
FROM $DataIn.ch1_shipsheet C
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=C.POrderId
LEFT JOIN $DataIn.ck5_llsheet S ON S.StockId=G.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
WHERE S.StockId is NULL AND G.OrderQty>0 AND T.mainType<2";


//3.出了货，但领料数量 不相等的,不等于采购订单号,这个有可能业务下错单，后来又重新更改了,但更改前已领料了,只用来参考。
$mySql.="  UNION ALL 
select '3' as Sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,S.StockId as StockId,P.cName,C.PorderId as PorderId
FROM (select sum(Qty) as Qty, StockId from  $DataIn.ck5_llsheet GROUP BY StockId) S
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.ch1_shipsheet C  ON C.POrderId=G.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
WHERE  (C.POrderId is Not NULL ) AND  S.Qty!=G.OrderQty    ";

$mySql.=") A order by sign,PorderId desc,StockId ";
//echo "$mySql";
$LastStockId="";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//sign,D.StuffId,D.StuffCname,G.OrderQty,S.Qty,G.StockId as StockId,P.cName,C.PorderId as PorderId
		$sign=$myRow["sign"];
		$StuffId=$myRow["StuffId"]==""?"&nbsp;":$myRow["StuffId"];		
		$StuffCname=$myRow["StuffCname"]==""?"&nbsp;":$myRow["StuffCname"];
		$OrderQty=$myRow["OrderQty"]==""?"&nbsp;":$myRow["OrderQty"];
		$Qty=$myRow["Qty"]==""?"&nbsp;":$myRow["Qty"];
		$StockId=$myRow["StockId"]==""?"&nbsp;":$myRow["StockId"];
		if($LastStockId!=$StockId){
			$LastStockId=$StockId;
			$errorcolor="";
			$reout="";
		}
		else{
			$LastStockId=$StockId;
			$errorcolor=" bgColor='#FFCC00' ";
			$reout="(！！重复出货！！！)";
		}
		
		
		$cName=$myRow["cName"]==""?"&nbsp;":$myRow["cName"];		
		$PorderId=$myRow["PorderId"]==""?"&nbsp;":$myRow["PorderId"];
        $Opration="&nbsp;";
		switch ($sign)
		{
			case 1: 
					$sign="领了料没有业务单流水号"; break;
			case 2: 
					$sign="出了货但没领料";
					$Opration="<input type='button' name='Desk_Old_Ver' id='Desk_Old_Ver' value='现在领料'  onclick='ToOld($PorderId);' />";
					break;
			case 3: 
					$sign="领料数量不对".$reout;
					$Opration="<input type='button' name='Desk_Old_Ver' id='Desk_Old_Ver' value='现在纠正'  onclick='ToLL($StockId);'   />";
					break;					
		}
		
			//$OrderSignColor="bgcolor='#FF6633'";
			//$myOpration="<a href='stuffreport_result.php?Idtemp=$StuffId&Nametemp=$StuffCname' target='_blank'>分析</a>";
			//$myOpration="<a href='' target='_blank'></a>";
			$ChooseOut="N";
			$ValueArray=array(
				array(0=>$sign,
						 1=>" $errorcolor align='Left'"),			  
				array(0=>$StuffId,
						 1=>"align='Left'"),
				array(0=>$StuffCname,
						 3=>"..."),
				array(0=>$OrderQty,
						 1=>"align='right'"),
				array(0=>$Qty,					
						 1=>"align='right'"),
				array(0=>$StockId,
						 1=>"align='Left'"),
				array(0=>$cName,
						 1=>"align='Left'"),
				array(0=>$PorderId,
						 1=>"align='Left'"),
				array(0=>$Opration,
						 1=>"align='center'"),				
				
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';

List_Title($Th_Col,"0",0);
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>

<script language="javascript" type="text/JavaScript">
function ToOld(PorderId)
{
	//alert(PorderId);
	var url="ckllcorrect_error.php?PorderId="+PorderId;
	//alert (url);
	window.open(url,"_self");

}
function ToLL(StockId)
{
	//alert(PorderId);
	var url="ckllcorrect_LL.php?StockId="+StockId;
	//alert (url);
	window.open(url,"_self");

}
</script>