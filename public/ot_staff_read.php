<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 外部人员列表");
$funFrom="ot_staff";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|编号|100|姓名|100|项目|250|状态|40|更新日期|100|操作|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

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
$mySql="SELECT * FROM $DataIn.ot_staff A WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];	
		$Forshort=$myRow["Forshort"];	
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];

		$comSql = "SELECT ForShort FROM trade_object WHERE Id IN ($Forshort) ";
        $comRes = mysql_query($comSql,$link_id);
        if($comRow = mysql_fetch_array($comRes)){
            do {
                if ($companyName) {
                    $companyName .= '，'.$comRow["ForShort"];
                }else{
                    $companyName = $comRow["ForShort"];
                }
            }while ($comRow = mysql_fetch_array($comRes));
        }
		$ValueArray=array(
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name),
			array(0=>$companyName),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		unset($companyName);
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