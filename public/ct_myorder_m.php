<?php
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"m":$From;
//需处理参数
$ColsNumber=10;
$tableMenuS=650;
ChangeWtitle("$SubCompany 点餐记录审核");
$funFrom="ct_myorder";
$nowWebPage=$funFrom."_m";
$MergeRows=4;
$Th_Col="操作|50|日期|70|单据|40|备注|50|选项|40|序号|40|餐厅|80|菜式分类|80|菜式名称|150|价格|60|点餐数量|60|点餐金额|60|备注|120|状态|40|更新日期|125|操作|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
   $monthResult = mysql_query("SELECT Date FROM $DataPublic.ct_myorder  group by DATE_FORMAT(Date,'%Y-%m-%d') order by Date DESC LIMIT 30",$link_id);
	if($monthResult && $monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			 $dateValue=date("Y-m-d",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月d日",strtotime($monthRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(A.Date,'%Y-%m-%d')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateText</option>";
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
    }
}

$Estate=$Estate==""?1:$Estate;
$EstateStr="Estate".$Estate;
$$EstateStr="selected";
echo "<select id='Estate' name='Estate' onchange='document.form1.submit()'>";
echo"<option value='1' $Estate1>未审核</option>";
echo"<option value='0' $Estate0>已审核</option>";
echo"</select>&nbsp;";
if($Estate!="")$SearchRows.=" AND A.Estate='$Estate'";
if($Estate==1 && ($Login_P_Number==10620 || $Login_P_Number==10082 || $Login_P_Number==10871 || $Login_P_Number==10369)){
	            $ActioToS="3,15,17";
            }
  else{
         	  $ActioToS="15";
          }
//权限设定
if($Keys & mLOCK){
	 //$SearchRows="";
	  //餐厅
	$checkCTSql=mysql_query("SELECT C.Id,C.Name FROM $DataPublic.ct_myorder  A LEFT JOIN $DataPublic.ct_data C ON C.Id=A.CtId WHERE 1 $SearchRows  GROUP BY C.Id ORDER BY C.Id",$link_id);
	if($checkCTRow=mysql_fetch_array($checkCTSql)){
		echo"<select name=CtId id=CtId onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
		do{
			$Id=$checkCTRow["Id"];
			$Name=$checkCTRow["Name"];
			if($Id==$CtId){
				echo"<option value='$Id' selected>$Name</option>";
				$SearchRows.=" AND A.CtId='$Id'";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkCTRow=mysql_fetch_array($checkCTSql));
		  echo"</select>&nbsp;";
		}

	$checkTypeSql=mysql_query("SELECT A.Operator,M.Name FROM $DataPublic.ct_myorder  A  LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator WHERE 1 $SearchRows  GROUP BY A.Operator ORDER BY A.Operator",$link_id);
	if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
		echo"<select name=mOperator id=mOperator onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
		do{
			$Id=$checkTypeRow["Operator"];
			$Name=$checkTypeRow["Name"];
			if($Id==$mOperator){
				echo"<option value='$Id' selected>$Name</option>";
				$SearchRows.=" AND A.Operator='$Id'";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
		echo"</select>&nbsp;";
		echo "<a href='ct_myorder_count.php' target='_blank'>点餐统计表</a>";
		}
	}
else{
	 $SearchRows=" AND A.Operator='$Login_P_Number' ";
	 $ActioToS="2,4";
}
//echo "</br><span style='color:#FF0000;margin-left:5px;'>点餐时间：午餐11:00前，晚餐5:00前。</span>";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#F5F5F5' width='$tableWidth' ><tr ><td height='15' class='' ><span style='color:#FF0000;margin-left:5px;'>点餐时间：午餐11:00前，晚餐5:00前。</span></td></tr></table>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Price,A.Qty,A.Amount,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS MenuName,C.Name AS CTName,D.Name AS MenuType,A.Mid,M.Date AS mainDate,M.Remark AS mainRemark,M.Bill
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.ct_myordermain M ON M.Id=A.Mid
LEFT JOIN $DataPublic.ct_menu B ON B.Id=A.MenuId
LEFT JOIN $DataPublic.ct_data C ON C.Id=B.CtId
LEFT JOIN $DataPublic.ct_type D ON D.Id=B.mType
WHERE 1 $SearchRows ORDER BY A.Mid";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$tbDefalut=0;
$midDefault="";
$dc=anmaIn("download/ctmyorderbill/",$SinkOrder,$motherSTR);
	do {
		$m=1;
         $LockRemark="";
		$checkidValue=$myRow["Id"];
		$Mid=$myRow["Mid"];
       $mainDate=$myRow["mainDate"];
		$CTName=$myRow["CTName"];
		$MenuType=$myRow["MenuType"];
		$MenuName=$myRow["MenuName"];
		$Price=$myRow["Price"];
		$Qty=$myRow["Qty"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"];
		$Remark=$Remark==""?"&nbsp;":$Remark;
	    $Bill=$myRow["Bill"];
         if($Bill!=""){
	        $fc=anmaIn($Bill,$SinkOrder,$motherSTR);
                  $Bill="<a href=\"../admin/openorload.php?d=$dc&f=$fc&Type=&Action=6\"target=\"download\">view</a>";
               }
       else $Bill="-";
		$mainRemark=$myRow["mainRemark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[mainRemark]' width='18' height='18'>";
        $Estate=$myRow["Estate"];
         switch($Estate){
             case "0":$Estate="<div class='greenB'>√</div>";
                 break;
             case "1":$Estate="<div class='redB'>×</div>";
                 break;
              }

		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
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

  $upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ct_myorder_upmain\",$Mid)' src='../images/edit.gif' alt='更新点餐主单资料' width='13' height='13'>";

	if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainDate</td>";//日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$Bill</td>";		//单据
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";		//备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=9;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose  $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$CTName</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$MenuType</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$MenuName</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Estate</td>";
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";
				$m=$m+2;
				echo"<td  width='' align='center'>$Operator</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";	//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainDate</td>";//日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$Bill</td>";		//单据
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";		//备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
	       	echo"<td class='A0001' width='$Field[$m]' align='center'>$CTName</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$MenuType</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' >$MenuName</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Estate</td>";
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";
				$m=$m+2;
				echo"<td  width='' align='center'>$Operator</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}

		}while ($myRow = mysql_fetch_array($myResult));
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