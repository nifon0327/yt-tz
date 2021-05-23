<?php
/*
//电信-zxq 2012-08-01
5.24加入未补数量
*/
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=24;
$tableMenuS=600;
ChangeWtitle("$SubCompany 采购单列表(结付分列)");
$funFrom="cg_cgdmainp";
$From=$From==""?"read":$From;
$Estate=$Estate==""?1:$Estate;
$sumCols="9,10,11,14,15,16,17,18,19";			//求和列,需处理
$MergeRows=3;
$Th_Col="操作|30|采购单号|60|备注|30|选项|60|行号|30|配件ID|40|配件名称|200|图档|30|历史订单|60|QC图|40|认证|40|品检<br>报告|40|需求数|45|增购数|45|实购数|45|含税价|45|单位|40|金额|60|金额(RMB)|60|收货数|45|领料数|45|未收数|45|未补数|45|货款|30|采购日期|70|交货日期|70|采购流水号|100|供应商|80|预付金额|60|出货日期|70";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量
$ActioToS="1";
if($Estate==1 && $From!="slist"){
	$otherAction="<span onclick='javascript:showMaskDiv()' $onClickCSS>请款</span>";
	}
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
//结付状态
	echo"<select name='Estate' id='Estate' onchange='zhtj(this.name)'>";
	echo"<option value='1' $EstateSTR1>未请款</option>";
	echo"<option value='2' $EstateSTR2>请款中</option>";
	echo"<option value='3' $EstateSTR3>请款通过</option>";
	echo"<option value='0' $EstateSTR0>已结付</option>";
	echo"</select>&nbsp;";
	if($Estate==1){
		$SearchRows=" and F.Estate IS NULL";
		}
	else{
		$SearchRows=" and F.Estate='$Estate'";
		}
