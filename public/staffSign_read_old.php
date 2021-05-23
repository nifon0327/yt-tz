<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
$DataPublic.branchdata
二合一已更新
*/
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	//需处理参数
	$ColsNumber=10;				
	$tableMenuS=600;
	ChangeWtitle("$SubCompany 员工薪资签名异常记录");	
	$funFrom="staffSign";
	$nowWebPage=$funFrom."_read";
	$Th_Col="选项|50|序号|50|员工ID|60|员工姓名|80|地点|60|部门|60|职位|60|签名日期|80|异常原因|200|扣款|80|备注|200|审核状态|120";
	$Pagination=$Pagination==""?0:$Pagination;
	$Page_Size = 100;
	$ActioToS="1,2,3,4,5,6";

	//步骤3：
	include "../model/subprogram/read_model_3.php";
	
	//步骤4：需处理-条件选项
	$signMonthSql = "SELECT distinct MONTH FROM  $DataPublic.wage_list WHERE MONTH > '2013-06' order by MONTH desc";
	$signMonthResult = mysql_query($signMonthSql);
	if($signRow = mysql_fetch_assoc($signMonthResult))
	{
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do
		{
			$signMont = $signRow["MONTH"];
			if($chooseMonth == "")
			{
				$chooseMonth = $signMont;
			}
			if($chooseMonth == $signMont)
			{
				echo"<option value='$signMont' selected>$signMont</option>";
				$SearchRows.=" and S.Month='$signMont'";
			}
			else
			{
				echo"<option value='$signMont'>$signMont</option>";					
			}
		}
		while($signRow = mysql_fetch_assoc($signMonthResult));
	}
	
	//步骤5：
	include "../model/subprogram/read_model_5.php";
	//步骤6：需处理数据记录处理
	List_Title($Th_Col,"1",0);
	$i=1;
	$j = 1;
	$mySql = "Select A.Number, A.Estate, A.Payment, B.Name, B.cSign, C.sign, C.Date as SignDate, E.Name as Branch, F.Name as Job, G.Date as listDate, A.Remark, A.Id
			  From $DataPublic.wage_sign_overtime A
			  Left Join $DataPublic.staffmain B On B.Number = A.Number
			  Left Join $DataPublic.wage_list_sign C On C.Number = A.Number and C.signMonth = A.Month
			  Left Join $DataPublic.branchdata E On E.Id = B.BranchId
			  Left Join $DataPublic.jobdata F On F.Id = B.JobId
			  Left Join $DataPublic.wage_list G On G.Month = A.Month and G.cSign = B.cSign
			  Where A.Month = '$chooseMonth'
			  Order By B.cSign Desc";
	
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_array($myResult))
	{
		do
		{
			$m=1;
			$Id = $myRow["Id"];
			$signDate = $myRow["SignDate"];
			$sign = $myRow["sign"];
			$number = $myRow["Number"];
			$name = $myRow["Name"];
			$branch = $myRow["Branch"];
			$job = $myRow["Job"];
			$cSign = $myRow["cSign"];
			$payDay = $myRow["listDate"];
			$state = $myRow["Estate"];
			$pay = $myRow["Payment"];
			$remark = $myRow["Remark"] == ' '?'':$myRow["Remark"];
			
			$location = ($cSign == "7")?"研砼":"皮套";
			$dataSource = ($cSign == "7")?$DataIn:$DataOut;
			$payStateSql = "Select Estate From $dataSource.cwxzsheet Where Month = '$chooseMonth' and Number = '$number' and Estate = '3' Limit 1";
			$payResult = mysql_query($payStateSql);
			
			
			$Locks = "";
			$LockRemark = "";
			if($state == "1"){
				$state = "<div class='greenB'>需扣款</div>";
				$Locks = 0;
				//$LockRemark = "已经审核";
			}else if($state == "0"){
				$state = "<div class='redB'>因请假等原因,无需扣款</div>";
				$Locks = 0;
				//$LockRemark = "已经审核";
			}else{
				$state = "<div>未审</div>";
			}
		
			$overDate = (strtotime($signDate) - strtotime($payDay))/3600/24 - 5;
		
			$error = "";
			
			if(mysql_num_rows($payResult) == 1){
				$error .= "未结付";
			}else if($sign == "" && $signDate == ""){
				$error .= "逾期未签名";
			}else{
				if($overDate > 0){
					$error .= "逾期签名 $overDate 天<br>";
					//$pay = 10 * $overDate;
				}
				
				if($sign == ""){
					$error .= "签名为空";
				}
			}
		
			$ValueArray=array(
						array(0=>$number, 1=>"align='center'"),
						array(0=>$name,	1=>"align='center'"),
						array(0=>$location,	1=>"align='center'"),
						array(0=>$branch,	1=>"align='center'"),
						array(0=>$job,	1=>"align='center'"),
						array(0=>$signDate,	1=>"align='center'"),
						array(0=>$error, 1=>"align='center'"),
						array(0=>$pay, 1=>"align='center'"),
						array(0=>$remark, 1=>"align='center'"),
						array(0=>$state, 1=>"align='center'")
						);
			$checkidValue=$Id;
			
			include "../model/subprogram/read_model_6.php";
		}
		while($myRow = mysql_fetch_array($myResult));
	}
	
	//步骤7：
echo '</div>';
	List_Title($Th_Col,"0",0);
	$myResult = mysql_query($mySql,$link_id);
	$RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
	
?>