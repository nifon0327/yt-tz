<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<script>
function zhtj(obj){
	switch(obj){
		case "chooseDate"://改变采购
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="zwwp_input_read.php";
	document.form1.submit();
}
</script>
<?php
//步骤2：需处理
$ColsNumber=9;
$tableMenuS=500;
ChangeWtitle("$SubCompany 总务物品入库记录");
$funFrom="zwwp_input";
$From=$From==""?"read":$From;
$sumCols="6,7";			//求和列,需处理
$MergeRows=5;
$Th_Col="操作|50|入库日期|70|购买<br>凭证|50|供应商|80|采购员|60|选项|40|序号|40|申购日期|70|申购人|60|申购物品|300|申购总数|60|单位|40|本次入库|60|未购回|60|在库|60|可用库存|60";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.zwwp5_inmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND ((B.Date>'$StartDate' AND B.Date<'$EndDate') OR B.Date='$StartDate' OR B.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$providerSql = mysql_query("SELECT B.CompanyId,C.Forshort 
			FROM $DataIn.zwwp5_inmain B,$DataPublic.zwwp0_retailer C 
			WHERE C.CompanyId=B.CompanyId $SearchRows GROUP BY B.CompanyId ORDER BY B.CompanyId",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" AND B.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		echo"</select>&nbsp;";
		}
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Date,B.voucher,B.Remark,B.CompanyId,B.BuyerId,
		A.Id,A.Mid,A.Pid,A.PurchaseId,A.GoodsId,A.Qty,A.Price,A.Currency,A.Estate,A.Locks,
		C.GoodsName,C.Unit,C.Attached,
		E.Forshort,F.Name,
		G.Purchaser,G.Date AS GDate,G.Qty AS GQty
FROM $DataIn.zwwp5_insheet A
LEFT JOIN $DataIn.zwwp5_inmain B ON A.Mid=B.Id 
LEFT JOIN $DataPublic.zwwp3_data C ON C.Id=A.GoodsId 
LEFT JOIN $DataPublic.zwwp2_subtype D ON D.Id=C.TypeId
LEFT JOIN $DataPublic.zwwp0_retailer E ON E.CompanyId=B.CompanyId 
LEFT JOIN $DataPublic.staffmain F ON F.Number=B.BuyerId 
LEFT JOIN $DataIn.zwwp4_purchase G ON G.Id=A.PurchaseId
WHERE 1 $SearchRows ORDER BY B.Date DESC,B.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$voucher=$mainRows["voucher"];
		$Forshort=$mainRows["Forshort"];
		$Name=$mainRows["Name"];
		$Remark=$mainRows["Remark"]==""?"":"title='$Remark'";

		//检查是否存在文件
		if($voucher!=""){
			$FilePath1="../download/zw_img/$voucher.jpg";
			if(file_exists($FilePath1)){
				$voucher="<a href='$FilePath1' target='_blank'>$voucher</a>";
				}
			}
		else{
			$voucher="&nbsp;";
			}
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"zwwp_input_upmain\",$Mid)' src='../images/edit.gif' alt='更新入库主单资料' width='13' height='13'>";
		//明细资料
		$GoodsId=$mainRows["GoodsId"];//物品ID
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];	//入库明细Id
			$GDate=$mainRows["GDate"];					//申购日期
			$Purchaser=$mainRows["Purchaser"];		//申购人
			$GoodsName=$mainRows["GoodsName"];//申购物品名称
			$Attached=$mainRows["Attached"];			//物品图片

			$GQty=$mainRows["GQty"];						//申购总数
			$Unit=$mainRows["Unit"];						//单位
			$Qty=$mainRows["Qty"];							//本次入库
			//此单入库总数
			$thisCheck=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.zwwp5_insheet WHERE PurchaseId='$PurchaseId'",$link_id));
			$unQty=$GQty-$thisCheck["Qty"];				//未入库

			//全部入库总数
			$rkCheck=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS rkQty FROM $DataIn.zwwp5_insheet WHERE GoodsId='$GoodsId'",$link_id));
			$rkQty=$rkCheck["rkQty"];
			//采购总数
			$cgCheck=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS cgQty FROM $DataIn.zwwp4_purchase WHERE GoodsId='$GoodsId'",$link_id));
			$cgQty=$rkCheck["cgQty"];
			//领料总数
			$llCheck=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS llQty FROM $DataIn.zwwp6_outsheet WHERE GoodsId='$GoodsId'",$link_id));
			$llQty=$rkCheck["llQty"];
			//在库=入库总数-领料总数
			$swQty=$rkQty-$llQty;
			//可用库存=采购总数-领料总数
			$ddQty=$cgQty-$llQty;

			if($unQty==0){
				$rkBgColor="class='greenB'";
				}
			else{
				$rkBgColor="class='redB'";
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
						$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='$LockRemark' width='15' height='15'/>";
						}
					else{
						$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
						}
					}
				else{//无权限
					$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='锁定操作!' width='15' height='15'/>";
					}
				}

			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";					//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";						//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$voucher</td>";	//凭证
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]'>$Forshort</td>";										//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Name</td>";					//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=11;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";					//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$QDate</td>";			//申购日期
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Purchaser</td>";		//申购人
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsName</td>";	//申购物品
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";				//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";					//本次入库
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$unQty</div></td>";		//未入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$swQty</td>";		//在库
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$ddQty</td>";		//可用库存
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";					//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";						//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$voucher</td>";	//凭证
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]'>$Forshort</td>";										//供应商
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Name</td>";					//采购
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";					//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$QDate</td>";			//申购日期
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Purchaser</td>";		//申购人
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GoodsName</td>";	//申购物品
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";				//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";					//本次入库
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$unQty</div></td>";		//未入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$swQty</td>";		//在库
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$ddQty</td>";		//可用库存
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
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
