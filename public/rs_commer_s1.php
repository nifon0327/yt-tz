<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|类型|60|员工姓名|60|部门|60|职位|60|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|单据|60|登记日期|100|操作员|80";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
//非必选,过滤条件
switch($Action){

     default:
     break;	
	}   
//步骤3：
include "../model/subprogram/s1_model_3.php";
$SearchRows =" AND S.TypeId=4";	
	$date_Result = mysql_query("SELECT S.Month FROM $DataIn.sbpaysheet S WHERE 1 $SearchRows GROUP BY S.Month order by S.Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
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
$mySql="SELECT 
	S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,M.Name,S.TypeId,P.Picture
	 FROM $DataIn.sbpaysheet S
    LEFT JOIN $DataIn.rs_casualty_picture P ON P.Mid=S.Id
	LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
	WHERE 1 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
            $checkidValue=$Id."^^".$Name;
			$Month =$myRow["Month"];
			$mAmount =$myRow["mAmount"];
			$cAmount =$myRow["cAmount"];
			$Amount=sprintf("%.2f",$mAmount +$cAmount);
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchId=$myRow["BranchId"];				
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$Mid=$myRow["Mid"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
            switch($myRow["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
                    case 3: $TypeName="商业险";break;
                   }
        $Picture=$myRow["Picture"];
        if($Picture!=""){
		    $Dir=anmaIn("download/Casualty/",$SinkOrder,$motherSTR);
			$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
			$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
            }
       else $Picture="";
		$ValueArray=array(
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Month, 	1=>"align='center'"),
			array(0=>$mAmount,	1=>"align='center'"),
			array(0=>$cAmount,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Picture, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		include "../model/subprogram/s1_model_6.php";
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