//采购
	$BuyerId=$BuyerId==""?$Login_P_Number:$BuyerId;
	/*$buyerSql = mysql_query("SELECT S.BuyerId,A.Name,A.Estate,A.OrderByKey
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN(
		SELECT Name,Estate,OrderByKey,Number FROM (
			SELECT Name,Estate,BranchId AS OrderByKey,Number
				FROM $DataPublic.staffmain
				WHERE 1 AND BranchId<>3 AND Estate<2
			UNION ALL
			SELECT Name,'0' AS Estate,BranchId AS OrderByKey,Number
				FROM $DataPublic.staffmain
				WHERE 1 AND BranchId<>3 AND Estate=2
			UNION ALL
				SELECT Name,Estate,'0' AS OrderByKey,Number
				FROM $DataPublic.staffmain
				WHERE 1 AND BranchId=3
			)B
		)A ON S.BuyerId=A.Number
	LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
	WHERE S.Mid>0 $SearchRows GROUP BY S.BuyerId ORDER BY A.Estate DESC,A.OrderByKey,S.BuyerId DESC",$link_id);*/
  $buyerSql = mysql_query("SELECT S.BuyerId,A.Name,(case A.Estate When 2 then 0 else A.Estate  end) AS Estate,(case A.BranchId When 3 then 0 else A.BranchId  end) as OrderByKey  
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataPublic.staffmain A ON S.BuyerId=A.Number 
	LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId 
	WHERE S.Mid>0 $SearchRows GROUP BY S.BuyerId ORDER BY Estate DESC,OrderByKey,S.BuyerId DESC",$link_id);
	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='BuyerId' id='BuyerId' onchange='zhtj(this.name)'>";
		do{
			$thisBuyerId=$buyerRow["BuyerId"];
			$Buyer=$buyerRow["Name"];
			if($FristBurerId=="")$FristBurerId=$thisBuyerId;
			$FontColor="";
			if($buyerRow["Estate"]!=1 || $buyerRow["OrderByKey"]>0){
				$FontColor="style='color:#99CC99'";
				}
			if ($BuyerId==$thisBuyerId){
				echo "<option value='$thisBuyerId' $FontColor selected>$Buyer</option>";
				$SearchRows1=" and S.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId' $FontColor>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}
	$SearchRows.=$SearchRows1==""?" and S.BuyerId='$FristBurerId'":$SearchRows1;
//结付方式
	$GysPayMode=$GysPayMode==""?0:$GysPayMode;
	echo"<select name='GysPayMode' id='GysPayMode' onchange='zhtj(this.name)'>";
	 switch($GysPayMode){
	 	case "1":
	 		echo"<option value='0'>月结</option><option value='1' selected>现金</option>";
			break;
	 	default:
	 		echo"<option value='0' selected>月结</option><option value='1'>现金</option>";
			break;
	 	}
	 echo"</select>&nbsp;";
//供应商
	$providerSql= mysql_query("SELECT 
	S.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
	WHERE S.Mid>0 $SearchRows AND P.GysPayMode='$GysPayMode' GROUP BY S.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
//月份
		$date_Result = mysql_query("SELECT M.Date 
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
		LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId		
		WHERE 1 $SearchRows AND S.Mid>0 group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Id DESC",$link_id);

		if ($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				$StartDate=$dateValue."-01";
				$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
				//$dateText=date("Y年m月",strtotime($dateRow["Date"]));
				$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					//$SearchRows.=" and  DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
					$SearchRows.=" and M.Date>='$StartDate' and M.Date<='$EndDate'";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
			}
		else{
			//无月份记录
			$SearchRows.=" and M.Date=''";
			}
		}
	else{
		//无供应商记录
		$SearchRows.=" and M.CompanyId=''";
		}
	}
else {
	$SearchRows=str_replace('F.Estate =1','F.Estate is Null',$SearchRows);
}
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain where Number=$Login_P_Number order by Id LIMIT 1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$JobId=$checkRow["JobId"];//3为采购
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,U.Name AS UnitName,
A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId,C.Forshort AS Client,P.cName,P.TestStandard,P.ProductId,H.Date as OutDate,V.Forshort
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN  $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
Left Join $DataIn.ch1_shipsheet E ON E.PorderId=S.PorderId
Left Join $DataIn.ch1_shipmain H ON H.Id=E.Mid
WHERE 1 $SearchRows  AND S.Mid>0  ORDER BY M.PurchaseID DESC";
//echo $mySql;

$mainResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$mainRows["Id"];
		$theDefaultColor=$DefaultBgColor;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$Dates=$Date."：".CountDays($Date,"");
		$PurchaseID=$mainRows["PurchaseID"];
		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$mainRows[Remark]' width='16' height='16'>";
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cg_cgdmain_upmain\",$Mid)' src='../images/edit.gif' alt='更新采购单资料!采购日期：$Dates' width='13' height='13'>";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$FactualQty=$mainRows["FactualQty"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$OrderPO=$mainRows["OrderPO"];
			$POrderId=$mainRows["POrderId"];
			$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
			$PQty=$mainRows["PQty"];
			$PackRemark=$mainRows["PackRemark"];
			$sgRemark=$mainRows["sgRemark"];
			$ShipType=$mainRows["ShipType"];
			$Leadtime=$mainRows["Leadtime"];
			$TypeId=$mainRows["TypeId"];
                        $Forshort=$mainRows["Forshort"];
			$Gremark=$mainRows["Gremark"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			$Picture=$mainRows["Picture"];
			include "../model/subprogram/stuffimg_model.php";

                        //配件QC检验标准图
                         include "../model/subprogram/stuffimg_qcfile.php";
                         //配件品检报告qualityReport
                         include "../model/subprogram/stuff_get_qualityreport.php";
             //REACH 法规图
		     include "../model/subprogram/stuffreach_file.php";
			$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";

			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C
			                              LEFT JOIN $DataIn.trade_object P  ON P.Currency=C.Id 
			                              WHERE P.CompanyId='$CompanyId' ORDER BY C.Id LIMIT 1",$link_id);
			if($RowTemp = mysql_fetch_array($currency_Temp)){
				$Rate=$RowTemp["Rate"];//汇率
				}
			$rmbAmount=sprintf("%.2f",$Amount*$Rate);
			///仓库情况////////////////////////////////////////
			//收货情况
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			//领料情况
			$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' order by Id",$link_id);
			$llQty=mysql_result($llTemp,0,"Qty");
			$llQty=$llQty==""?0:$llQty;
			$llBgColor="";
			if($tdBGCOLOR==""){
				if($llQty==$OrderQty){
					$llBgColor="class='greenB'";
					}
				else{
					$llBgColor="class='yellowB'";
					}
				}
			else{
				$llBgColor="class='greenB'";
				}
			//结付情况/**/
			$LockRemark="";
			$checkPay=mysql_query("SELECT Estate,Month FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPayRow=mysql_fetch_array($checkPay)){
				$cwEstate=$checkPayRow["Estate"];
				$AskMonth=$checkPayRow["Month"];
				switch($cwEstate){
					case 0://已结付
						$cwEstate="<div class='greenB' title='已结付...货款月份:$AskMonth'>√</div>";
						$LockRemark="已结付，锁定操作";
					break;
					case 2:	//请款中
						$cwEstate="<div class='yellowB' title='请款中...货款月份:$AskMonth'>×.</div>";
						$LockRemark="已请款，锁定操作";
					break;
					case 3://请款通过
						$cwEstate="<div class='yellowB' title='等候结付...货款月份:$AskMonth'>√.</div>";
						$LockRemark="已请款通过，锁定操作";
					break;
					}
				}
			else{
				$cwEstate="<div class='redB'>×</div>";
				}

			//已付订金 add by zx 2011-01-24

			$PreAmount="&nbsp;";
			$checkPrePay=mysql_query("SELECT Amount   FROM $DataIn.cw2_fkdjsheet WHERE PurchaseID='$PurchaseID' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPrePayRow=mysql_fetch_array($checkPrePay)){
			   //$LockRemark="已付订金，锁定操作";
			   $PreAmount=$checkPrePayRow["Amount"];

			}

			//尾数
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					//$Mantissa="错误(入库>采购)";
					$Mantissa="<div class='redB' title='错误(入库数量>采购数量)'>错误</div>";
					}
				}
			else{
				if($Mantissa==$Qty){
					$BGcolor="class='redB'";
					}
				else{
					$BGcolor="class='yellowB'";$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
					}
				}
			//未补数量计算
		$sSearch1=" AND S.StuffId='$StuffId'";
		$checkSql=mysql_query("
		SELECT (B.thQty-A.bcQty) AS wbQty
			FROM (
				SELECT IFNULL(SUM(S.Qty),0) AS thQty,'$StuffId' AS StuffId FROM $DataIn.ck2_thsheet S 
				LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				)B
			LEFT JOIN (
				SELECT IFNULL(SUM(Qty),0) AS bcQty,'$StuffId' AS StuffId FROM $DataIn.ck3_bcsheet  S
				LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				) A ON A.StuffId=B.StuffId",$link_id);
		$wbQty=mysql_result($checkSql,0,"wbQty");
		if($wbQty!=0){
			$wbQty="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>$wbQty</a>";
			}
		else{
			$wbQty="&nbsp;";
			}
			//////////////////////////////////////////////////
			///权限///////////////////////////////////////////
			if($Estate==1){
				$LockRemark="未审核";
				}
			if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
				if($LockRemark!=""){
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='$LockRemark' width='15' height='15'>";
					}
				else{
					$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
					}
				}
			else{//无权限
				$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='锁定操作!' width='15' height='15'>";
				}

			$cName=$mainRows["cName"];
			$Client=$mainRows["Client"];
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			$DeliveryDate=$mainRows["DeliveryDate"];
			//交货日期颜色
			if($DeliveryDate=="0000-00-00"){
				$DeliveryDateShow="未设置";
				}
			else{
				$SetDate=CountDays($DeliveryDate,5);
				if($SetDate>-1){		//离交期不大于一天，为红色
					$DeliveryDateShow="<span class='redB'>".$DeliveryDate."</span>";
					}
				else{
					if($SetDate>-5){
						$DeliveryDateShow="<span class='yellowB'>".$DeliveryDate."</span>";
						}
					else{
						$DeliveryDateShow="<span class='greenB'>".$DeliveryDate."</span>";
						}
					}
				}

			$OutDate=$mainRows["OutDate"]==""?"&nbsp":$mainRows["OutDate"];


            /*加入同一单的配件  // add by zx 2011-08-04 */
			$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			$XtableWidth=$tableWidth-160;
			$XtableWidth=0;
			//$XsubTableWidth=$subTableWidth-160;
			$ProductId=$mainRows["ProductId"];
			$TestStandard=$mainRows["TestStandard"];
			include "../admin/Productimage/getProductImage.php";
			$StuffListTB="
				<table width='$XtableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $TestStandard)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
				</tr>
				
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderQtyInfo</td>";		//历史订单
				$m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>";  //REACH
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";	//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";				//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";		//金额RMB
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//未收数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$wbQty</td>";		//未补数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$DeliveryDateShow</td>";		//交货日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockId</div></td>";//需求流水号
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$PreAmount</td>";//订金
				$m=$m+2;
				echo"<td  class='A0000' width='' align='center'>$OutDate</td>";//出货日期
				//$m=$m+2;
				//echo"<td  class='A0001' width='' align='center'>&nbsp;</td>";
				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'  bgcolor='#FFFFFF'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderQtyInfo</td>";		//历史订单
				$m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>";  //REACH
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";		//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";	//金额RMB
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";	//未收数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$wbQty</td>";		//未补数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";		//结付状态
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$DeliveryDateShow</td>";		//交货日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockId</div></td>";	//需求流水号
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$PreAmount</td>";//订金
				$m=$m+2;
				echo"<td  class='A0001' width='' align='center'>$OutDate</td>";//出货日期
				//$m=$m+2;
				//echo"<td  class='A0001' width='' align='center'>&nbsp;</td>";
				echo"</tr></table>";
				$i++;
				}

			echo $StuffListTB;

			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
?>
<div id='divShadow' class="divShadow" style="display:none;">
	<div class='divInfo' id='divInfo'>
	<table width="300">
		<tr><td align="left">请输入请款月份</td></tr>
		<tr><td align="center"><input name="Month" type="text" id="Month" value="" maxlength="7"></td></tr>
		<tr><td align="right"><a href="javascript:ckeckForm()">确定</a> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
	</table>
	</div>
</div>
<div id="divPageMask" class="divPageMask" style="display:none;">
	<iframe scrolling="no" height="100%" width="100%" marginwidth="0" marginheight="0" src="MaskBgColor.htm"></iframe>
</div>
<?php
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function showMaskDiv(){	//显示遮罩对话框
	//检查是否有选取记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					}
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录!");
		}
	else{
		document.form1.Month.value="";
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		}
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}
function ckeckForm(){
	//检查月份
	var checkMonth=yyyymmCheck(document.form1.Month.value);
	if(checkMonth){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				}
			}
		document.form1.action="cg_cgdmainP_updated.php?ActionId=14";
		document.form1.submit();
		}
	else{
		alert("格式不对(YYYY-MM)");
		}
	}
function zhtj(obj){
	switch(obj){
		case "BuyerId"://改变采购
			document.forms["form1"].elements["GysPayMode"].value="";
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "GysPayMode":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		case "CompanyId":
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		break;
		}
	document.form1.action="cg_cgdmainp_read.php";
	document.form1.submit();
	}
</script>