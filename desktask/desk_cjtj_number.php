<?php
//2014-01-07 ewen 修正OK
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 各部门人数统计");
$funFrom="desk_worknumber";
$nowWebPage=$funFrom."_ajax";
$Th_Col="序号|40|员工ID|80|员工姓名|80|部门|80|小组|80|职位|80|薪酬类型|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

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
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$GroupIdSTR=$GroupId==0?"AND  G.GroupId>600 AND G.GroupId<801":"AND  G.GroupId=$GroupId";
$mySql="SELECT M.Number,M.Name,B.Name AS Branch,J.Name AS Job,G.GroupName,M.KqSign
	             FROM $DataPublic.staffmain M
				 LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
				 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
				 WHERE 1   $KqStr AND M.Estate=1 $GroupIdSTR  AND M.cSign=$Login_cSign ORDER BY M.KqSign,M.BranchId,M.GroupId,M.Name";		 
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
		$KqSign=$myRow["KqSign"]==1?"非固定薪":"<sapn class='redB'>固定薪</sapn>";		
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' width=$Field[1] align='center' height='20'>$i</td>";
		echo"<td class='A0101' width=$Field[3] align='center'>$Number</td>";
		echo"<td class='A0101' width=$Field[5]>$Name</td>";
		echo"<td class='A0101' width=$Field[7]>$Branch</td>";
		echo"<td class='A0101' width=$Field[9]>$GroupName</td>";
		echo"<td class='A0101' width=$Field[11]>$Job</td>";
		echo"<td class='A0101' width=''>$KqSign</td>";
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
include "../model/subprogram/read_model_menu.php";
?>