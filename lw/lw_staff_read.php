<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$tableMenuS=1050;
ChangeWtitle("$SubCompany 劳务员工资料列表");
$funFrom="lw_staff";
$nowWebPage=$funFrom."_read";
$ColsNumber=18;
$Th_Col="选项|40|序号|30|劳务公司|60|工作地点|60|员工ID|50|姓名|60|身份证号码|120|ID卡号|60|部门|60|小组|60|职位|60|考勤|30|电话|100|邮件|40|年龄|30|性别|40|籍贯|40|入职日期|70|在职时间|70|地址|250";
	
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,31,32,38";
include "../model/subprogram/read_model_3.php";

 if($From!="slist"){  
	 $companySql = mysql_query("SELECT M.CompanyId,P.Forshort
			FROM $DataIn.lw_staffmain M
			LEFT JOIN $DataIn.lw_company P  ON M.CompanyId=P.CompanyId 
			WHERE  1 $SearchRows GROUP BY M.CompanyId ",$link_id);
	if($companyRow = mysql_fetch_array($companySql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
		echo"<option value='' selected>全部</option>";
		do{
			$Forshort=$companyRow["Forshort"];
			$thisCompanyId=$companyRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($companyRow = mysql_fetch_array($companySql));
		echo"</select>&nbsp;";
		}
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//增加快带查询Search按钮
$searchtable="$DataPublic.lw_staffmain|M|Name|0|0"; 
$searchfile="../model/subprogram/Quicksearch_ajax.php";
include "../model/subprogram/QuickSearch.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$today=date("Y-m-d");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT M.Id,M.Number,M.Name,M.Nickname,M.ComeIn,M.WorkAdd,M.KqSign,M.IdNum,M.Estate,M.Date,M.Operator,S.Birthday,S.Sex,S.Rpr,
S.Idcard,S.Mobile,S.eMail,S.Photo,S.IdcardPhoto,S.HealthPhoto,S.Tel,B.Name AS Branch,J.Name AS Job,G.GroupName,C.Forshort AS CompanyName
FROM $DataIn.lw_staffmain M
LEFT JOIN $DataIn.lw_staffsheet S ON S.Number=M.Number
LEFT JOIN $DataIn.lw_company C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
LEFT JOIN $DataIn.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId
LEFT JOIN $DataIn.attendance_floor AT On M.AttendanceFloor = AT.Id
WHERE 1 AND M.Estate=1 $SearchRows ORDER BY M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$gl_STR="&nbsp;";
		//****************************年龄计算
		$Birthday =$myRow["Birthday"];
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
		
	    $pih="";
		$Photo=$myRow["Photo"];
		if($Photo==0)$pih.="P";
		$IdcardPhoto=$myRow["IdcardPhoto"];
		if($IdcardPhoto==0)$pih.="I";
		$HealthPhoto=$myRow["HealthPhoto"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
        $ViewId=anmaIn($Id,$SinkOrder,$motherSTR);
		if($Age<18){
			$Name="<a href='lw_staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]  $pih </div></a>";
			}
		else{
			$Name="<a href='lw_staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]  </div> <div class='redB'> $pih  </div></a>";
			}
        /***************************/
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
	    $IdNum=$myRow["IdNum"]==0?"&nbsp;":$myRow["IdNum"];
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$eMail=$myRow["eMail"]==""?"&nbsp;":"<a href='mailto:$myRow[eMail]'><img src='../images/email.gif' title='$myRow[eMail]' width='18' height='18' border='0'></a>";
		
		$ComeIn=$myRow["ComeIn"];
		$Nickname=$myRow["Nickname"]==""?"&nbsp;":$myRow["Nickname"];
		$Address=$myRow["Address"]==""?"&nbsp;":$myRow["Address"];
		$GroupName=$myRow["GroupName"]==""?"&nbsp;":$myRow["GroupName"];
		$CompanyName=$myRow["CompanyName"]==""?"&nbsp;":$myRow["CompanyName"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$Idcard=$myRow["Idcard"];
		if(strlen($Idcard)==18){
			$IdBirthday=substr($Idcard,6,4)."-".substr($Idcard,10,2)."-".substr($Idcard,12,2);
			if ($IdBirthday!=$Birthday){
				echo "$IdBirthday!=$Birthday";
			}
		}
		
		$WorkAddFrom=$myRow["WorkAdd"];
		include "../model/subselect/WorkAdd.php";
		
		$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
		//*********************************************籍贯
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if ($rResult ){
				if($rRow = mysql_fetch_array($rResult)){
					$Rpr=$rRow["Name"];
					}
		    }
        $ageMin = 16;
        $ageMax = $Sex == '男'?55:50;
        if($Age <= $ageMin || $Age >= $ageMax){
            $Age = "<span class='redB'>$Age</span>";
        }
        $Gl_STR="&nbsp;";
        include "subprogram/staff_model_gl.php";
		$ValueArray=array(
		    array(0=>$CompanyName,1=>"align='center'"),
			array(0=>$WorkAdd,1=>"align='center'"),
			array(0=>$Number,1=>"align='center' $qjcolor"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Idcard,1=>"align='center'"),
			array(0=>$IdNum,1=>"align='center'"),
			array(0=>$Branch,1=>"align='center'"),
			array(0=>$GroupName,1=>"align='center'"),
			array(0=>$Job,1=>"align='center'"),
			array(0=>$KqSign,1=>"align='center'"),
			array(0=>$Mobile,1=>"align='center'"),
			array(0=>$eMail,1=>"align='center'"),
			array(0=>$Age,1=>"align='center'"),
			array(0=>$Sex,1=>"align='center'"),
			array(0=>$Rpr,1=>"align='center'"),
			array(0=>$ComeIn,1=>"align='center'"),
			array(0=>$Gl_STR,1=>"align='center'"),
		    array(0=>$Address,1=>"align='center'"),
			
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
include "../model/subprogram/read_model_menu.php";
?>