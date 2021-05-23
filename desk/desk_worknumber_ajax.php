<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
    switch($workaddId){
         case 1: $SubCompany="48";break;
         case 2: $SubCompany="47";break;
         case 3: $SubCompany="WXD";break;
         case 4: $SubCompany="BHS";break;
         }
ChangeWtitle("$SubCompany 上班人数统计");
$funFrom="desk_worknumber";
$nowWebPage=$funFrom."_ajax";
if($ActionId == "4" || $ActionId == "3"){
  $Th_Col="序号|40|工作地点|50|员工ID|80|员工姓名|80|部门|80|小组|80|职位|80|薪酬类型|80|请假时间|250";
}
else{
  $Th_Col="序号|40|工作地点|50|员工ID|80|员工姓名|80|部门|80|小组|80|职位|80|薪酬类型|80";
}

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,11";
//步骤3：
$ToDay=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";	
	$selStr="selSign".$ActionId;
	$$selStr="selected";			  
    echo"<select name='ActionId' id='ActionId' onchange='ChangeAction()'>";
	echo"<option value='0' $selSign0>$SubCompany-总人数</option>";
	echo"<option value='1' $selSign1>$SubCompany-上班(固定薪)</option>";
	echo"<option value='2' $selSign2>$SubCompany-上班(计时薪)</option>";
	echo"<option value='3' $selSign3>$SubCompany-请假(固定薪)</option>";
	echo"<option value='4' $selSign4>$SubCompany-请假(计时薪)</option>";
	echo"<option value='5' $selSign5>$SubCompany-无记录</option>";
	echo"</select>&nbsp;";
  }
echo "<input type='hidden' id='workaddId' name='workaddId' value='$workaddId'>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

	/***************
	若在d7查询皮套时，由于staffgroup不是共享d0，所以先要判断要关联哪个staffgroup.--jo
	****************/
	
	if($DataIn == "d7" && $SubCompany != "48")
	{
		$targetDataIn = $DataOut;
	}
	else
	{
		$targetDataIn = $DataIn;
	}

switch($ActionId){
case 0://全部总人数
    $mySql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
    FROM $DataPublic.staffmain M 
    LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    WHERE  M.Estate=1 AND M.WorkAdd='$workaddId' ORDER BY M.BranchId,M.GroupId,M.Number";
       break;
case 1://固定薪的上班人数
    $mySql="SELECT  M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
    FROM $DataPublic.staffmain M 
    LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    WHERE M.KqSign>1 AND M.Estate=1 AND M.WorkAdd='$workaddId' AND M.Number 
    NOT IN (SELECT Number FROM $DataPublic.kqqjsheet J  WHERE J.EndDate>='$DateTime' AND J.StartDate<='$DateTime' AND J.Estate=0) ORDER BY M.BranchId,M.GroupId,M.Number";
       break;
case 2://非固定薪的上班人数
		
    $mySql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
        FROM (
             SELECT Number FROM (
	              SELECT C.Number FROM $DataIn.checkinout  C
                  LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
                  WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay' AND M.WorkAdd='$workaddId' 
              )A GROUP BY A.Number ) B 
      LEFT JOIN $DataPublic.staffmain M ON M.Number= B .Number
      LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	  LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId WHERE 1 ORDER BY M.BranchId,M.GroupId,M.Number ";
	  //echo $mySql;
       break;
case 3://固定薪请假人数
       $mySql="SELECT  M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign,K.StartDate,K.EndDate
    FROM $DataPublic.kqqjsheet K
    LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number
     LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	  LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    WHERE DATE_FORMAT(K.EndDate,'%Y-%m-%d')>='$ToDay' AND DATE_FORMAT(K.StartDate,'%Y-%m-%d')<='$ToDay'  AND M.WorkAdd='$workaddId' AND M.Estate>0 AND M.KqSign>1    ORDER BY M.BranchId,M.GroupId,M.Number ";
       break;
case 4://非固定薪的请假人数(如果当天打卡，则视为上班，也就是请半天家的人剔除)
       $mySql="SELECT  M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign,K.StartDate,K.EndDate
    FROM $DataPublic.kqqjsheet K
    LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number
     LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	  LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    WHERE DATE_FORMAT(K.EndDate,'%Y-%m-%d')>='$ToDay' AND DATE_FORMAT(K.StartDate,'%Y-%m-%d')<='$ToDay'  AND M.WorkAdd='$workaddId' AND M.Estate>0 AND M.KqSign=1    
AND K.Number NOT IN (
                SELECT Number FROM (
	            SELECT C.Number FROM $DataIn.checkinout  C
                LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
                WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay' AND M.WorkAdd='$workaddId' 
              )A GROUP BY A.Number
      )  ORDER BY M.BranchId,M.GroupId,M.Number ";
       break;
case 5://非固定薪既没有请假，也没有打卡的记录
    $mySql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
    FROM $DataPublic.staffmain M 
    LEFT JOIN $targetDataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
    WHERE  M.Estate=1 AND M.WorkAdd='$workaddId'  AND M.KqSign=1 
    AND M.Number NOT IN (
           SELECT M.Number
           FROM $DataPublic.kqqjsheet J
           LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
          WHERE DATE_FORMAT(J.EndDate,'%Y-%m-%d')>='$ToDay' AND DATE_FORMAT(J.StartDate,'%Y-%m-%d')<='$ToDay'  AND M.WorkAdd='$workaddId'
       )
   AND M.Number NOT IN (
                 SELECT Number FROM (
                       SELECT C.Number FROM $DataIn.checkinout  C
                       LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
                      WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay' AND M.WorkAdd='$workaddId' 
                ) A GROUP BY Number
        )";
       break;
}			 
			
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
//echo $mySql;
$myResult = mysql_query($mySql."",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$GroupName=$myRow["GroupName"];
    $leaveDate = substr($myRow["StartDate"], 0, 16). "~" .substr($myRow["EndDate"], 0, 16);

		$KqSign=$myRow["KqSign"]==1?"非固定薪":"固定薪";		
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' width=$Field[1] align='center' height='20'>$i</td>";
		echo"<td class='A0101' width=$Field[3] align='center'>$SubCompany</td>";
		echo"<td class='A0101' width=$Field[5] align='center'>$Number</td>";
		echo"<td class='A0101' width=$Field[7] align='center'>$Name</td>";
		echo"<td class='A0101' width=$Field[9] align='center'>$Branch</td>";
		echo"<td class='A0101' width=$Field[11] align='center'>$GroupName</td>";
		echo"<td class='A0101' width=$Field[13] align='center'>$Job</td>";
    if($ActionId == "3" or $ActionId == "4"){
      echo"<td class='A0101' width=$Field[15] align='center'>$KqSign</td>";
      echo"<td class='A0101' width=$Field'' align='center'>$leaveDate</td>";
    }
    else{
      echo"<td class='A0101' width='' align='center'>$KqSign</td>";
    }
		echo"</tr></table>";
		$i++;		
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function ChangeAction(){
  var workaddId=document.getElementById("workaddId").value;
  var ActionId=document.getElementById("ActionId").value;
 document.form1.action="desk_worknumber_ajax.php?workaddId="+workaddId+"&ActionId="+ActionId;
 document.form1.submit();
}
</script>