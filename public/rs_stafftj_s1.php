<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|体检类型|60|员工姓名|60|部门|60|职位|60|入职日期|70|金额|60|备注|250|凭证|30|体检日期|80|合格与否|60|报告单|50|登记日期|70|操作员|60";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/s1_model_3.php";
	$date_Result = mysql_query("SELECT Month FROM $DataIn.cw17_tjsheet WHERE 1 GROUP BY Month order by Month DESC",$link_id);
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
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT,S.tjDate,S.HG
	 FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"];
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
            $tjDate=$myRow["tjDate"]=="0000-00-00"?"":$myRow["tjDate"];
            $tjDate="<span class='yellowB'>$tjDate</span>";	
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
            $Remark=$myRow["Remark"];
            $tjType=$myRow["tjType"];
            $CheckT=$myRow["CheckT"];
            $CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
				case "4":  $tjType="健康体检".$CheckTime;  break;
                }
			include "../model/subprogram/staffname.php";
           $HG=$myRow["HG"];
            switch($HG){
                 case 1: 
                   $HG="<span class='greenB'>合格</span>";
                       break;
                 case 0:
                      $HG="<span class='redB'>不合格</span>";
                       break;
               }

			$Estate=$myRow["Estate"];
			switch($Estate){
				case 1:
					if($Mid==0){
						$Estate="<span class='redB'>未处理</span>";$LockRemark="";}
					else{
						$Estate="<span class='redB'>错误(状态为未处理但已付)</span>";
						$LockRemark="错误,需核查并请IT处理.";
						}
				break;
				case 2:
					$Estate="<span class='yellowB'>请款中</span>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 3:
				$Estate="<span class='yellowB'>请款通过</span>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 0:
					$Estate="<span class='greenB'>已结付</span>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
				break;
				}
            $Attached=$myRow["Attached"];
        if($Attached!="" && $Attached!=0){
		     $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		     $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
             $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
          }
        else $Attached="&nbsp;";
         $filereportResult=mysql_fetch_array(mysql_query("SELECT Attached FROM $DataPublic.staff_tj WHERE Number=$Number AND Mid=$Id order by id desc",$link_id));
         $ReportAttached=$filereportResult["Attached"];
		// echo $ReportAttached;
         if($ReportAttached!=""){
		      $f2=anmaIn($ReportAttached,$SinkOrder,$motherSTR);
		      $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
               $FileReport="<a href=\"openorload.php?d=$d1&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
               }
        else {
               $FileReport="";
                }
		$ValueArray=array(
			array(0=>$tjType,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
		    array(0=>$ComeIn, 	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Remark, 	1=>"align='left'"),
			array(0=>$Attached, 	1=>"align='center'"),
			array(0=>$tjDate, 	1=>"align='center'"),
			array(0=>$HG, 	1=>"align='center'"),
			array(0=>$FileReport, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		 $checkidValue=$Id."^^".$Name;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>