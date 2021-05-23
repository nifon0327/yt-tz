<?php 
//电信-EWEN
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
//步骤3：
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	
	
	    $cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.staff_outsubsidysheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 and S.Estate='$Estate'  $SearchRows  GROUP BY S.cSign ",$link_id);
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
		
		
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.staff_outsubsidysheet S 
	WHERE 1 and S.Estate='$Estate'  $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
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
	$SearchRows.="and S.Estate=3";
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
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,S.TotalRate,S.Time,M.Name,B.Name AS Branch,M.ComeIn,D.outDate,S.AveAmount,S.Number,E.Name AS AuditStaff,T.Name AS TypeName,S.PaySign,D.Reason AS LeaveReason,S.TypeId,S.cSign
 	FROM $DataIn.staff_outsubsidysheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
	LEFT JOIN $DataPublic.staffmain E ON E.Number=S.Auditor 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
	WHERE 1 $SearchRows order by S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Number=$myRow["Number"];
		$Amount=$myRow["Amount"];
       $AveAmount=$myRow["AveAmount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$ComeIn=$myRow["ComeIn"];
        $TypeName=$myRow["TypeName"];
		$LeaveReason=$myRow["LeaveReason"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[LeaveReason]' width='16' height='16'>";
		$AuditStaff=$myRow["AuditStaff"]==""?"&nbsp;":$AuditStaff;
         /*********************************************/
		 //工龄计算
		 $ComeInYM=substr($ComeIn,0,7);
		 include "subprogram/staff_model_gl.php";
       $outDate=$myRow["outDate"];
       $TotalRate =$myRow["TotalRate"];
       $Time ="第".$myRow["Time"]."次";
       $PaySign =$myRow["PaySign"];
       if($PaySign==1)$Time="<span class='redB'>一次性支付</span>";
       $Rate =$TotalRate."个月";
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/staff_subsidy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill=$Number.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];		
		switch($Estate){
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			}
       $TypeId=$myRow["TypeId"]==1?"离职补助":"辞退赔偿金";	
       $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";	
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$TypeId, 1=>"align='center'"),
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Branch,1=>"align='center'"),
			array(0=>$Gl_STR,1=>"align='center'"),
			array(0=>$outDate,1=>"align='center'"),
		    array(0=>$TypeName,1=>"align='center'"),
			array(0=>$LeaveReason,1=>"align='center'"),
			array(0=>$AveAmount, 1=>"align='center'"),
			array(0=>$Rate, 1=>"align='center'"),
			array(0=>$Time, 1=>"align='center'"),
			array(0=>$Amount, 1=>"align='center'"),
			array(0=>$Currency, 1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$AuditStaff,	3=>"...")
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