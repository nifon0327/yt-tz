<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.staffsheet
$DataPublic.branchdata
$DataPublic.jobdata
$DataPublic.rprdata
$DataPublic.sbdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=500;
ChangeWtitle("$SubCompany 未成年员入职资料");
$funFrom="staff_minors";
$nowWebPage=$funFrom."_read";
//$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|部门|60|职位|入职日期|性别|40|籍贯|40|企业招用登记表|100|劳动合同签收|100|未成年工登记表|100";
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|部门|50|职位|60|等级|50|考勤|40|移动电话|80|短号|60|邮件|40|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|介绍人|50";
$ColsNumber=18;
$GradeHidden="";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT 
	M.Id,M.Number,M.Name,M.ComeIn,S.Sex,S.Rpr,B.Name AS Branch,J.Name AS Job
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	WHERE M.cSign='$Login_cSign' AND M.Estate=1 $SearchRows ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$gl_STR="&nbsp;";
		$MonthSTR=0;
		//年龄计算
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
		$ViewId=anmaIn($Id,$SinkOrder,$motherSTR);
		if($Age<18){
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]</div></a>";
            $tempAge=1;
			}
		else{
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]</div></a>";
           $tempAge=0;
			}
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Grade=$myRow["Grade"]==0?"&nbsp;":$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"&nbsp;":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' alt='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$ComeIn=$myRow["ComeIn"];
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
			}
		$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		if($sbResult && ($sbRow = mysql_fetch_array($sbResult))){
			$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
			$Sb="<a href='staff_sbview.php?f=$ViewNumber' target='_blank'>查看</a>";
			}
		else{
			$Sb="&nbsp;";
			}
		$Introducer=$myRow["Introducer"];
		$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
		if($iResult && ($iRow = mysql_fetch_array($iResult))){
			$Introducer=$iRow["Name"];
			}
		else{
			$Introducer="&nbsp;";
			}
//////////////////////////////////////////////
		//计算在职时间
		$ThisDate=date("Y-m-d");
		$ThisYM=date("Ym");
		$InYM=date("Ym",strtotime($ComeIn));
		//比较
		$ThisDay=date("d",strtotime($ThisDate));
		$InDay=date("d",strtotime($ComeIn));
		if($InYM<=$ThisYM-1){//在职时间在一个月或1个月以上
			if($InYM==$ThisYM-1){//如果是入职的前一个月，则要对比天数来确定是否足月
				if($ThisDay>=$InDay){//满一个月
					$gl_STR="1个月";
					}
				}
			else{//一个月以上
				/////////////////////////////////////////////////
				$Years=date("Y",strtotime($ThisDate))-date("Y",strtotime($ComeIn));
				$ThisMonth=date("m",strtotime($ThisDate));	//当前月份
				$CominMonth=date("m",strtotime($ComeIn));	//入职月份
				//年计算
				if($ThisMonth<$CominMonth){//当前月份少于入职月份，年数需减一，即有不足年
					$Years=($Years-1);
					$gl_STR=$Years<=0?"&nbsp;":$Years."年";
					if($ThisDay>=$InDay){//当前日》入职日
						$MonthSTR=$ThisMonth+12-$CominMonth;
						}
					else{//有一个月不足月
						$MonthSTR=$ThisMonth+11-$CominMonth;
						}
					}
				else{
					if($ThisMonth==$CominMonth){
						if($ThisDay<$InDay){//有不足年,年数减1，月份数为11
							$Years=$Years-1;
							$MonthSTR=11;
							}
						$gl_STR=$Years<=0?"&nbsp;":$Years."年";
						}
					else{					//如果当前月份比入职月份大,则足年
						$gl_STR=$Years<=0?"&nbsp;":$Years."年";
						if($ThisDay>=$InDay){//当前日》入职日
							$MonthSTR=$ThisMonth-$CominMonth;
							}
						else{
							$MonthSTR=$ThisMonth-$CominMonth-1;
							}
						}
					}
				$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
				$gl_STR=$gl_STR.$MonthSTR;
				/////////////////////////////////////////////////
				}
			}
//////////////////////////////////////////////
		
		$Locks=$myRow["Locks"];
if($tempAge==1){
			$ValueArray=array(
				0=>array(0=>$Number,
						 1=>"align='center'"),
				1=>array(0=>$Name,
						 1=>"align='center'"),
				2=>array(0=>$Branch,
						 1=>"align='center'"),
				3=>array(0=>$Job,
						 1=>"align='center'"),
				4=>array(0=>$Grade,
						 1=>"align='center'",
						 4=>$GradeHidden),
				5=>array(0=>$KqSign,
						 1=>"align='center'"),
				6=>array(0=>$Mobile,					
						 1=>"align='center'"),
				7=>array(0=>$Dh,
						 1=>"align='center'"),
				8=>array(0=>$Mail,
						 1=>"align='center'"),
				9=>array(0=>$ComeIn,
						 1=>"align='center'"),
				10=>array(0=>$gl_STR,
						 1=>"align='center'"),
				11=>array(0=>$Sex,
						 1=>"align='center'"),
				12=>array(0=>$Rpr,
						 1=>"align='center'"),
				13=>array(0=>$Sb,					
						 1=>"align='center'"),
				14=>array(0=>$Introducer,
						 1=>"align='center'")					 
				);
		     $checkidValue=$Id;
		     include "../model/subprogram/read_model_6.php";
          }
		}while ($myRow = mysql_fetch_array($myResult));
	}
if($tempAge==0){
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>