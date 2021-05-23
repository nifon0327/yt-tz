<?php 
include "../model/modelhead.php";
$sumCols="8";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 购房补助费用申请");
$funFrom="staffhouse_subsidy";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|60|申请月份|70|部门|70|职位|70|员工ID|60|员工姓名|100|申请金额|60|凭证|60|备注|250|状态|40|更新日期|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,14";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$DefaultMonth="2016-01-01";
	$NewMonth=date("Y-m");
	$Months=intval(abs((date("Y")-2016)*12+date("m")));
	for($i=$Months-2;$i>=0;$i--){
		$dateValue=date("Y-m",strtotime("$i month", strtotime($DefaultMonth))); 
		$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$optionStr.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				$optionStr.="<option value='$dateValue'>$dateValue</option>";					
			   }
		}
	}
	echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>$optionStr</select>&nbsp;";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT   S.Id,S.Amount,S.Remark,S.Month,S.Attached,S.Date,S.Estate,S.Locks,S.Operator,
M.Name,B.Name AS Branch,J.Name AS Job,S.Number,M.Estate AS mEstate,S.cSign
FROM  $DataIn.cw21_housefeesheet   S 
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Number
LEFT JOIN $DataIn.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=S.JobId
WHERE 1 $SearchRows ORDER BY S.Estate DESC,S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/staffhouseinfo/",$SinkOrder,$motherSTR);
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];	
		$Number	=$myRow["Number"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
		$Month=$myRow["Month"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$Estate=$myRow["Estate"];
	    switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$checkPay= mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.cw21_housefeemain WHERE Id='$Mid' LIMIT 1",$link_id));
				$PayDate=$checkPay["PayDate"];
				$Estate="<div align='center' class='greenB' title='已结付,结付日期：$PayDate'>√</div>";
				$LockRemark="记录已经结付，强制锁定操作！";
				$Locks=0;
				break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];

		$Attached=$myRow["Attached"];
		
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
            $Attached="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffhouse_subsidy_file\",\"$Id\")' src='../images/edit.gif' title='上传凭证' width='13' height='13'>&nbsp;&nbsp;&nbsp;<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
              $Attached="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffhouse_subsidy_file\",\"$Id\")' src='../images/edit.gif' title='上传凭证' width='13' height='13'>";
			}

       if($Remark!=""){
             $Remark="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffhouse_subsidy_file\",\"$Id\")' src='../images/edit.gif' title='更新备注' width='13' height='13'>&nbsp;&nbsp;&nbsp;$Remark";
             }
     else{
           $Remark="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffhouse_subsidy_file\",\"$Id\")' src='../images/edit.gif' title='更新备注' width='13' height='13'>";
          }

		if($myRow["mEstate"]==0){
			$Name="<div class='redB'>$Name</div>";
			}
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Attached),
			array(0=>$Remark),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>