<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=500;
ChangeWtitle("$SubCompany 客户图档列表");
$funFrom="clientdata";
$From=$From==""?"read":$From;
$Th_Col="选项|80|序号|40|编号|80|简 称|120|图档|100|备注|350|";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,9,10";
$CompanyId= $_GET['f'];
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='1'>
	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT I.CompanyId,I.Name,I.Picture,P.Forshort
FROM $DataIn.clientimg I
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=I.CompanyId
WHERE 1 AND I.CompanyId='$CompanyId' ORDER BY I.Picture";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;	
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$Name=$myRow["Name"]==""?"&nbsp;":$myRow["Name"];	
		$Picture=$myRow["Picture"];		
					
		$ValueArray=array(
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$Picture,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'")
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