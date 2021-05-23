<?php 
//电信-ZX   2012-08-01
//代码、数据库合并后共享-EWEN 2012-08-20
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 外出记录列表");
$funFrom="info_business";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|登记人|60|外出起始时间|120|外出结束时间|120|外出用车|60|司机|60|外出说明|350|起始里程|60|结束里程|60|行走里程|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
$sumCols="10";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$carResult = mysql_query("SELECT I.CarId,C.carListNo,T.Name,T.Color,D.CShortName 
	FROM $DataPublic.info1_business  I
	LEFT JOIN $DataPublic.cardata C ON C.Id=I.CarId
	LEFT JOIN $DataPublic.cartype T ON T.Id=C.TypeId 
	LEFT JOIN $DataPublic.companys_group D ON D.cSign=C.cSign
	WHERE C.Estate=1
	GROUP BY I.CarId ORDER BY C.cSign DESC,T.Id,C.carListNo
	",$link_id);
	if($carRow = mysql_fetch_array($carResult)){
		echo"<select name='CarId' id='CarId' onchange='document.getElementById(\"chooseDate\").value=\"\";document.form1.submit()'>";
		$i=1;
		do{			
			$CarValue=$carRow["CarId"];	//车辆Id
			$CarId=$CarId==""?$CarValue:$CarId;
		    $CarName=$carRow["carListNo"];	//车辆名称
			$CShortName=$carRow["CShortName"];
			$TypeName=$carRow["Name"];
			$Color=$carRow["Color"];
			if($CarId==$CarValue){
				echo"<option value='$CarValue' style= 'color: $Color;font-weight: bold' selected>$CShortName ($i-$TypeName) $CarName</option>";
				$SearchRows.=" and I.CarId='$CarValue'";
				}
			else{
				echo"<option value='$CarValue' style= 'color: $Color;font-weight: bold'>$CShortName ($i-$TypeName) $CarName</option>";					
				}
			$i++;
			}while($carRow = mysql_fetch_array($carResult));
		echo"</select>&nbsp;";
		}
	$date_Result = mysql_query("SELECT I.StartTime FROM $DataPublic.info1_business I WHERE 1 $SearchRows GROUP BY DATE_FORMAT(I.StartTime,'%Y-%m') ORDER BY I.StartTime DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["StartTime"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and DATE_FORMAT(I.StartTime,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT I.Id,I.Businesser,I.StartTime,I.EndTime,I.CarId,I.Drivers,I.Remark,I.sCourses,I.eCourses,I.Date
FROM $DataPublic.info1_business I WHERE 1 $SearchRows
ORDER BY I.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Businesser=$myRow["Businesser"];
		$StartTime=$myRow["StartTime"];
		$EndTime=$myRow["EndTime"]=="0000-00-00 00:00:00"?"<div class='redB'>未回</div>":$myRow["EndTime"];
		$CarId=$myRow["CarId"];
		$sCourses=$myRow["sCourses"];
		$eCourses=$myRow["eCourses"];
		$Courses=$eCourses-$sCourses;
		
		$sCourses=$sCourses==0?"&nbsp;":$sCourses;
		$eCourses=$eCourses==0?"&nbsp;":$eCourses;
		$Courses=$Courses==0?"&nbsp;":$Courses;
		$CarSql=mysql_fetch_array(mysql_query("SELECT CarNo FROM $DataPublic.cardata WHERE Id='$CarId' LIMIT 1",$link_id));
		$CarNo=$CarSql["CarNo"];
		$Drivers=$myRow["Drivers"];
		$CheckSql=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Drivers' LIMIT 1",$link_id));
		$Drivers=$CheckSql["Name"]==""?"自驾":$CheckSql["Name"];
		$Remark=$myRow["Remark"];
		$Entourage=$myRow["Entourage"];
		$Name=$myRow["Name"];		
		$Locks=1;
		$ValueArray=array(
			array(0=>$Businesser,1=>"align='center'"),
			array(0=>$StartTime,1=>"align='center'"),
			array(0=>$EndTime,1=>"align='center'"),
			array(0=>$CarNo,1=>"align='center'"),
			array(0=>$Drivers,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$sCourses,1=>"align='center'"),
			array(0=>$eCourses,1=>"align='center'"),
			array(0=>$Courses,1=>"align='center'"),
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
