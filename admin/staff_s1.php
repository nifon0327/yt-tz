<?php   
/*电信---yang 20120801
代码共享-EWEN 2012-08-10
*/
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|部门|60|小组|60|职位|70|员工等级|80|考勤状态|80|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|介绍人|50";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid,Kid,$Kid,Month,$Month";
$BranchIdSTR=$Bid==""?"":" and M.BranchId=$Bid";
$JobIdSTR=$Jid==""?"":" and M.JobId=$Jid";
$KqSignSTR=$Kid==""?"":" and M.KqSign=$Kid";
//非必选,过滤条件
 //echo "$Action";
switch($Action){
	case"0"://来自新增社保资料，需过滤已加入社保的记录OKM.cSign='$Login_cSign' and
		$AddTB=" LEFT JOIN $DataIn.sbdata Z ON Z.Number=M.Number";
		$NumberSTR="  AND  M.Estate=1 AND Z.Number IS NULL ".$BranchIdSTR;
		break;
	case"1"://来自于登录帐户：EWEN 2012-08-10
		$SelectFrom=5;
		$AddTB=" LEFT JOIN $DataIn.usertable Z ON Z.Number=M.Number";
		$NumberSTR=" and M.Estate=1 AND Z.Number IS NULL";
	break;
	case "2"://来自员工等级设定OKM.cSign='$Login_cSign'  and 

			$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR.$JobIdSTR;
	break;
	case "3"://来自考勤:过滤 1:考勤有效		2：考勤无效		3：无须考勤(过滤)OKM.cSign='$Login_cSign' and 
		//$NumberSTR="  AND M.Estate=1 and M.KqSign!=3 ".$BranchIdSTR.$JobIdSTR;
		$NumberSTR="  AND M.Estate=1  ".$BranchIdSTR.$JobIdSTR;
	break;
	case "4"://来自部门界定OKM.cSign='$Login_cSign'  and 
		$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR;
	break;
	case "5"://来自职位设定OKM.cSign='$Login_cSign' and 
		$NumberSTR="  AND M.Estate=1 ".$JobIdSTR;
	break;
	case "6"://社保有效 且当月没有缴费的     来自社保缴费记录 OK" ".
		$MonthSTR=$Month==""?"":" and M.Number NOT IN(SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$Month' ORDER BY Number)";
		$NumberSTR="  AND M.cSign='$Login_cSign' and M.Number IN(SELECT Number FROM $DataPublic.sbdata WHERE eMonth='' OR eMonth>'$Month' OR eMonth='$Month' ORDER BY Number) ".$MonthSTR.$BranchIdSTR;
	break;
	case "7"://来自考勤调动M.cSign='$Login_cSign' and 
		$NumberSTR=" AND M.Estate=1 ".$KqSignSTR;
	break;
	case "8"://M.cSign='7' and 
		$NumberSTR="  AND M.Estate=1 and M.KqSign=3";
	break;
	case "9":
	    $NumberSTR=" AND M.Estate=1 AND M.JobId>10 AND M.GroupId=0 ".$JobIdSTR;
		//$NumberSTR=" AND M.Estate=1 AND M.BranchId=5 AND M.JobId>10 ".$JobIdSTR." AND M.Number NOT IN(SELECT Number FROM $DataIn.sc1_member WHERE 1 ORDER BY Number)";
		break;
	case "10"://来自员工等级设定OKM.cSign='$Login_cSign'  and 
		$NumberSTR=" AND M.Estate=1 ".$BranchIdSTR.$JobIdSTR;
	break;
	case "11":
		$NumberSTR =  " AND M.Estate=1 And M.cSign in ('3',''7) ";
	
	break;		
	}   
//步骤3：
include "../model/subprogram/s1_model_3.php";
if($SelectFrom!=""){
	$cSignTB="M";
	include "../model/subselect/cSign.php";
	}
else{
	include "../model/subprogram/read_cSign.php";//过滤公司员工，只读取当前公司的员工资料
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
	M.Id,M.Number,M.Name,M.BranchId,M.JobId,M.Grade,M.Introducer,M.ComeIn,
	S.Sex,S.Rpr,B.Name AS BranchName,J.Name AS JobName,K.Name AS KqSign,G.GroupName
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.staffsheet S ON M.Number=S.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	LEFT JOIN  $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.kqtype K ON M.KqSign=K.Id
	$AddTB
	WHERE 1 $NumberSTR $sSearch $SearchRows ORDER BY M.BranchId,M.GroupId,M.Number";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];		
		$BranchName=$myRow["BranchName"];
		$JobName=$myRow["JobName"];
		$Grade=$myRow["Grade"]==0?"&nbsp;":$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"&nbsp;":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email1.gif' alt='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$ComeIn=$myRow["ComeIn"];
		$GroupName=$myRow["GroupName"];
		$JobId=$myRow["JobId"];		
		switch($Action){
			case"2"://等级设定
				$Low=1;$Hight=30;
				$gResult = mysql_query("SELECT Low,Hight FROM $DataPublic.gradedata where 1 and Id=$JobId LIMIT 1",$link_id);
				if($gRow=mysql_fetch_array($gResult)){
					$Low=$gRow["Low"];$Hight=$gRow["Hight"];
					}
				$checkidValue=$Number."^^".$Name."^^".$BranchName."^^".$JobName."^^".$Low."^^".$Hight."^^".$Grade;
			break;
			case"10"://等级设定
				$checkidValue=$Name."^^".$Number;
			break;
			default:
				$checkidValue=$Number."^^".$Name;
			break;
			}
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Rpr=$myRow["Rpr"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
			}
	/*	$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		$Sb="&nbsp;";
		if($sbRow = mysql_fetch_array($sbResult)){
			$Sb="<a href='staff_sbview.php?Number=$Number' target='_blank'>查看</a>";
			}*/
		$Introducer=$myRow["Introducer"];
       if($Introducer!=""){
		$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
		if($iRow = mysql_fetch_array($iResult)){
			$Introducer=$iRow["Name"];
			}
          }
       else $Introducer="&nbsp;";
		//计算在职时间
		$ThisDay=date("Y-m-d");
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
		if(date("d",strtotime($ComeIn))<4){
			$MonthSTR=$MonthSTR+1;
			}
		$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
		$gl_STR=$gl_STR.$MonthSTR;
		$Locks=1;
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name, 		1=>"align='center'"),
			array(0=>$BranchName,	1=>"align='center'"),
			array(0=>$GroupName,	1=>"align='center'"),
			array(0=>$JobName,		1=>"align='center'"),
			array(0=>$Grade,		1=>"align='center'"),
			array(0=>$KqSign,		1=>"align='center'"),
			array(0=>$ComeIn, 		1=>"align='center'"),
			array(0=>$gl_STR,		1=>"align='center'"),
			array(0=>$Sex,			1=>"align='center'"),
			array(0=>$Rpr, 			1=>"align='center'"),
			array(0=>$Sb,			1=>"align='center'"),
			array(0=>$Introducer,	1=>"align='center'")
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