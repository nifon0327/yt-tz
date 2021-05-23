<?php 
//电信-ZX
//已更新
include "../model/modelhead.php";
$From=$From==""?"ts":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=450;
ChangeWtitle("$SubCompany 检讨报告审核");
$funFrom="errorcase";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|50|序号|30|分类|100|检讨主题|340|相关责任人|250|内容|50|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
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
$mySql="SELECT E.Id,E.Title,E.Picture,E.Owner,E.Date,E.Operator,E.Locks,M.Name,T.Name AS TypeName 
FROM $DataIn.errorcasedata E 
LEFT JOIN $DataPublic.errorcasetype T ON T.Id=E.Type
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Operator WHERE 1 AND E.Estate=2 ORDER BY E.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeName=$myRow["TypeName"];
		$Title=$myRow["Title"];
		$Date=substr($myRow["Date"],0,10);
		$FileName=$myRow["Picture"];
		$File=anmaIn($FileName,$SinkOrder,$motherSTR);
		$Dir=download."/errorcase/";
		$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;' class='blueB'>审核中</span>";
		$Owner=$myRow["Owner"]==""?"&nbsp":$myRow["Owner"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];		
		$ValueArray=array(
			array(0=>$TypeName),
			array(0=>$Title),
			array(0=>$Owner),
			array(0=>$Picture,
					 1=>"align='center'"),
			array(0=>$Date,					
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
