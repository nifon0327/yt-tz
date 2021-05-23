<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}
/* 为 图片 加阴影 */
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; }
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; }
.imgContainer img {     display:block; }
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>

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

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";


//步骤2：需处理
$ColsNumber=19;
$tableMenuS=600;
ChangeWtitle("$SubCompany 入库错误列表");
$funFrom="cg_cgdmain";
$From=$From==""?"error":$From;
$sumCols="5,6,7,8,9,10";			//求和列,需处理
$MergeRows=3;
//$Th_Col="选项|55|序号|40|操作|30|采购单号|60|备注|30|配件ID|40|配件名称|200|图档|30|需求数|45|增购数|45|实购数|45|单价|45|金额|60|金额(RMB)|60|收货数|45|领料数|45|欠数|45|货款|30|交货日期|80|采购流水号|100";
$Th_Col="选项|55|序号|40|采购单号|60|配件ID|40|配件名称|200|需求数|45|增购数|45|实购数|45|收货数|45|领料数|45|欠数|45|采购流水号|100";

//必选，分页默认值
//$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Pagination=0;
$Page_Size = 200;							//每页默认记录数量,13
//$ActioToS="1,3,26,27,7,8";
$ActioToS="1";

//步骤3：
$nowWebPage=$funFrom."_error";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$BuyerId=$BuyerId==""?$Login_P_Number:$BuyerId;
	$buyerSql = mysql_query("SELECT M.BuyerId,S.Name,S.Estate,S.OrderByKey
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN (
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
		)A  )S ON M.BuyerId=S.Number 
	WHERE M.BuyerId>0 
	GROUP BY M.BuyerId ORDER BY S.Estate DESC,S.OrderByKey,S.Number DESC",$link_id);
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
				$SearchRows=" and M.BuyerId='$thisBuyerId'";
				}
			else{
				echo "<option value='$thisBuyerId' $FontColor>$Buyer</option>";
				}
			}while ($buyerRow = mysql_fetch_array($buyerSql));
		echo"</select>&nbsp;";
		}
		$SearchRows=$SearchRows==""?" AND M.BuyerId='$FristBurerId'":$SearchRows;
//结付方式
	/*
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
	 */
//供应商
	$providerSql= mysql_query("SELECT 
	M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	WHERE 1 $SearchRows  GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
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
			}
	else{
		//无供应商记录
		$SearchRows.=" and M.CompanyId=''";
		}

		//增加检索条件 物料编码  物料规格 物料名称
		echo "&nbsp;物料编号:<input name='searchId' type='text' id='searchId' value='".$searchId."' autocomplete='off' style='width:50' />";
		echo "&nbsp;物料规格:<input name='searchSpec' type='text' id='searchSpec' value='".$searchSpec."' autocomplete='off' style='width:50' />";
		echo "&nbsp;物料名称:<input name='searchName' type='text' id='searchName' value='".$searchName."' autocomplete='off' style='width:100' />";
		echo "&nbsp;<span name='Submit' value='快速查询' onClick='RefreshPage(\"$nowWebPage\")' class='btn-confirm' style='width: auto;font-size: 12px;height: 22px;line-height: 22px;'>快速查询</span>";

		if ($searchId) {
		    $SearchRows .= " AND S.StuffId like '%$searchId%' ";
		}
		if ($searchSpec) {
		    $SearchRows .= " AND A.Spec like '%$searchSpec%' ";
		}
		if ($searchName) {
		    $SearchRows .= " AND A.StuffCname like '%$searchName%' ";
		}


	}
