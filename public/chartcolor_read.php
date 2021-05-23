<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 客户图例颜色");
$funFrom="chartcolor";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|颜色|30|颜色代码|100|客户ID|80|客户名称|100|可用|60|更新日期|100|操作员|80";
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
$mySql="SELECT A.Id,A.CompanyId,A.ColorCode,A.Date,A.Estate,A.Locks,A.Operator,B.Forshort 
FROM $DataIn.chart2_color A 
LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
WHERE 1 $SearchRows AND B.CompanyId>0  ORDER BY A.Estate DESC,B.Forshort";//AND B.cSign='$Login_cSign'
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$ColorCode=$myRow["ColorCode"];
		$R1=hexdec(substr($ColorCode,0,2));
		$G1=hexdec(substr($ColorCode,2,2));
		$B1=hexdec(substr($ColorCode,-2));
		$Code="#".$Code;
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>"&nbsp;",		1=>"align='center' bgcolor='$ColorCode'"),
			array(0=>$ColorCode,		1=>"align='center'"),
			array(0=>$CompanyId,		1=>"align='center'"),
			array(0=>$Forshort),			
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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