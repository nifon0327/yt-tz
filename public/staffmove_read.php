<?php 
/*
已更新$DataIn.电信---yang 20120801
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 员工调动列表");
$funFrom="staffmove";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|性别|40|现部门|60|现职位|60|原公司|60|原部门|60|原职位|60|调动日期|65|说明|120";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,7,8,50";
$ActioToS="1,50";
//步骤3：
include "../model/subprogram/read_model_3.php";
/*if($From!="slist"){
	    $SearchRows="";
	    $SelectFrom=5;
        $cSignTB="P";
        $SharingShow="";
        include "../model/subselect/cSign.php";
}*/
//include "../model/subprogram/read_cSign.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.Introducer,S.Sex,S.Rpr,S.Birthday,S.Mobile,S.Idcard,
P.Id,P.OcSign,P.OBranchId,P.OJobId,P.OGroupId,P.Date
	FROM $DataIn.staff_move P
	LEFT JOIN $DataIn.staffmain M ON M.Number=P.Number
	LEFT JOIN $DataIn.staffsheet S ON S.Number=M.Number
	WHERE 1  $SearchRows ORDER BY P.Date DESC, M.BranchId,M.JobId,M.Number";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];
			$Name=$myRow["Name"];
			$Number=$myRow["Number"];
			$BranchId=$myRow["BranchId"];		
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$ComeIn=$myRow["ComeIn"];
			$Sex=$myRow["Sex"]==1?"男":"女";

		    $Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
		   //原来公司 
            $OcSign=$myRow["OcSign"];	
            $ODate=$myRow["Date"];
            $OBranchId=$myRow["OBranchId"];
            $OJobId=$myRow["OJobId"];
            if ($OcSign>0){
                   $cSignResult = mysql_query("SELECT Db,CShortName FROM $DataPublic.companys_group WHERE cSign=$OcSign ORDER BY Id",$link_id);
                   if($cSignRow = mysql_fetch_array($cSignResult)){
                        $ODb=$cSignRow["Db"];
                        $OCShortName=$cSignRow["CShortName"];
                        $OB_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$OBranchId LIMIT 1",$link_id));
			            $OBranch=$OB_Result["Name"];	
                        $OJ_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$OJobId LIMIT 1",$link_id));
			            $OJob=$OJ_Result["Name"];
                      }
                      $Remark="从".$OCShortName."调出";
                     }
           else{
                      $Remark="&nbsp;";
                }
		$ValueArray=array(
		    array(0=>$Number,	            1=>"align='center'"),	
			array(0=>$Name,		        1=>"align='center'"),
            array(0=>$Sex, 		            1=>"align='center'"),
			array(0=>$Branch, 	            1=>"align='center'"),
			array(0=>$Job, 		            1=>"align='center'"),	
			array(0=>$OCShortName,	1=>"align='center'"),
			array(0=>$OBranch, 	        1=>"align='center'"),
			array(0=>$OJob, 		        1=>"align='center'"), 
			array(0=>$ODate,		        1=>"align='center'"),
		    array(0=>$Remark,             1=>"align='center'")
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