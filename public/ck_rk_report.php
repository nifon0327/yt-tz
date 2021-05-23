<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
$Th_Col="序号|30|流水号|150|已收货总数|100|采购总数|100|提示|200";
include "../model/subprogram/read_model_3.php";
$myResult = mysql_query("SELECT R.StockId,SUM(R.Qty) AS QTY,D.StuffCname 
	FROM $DataIn.ck1_rksheet R
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=R.StuffId 
	WHERE R.StuffId=$StuffId AND R.Type=1 GROUP BY R.StockId ORDER BY R.Id desc",$link_id);
List_Title($Th_Col,"1",1);
$j=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StockId=$myRow["StockId"];
		$QTY=$myRow["QTY"];			  
		$StuffCname=$myRow["StuffCname"];
		$checkResult=mysql_query("SELECT SUM(FactualQty+AddQty) AS BuyQty FROM $DataIn.cg1_stocksheet WHERE StockId=$StockId",$link_id);
		if($checkRrow = mysql_fetch_array($checkResult)){
			$errorSTR="正常";
			$BuyQty=$checkRrow["BuyQty"];
			if($BuyQty==0){
				$errorSTR="<div class='style2'>出错1：无需采购</div>";
				$BuyQty="×";
				}
			}
		else{
			$errorSTR="<div class='style2'>出错2：没有此需求流水号</div>";
			$BuyQty="×";
			}
		if($QTY>$BuyQty){
			$errorSTR="<div class='style2'>出错3：收货数大于采购数</div>";
			$BuyQty="×";
			}
		$ChooseOut="N";
		$ValueArray=array(
			0=>array(0=>$StockId,
					 1=>"align='center'"),
			1=>array(0=>$QTY,
					 1=>"align='center'"),
			2=>array(0=>$BuyQty,
					 1=>"align='center'"),
			3=>array(0=>$errorSTR)
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
