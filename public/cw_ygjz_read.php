<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cwygjz
$DataIn.cwygjz
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 员工借支列表");
$funFrom="cw_ygjz";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工ID|50|借支员工|60|借支日期|90|借支金额|60|结付银行|100|借据|40|经手人|60|还款日期|90|备注|350";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
$sumCols="5";		//求和列
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$Estate=$Estate==""?1:$Estate;
	$SearchRows=$Estate==0?" and J.InDate>'0000-00-00'":" and J.InDate='0000-00-00'";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT left(J.PayDate,7) AS Month FROM $DataIn.cwygjz J WHERE 1 $SearchRows group by DATE_FORMAT(J.PayDate,'%Y-%m') order by J.Id DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and DATE_FORMAT(J.PayDate,'%Y-%m')='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$MonthSelect.="</select>&nbsp;";
		}
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='1' $EstateSTR1>未还款</option>
	<option value='0' $EstateSTR0>已还款</option>
	</select>&nbsp;";
	echo $MonthSelect;
	}
echo"$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT J.Id,J.Mid,J.Number,J.PayDate,J.Amount,J.InDate,J.Payee,J.Remark,J.Locks,J.Operator,M.Name,B.Title
FROM $DataIn.cwygjz J
LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=J.BankId
WHERE 1 $SearchRows
ORDER BY J.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$Amount=$myRow["Amount"];
		$BankName=$myRow["Title"];
		$Payee=$myRow["Payee"];
		$PayDate=$myRow["PayDate"];
		if($myRow["InDate"]=="0000-00-00"){
			$LockRemark="";
			if($myRow["Mid"]==0){
				$InDate="<div class='redB'>未还款</div>";
				}
			else{
				$InDate="<div class='yellowB'>准备还款</div>";
				}
			}
		else{
			$InDate="<div class='greenB'>".$myRow["InDate"]."</div>";
			$LockRemark="已从薪资扣除，操作锁定！修改需取消结付。";
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];		
		$Dir=anmaIn("download/cwygjz/",$SinkOrder,$motherSTR);
		if($Payee==1){
			$Payee="J".$Id.".jpg";
			$Payee=anmaIn($Payee,$SinkOrder,$motherSTR);
			$Payee="<span onClick='OpenOrLoad(\"$Dir\",\"$Payee\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Payee="-";
			}
		$ValueArray=array(
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$PayDate,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$BankName),
			array(0=>$Payee,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'"),
			array(0=>$InDate,	1=>"align='center'"),
			array(0=>$Remark,	3=>"...")
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
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>