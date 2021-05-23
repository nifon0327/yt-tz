<?php 
//$DataIn.oprationlog 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
ChangeWtitle("$SubCompany 操作日志");
$funFrom="oprationlog";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1";
$tableWidth=850;
$tableMenuS=600;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$ToDay=date("Y-m-d");
	//$SearchRows=" AND left(DateTime,10)='$ToDay'";
    $checkDay=$checkDay==""?date("Y-m-d"):$checkDay;	
	echo"<input name='checkDay' type='text' id='checkDay' size='10' maxlength='10' value='$checkDay'   onFocus='WdatePicker({onpicked:function(dp){document.form1.submit();}})' />&nbsp;";
	$SearchRows=" AND DATE_FORMAT(DateTime,'%Y-%m-%d') ='$checkDay' ";
	  
     echo"<select name='sOperator' id='sOperator' onchange='document.form1.submit()'>";
     echo"<option value='' selected>操作员</option>";
	$checkType= mysql_query("SELECT M.Number,M.Name FROM $DataPublic.staffmain M,$DataIn.oprationlog P WHERE P.Operator=M.Number AND  left(P.DateTime,10)='$ToDay' GROUP BY M.Number ORDER BY M.Name",$link_id);
	if($TypeRow = mysql_fetch_array($checkType)){			
		do{
			$thisNumber=$TypeRow["Number"];
			$thisName=$TypeRow["Name"];
                        if ($thisNumber==$sOperator){
			   echo"<option value='$thisNumber' selected>$thisName</option>";
                           $SearchRows .=" AND Operator='$thisNumber'";
                        }else{
                           echo"<option value='$thisNumber'>$thisName</option>"; 
                        }
			}while ($TypeRow = mysql_fetch_array($checkType));
		}	
		echo"</select>&nbsp;&nbsp;";
		
	
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
include "../model/subprogram/read_model_5.php";

$mySql = "SELECT * FROM $DataIn.oprationlog  WHERE 1 $SearchRows ORDER BY DateTime DESC";
//echo $mySql;
//表格表头排序处理
$i=1;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$bgcolorSTR="bgcolor='#CCCCCC'";
		$Id=$myRow["Id"];	
		$DateTime=$myRow["DateTime"];	
		$Item=$myRow["Item"];
		$Funtion=$myRow["Funtion"];	
		$Log=$myRow["Log"];	
		$OperationResult=$myRow["OperationResult"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		if($i%2==0){
			$bgcolorSTR="bgcolor='#FFFFFF'";
			}
		echo"<table width='$tableWidth' border='0' cellpadding='3' cellspacing='0' id='ListTable$i' $bgcolorSTR>
			<tr>
				<td width='10' class='A0010' bgcolor='#FFFFFF'>&nbsp;</td>
				<td width='150' class='A1111' valign='top'>
					序&nbsp;&nbsp;&nbsp;&nbsp;号：$Id<br>
					操作日期：$DateTime<br>项&nbsp;&nbsp;&nbsp;&nbsp;目：$Item<br>
					动&nbsp;&nbsp;&nbsp;&nbsp;作：$Funtion</br>
					操 作 员：$Operator
				</td>
				<td height='60' class='A1101' valign='top'>内容：$Log</td>
				<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
			</tr>
			<tr>
				<td colspan='4' class='A0011' bgcolor='#FFFFFF'>&nbsp;</td>
			</tr>
		</table>";
		$i++;
		}while($myRow = mysql_fetch_array($myResult));
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//include "../model/subprogram/read_model_menu.php";
?>
