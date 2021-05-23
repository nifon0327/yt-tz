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
	document.form1.action="ck_gp_read.php";
	document.form1.submit();
}
</script>
<?php
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 物料供应商备品列表");
$funFrom="ck_gp";
$From=$From==""?"read":$From;
$sumCols="6";			//求和列,需处理
$MergeRows=3;
$Th_Col="操作|40|入仓日期|70|送货单号|75|选项|40|序号|40|配件Id|50|配件名称|300|单位|40|图档|30|备品数量|60|备注|60";

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
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck11_bpmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.ck11_bpmain M,$DataIn.trade_object P WHERE M.CompanyId=P.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
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
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
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
$mySql="SELECT M.BillNumber,M.Date,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Locks,D.StuffCname,D.Gfile,D.Gstate,D.Picture,U.Name AS UnitName
FROM $DataIn.ck11_bpsheet S
LEFT JOIN $DataIn.ck11_bpmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$ImgDir="../download/thimg/";
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//主单信息/补仓日期/补仓单号
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$BillNumber=$mainRows["BillNumber"];
		$FilePath1="../download/deliverybill/$BillNumber.jpg";
		if(file_exists($FilePath1)){
			$BillNumber="<a href='$FilePath1' target='_blank'>$BillNumber</a>";
			}

		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ck_bc_upmain\",$Mid)' src='../images/edit.gif' alt='更新补仓主单资料' width='13' height='13'>";
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$Remark=trim($mainRows["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$mainRows[Remark]' width='18' height='18'>";
			$Locks=$mainRows["Locks"];

			$Picture=$mainRows["Picture"];
			$Gfile=$mainRows["Gfile"];
			$Gstate=$mainRows["Gstate"];



			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
			include "../model/subprogram/stuffimg_model.php";	//检查是否有图

			//检查是否有图片

			//include "../model/subprogram/stuffimg_model.php";
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
				if($Keys & mUPDATE || $Keys & mDELETE || $Keys & mLOCK){//有权限
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
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//补仓日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";//补仓单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";				//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$Gfile</td>";				//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//补仓数量
				$m=$m+2;
				echo"<td  width='' align='center'>$Remark</td>";		//补仓原因
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//补仓日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumber</td>";//补仓单号
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";				//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$Gfile</td>";				//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";		//补仓数量
				$m=$m+2;
				echo"<td  width='' align='center'>$Remark</td>";		//补仓原因
				echo"</tr></table>";
				$i++;
				$j++;
				}
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
