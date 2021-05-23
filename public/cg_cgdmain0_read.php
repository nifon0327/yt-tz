<?php
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.cg1_stocksheet
$DataIn.cg1_stockmain
$DataIn.stuffdata
$DataPublic.staffmain
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=600;
ChangeWtitle("$SubCompany 采购单列表");
$funFrom="cg_cgdmain";
$From=$From==""?"read":$From;
$sumCols="5,6,7,8,9,10";			//求和列,需处理
$MergeRows=3;
$Th_Col="操作|30|采购单号|60|备注|30|选项|35|行号|30|配件ID|40|配件名称|200|图档|30|需求数|45|增购数|45|实购数|45|单价|45|金额|60|金额(RMB)|60|收货数|45|领料数|45|欠数|45|货款|30|采购流水号|100";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
$ActioToS="1,3,26,27,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$buyerSql = mysql_query("SELECT M.BuyerId,S.Name,S.Estate 
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataPublic.staffmain S ON M.BuyerId=S.Number GROUP BY M.BuyerId ORDER BY S.Estate DESC,M.BuyerId",$link_id);
	if($buyerRow = mysql_fetch_array($buyerSql)){
		echo"<select name='BuyerId' id='BuyerId' onchange='zhtj(this.name)'>";
		do{
			$thisBuyerId=$buyerRow["BuyerId"];
			$Buyer=$buyerRow["Name"];
			$BuyerId=$BuyerId==""?$thisBuyerId:$BuyerId;
			$FontColor=$buyerRow["Estate"]==1?"":"style='color:#99CC99'";
			if ($BuyerId==$thisBuyerId){
				echo "<option value='$thisBuyerId' $FontColor selected>$Buyer</option>";
				$SearchRows=" and M.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId' $FontColor>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}
//结付方式
	$GysPayMode=$GysPayMode==""?1:$GysPayMode;
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
	M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	WHERE 1 $SearchRows AND P.GysPayMode='$GysPayMode' GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
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
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
//月份
		$date_Result = mysql_query("SELECT M.Date 
		FROM $DataIn.cg1_stockmain M
		WHERE 1 $SearchRows group by DATE_FORMAT(M.Date,'%Y-%m') order by M.Id DESC",$link_id);
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
					$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
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
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Gfile,A.Gremark,A.TypeId,C.Forshort AS Client,P.cName
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
WHERE 1 $SearchRows AND S.Mid>0 ORDER BY M.PurchaseID DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$Dates=$Date."：".CountDays($Date);
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
			$OrderQty=$mainRows["OrderQty"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$Locks=$mainRows["Locks"];
			$BuyerId=$mainRows["BuyerId"];
			$CompanyId=$mainRows["CompanyId"];
			$tdBGCOLOR=$mainRows["POrderId"]==""?"bgcolor='#FFCC99'":"";
			$TypeId=$mainRows["TypeId"];
				$Gfile=$mainRows["Gfile"];
				$Gremark=$mainRows["Gremark"];
				$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
				//加密
				if($Gfile!=""){
					$f=anmaIn($Gfile,$SinkOrder,$motherSTR);
					$Gfile="<img onClick='OpenOrLoad(\"$d\",\"$f\",6)' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";
					}
				else{
					$Gfile="&nbsp;";
					}
				//检查是否有图片
				include "../model/subprogram/stuffimg_model.php";
			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C,$DataIn.trade_object P WHERE P.CompanyId='$CompanyId' and P.Currency=C.'Id' ORDER BY C.'Id' LIMIT 1",$link_id);
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

			//尾数
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					$Mantissa="错误";
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
			//////////////////////////////////////////////////
			///权限///////////////////////////////////////////
			if($Estate==1){
				$LockRemark="未审核";
				}
			if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
				if($Keys & mLOCK){
					if($LockRemark!=""){//财务强制锁定
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='$LockRemark' width='15' height='15'>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
						}
					}
				else{		//A2：无权限对锁定记录操作
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='锁定操作!' width='15' height='15'>";
					}
				}
			else{
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
				}

			$cName=$mainRows["cName"];
			$Client=$mainRows["Client"];
			include "subprogram//cg_cgd_jj.php";
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
				echo"<td width='$unitWidth' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";	//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";				//单价
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";		//金额RMB
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";		//领料数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockId</div></td>";//需求流水号
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
				//并行宽
				echo"<td width='$unitWidth' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";		//增购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
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
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";		//结付状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockId</div></td>";	//需求流水号
				echo"</tr></table>";
				$i++;
				}
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
<script>
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
	document.form1.action="cg_cgdmain_read.php";
	document.form1.submit();
}
</script>