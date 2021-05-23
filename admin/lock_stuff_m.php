<?php 
include "../model/modelhead.php";
$ColsNumber=23;
$tableMenuS=500;
ChangeWtitle("$SubCompany 配件锁定审核列表");
$funFrom="lock_stuff";
$From=$From==""?"m":$From;
$sumCols="15,16,17,18,19,20";			//求和列,需处理
$Th_Col="选项|60|序号|30|PO|80|采购流水号|90|下单<br>时间|45|配件ID|45|配件名称|280|图档|30|QC图|40|认证|40|开发|40|送货</br>楼层|40|历史<br>资料|40|单价|50|单位|45|订单<br>数量|40|使用<br>库存|40|需购<br>数量|40|增购<br>数量|40|实购<br>数量|40|金额|55|锁单备注|250|锁单人|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//$SearchRows.=" AND (T.mainType<2)";//需采购的配件需求单
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
//采购
		/*$buyerSql = mysql_query("SELECT  L.Operator AS Number,M.Name 
	FROM $DataIn.cg1_lockstock  L 
    LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId
	LEFT JOIN $DataPublic.staffmain M ON L.Operator=M.Number 
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty>0 OR S.AddQty>0) AND L.Estate>0  AND L.Locks=0 GROUP BY L.Operator ORDER BY L.Operator",$link_id);
	
	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='Number' id='Number' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$thisNumber=$buyerRow["Number"];
			$ClientName=$buyerRow["Name"];
			if ($Number==$thisNumber){
				echo "<option value='$thisNumber' selected>$ClientName</option>";
				$SearchRows.=" AND L.Operator='$thisNumber'";
				}
			else{
				echo "<option value='$thisNumber'>$ClientName</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}*/
	}
	$ActioToS="17,15";
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain WHERE Number=$Login_P_Number order by Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$JobId=$checkRow["JobId"];//3为采购
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr ";
//步骤5：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT C.Forshort AS Client,L.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,
S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,
A.StuffCname,P.cName,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.SendFloor,A.TypeId,A.DevelopState,A.Price AS  DefaultPrice ,U.Name AS UnitName,A.ForcePicSpe,T.ForcePicSign,TIMESTAMPDIFF(DAY,S.ywOrderDTime,NOW()) AS xdDays,L.Remark AS LockRemark,L.Operator AS LockOperator
	FROM $DataIn.cg1_lockstock  L 
    LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId  
WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0)  AND L.Estate=1  AND L.Locks=0 ORDER BY S.Estate DESC,S.ywOrderDTime ";
//echo $mySql;
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
		$Leadtime=$myRow["Leadtime"];				
		
		$theDefaultColor=$DefaultBgColor;
		$OrderSignColor=$POrderId==""?"bgcolor='#FFCC99'":"";
		
		$xdDays=$myRow["xdDays"];
		$xdDays=$xdDays==0?"today":$xdDays . "d";
		
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];
		//加急订单标色
		include "../model/subprogram/cg_cgd_jj.php";
		$StuffId=$myRow["StuffId"];
		$cName=$myRow["cName"];
		$Client=$myRow["Client"];
		$LockStockId=$StockId;
		$StockId="<div title='$Client : $cName'>$StockId</div>";
		$StuffCname=$myRow["StuffCname"];
		$TypeId=$myRow["TypeId"];
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
				
		$ForcePicSpe=$myRow["ForcePicSpe"];
		$ForcePicSign=$myRow["ForcePicSign"];
		if ($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
			$ForcePicSign=$ForcePicSpe;  
		}
		
		
		
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$OrderQty=$myRow["OrderQty"];
		$StockQty=$myRow["StockQty"];
		$AddQty=$myRow["AddQty"];
		$FactualQty=$myRow["FactualQty"];
		$Qty=$AddQty+$FactualQty;
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计

				//默认单价
		 $DefaultPrice=$myRow["DefaultPrice"];
        $PriceTitle="";
		if($DefaultPrice!=$Price){
			$Price="<div class='redB'>$Price</div>";
			$PriceTitle="Title=\"默认单价：$DefaultPrice\"";
		}
			//默认供应商
      $CompanyId=$myRow["CompanyId"];
		$providerRes=mysql_query("SELECT S.CompanyId,P.Forshort FROM $DataIn.bps S 
								LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
								WHERE S.StuffId='$StuffId'",$link_id);
		if($providerRow=mysql_fetch_array($providerRes)){
			$DefaultCompanyId=$providerRow["CompanyId"];
			$DefaultForshort=$providerRow["Forshort"];
		}
		
		if($DefaultCompanyId!=$CompanyId && $DefaultCompanyId!=""){
			$WarnRemark="默认供应商已更改为:$DefaultForshort";
			$OrderSignColor="bgcolor='#FFFF00'";
		 }
	     else{
		   $WarnRemark="";
	      }

		$Estate=$myRow["Estate"];
        $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];        
                $StockRemark=$myRow["StockRemark"];
                $StockRemarkTB="<input type='hidden' id='StockRemark$i' name='StockRemark$i' value='$StockRemark'/>";
                if ($StockRemark=="") {
                    $StockRemark="&nbsp;";
                   }
                else{
                   $StockRemark="<div title='$StockRemark'><img src='../images/remark.gif'></div>"; 
                }
                $AddRemark=$myRow["AddRemark"]==""?"&nbsp;":$myRow["AddRemark"];
          $tempLockRemark=$myRow["LockRemark"]==""?"&nbsp;":$myRow["LockRemark"];
		
		$checkKC=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId",$link_id));
		$oStockQty=$checkKC["oStockQty"];
		$mStockQty=$checkKC["mStockQty"]==0?"&nbsp;":$checkKC["mStockQty"];

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

      $Operator=$myRow["LockOperator"];
			include "../model/subprogram/staffname.php";		
			
		$Estate=$Estate==0?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";


		$Locks=0;
		/*加入同一单的配件  // add by zx 2011-08-04 */
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
			array(0=>$OrderPO, 		1=>"align='center'"),			  
			array(0=>$StockId, 		1=>"align='center'"),
			array(0=>$xdDays, 		1=>"align='center'"),
			array(0=>$StuffId,		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile,		1=>"align='center'"),
			array(0=>$QCImage, 	1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
			array(0=>$DevelopState, 	1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$OrderQtyInfo, 1=>"align='center'"),
			array(0=>$Price,	 	1=>"align='right' $PriceTitle"),
			array(0=>$UnitName,	 	1=>"align='center'"),
			array(0=>$OrderQty,		1=>"align='right'"),
			array(0=>$StockQty,		1=>"align='right'"),
			array(0=>$FactualQty, 	1=>"align='right'"),
			array(0=>$AddQty, 		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='right'"),
            array(0=>$tempLockRemark),
			array(0=>$Operator, 		1=>"align='center'")
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
