<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 劳务工离职列表");
$funFrom="lw_staffout";
$nowWebPage=$funFrom."_read";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|身份证号码|150|部门|55|小组|50|职位|55|移动电话|80|离职分类|90|离职原因|60|入职日期|80|离职日期|80|在职时间|80|性别|40|籍贯|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$ActioToS="1";
//步骤3：
include "../model/subprogram/read_model_3.php";
//include "../model/subprogram/read_cSign.php";
if($From!="slist"){

	  $SearchRows='';
		$monthResult = mysql_query("SELECT outDate FROM $DataPublic.lw_dimissiondata WHERE 1  group by DATE_FORMAT(outDate,'%Y-%m') order by outDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["outDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["outDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows="and  DATE_FORMAT(D.outDate,'%Y-%m')='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";					
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and  DATE_FORMAT(D.outDate,'%Y-%m')='$FirstValue'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
		}
		echo $MonthSelect;	  
	  
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.Number,D.Reason,D.outDate,D.Locks,M.Id as MId,M.Name,M.BranchId,M.JobId,M.ComeIn,
	S.Sex,S.Rpr,S.Birthday,S.Mobile,S.Idcard,DT.Name AS dName,G.GroupName	
	FROM $DataPublic.lw_staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
    LEFT JOIN $DataPublic.lw_staffsheet S ON M.Number=S.Number
    LEFT JOIN $DataPublic.lw_dimissiondata D ON D.Number=M.Number
    LEFT JOIN $DataPublic.dimissiontype DT ON DT.Id=D.Type
	WHERE 1 AND M.Estate=0 $SearchRows ORDER BY D.outDate DESC,M.BranchId,M.JobId,M.Number";
//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];
			$Name=$myRow["Name"];
			$Number=$myRow["Number"];
			$BranchId=$myRow["BranchId"];	
			$GroupName=$myRow["GroupName"]==""?"<div class=\"redB\">未设置</div>":$myRow["GroupName"];//小组
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
			$dName=$myRow["dName"];
			$Reason=$myRow["Reason"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Reason]' width='18' height='18'>";
			$ComeIn=$myRow["ComeIn"];
			$outDate=$myRow["outDate"];
			$Name="<span class='redB'>$Name</span>";
			$Sex=$myRow["Sex"]==1?"男":"女";
			$Birthday=$myRow["Birthday"];
			$Rpr=$myRow["Rpr"];
			$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
			if($rRow = mysql_fetch_array($rResult)){
				$Rpr=$rRow["Name"];
				}
			$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
			

							
		//计算在职时间
		$ThisDay=$outDate;
		$ThisEndDay=$Month."-".date("t",strtotime($ThisDay));		
		$Years=date("Y",strtotime($ThisDay))-date("Y",strtotime($ComeIn));
		$ThisMonth=date("m",strtotime($ThisDay));
		$CominMonth=date("m",strtotime($ComeIn));
		//年计算
		if($ThisMonth<$CominMonth){//计薪月份少于进公司月份
			$Years=($Years-1);
			$MonthSTR=$ThisMonth+12-$CominMonth;
			$gl_STR=$Years<=0?"&nbsp;":$Years."年";
			}
		else{
			$MonthSTR=$ThisMonth-$CominMonth;
			$gl_STR=$Years<=0?"&nbsp;":$Years."年";
			}

		//月计算
		//如果是当月，如果入职日期是3号之前，则当整月，否则不足月
		if(date("d",strtotime($ComeIn))<4){
			$MonthSTR=$MonthSTR+1;
			}
		$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
		$gl_STR=$gl_STR.$MonthSTR;
		$Locks=$myRow["Locks"];
		
		$MId=$myRow["MId"];
		$Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($Birthday))){
			if (date('d', time()) > date('d', strtotime($Birthday))){
				$Age++;
				}
			}
		else{
			if (date('m', time()) > date('m', strtotime($Birthday))){
			$Age++;
			}
		}
		$ViewId=anmaIn($MId,$SinkOrder,$motherSTR);		
		if($Age<18){
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$Name</div></a>";
			}
		else{
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$Name</div></a>";
			}
			
		$ValueArray=array(
		    array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Idcard,1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$GroupName,1=>"align='center'"),
			array(0=>$Job, 		1=>"align='center'"),		
			array(0=>$Mobile,	1=>"align='center'"),
			array(0=>$dName,	1=>"align='center'"),
			array(0=>$Reason, 	1=>"align='center'"),
			array(0=>$ComeIn, 	1=>"align='center'"),
			array(0=>$outDate,	1=>"align='center'"),
			array(0=>$gl_STR, 	1=>"align='center'"),
			array(0=>$Sex, 		1=>"align='center'"),
			array(0=>$Rpr, 		1=>"align='center'")	
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
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
include "../admin/subprogram/read_model_menu.php";
?>