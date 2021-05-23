<?php 
include "../model/modelhead.php";
$ColsNumber=23;
$tableMenuS=500;
ChangeWtitle("$SubCompany 采购各周订单列表");
$funFrom="cg_cgdmain";
$From=$From==""?"tj":$From;
$sumCols="14,15,16,17,18,19,20,21";			//求和列,需处理
$Th_Col="选项|60|序号|30|供应商|120|PO|80|采购流水号|90|配件ID|45|配件名称|200|图档|30|QC图|40|认证|40|送货</br>楼层|40|历史<br>资料|40|单价|50|单位|45|订单<br>数量|40|使用<br>库存|40|需购<br>数量|40|增购<br>数量|40|实购<br>数量|40|待检<br>数量|40|入库<br>数量|40|未送<br>数量|50|采购<br>备注|230|增购备注|160";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$nowWebPage=$funFrom."_tj";
include "../model/subprogram/read_model_3.php";



$SearchRows=" AND S.BuyerId=$BuyerId AND YEARWEEK(S.DeliveryDate,1)='$Weeks'";

	//供应商
	$providerSql= mysql_query("SELECT 
	M.CompanyId,V.Forshort,V.Letter 
	FROM $DataIn.cg1_stockmain M 
   LEFT JOIN $DataIn.cg1_stocksheet  S ON S.Mid=M.Id 
	LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId
	WHERE 1 $SearchRows  AND V.Estate=1 AND S.Mid>0 AND S.rkSign>0 GROUP BY V.CompanyId ORDER BY V.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
				echo"<option value=''>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
		//	$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and V.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr ";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT C.Forshort AS Client,S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,
S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,
A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.SendFloor,A.TypeId,A.DevelopState,A.Price AS  DefaultPrice ,U.Name AS UnitName,V.Forshort AS GysForshort
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId  
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=S.CompanyId
WHERE 1 $SearchRows and S.Mid>0 AND S.rkSign>0 ORDER BY S.Estate DESC,S.StockId DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$LockRemark="";
		$OrderPO=toSpace($myRow["OrderPO"]);
		$POrderId=$myRow["POrderId"];
		$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
		$PQty=$myRow["PQty"];
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$ShipType=$myRow["ShipType"];		
		
		$theDefaultColor=$DefaultBgColor;
		$OrderSignColor=$POrderId==""?"bgcolor='#FFCC99'":"";
		
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];
		//加急订单标色
		include "../model/subprogram/cg_cgd_jj.php";
		$StuffId=$myRow["StuffId"];
		$cName=$myRow["cName"];
		$Client=$myRow["Client"];
		$LockStockId=$StockId;
		$StockIdStr="<div title='$Client : $cName'>$StockId</div>";
		$StuffCname=$myRow["StuffCname"];
		$TypeId=$myRow["TypeId"];
        $GysForshort=$myRow["GysForshort"];
		//配件QC检验标准图
        $QCImage="";
        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage=$QCImage==""?"&nbsp;":$QCImage;
		$Gremark=$myRow["Gremark"];
		$Gfile=$myRow["Gfile"];
		$tempGfile=$Gfile;  ////2012-10-29
		$Gstate=$myRow["Gstate"];
		//REACH 法规图
		include "../model/subprogram/stuffreach_file.php";
		//=====
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		//检查是否有图片
		$Picture=$myRow["Picture"];
		include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
		
		
		
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$Qty=$AddQty+$FactualQty;
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计
		$Estate=$myRow["Estate"];
        $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];        
                $StockRemark=$myRow["StockRemark"];

                $AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];

		
		$Locks=1;
		$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
		$oStockQty=$checkKC["oStockQty"];
		$mStockQty=$checkKC["mStockQty"]==0?"&nbsp;":$checkKC["mStockQty"];
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;

			       $shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet 
			       WHERE 1 AND SendSign=0 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId",$link_id);  
			       $shQty=mysql_result($shSql,0,"Qty");
			       $shQty=$shQty==""?0:$shQty;
        $lessQty=$Qty-$rkQty-$shQty;
		//清0
		
		$checkNum=mysql_query("SELECT S.StuffId FROM $DataIn.cg1_stocksheet S
	                          WHERE S.StuffId=$StuffId and S.Mid!=0 LIMIT 1",$link_id);
		if($checkRow=mysql_fetch_array($checkNum)){
		  $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>"; 
		}
		else{
		 $OrderQtyInfo="&nbsp;";}
		
		$OrderQty=zerotospace($OrderQty);
		$StockQty=zerotospace($StockQty);
		$FactualQty=zerotospace($FactualQty);
		$AddQty=zerotospace($AddQty);
		$oStockQty=zerotospace($oStockQty);
		if ($mStockQty>0){
			$mStockColor="title='最低库存:$mStockQty'";
			$oStockQty="<span style='color:#FF9900;font-weight:bold;'>$oStockQty</span>";
			}
		else{
			$mStockColor="";	
			}

		$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
	  	

		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$XtableWidth=0;
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $cName)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
			</tr>
			
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
	
		$ValueArray=array(
			array(0=>$GysForshort),		
			array(0=>$OrderPO, 		1=>"align='center'"),			  
			array(0=>$StockIdStr, 		1=>"align='center'"),
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,		1=>"align='center'"),
			array(0=>$QCImage, 	1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$OrderQtyInfo, 1=>"align='center'"),
			array(0=>$Price,	 	1=>"align='right' $PriceTitle"),
			array(0=>$UnitName,	 	1=>"align='center'"),
			array(0=>$OrderQty,		1=>"align='right'"),
			array(0=>$StockQty,		1=>"align='right'"),
			array(0=>$FactualQty, 	1=>"align='right'"),
			array(0=>$AddQty, 		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$shQty, 		1=>"align='right'"),
			array(0=>$rkQty, 		1=>"align='right'"),
			array(0=>$lessQty, 		1=>"align='right'"),
			array(0=>$StockRemark),
            array(0=>$AddRemark)
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;echo $StockRemarkTB;	
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
