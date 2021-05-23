<?php 
//$DataPublic.msg2_overtime 二合一已更新电信---yang 20120801
//代码共享-EWEN 2012-09-05
include "../model/modelhead.php";
$gl = include "../basic/global.conf.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=5;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 桌面特别提醒记录");
$funFrom="msg_remind";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|45|序号|45|标识|40|通知内容|550|状态|50|日期|80|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = isset($gl['page'])?$gl['page']:10;
$ActioToS="1,2,3,4,5,6";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.msg4_remind A WHERE 1 $SearchRows AND (A.cSign=0 OR A.cSign='$Login_cSign') ORDER BY A.Estate DESC,A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Content=nl2br($myRow["Content"]);
		$Date=$myRow["Date"];
        $Estate=$myRow["Estate"];
        $Estate=$Estate==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include "../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$Content),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
