<?php
include "../model/modelhead.php";
$ColsNumber=27;
$tableMenuS=600;
ChangeWtitle("$SubCompany 采购单列表");
$funFrom="cg_cgdmain";
$From=$From==""?"tj":$From;
$sumCols="10,11,12,15,16,17,18,19,20,21";			//求和列,需处理
$MergeRows=4;
$Th_Col="操作|30|采购单号|60|备注|30|XML|30|选项|60|行号|30|配件ID|40|配件名称|250|图档|30|历史<br>订单|40|QC图|40|品检<br>报告|40|认证|40|开发|40|需求数|45|增购数|45|实购数|45|单价|40|单位|45|金额|60|金额(RMB)|60|收货数|45|领料数|45|欠数|45|退货|45|补仓|45|请款<br>方式|30|货款|30|采购日期|70|交货日期|90|采购流水号|100|供应商|80|预付金额|80";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
$ActioToS="1";
$nowWebPage=$funFrom."_tj";
include "../model/subprogram/read_model_3.php";
$SearchRowsA=$Weeks>0?" AND S.BuyerId='$BuyerId' AND YEARWEEK(S.DeliveryDate,1)='$Weeks'": " AND S.BuyerId=$BuyerId AND S.DeliveryDate='0000-00-00'";
$SearchRows=$Weeks>0?" AND S.BuyerId='$BuyerId' AND YEARWEEK(S.DeliveryDate,1)='$Weeks'": " AND S.BuyerId=$BuyerId AND S.DeliveryDate='0000-00-00'";
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
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,S.rkSign,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,
A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId,A.DevelopState,C.Forshort AS Client,P.cName,P.TestStandard,P.ProductId,U.Name AS UnitName,V.Forshort 
FROM (
       SELECT A.* FROM( 
		      SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
		      FROM $DataIn.cg1_stocksheet S 
		      LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
		      LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
		      WHERE  S.Mid>0 AND  S.rkSign>0 AND M.CompanyId NOT IN (getSysConfig(106)) $SearchRowsA GROUP BY S.StockId 						      
		)A WHERE A.Qty>A.rkQty 
) K  								
LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=K.StockId  
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
WHERE 1 $SearchRows  $SearchEstate  AND  S.Mid>0  GROUP BY S.StockId ORDER BY S.Mid DESC,M.Date DESC,S.POrderId";
//echo $mySql;
/*
  UNION ALL
									          SELECT  G.StockId,0 AS Qty,0 AS rkQty,SUM(G.Qty) AS SendQty
									          FROM $DataIn.gys_shsheet G
                                              LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId
									          WHERE  G.SendSign=0 AND G.Estate>0 AND  S.StockId>0   AND S.Mid>0  $SearchRowsA  GROUP BY G.StockId

*/
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
		$Dates=$Date."：".CountDays($Date,10); //10为无用参数
		$PurchaseID=$mainRows["PurchaseID"];
		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='16' height='16'>";
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer'  src='../images/edit.gif' alt='更新采购单资料!采购日期：$Dates' width='13' height='13'>";
		//明细资料
        $xmlFile="<a href='cg_cgdmain_toxml.php?PurchaseID=$PurchaseID' target='_download'>XML</a>";
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$OrderQty=$mainRows["OrderQty"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$OrderPO=$mainRows["OrderPO"];
			$POrderId=$mainRows["POrderId"];
			//$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
			$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
			$PQty=$mainRows["PQty"];
			$PackRemark=$mainRows["PackRemark"];
			$sgRemark=$mainRows["sgRemark"];
			$ShipType=$mainRows["ShipType"];
			$Leadtime=$mainRows["Leadtime"];

			$Forshort=$mainRows["Forshort"];
			$TypeId=$mainRows["TypeId"];
			$Gremark=$mainRows["Gremark"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			//检查是否有图片

             //配件QC检验标准图
             include "../model/subprogram/stuffimg_qcfile.php";
            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
			//REACH 法规图
		   include "../model/subprogram/stuffreach_file.php";

			$Picture=$mainRows["Picture"];
			include "../model/subprogram/stuffimg_model.php";
			include"../model/subprogram/stuff_Property.php";//配件属性

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
		//退换数量
		$UnionSTR7=mysql_query("SELECT SUM(Qty) AS thQty FROM $DataIn.ck2_thsheet WHERE StuffId='$StuffId'",$link_id);
		$thQty=mysql_result($UnionSTR7,0,"thQty");
		$thQty=$thQty==""?0:$thQty;

		$LockRemark="";
		//补仓数量
		$UnionSTR8=mysql_query("SELECT SUM(Qty) AS bcQty FROM $DataIn.ck3_bcsheet WHERE StuffId='$StuffId'",$link_id);
		$bcQty=mysql_result($UnionSTR8,0,"bcQty");
		$bcQty=$bcQty==""?0:$bcQty;
        if($bcQty<$thQty) {
			if ($isEstate==1) {  //如果是请款，则不用锁定
				//$LockRemark="未补完货!";
			}
			$bcQty="<span class='redB'>$bcQty</span>";
		}
		else {
			if($bcQty>0) {
				$bcQty="<span class='greenB'>$bcQty</span>";
			}
		}
        if($thQty>0)$thQty="<a href='ck_th_read.php?tempStuffId=$StuffId' target='_blank'><span style='color:#000'>$thQty</span></a>";


		   //结付情况/**/

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

			//1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
			$Autobgcolor="";
			$AutoSign=$mainRows["AutoSign"];
			switch($AutoSign){
				case 2:
					$AutoSign="<image src='../images/AutoCheckB.png' style='width:20px;height:20px;' title='人工请款自动通过'/>";
					break;
				case 4:
					$AutoSign="<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款自动通过'/>";
					//$Autobgcolor="bgcolor='##FF0000'";
					break;
				default:
					$AutoSign="&nbsp;";
					break;

			}

             $CheckOrderResult=mysql_fetch_array(mysql_query("SELECT Id  FROM  $DataSub.yw1_orderdeleted   WHERE  OrderNumber=$StockId AND Estate>0",$link_id));
             $CheckOrderId=$CheckOrderResult["Id"];
              if($CheckOrderId!=""){
				 $LockRemark="此采购单已做还原动作，需子系统相关人员对订单进行删除审核";
                 $tdBGCOLOR="bgcolor='#FF0000'";
               }

			//已付订金 add by zx 2011-01-24
			$PreAmount="&nbsp;";
			$checkPrePay=mysql_query("SELECT Amount   FROM $DataIn.cw2_fkdjsheet WHERE PurchaseID='$PurchaseID' AND Estate!=1 ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPrePayRow=mysql_fetch_array($checkPrePay)){
				if ($isEstate==1) {  //如果是请款，则不用锁定
					 $PreAmount=$checkPrePayRow["Amount"];
				}
				else {
				   if ($Login_P_Number!=10341 && $Login_P_Number!=10868) $LockRemark="已付订金，锁定操作";
				   $PreAmount=$checkPrePayRow["Amount"];
				}

			}

			//尾数
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					//$Mantissa="错误";
					$Mantissa="<div class='redB' title='错误(入库数量>采购数量)'>错误</div>";
					}
					$rkSign=$mainRows["rkSign"];
					if ($rkSign>0){
					      //更改入库标记
						  $uprkSignSql="UPDATE $DataIn.cg1_stocksheet  SET rkSign=0 WHERE StockId='$StockId'";
						  $UprkResult=mysql_query($uprkSignSql);
						  echo "<div class='redB'>入库标志更新:该采购单已全部入库</div>";
					}
				}
			else{
				$StockIdShow=$StockId;
				if($Mantissa==$Qty){
					$BGcolor="class='redB'";

					}
				else{
					//$LockRemark="已收货，锁定操作";

					$BGcolor="class='yellowB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
					}
				/*
				if ($isEstate==1) {  //如果是请款，则不用锁定
					$LockRemark="未收完货!";
				}
				*/
			}

			//默认单价
		$priceRes=mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'",$link_id);
		if($priceRow=mysql_fetch_array($priceRes)){
			$DefaultPrice=$priceRow["Price"];
		}
		if($DefaultPrice!=$Price){
			$Price="<div class='redB'>$Price</div>";
			$PriceTitle="Title=\"默认单价：$DefaultPrice\"";
		}

			//////////////////////////////////////////////////
			///权限///////////////////////////////////////////
			if($Estate==1){
				$LockRemark="未审核";
				}
			if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
				if($Keys & mLOCK){
					if($LockRemark!=""){//财务强制锁定
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
						}
					}
				else{		//A2：无权限对锁定记录操作
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
					}
				}
			else{
				if(($BuyerId==$Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK){//有权限
					if($LockRemark!=""){
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='锁定操作!' width='15' height='15'>";
					}
				}

			$cName=$mainRows["cName"];
			$Client=$mainRows["Client"];
			include "../model/subprogram/cg_cgd_jj.php";
			//交货日期颜色
			$OnclickStr="";
			$DeliveryDate=$mainRows["DeliveryDate"];
			/*
			if($Login_P_Number==10002 || $Login_P_Number==10341 || $Login_P_Number==10007){
				   $OnclickStr="onclick='updateJq($i,$StockId)' style='CURSOR: pointer;'";
		   }
		   */
				/*
			else{
				   //$DeliveryDateShow=$DeliveryDate=="0000-00-00"?"未设置":$DeliveryDate;
				}
				*/
			/*
			if($DeliveryDate=="0000-00-00"){
				   $DeliveryDateShow="<span class='yellowN' style='vertical-align:middle;'>未设置</span>";
				}
			else{
				$SetDate=CountDays($DeliveryDate,5);
				if($SetDate>-1){		//离交期不大于一天，为红色
					$DeliveryDateShow="<span class='redB' style='vertical-align:middle;'>".$DeliveryDate."</span>";
					}
				else{
					if($SetDate>-5){
						$DeliveryDateShow="<span class='yellowB' style='vertical-align:middle;'>".$DeliveryDate."</span>";
						}
					else{
						$DeliveryDateShow="<span class='greenB' style='vertical-align:middle;'>".$DeliveryDate."</span>";
						}
					}
				}
				*/
			$OutDate=$mainRows["OutDate"]==""?"&nbsp":$mainRows["OutDate"];

			include "../model/subprogram/CG_DeliveryDate.php";
			//原交货日期
			$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate!='$DeliveryDate' ORDER BY Id DESC LIMIT 1",$link_id);
			if($oldDateRow = mysql_fetch_array($CheckOldDate)){
			       $oldDeliveryDate="Week " . substr($oldDateRow["Week"],4,2);
			       $dateSignImage="<div style='float:left;margin:0px 5px 0px 5px'><img src='../images/icon_abnormal.gif'  width='20' height='20' title='原交期:". $oldDeliveryDate . " ' style='vertical-align:middle;'/></div>";
			}
			else{
				   $dateSignImage="";
			}

	   $DevelopWeekState=1;
	   $DevelopState=$mainRows["DevelopState"];
		include "../model/subprogram/stuff_developstate.php";

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
				<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

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
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xmlFile</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}

			$AddRemark=$mainRows["AddRemark"];
			if ($AddRemark!="") {
				$StuffId="<div title='$AddRemark'>$StuffId</div>";
			}

			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=9;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'   $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
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
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>"; //REACH
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$DevelopState</td>"; //REACH
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";	//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right' $PriceTitle>$Price</td>";				//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right' >$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";		//金额RMB
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$thQty</td>";//退货
               $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$bcQty</td>";//退货
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状态
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
                                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center' $OnclickStr>$dateSignImage $DeliveryDateShow</td>";		//交货日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockIdShow</div></td>";//需求流水号
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
				$m=$m+2;
				echo"<td  class='A0001' width='' align='right'>$PreAmount</td>";//订金

				echo"</tr></table>";
				$i++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
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
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xmlFile</td>";		//下单备注
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
				echo"<td class='A0001' width='$unitFirst'  $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
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
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>"; //REACH
                $m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$DevelopState</td>"; //REACH
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
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";	//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$thQty</td>";//退货
               $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$bcQty</td>";//退货
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状
				 $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";		//结付状态
                                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center' $OnclickStr>$dateSignImage $DeliveryDateShow</td>";		//交货日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockIdShow</div></td>";	//需求流水号
				$m=$m+2;
                                echo"<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
				$m=$m+2;
				echo"<td  class='A0001' width='' align='right'>$PreAmount</td>";//订金

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
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
