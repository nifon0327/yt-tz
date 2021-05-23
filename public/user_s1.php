<?php 
//电信-EWEN
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|在线|50|登录名|100|姓名|60|部门|120|印章|40|状态|40|最后登录日期|140";
$ColsNumber=12;
$tableMenuS=500;
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid,Kid,$Kid,Month,$Month";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT U.Id,U.uName,U.Number,U.uSeal,U.lDate,U.Estate,M.Name,M.BranchId 
FROM $DataIn.usertable U
LEFT JOIN $DataPublic.staffmain M ON U.Number=M.Number
WHERE 1 and U.uType=0 and U.Estate=1 $sSearch order by M.BranchId,M.JobId,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$uName=$myRow["uName"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$uSeal=$myRow["uSeal"];
		if($uSeal==1){
			$File="u".$Number.".gif";
			$Dir="userseal";
			$File=anmaIn($File,$SinkOrder,$motherSTR);
			$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
			}
		$uSeal=$uSeal==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		$lDate=$myRow["lDate"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=1;
		$BranchId=$myRow["BranchId"];
		$bResult = mysql_query("SELECT Name AS Branch FROM $DataPublic.branchdata WHERE Id=$BranchId ORDER BY Id LIMIT 1",$link_id);
		if($bRow = mysql_fetch_array($bResult)){
			$Branch=$bRow["Branch"];
			}
		//在线检测							
		$oResult = mysql_query("SELECT uId FROM $DataIn.online WHERE 1 AND uId=$Id ORDER BY uId LIMIT 1",$link_id);
		if($oRow = mysql_fetch_array($oResult)){
			$Online="<div class='greenB'>●</div>";
			}
		else{
			$Online="&nbsp;";
			}
		$checkidValue=$Name;
		$ValueArray=array(
			array(0=>$Online,1=>"align='center'"),
			array(0=>$uName),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Branch,1=>"align='center'"),
			array(0=>$uSeal,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$lDate,1=>"align='center'")
			);
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