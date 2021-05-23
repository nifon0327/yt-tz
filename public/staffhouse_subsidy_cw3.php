<?php 
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//结付状态	
	$SearchRows="";
	 $cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.cw21_housefeesheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 AND S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  S.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
		
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw21_housefeesheet  S WHERE 1 AND S.Estate='$Estate' $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and  DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$SearchRows=$SearchRows==""?"and  DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'":$SearchRows;
		$MonthSelect.="</select>&nbsp;";
	}	
				
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	$SearchRows.=" and S.Estate=3";
	echo $cSignSelect;
	echo $MonthSelect;
	
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	</select>&nbsp;";
	}


//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo"$CencalSstr";

//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$mySql = "SELECT   S.Id,S.Amount,S.Remark,S.Month,S.Attached,S.Date,S.Estate,S.Locks,S.Operator,M.Name,B.Name AS Branch,J.Name AS Job,S.Number,S.cSign
FROM  $DataIn.cw21_housefeesheet   S 
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Number
LEFT JOIN $DataIn.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=S.JobId
WHERE 1 $SearchRows";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	    $m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$Number=$myRow["Number"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
		$Month=$myRow["Month"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$Amount=$myRow["Amount"];
		$Estate=$myRow["Estate"];
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
        $Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];

		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
		
               $Attached="<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
               $Attached="-";
			}

		if($Keys & mUPDATE){
			$Locks=1;
			}
		else{
			$Locks=0;
			}	

		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'"),
            array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
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
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>