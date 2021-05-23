<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=600;
ChangeWtitle("$SubCompany 待出订单未领料列表");
$funFrom="ch_shippinglist";
$From=$From==""?"noll":$From;
$sumCols="8,9";			//求和列,需处理
$Th_Col="选项|60|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|售价|60|订单数量|60|金额|60|订单日期|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$ActioToS="90";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项 200907300601
//客户
$SearchRows=" and S.Estate='2' AND S.scFrom=0";
/*$clientResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	WHERE 1 AND S.Estate='2' GROUP BY M.CompanyId 
    UNION
	SELECT S.CompanyId,C.Forshort 
	FROM $DataIn.ch5_sampsheet S 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId WHERE 1  and S.Estate='1'
	",$link_id);
if($clientRow = mysql_fetch_array($clientResult)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ch_shippinglist_noll\")'>";
	do{			
		$thisCompanyId=$clientRow["CompanyId"];
		$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
		$Forshort=$clientRow["Forshort"];
		if($CompanyId==$thisCompanyId){
			echo"<option value='$thisCompanyId' selected>$Forshort</option>";
			$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
			$ModelCompanyId=$thisCompanyId;
			}
		else{
			echo"<option value='$thisCompanyId'>$Forshort</option>";					
			}
		}while ($clientRow = mysql_fetch_array($clientResult));
	echo"</select>&nbsp;";
	}*/

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr  $MaxStr ";
	

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,S.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 $SearchRows";
	//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$k=0;
	do{	
	    $m=1;	
	    $POrderId=$myRow["POrderId"];
		//******************检查领料记录 备料总数与领料总数比较
		$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType<2",$link_id));
		$blQty=$CheckblQty["blQty"];
		$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId'",$link_id));
		$llQty=$CheckllQty["llQty"];
	    if($llQty!=$blQty){
	          $checkResultB = mysql_query("SELECT * FROM $DataIn.yw9_blsheet WHERE POrderId='$POrderId' AND Estate='1'  LIMIT 1",$link_id);
	        if(!$checkRowB = mysql_fetch_array($checkResultB)){
		      $LockRemark="";
		      $OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		      $OrderDate=$myRow["OrderDate"];
		
		      $Id=$myRow["Id"];
		      $checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id LIMIT 1",$link_id);
		     if($checkExpressRow = mysql_fetch_array($checkExpress)){
			     $ColbgColor="bgcolor='#0066FF'";
			     }
		     else{
			     $ColbgColor="";
			     }
		     $ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		     $Qty=$myRow["Qty"];
		     $Price=$myRow["Price"];	
		     $Amount=sprintf("%.2f",$Qty*$Price);
		     $PackRemark=$myRow["PackRemark"]; 
		     $cName=$myRow["cName"]; 
		     $eCode=$myRow["eCode"]; 
		     $Description=$myRow["Description"];
		     $Type=$myRow["Type"];
		
		     $OrderPO=$Type==2?"随货项目":$OrderPO;
		     $checkidValue=$Id."^^".$Type;
		     $Locks=1;
		
		     $showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		    $StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		    $ValueArray=array(
			    array(0=>$OrderPO,1=>"align='center'"),
			    array(0=>$POrderId,1=>"align='center'"),
			    array(0=>$ProductId,1=>"align='center'"),
			    array(0=>$cName,3=>"..."),
			    array(0=>$eCode.$gxQty."/".$scQty,3=>"..."),
			    array(0=>$Price,1=>"align='center'"),
			    array(0=>$Qty,1=>"align='center'"),
			    array(0=>$Amount,1=>"align='center'"),
			    array(0=>$OrderDate,1=>"align='center'")
			     );
		        include "../model/subprogram/read_model_6.php";
		       echo $StuffListTB;
		       $k=1;
	           }//endif check
			 }//endif  if($llQty!=$blQty)
	       }while ($myRow = mysql_fetch_array($myResult));
	     if ($k==0) noRowInfo($tableWidth);
	 }
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>