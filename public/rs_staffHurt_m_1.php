<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=600;
$sumCols="14";		//求和列
ChangeWtitle("$SubCompany 员工工伤报销费用审核");
$funFrom="Rs_staffHurt";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|40|所属公司|60|员工姓名|60|部门|60|职位|60|入职日期|70|总金额|60|备注|250|工伤日期|80|工伤凭证|30|社保凭证|30|社保金额|60|费用凭证|30|实报金额|60|结付状态|60|登记日期|70|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,17,15";	
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.cw18_workhurtsheet WHERE 1 AND Estate=2 GROUP BY Month order by Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$B_Result=mysql_query("SELECT B.Id,B.Name FROM $DataIn.cw18_workhurtsheet S 
	      LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
          LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
          WHERE 1 AND S.Estate=2 $SearchRows  GROUP BY B.Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
	     echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND P.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT 
	S.Id,S.cSign,S.Number,S.Month,S.Amount,S.SSecurityAmout,S.AllAmout,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,P.ComeIn,S.CheckT,S.HurtDate,S.HostpitalInvoice,S.SSecurityInvoice
	 FROM $DataIn.cw18_workhurtsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 AND S.Estate=2  $SearchRows ";	
	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"]; //公司需要报销的费用
			$SSecurityAmout =$myRow["SSecurityAmout"];  //社保局支付费用
			$AllAmout =$myRow["AllAmout"];  //总费用
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
            $HurtDate=$myRow["HurtDate"]=="0000-00-00"?"&nbsp;":$myRow["HurtDate"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
            $Remark=$myRow["Remark"];
            $CheckT=$myRow["CheckT"];
            //$CheckTime=$GDnumber[$CheckT];
 			include "../model/subprogram/staffname.php";
 			$cSignFrom=$myRow["cSign"];
		    include"../model/subselect/cSign.php";
 			$Estate="<span class='yellowB'>请款中</span>";

			$Attached=$myRow["Attached"];
			//echo "Attached1:$Attached";
			if($Attached!="" ){
				 //echo "Attached2:$Attached";
				 $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
				 
			  }
			else $Attached="&nbsp;";
			
			$HostpitalInvoice=$myRow["HostpitalInvoice"];
			if($HostpitalInvoice!="" ){
				 $f1=anmaIn($HostpitalInvoice,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $HostpitalInvoice="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			  }
			else $HostpitalInvoice="&nbsp;";
	
			$SSecurityInvoice=$myRow["SSecurityInvoice"];
			if($SSecurityInvoice!="" ){
				 $f1=anmaIn($SSecurityInvoice,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/Hurtfile/",$SinkOrder,$motherSTR);		
				 $SSecurityInvoice="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			  }
			else $SSecurityInvoice="&nbsp;";

        /*
         $filereportResult=mysql_fetch_array(mysql_query("SELECT Attached FROM $DataPublic.staff_tj WHERE Number=$Number AND Mid=$Id",$link_id));
         $ReportAttached=$filereportResult["Attached"];
         if($ReportAttached!=""){
		      $f2=anmaIn($ReportAttached,$SinkOrder,$motherSTR);
		      $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
               $FileReport="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Rs_staffHurt_tjfile\",\"$Id\")' src='../images/edit.gif' title='上传体检报告' width='13' height='13'><a href=\"openorload.php?d=$d1&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
               }
        else {
               $FileReport="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Rs_staffHurt_tjfile\",\"$Id\")' src='../images/edit.gif' title='上传体检报告' width='13' height='13'>";
                }
				*/
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
		    array(0=>$ComeIn, 	1=>"align='center'"),
			array(0=>$AllAmout,	1=>"align='right'"),
			array(0=>$Remark, 	1=>"align='left'"),
			array(0=>$HurtDate, 	1=>"align='center'"),
			array(0=>$Attached, 	1=>"align='center'"),
			array(0=>$SSecurityInvoice, 	1=>"align='center'"),
			array(0=>$SSecurityAmout,	1=>"align='right'"),			
			array(0=>$HostpitalInvoice, 	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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