/*
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='0' $Pagination0>不分页</option><option value='1' $Pagination1>分页</option></select>
	$CencalSstr";
*/
//echo"<select name='Pagination' id='Pagination' '><option value='0' selected>不分页</option></select>";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId,C.Forshort AS Client,P.cName
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
WHERE 1 $SearchRows and M.Date>='2010-06-01' AND S.Mid>0 ORDER BY S.StockId DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$curSumQty=0;
	$curStuffId='';
	$Rc=0;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
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
			$Gremark=$mainRows["Gremark"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			//检查是否有图片
			$Picture=$mainRows["Picture"];
			include "../model/subprogram/stuffimg_model.php";
			//供应商结付货币的汇率
			$Rate=1;
			$currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C,$DataIn.trade_object P WHERE P.CompanyId='$CompanyId' and P.Currency=C.Id ORDER BY C.Id LIMIT 1",$link_id);
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
			$IsMantissa=0;
			$Mantissa=$Qty-$rkQty;$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			if($Mantissa<=0){
				$BGcolor="class='greenB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
				if($Mantissa<0){
					$BGcolor="class='redB'";
					$IsMantissa=-1;
					//$Mantissa="错误";
					//$Mantissa="<div class='redB' title='错误(入库数量>采购数量)'>错误</div>";
					//$Mantissa="<div class='redB' title='错误(入库数量>采购数量)'>$Mantissa</div>";
					}
				}
			else{
				$StockIdShow=$StockId;
				if($Mantissa==$Qty){
					$BGcolor="class='redB'";
					}
				else{
					$BGcolor="class='yellowB'";$StockIdShow="<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
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
				include "../model/subprogram/cg_cgd_jj.php";
				//交货日期颜色
				$OnclickStr="";
				$DeliveryDate=$mainRows["DeliveryDate"];
				if($Login_P_Number==$BuyerId || $Login_P_Number==10002){
					$OnclickStr="onclick='updateJq($i,$StockId)' style='CURSOR: pointer;'";
					if($DeliveryDate=="0000-00-00"){
						$DeliveryDateShow="<span class='yellowN'>未设置</div>";
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
					}
				else{
					$DeliveryDateShow=$DeliveryDate=="0000-00-00"?"未设置":$DeliveryDate;
					}


				if($Mantissa<0){

					$curSumQty=$curSumQty+$Mantissa;  //统计数同一类的欠数
					$curStuffId=$StuffId;
					$Rc=$Rc+1;
					$URL="Cg_cgdmain_error_ajax.php";
					$theParam="StockId=$StockId";
					//echo "$theParam";
					$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
					alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
					//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
					$StuffListTB="
						<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
						<tr bgcolor='#B7B7B7'>
						<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

					//$StuffListTB="";
					$ValueArray=array(
					array(0=>$PurchaseIDStr),
					array(0=>$StuffId),
					array(0=>$StuffCname, 		1=>"align='left'"),
					array(0=>$FactualQty, 		1=>"align='center'"),
					array(0=>$AddQty, 		1=>"align='center'"),
					array(0=>$Qty,		1=>"align='center'"),
					array(0=>$rkQty,		1=>"align='center'"),
					array(0=>$llQty,		1=>"align='center'"),
					array(0=>"<div class='redB' title='错误(入库数量>采购数量)'>$Mantissa</div>"),
					array(0=>$StockIdShow, 		1=>"align='center'")

					);
					$checkidValue=$Id;
					$showFlag=1;
					include "../model/subprogram/read_model_6.php";

					echo $StuffListTB;
				}

			}
		}while($mainRows = mysql_fetch_array($mainResult));
	//echo"</tr></table>";
	 if ($showFlag==0) noRowInfo($tableWidth);
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
<script language="JavaScript" type="text/JavaScript">
<!--
function updateJq(TableId,runningNum){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		InfoSTR="<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='14' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(){
	var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempDeliveryDate=document.form1.DeliveryDate.value;
	myurl="purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
	retCode=openUrl(myurl);
	if (retCode!=-2){
		//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
		if(tempDeliveryDate==""){
			tempDeliveryDate="未设置";
			}
		var ColorDate=Number(DateDiff(tempDeliveryDate));
		if(ColorDate<2){
			eval("ListTable"+tempTableId).rows[0].cells[15].innerHTML="<div class='redB'>"+tempDeliveryDate+"</div>";
			}
		else{
			if(ColorDate<5){
				eval("ListTable"+tempTableId).rows[0].cells[15].innerHTML="<div class='yellowB'>"+tempDeliveryDate+"</div>";
				}
			else{
				eval("ListTable"+tempTableId).rows[0].cells[15].innerHTML="<div class='greenB'>"+tempDeliveryDate+"</div>";
				}
			}
		CloseDiv();
		}
	}
function zhtj(obj){
	switch(obj){
		case "BuyerId"://改变采购
			//document.forms["form1"].elements["GysPayMode"].value="";
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			/*
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
				*/
		break;
		/*
		case "GysPayMode":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
		*/
		break;
		case "CompanyId":
			/*
			if(document.all("chooseDate")!=null){
				document.forms["form1"].elements["chooseDate"].value="";
				}
			*/
		break;
		}
	document.form1.action="cg_cgdmain_error.php";
	document.form1.submit();
}
</script>