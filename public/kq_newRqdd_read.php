<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 上班日期对调");
$funFrom="kq_newRqdd";
$nowWebPage=$funFrom."_read";
$sumCols="";		//求和列
$Th_Col="选项|40|序号|40|员工ID|60|员工姓名|60|对调前原工作日|200|对调前原工作日时间|200|对调前原休息日|200|对调前元休息日时间|200|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
	$date_Result = mysql_query("SELECT DATE_FORMAT(GDate,'%Y-%m') AS Month FROM $DataIn.kq_rqddnew group by DATE_FORMAT(GDate,'%Y-%m') order by GDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and DATE_FORMAT(D.GDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		}
	 	echo"</select>&nbsp;";
		$FormalSign=$FormalSign==""?0:$FormalSign;
		$selStr="selFlag" . $FormalSign;
		$selStr="selected";
		echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
		     <option value='0' $selFlag0>全部</option>
			 <option value='1' $selFlag1>正式工</option>
			 <option value='2' $selFlag2>试用期</option>";
		echo "</select>&nbsp;";
		if($FormalSign>0)$SearchRows.=" AND M.FormalSign='$FormalSign'";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$mySql="SELECT A.Id,A.Number,D.GDate,D.GTime,D.XDate,D.XTime,A.Operator,M.Name 
        FROM $DataIn.kq_ddsheet A
        Left Join $DataIn.kq_rqddnew D On D.Id = A.ddItem
        LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
        WHERE 1 $SearchRows 
        order by D.GDate DESC,A.Number";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];	
		
		$GDate=$myRow["GDate"];
		$MonthTemp=substr($GDate,0,7);
		$weekTemp1="(星期".$Darray[date("w",strtotime($GDate))].")";
		$GDate.=$weekTemp1;
		$GTime = $myRow["GTime"];
		
		$XDate=$myRow["XDate"];
		if($XDate != "")
		{
			$weekTemp2="(星期".$Darray[date("w",strtotime($XDate))].")";
			$XDate.=$weekTemp2;
			
			$XTime = $myRow["XTime"];		
		}
		else
		{
			$XDate = "待定";
			$XTime = "待定";
		}
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
		$LockRemark="";
		// if($checkRow = mysql_fetch_array($checkMonth)){
		// 	$LockRemark="该月考勤统计已生成,禁止修改.";
		// 	}			
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$GDate, 		1=>"align='center'"),
			array(0=>$GTime, 		1=>"align='center'"),
			array(0=>$XDate,		1=>"align='center'"),
			array(0=>$XTime,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
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