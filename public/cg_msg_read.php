<?php 
//电信-zxq 2012-08-01
//$DataPublic.msg3_notice 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=5;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 供应商提示记录");
$funFrom="cg_msg";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|45|提示内容|550|日期|80|供应商|80|状态|40|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	//月份
		$date_Result = mysql_query("SELECT N.Date FROM $DataIn.info4_cgmsg N GROUP BY DATE_FORMAT(N.Date,'%Y-%m') ORDER BY N.Date DESC",$link_id);
		if($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
			do{			
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					$SearchRows.=" AND DATE_FORMAT(N.Date,'%Y-%m')='$dateValue'";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
			}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT N.Id,N.Remark,N.Date,N.Estate,N.Operator,N.CompanyId,P.Forshort
         FROM $DataIn.info4_cgmsg N 
		 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=N.CompanyId
         WHERE 1 $SearchRows ORDER BY N.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		$Remark=nl2br($myRow["Remark"]);		
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$Forshort=$myRow["CompanyId"]==1?"全部":$Forshort;

		$Operator=$myRow["Operator"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		include "../model/subprogram/staffname.php";
		$Locks=1;
		if($Date<date("Y-m-d")){
			$Locks=0;
			}
		$ValueArray=array(
			array(0=>$Remark,
					 3=>"..."),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Forshort,
					 1=>"align='center'"),
		   array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
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
