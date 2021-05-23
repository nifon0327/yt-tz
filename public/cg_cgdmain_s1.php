<?php 

include "../model/subprogram/s1_model_1.php";
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.stuffdata
$DataIn.ck9_stocksheet
$DataIn.cg1_stocksheet
二合一已更新
*/
//include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 待购列表");
$funFrom="cg_cgdsheet";
$From=$From==""?"read":$From;
$sumCols="11";			//求和列,需处理
$Th_Col="选项|40|序号|30|采购流水号|90|配件ID|45|配件名称|200|历史<br>单价|40|单价|50|订单<br>数量|40|使用<br>库存|40|需购<br>数量|40|增购<br>数量|40|实购<br>数量|40|金额|55|审核|35|增购备注|160|可用<br>库存|40";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
//$Parameter.=",Bid,$CompanyId,Jid,$BuyerId";
//From=slist&tSearchPage=$tSearchPage&fSearchPage=$fSearchPage&SearchNum=$SearchNum&Action=$Action&uType=$uType&Bid=$Bid&Jid=$Jid&Kid=$Kid&Month=$Month' 这几个变量一般从上一个调用的页面加入
$ReturnParameter="CompanyId|$CompanyId|BuyerId|$BuyerId";  //这几个要保持不变的值，最终要返回到本页面的,也就是点击查询后不变的.
$Parameter.=",CompanyId,$CompanyId,BuyerId,$BuyerId,ReturnParameter,$ReturnParameter";  //这几个要带过去，也就是要带到 _s2.php


$nowWebPage=$funFrom."_read";
//include "../model/subprogram/read_model_3.php";
include "../model/subprogram/s1_model_3.php";
$sSearch=$From!="slist"?"":$sSearch;
/*
if ($sSearch!="") {
	$SearchRows=" and S.CompanyId=$Bid and S.BuyerId=$Jid";
}
else {
	$SearchRows=" and S.CompanyId=$CompanyId and S.BuyerId=$BuyerId";
}
*/
$SearchRows=" and S.CompanyId=$CompanyId and S.BuyerId=$BuyerId";

//步骤4：需处理-可选条件下拉框
$otherAction="<span onClick='Comeback($Action)' $onClickCSS>确定</span>&nbsp;";//自定义功能
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
//include "../model/subprogram/read_model_5.php";
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Picture 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
WHERE 1 $SearchRows $sSearch and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) and S.Estate=0 ORDER BY S.StuffId DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$LockRemark="";
		$OrderSignColor=$myRow["POrderId"]==""?"bgcolor='#FFCC99'":"";
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];
		$StuffId=$myRow["StuffId"];		
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$BackData=$StockId."^^".$StuffCname."^^".$Price."^^".$OrderQty."^^".$StockQty."^^".$FactualQty."^^".$AddQty;	
		$Qty=$AddQty+$FactualQty;
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计
		$Estate=$myRow["Estate"];				
		$AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
		$Locks=$myRow["Locks"];		
		//可用库存计算
		if($StuffId!=$tempStuffId){
			$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
			$oStockQty=$checkKC["oStockQty"];
			$tempStuffId=$StuffId;
			//历史单价,最大值和最小值
			$checkPrice=mysql_query("SELECT MAX(Price) AS maxPrice,MIN(Price) AS minPrice FROM $DataIn.cg1_stocksheet WHERE Mid>0 and StuffId='$StuffId' ORDER BY StuffId",$link_id);
			$maxPrice=mysql_result($checkPrice,0,"maxPrice");
			$minPrice=mysql_result($checkPrice,0,"minPrice");
			if($maxPrice==""){
				$PriceInfo="&nbsp;";
				}
			else{
				$PriceInfo="<a href='cg_historyprice.php?StuffId=$StuffId' target='_blank' title='最低历史单价: $minPrice 最高历史单价: $maxPrice'>查看</a>";
				}
			}
		//清0
		$OrderQty=zerotospace($OrderQty);
		$StockQty=zerotospace($StockQty);
		$FactualQty=zerotospace($FactualQty);
		$AddQty=zerotospace($AddQty);
		$oStockQty=zerotospace($oStockQty);
		if($Estate==1){
			$LockRemark="需审核";
			}
		$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$ValueArray=array(
			array(0=>$StockId,
					 1=>"align='center'"),
			array(0=>$StuffId,
					 1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$PriceInfo,
					 1=>"align='center'"),
			array(0=>$Price,					
					 1=>"align='right'"),
			array(0=>$OrderQty,
					 1=>"align='right'"),
			array(0=>$StockQty,
					 1=>"align='right'"),
			array(0=>$FactualQty,
					 1=>"align='right'"),
			array(0=>$AddQty,
					 1=>"align='right'"),
			array(0=>$Qty,
					 1=>"align='right'"),
			array(0=>$Amount,
					 1=>"align='right'"),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$AddRemark),
			array(0=>$oStockQty,
					 1=>"align='center'")
			);
		$checkidValue=$BackData;
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
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);//visibility:hidden;
?>
<script  type=text/javascript>
//返回选定的采购流水号
function Comeback(Action){
	var returnq="";
	var j=1;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if(e.checked){
				if (j==1){
					returnq=e.value;j++;
					}
				else{
					returnq=returnq+""+e.value;j++;
					}					
				} 
			}
		}
	returnValue=returnq;
	this.close();
	}
</script>