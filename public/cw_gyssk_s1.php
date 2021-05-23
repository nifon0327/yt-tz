<?php
/*电信---yang 20120801
代码共享-EWEN 2012-08-10
*/
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
//$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|部门|60|小组|60|职位|70|员工等级|80|考勤状态|80|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|介绍人|50";
$Th_Col="操作|30|下单日期|70|采购|50|供应商|80|采购单号|60|采购备注|60|预付金额|60|选项|60|行号|30|配件编码|50|非bom配件名称|250|配件条码|80|申购<br>备注|30|货币|30|单价|60|申购数量|60|单位|30|金额|60|收货数|50|欠数|50|在库|60|采购库存|60|最低库存|60|记录<br>状态|40|收货<br>状态|40|货款<br>状态|40|申购时间|80|申购人|50";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.="";
//非必选,过滤条件
//echo "Action:$Action";
switch($Action){
     default:
     break;
	}
//步骤3：
include "../model/subprogram/s1_model_3.php";
//if($SelectFrom!=""){
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商
	$SearchRows="";
	//月份
	$checkResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m') AS Month FROM $DataIn.nonbom6_cgmain A  GROUP BY DATE_FORMAT(A.Date,'%Y-%m') ORDER BY A.Date DESC",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择月份</option>";
		do{
			$Temp_Month=$checkRow["Month"];
			if($Temp_Month==$chooseDate){
				echo"<option value='$Temp_Month' selected>$Temp_Month</option>";
				$SearchRows=" AND DATE_FORMAT(G.Date,'%Y-%m')='$Temp_Month'";
				}
			else{
				echo"<option value='$Temp_Month'>$Temp_Month</option>";
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	//采购
	$checkResult = mysql_query("SELECT G.BuyerId,C.Name 
							   FROM $DataIn.nonbom6_cgmain G 
							   LEFT JOIN $DataPublic.staffmain C ON C.Number=G.BuyerId 
							   WHERE 1 $SearchRows GROUP BY G.BuyerId ORDER BY C.Name",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择采购</option>";
		do{
			$Temp_BuyerId=$checkRow["BuyerId"];
			$Temp_Name=$checkRow["Name"];
			if($Temp_BuyerId==$BuyerId){
				echo"<option value='$Temp_BuyerId' selected>$Temp_Name</option>";
				$SearchRows.=" AND G.BuyerId='$Temp_BuyerId'";
				}
			else{
				echo"<option value='$Temp_BuyerId'>$Temp_Name</option>";
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}

	//供应商
	$checkResult = mysql_query("SELECT Ee.Letter,Ee.Forshort,Ee.CompanyId 
							   FROM $DataIn.nonbom6_cgmain G 
							   LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=G.CompanyId
							   WHERE 1 $SearchRows GROUP BY Ee.Forshort ORDER BY Ee.Letter",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择供应商</option>";
		do{
			$Temp_CompanyId=$checkRow["CompanyId"];
			$Temp_Name=$checkRow["Letter"] . "-" . $checkRow["Forshort"];
			if($Temp_CompanyId==$CompanyId){
				echo"<option value='$Temp_CompanyId' selected>$Temp_Name</option>";
				$SearchRows.=" AND Ee.CompanyId='$Temp_CompanyId'";
				}
			else{
				echo"<option value='$Temp_CompanyId'>$Temp_Name</option>";
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}

	//选择状态
	switch($chooseType){
		case 1:
		$SearchRows.=" AND F.rkSign='0'";
		$TypeSTR1="selected";
		$ActioToS="1";
		//if($BuyerId==$Login_P_Number){
			$ActioToS.=",14";//采购本人方可请款
			$unLocks="1";
			//条件过滤：需不在请款数据表内的
			$SearchRows.=" AND B.cgId IS NULL";
			//}
		break;
		default:
		$TypeSTR0="selected";
		break;
		}
	echo"<select name='chooseType' id='chooseType' onchange='ResetPage(this.name)'>";
	echo"<option value='' $TypeSTR0>选择分列</option>";
	echo"<option value='1' $TypeSTR1>可请款</option>";
	echo"</select>&nbsp;";
	//分列：全部、可请款、请款中、已结付、未收货、已收货
	//主分类
	$checkResult = mysql_query("SELECT  Dd.mainType,Dm.Name  
							   FROM $DataIn.nonbom6_cgmain G 
							   LEFT JOIN $DataIn.nonbom6_cgsheet F ON G.Id=F.Mid 
							   LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=G.CompanyId
							   LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
	                           LEFT JOIN $DataPublic.nonbom2_subtype Dd ON Dd.Id=D.TypeId
	                           LEFT JOIN $DataPublic.nonbom1_maintype Dm  ON Dm.Id=Dd.mainType 
							   LEFT JOIN $DataIn.nonbom12_cwsheet B ON B.cgId=F.Id
							   WHERE 1 $SearchRows AND Dd.mainType>0 GROUP BY Dd.mainType",$link_id);

	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='mainType' id='mainType' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择主分类</option>";
		do{
			$mainTypeTemp=$checkRow["mainType"];
			$NameTemp=$checkRow["Name"];
			if($mainType==$mainTypeTemp){
				echo"<option value='$mainTypeTemp' selected>$NameTemp </option>";
				$SearchRows.=" AND Dd.mainType='$mainTypeTemp'";
				}
			else{
				echo"<option value='$mainTypeTemp'>$NameTemp</option>";
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}

	//子分类
	$checkResult = mysql_query("SELECT  Dd.Id,Dd.TypeName  
							   FROM $DataIn.nonbom6_cgmain G 
							   LEFT JOIN $DataIn.nonbom6_cgsheet F ON G.Id=F.Mid 
							   LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=G.CompanyId
							   LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
	                           LEFT JOIN $DataPublic.nonbom2_subtype Dd ON Dd.Id=D.TypeId
							   LEFT JOIN $DataIn.nonbom12_cwsheet B ON B.cgId=F.Id
							   WHERE 1 $SearchRows AND Dd.Id>0 GROUP BY Dd.Id",$link_id);


	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='subType' id='subType' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择子分类</option>";
		do{
			$subTypeTemp=$checkRow["Id"];
			$NameTemp=$checkRow["TypeName"];
			if($subType==$subTypeTemp){
				echo"<option value='$subTypeTemp' selected>$NameTemp </option>";
				$SearchRows.=" AND D.TypeId='$subTypeTemp'";
				}
			else{
				echo"<option value='$subTypeTemp'>$NameTemp</option>";
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	}

//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
switch($Action){
   default:
	   $mySql="SELECT G.Date AS cgDate,G.PurchaseID,G.Remark AS mainRemark,G.BuyerId,
				F.Id,F.Mid,F.GoodsId,F.Qty,F.Price,F.Remark,F.ReturnReasons,F.rkSign,F.Estate,F.Locks,F.Date,
				D.GoodsName,D.BarCode,D.Attached,D.Unit,
				Dd.TypeName,
				Ee.Forshort,Ee.CompanyId,Ff.Name,
				Gg.wStockQty,Gg.oStockQty,Gg.mStockQty,IFNULL(B.Estate,9) AS cwSign,C.Symbol 
			FROM $DataIn.nonbom6_cgmain G
			LEFT JOIN $DataIn.nonbom6_cgsheet F ON G.Id=F.Mid
			LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
			LEFT JOIN $DataPublic.nonbom2_subtype Dd ON Dd.Id=D.TypeId
			LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=G.CompanyId 
			LEFT JOIN $DataPublic.staffmain Ff ON Ff.Number=F.Operator 
			LEFT JOIN $DataPublic.nonbom5_goodsstock Gg ON Gg.GoodsId=F.GoodsId
			LEFT JOIN $DataIn.nonbom12_cwsheet B ON B.cgId=F.Id
			LEFT JOIN $DataPublic.currencydata C ON C.Id=Ee.Currency  
			WHERE 1 $SearchRows ORDER BY G.Date DESC,G.Id DESC";
	break;
}

//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$LockRemark=$rkBgColor=$wsBgColor="";
		//主单信息
		$cgDate=$mainRows["cgDate"];
		$PurchaseID=$mainRows["PurchaseID"];
		$KtmpPurchaseID=$PurchaseID;
		//预付订金
		$checkDj=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS djAmount,Estate AS djEstate FROM $DataIn.nonbom11_djsheet WHERE PurchaseID='$PurchaseID' AND Estate='0'",$link_id));

		$Mid=$mainRows["Mid"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$mainRemark=$mainRows["mainRemark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[mainRemark]' width='18' height='18'>";
		$Operator=$mainRows["BuyerId"];
		include "../model/subprogram/staffname.php";
		//$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom6_upmain\",$Mid)' src='../images/edit.gif' title='更新采购主单资料' width='13' height='13'>";
		$upMian="&nbsp;";
		//明细资料
		$GoodsId=$mainRows["GoodsId"];
		if($GoodsId!=""){
			//$checkidValue=$mainRows["Id"];
			$KtmpId=$mainRows["Id"];
			$GoodsId=$mainRows["GoodsId"];
			$GoodsName=$mainRows["GoodsName"];
			$KtempGoodsName=$GoodsName;
			$Attached=$mainRows["Attached"];
			$BarCode=$mainRows["BarCode"];
			$Remark=$mainRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
			$Unit=$mainRows["Unit"];
			$Symbol=$mainRows["Symbol"];
			$Price=$mainRows["Price"];
			$Qty=del0($mainRows["Qty"]);
			$KtmpQty=$Qty;

			$checkidValue=$KtmpId."^^"."-(采购单号:$KtmpPurchaseID)-$KtempGoodsName-(数量:$KtmpQty)";

			$Amount=$Qty*$Price;
			//入库数量
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$checkidValue'",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:del0($rkQty);
			$wsQty=$Qty-$rkQty;
			if($rkQty==$Qty){
				$rkBgColor="class='greenB'";
				$rkSign="<sapn class='greenB'>已收货</span>";
				$rkQty="<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#093'>$rkQty</a>";
				//更新入库标记
					if ($mainRows["rkSign"]>0){
						 $UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='0' WHERE Id='$checkidValue' ";
			              $UprkSignResult = mysql_query($UprkSignSql,$link_id);
					}
				}
			else{
				$rkBgColor="class='redB'";
				$wsBgColor="class='redB'";
				if($rkQty==0){
					$rkSign="<sapn class='redB'>未收货</span>";
					$rkQty="&nbsp;";
					$rkSignVal=1;
					}
				else{
					$rkSign="<sapn class='yellowB'>部分收货</span>";
					$rkQty="<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#F00'>$rkQty</a>";
					$rkSignVal=2;
					}
					//更新入库标记
					if ($mainRows["rkSign"]==0){
						 $UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='$rkSignVal' WHERE Id='$checkidValue' ";
			              $UprkSignResult = mysql_query($UprkSignSql,$link_id);
					}
				}
			$wsQty=$wsQty==0?"&nbsp;":$wsQty;
			$wStockQty=del0($mainRows["wStockQty"]);
			$oStockQty=del0($mainRows["oStockQty"]);
			$mStockQty=del0($mainRows["mStockQty"]);
			$Attached=$mainRows["Attached"];
			if($Attached==1){
				$Attached=$GoodsId.".jpg";
				$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
				$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
				}
			$Locks=$unLocks==1?1:$mainRows["Locks"];
			$cwSign=$mainRows["cwSign"];
			switch($cwSign){
				case 0://已结付
					$cwSign="<span class='greenB'>已结付</span>";
					$LockRemark="记录已经结付，强制锁定操作！";
					break;
				case 2://请款中
					$cwSign="<span class='yellowB'>请款中</span>";
					break;
				case 3://请款通过
					$cwSign="<span class='yellowB'>未结付</span>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					break;
				case 1://审核退回
					$ReturnReasons=$mainRows["ReturnReasons"]==""?"请款退回:未填写退回原因":"请款退回:".$mainRows["ReturnReasons"];
			    	$cwSign="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
					break;
				default:
						$cwSign="<span class='redB'>未请款</span>";
					break;
				}
			$Estate=$mainRows["Estate"];
			switch($Estate){
				case 1:
				$Estate="<span class='greenB'>已审核</span>";
				break;
				case 4://审核退回
					$ReturnReasons=$mainRows["ReturnReasons"]==""?"审核退回:未填写退回原因":"审核退回:".$mainRows["ReturnReasons"];
			    	$Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
				break;
				default:
				$LockRemark="记录有更新，更新审核中，锁定操作！";
				$Estate="<span class='redB'>需审核</span>";
				break;

				}
			$Forshort=$mainRows["Forshort"];
			$Date=$mainRows["Date"];
			$Name=$mainRows["Name"];

			$CompanyId=$mainRows["CompanyId"];
			//加密
			$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);
			$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
			//历史单价
			$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
			//配件分析
			$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
			//预付金额
			$djAmount=$checkDj["djAmount"];
			$djEstate=$checkDj["djEstate"];
			if($djAmount>0){
				if($Amount==$djAmount){
					$djAmount="<span class='greenB'>$djAmount</span>";
					}
				else{
					$djAmount="<span class='redB'>$djAmount</span>";
					}
				}
			else{
				$djAmount="&nbsp;";
				}
			$LockRemark="";
			$Keys=31;

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
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'/>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'/>";
					}
				}
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			//$Th_Col="操作|30|下单日期|70|采购单号|60|操作员|60|选项|60|行号|30|编号|40|非bom配件名称|250|历史<br>订单|40|申购数量|45|单价|40|单位|45|金额|60|收货数|45|欠数|45|货款|30|供应商|80";
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";		//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]'>$cgDate</td>";			//下单日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";		//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";		//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";		//备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djAmount</td>";			//定金
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=15;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose  $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";				//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsId</td>";		//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$GoodsName</td>";					//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$BarCode</td>";					//配件条码
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";		//采购备注
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Symbol</td>";			//货币
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";			//单价
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";			//采购数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";			//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";		//已收货数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $wsBgColor>$wsQty</div></td>";		//欠数
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$wStockQty</td>";		//在库
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$oStockQty</td>";			//采购库存
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$mStockQty</td>";		//最低库存
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Estate</td>";		//审核状态
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$rkSign</td>";		//收货状态
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$cwSign</td>";		//货款状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]'>$Date</td>";			//申购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Name</td>";		//申购人
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";		//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]'>$cgDate</td>";			//下单日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";		//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";		//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";		//采购备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djAmount</td>";			//定金
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";				//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsId</td>";		//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$GoodsName</td>";					//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$BarCode</td>";					//配件条码
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";		//采购备注
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Symbol</td>";			//货币
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";			//单价
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";			//采购数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";			//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";		//已收货数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $wsBgColor>$wsQty</div></td>";		//欠数
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$wStockQty</td>";		//在库
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$oStockQty</td>";			//采购库存
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$mStockQty</td>";		//最低库存
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Estate</td>";		//审核状态
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$rkSign</td>";		//收货状态
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$cwSign</td>";		//货款状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]'>$Date</td>";				//申购日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Name</td>";			//申购人
				echo"</tr></table>";
				$i++;
				$j++;
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