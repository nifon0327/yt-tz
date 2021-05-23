<?php   
//电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 上班人数统计");
$funFrom="desk_worknumber";
$nowWebPage=$funFrom."_ajax";
$Th_Col="序号|40|员工ID|80|员工姓名|80|部门|80|小组|80|职位|80|薪酬类型|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,11";
//步骤3：
$ToDay=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";	
	$selStr="selSign".$KqSign;
	$$selStr="selected";			  
    echo"<select name='KqSign' id='KqSign' onchange='ResetPage(this.name)'>";
	echo"<option value='0' $selSign0>全部</option>";
	echo"<option value='3' $selSign3>固定薪</option>";
	echo"<option value='1' $selSign1>非固定薪</option>";
	echo"</select>&nbsp;";
	
    switch($KqSign){
	    case 0:$KqStr.="";break; 
		case 1:$KqStr.=" AND M.KqSign=1";break;
		case 3:$KqStr.=" AND M.KqSign=3";break;  
      }
  }
 switch($BranchId){
	    case 0:$SearchRows.="";break; 
		default:$SearchRows.=" AND M.BranchId=$BranchId";break;  
      }
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$TotalSql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 $SearchRows  $KqStr AND M.Estate=1";
				 
				 
				 
$WorkSql="SELECT * FROM (
              SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.KqSign='3' $SearchRows AND M.Number NOT IN
				 (SELECT Number FROM $DataPublic.kqqjsheet K 
				 WHERE K.EndDate>='$DateTime' AND K.StartDate<='$DateTime' AND K.Estate=0)
				 UNION ALL
			  SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.KqSign='1' $SearchRows AND M.Number IN 
				 (SELECT Number FROM $DataIn.checkinout C 
				 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay')
				 ) M WHERE 1 $KqStr ";

				 
$QjSql="SELECT * FROM (
              SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.KqSign='3' $SearchRows AND M.Number  IN
				 (SELECT Number FROM $DataPublic.kqqjsheet K 
				 WHERE K.EndDate>='$DateTime' AND K.StartDate<='$DateTime' AND K.Estate=0)
				 UNION ALL
			  SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.KqSign='1' $SearchRows AND M.Number  NOT IN 
				 (SELECT Number FROM $DataIn.checkinout C 
				 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$ToDay')
				 ) M WHERE 1 $KqStr ";
				 
/*$TempTotalSql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,'1' AS KqSign
	             FROM $DataIn.stafftempmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1";
$TempWorkSql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,'1' AS KqSign
	             FROM $DataIn.stafftempmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.Number IN(
				      SELECT Number FROM $DataIn.checkiotemp 
                      WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')='$ToDay' GROUP BY Number)";
$TempQjSql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,'1' AS KqSign
	             FROM $DataIn.stafftempmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1 AND M.Estate=1 AND M.Number NOT IN(
				      SELECT Number FROM $DataIn.checkiotemp 
                      WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')='$ToDay' GROUP BY Number)";*/
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
switch($Type){
       case 1:$mySql=$TotalSql;break;
	   case 2:$mySql=$WorkSql;break;
	   case 3:$mySql=$QjSql;break;
	   }
/*switch($TempType){
       case 1:$mySql=$TempTotalSql;break;
	   case 2:$mySql=$TempWorkSql;break;
	   case 3:$mySql=$TempQjSql;break;
	   }*/
/*switch($TotalType){
       case 1:$mySql=$TotalSql." UNION ALL ".$TempTotalSql;break;
	   case 2:$mySql=$WorkSql." UNION ALL ".$TempWorkSql;break;
	   case 3:$mySql=$QjSql." UNION ALL ".$TempQjSql;break;
	   }*/
switch($TotalType){
       case 1:$mySql=$TotalSql;break;
	   case 2:$mySql=$WorkSql;break;
	   case 3:$mySql=$QjSql;break;
	   }
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
		$KqSign=$myRow["KqSign"]==1?"非固定薪":"固定薪";		
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' width=$Field[1] align='center' height='20'>$i</td>";
		echo"<td class='A0101' width=$Field[3] align='center'>$Number</td>";
		echo"<td class='A0101' width=$Field[5] align='center'>$Name</td>";
		echo"<td class='A0101' width=$Field[7] align='center'>$Branch</td>";
		echo"<td class='A0101' width=$Field[9] align='center'>$GroupName</td>";
		echo"<td class='A0101' width=$Field[11] align='center'>$Job</td>";
		echo"<td class='A0101' width='' align='center'>$KqSign</td>";
		echo"</tr></table>";
		$i++;		
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
List_Title($Th_Col,"0",0);
Page_Bottom($i-1,$i-1,$Page,$Page_count,$timer,$TypeSTR,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 每天上班人数列表");
include "../model/subprogram/read_model_menu.php";
?>