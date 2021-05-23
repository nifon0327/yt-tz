<?php 
//$DataPublic.dimissiontype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 设备维护资料项目");
$funFrom="fixed_maintain";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|80|序号|40|设备分类名称|200|排序字母|80|设置日期|150|状态|80|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3";

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

$DirArray = explode('/', $_SERVER['PHP_SELF']);
$DirArray = array_reverse($DirArray);
$FromDir=$DirArray['1'];


$mySql="SELECT * FROM $DataPublic.oa2_fixedsubtype T WHERE 1 $SearchRows ORDER BY T.Estate DESC, T.Letter";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Letter=$myRow["Letter"]==""?"&nbsp;":$myRow["Letter"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		
		$URL="fixed_maintain_ajax.php";
        $theParam="TypeID=$Id";
		//echo "$theParam";
		//获取当前文件所在目录
		$showPurchaseorder="<img onClick='P_ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"$FromDir\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏子分类情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		
		$ValueArray=array(
			array(0=>$Name,		1=>"align='left'"),
			array(0=>$Letter,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;		
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

<script src='../model/publicfun.js' type=text/javascript></script>