<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 公司基本资料");
$funFrom="my_companyinfo";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|70|类别|40|公司全称|180|公司简称|100|电话号码|120|传真号码|120|公司地址|320|邮政编码|60|网站|40|联系人|60|移动电话|80|邮件|40|操作时间|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,7,8";

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
$mySql="SELECT * FROM $DataIn.my1_companyinfo A WHERE 1 $SearchRows ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Type=$myRow["Type"];
		switch($Type){
			case"S":$Type="简体";break;
			case"E":$Type="英文";break;
			case"C":$Type="繁体";break;
			}
		$Company=$myRow["Company"];
		$Forshort=$myRow["Forshort"];
		$Tel=$myRow["Tel"];
		$Fax=$myRow["Fax"];
		$Address=$myRow["Address"];
		$ZIP=$myRow["ZIP"];
		$WebSite=$myRow["WebSite"]==""?"&nbsp;":"<a href='$myRow[WebSite]' target='_blank'><img src='../images/ie.jpg' alt='$myRow[WebSite]' width='16' height='16' border='0'></a>";
		$LinkMan=$myRow["LinkMan"];
		$Mobile=$myRow["Mobile"];
		$Email=$myRow["Email"]==""?"&nbsp;":"<a href='mailto:$myRow[Email]'><img src='../images/email.gif' alt='$myRow[Email]' width='16' height='16' border='0'></a>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 	1=>"align='center'"),
			array(0=>$Type, 	1=>"align='center'"),
			array(0=>$Company),
			array(0=>$Forshort,	1=>"align='center'"),
			array(0=>$Tel,		1=>"align='center'"),
			array(0=>$Fax, 		1=>"align='center'"),
			array(0=>$Address),
			array(0=>$ZIP,	 	1=>"align='center'"),
			array(0=>$WebSite,	1=>"align='center'"),
			array(0=>$LinkMan,	1=>"align='center'"),
			array(0=>$Mobile,	1=>"align='center'"),
			array(0=>$Email,	1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator, 		1=>"align='center'")
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