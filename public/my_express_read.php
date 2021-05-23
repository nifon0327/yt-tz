<?php 
//代码、数据库共享-zx
/*电信---yang 20120801
$DataPublic.my3_express
$DataPublic.my3_exadd
$DataPublic.freightdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
$funFrom="my_express";
$nowWebPage=$funFrom."_read";
	//划分权限:如果没有最高权限，则只显示自己的记录
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($Log_Item."我的已寄快递记录");$ColsNumber=11;
		$Th_Col="选项|40|序号|40|寄出日期|70|快递公司|60|快递类型|60|快递单号|80|收件人|110|收件公司|180|件数|60|物品说明|200|托寄内容|200|登记日期|70|登记人|70";
		$EstateSTR0="selected";
		$ActioToS="";//1,2,3,8,9,10
		break;
	default:			//未结付处理
		ChangeWtitle($Log_Item."我的待寄出快递记录");$ColsNumber=9;
		$Th_Col="选项|40|序号|40|快递公司|60|快递类型|60|收件人|130|收件公司|180|件数|80|物品说明|200|托寄内容|200|登记日期|80|登记人|70";
		$Estate=1;
		$EstateSTR1="selected";
		$ActioToS="1,2,3,4";//,1041,
		break;
	}
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows=" AND E.Estate='$Estate'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
		<option value='1' $EstateSTR1>待寄</option>
		<option value='0' $EstateSTR0>已寄</option></select>&nbsp;";
/*		
		if ($Estate==0){
					$checkResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m') AS Month FROM $DataPublic.my3_express A  GROUP BY DATE_FORMAT(A.Date,'%Y-%m') ORDER BY A.Date DESC",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)) {
				echo"<select name='chooseDate' id='chooseDate' onchange='ResetPage(this.name)'>";
				echo"<option value='' selected>选择月份</option>";
				do{			
					$Temp_Month=$checkRow["Month"];
					$chooseDate=$chooseDate==""?$Temp_Month:$chooseDate;
					if($Temp_Month==$chooseDate){
						echo"<option value='$Temp_Month' selected>$Temp_Month</option>";
						$SearchRows.=" AND DATE_FORMAT(E.Date,'%Y-%m')='$Temp_Month'";
						}
					else{
						echo"<option value='$Temp_Month'>$Temp_Month</option>";					
						}
					}while($checkRow = mysql_fetch_array($checkResult));
				echo"</select>&nbsp;";
				}
		}
	*/
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

if ($Login_P_Number!="10051" && $Login_P_Number!="10868") {
	$SearchRows.=" AND E.Shipper='$Login_P_Number'";
}
$mySql="SELECT E.Id,E.SendDate,E.BillNumber,E.CompanyId,E.Contents,E.Pieces,E.cWeight,E.dWeight,E.Amount,E.Estate,E.Locks,E.Date,E.SendContent,
A.Name AS Receiver,A.Company AS Company,F.Forshort,E.expressType,E.Shipper 
FROM $DataPublic.my3_express E
LEFT JOIN $DataPublic.my3_exadd A ON A.Id=E.Receiver 
LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=E.CompanyId
WHERE 1 $SearchRows 
ORDER BY E.Id DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Receiver=$myRow["Receiver"];
		$Company=$myRow["Company"];
		$Pieces=$myRow["Pieces"];
		$Contents=$myRow["Contents"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
        $Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
        $expressType=$myRow["expressType"]==1?"正规":"代理";
		$SendContent=$myRow["SendContent"]==""?"&nbsp;":$myRow["SendContent"];
		
		$Operator=$myRow["Shipper"];
		include "../model/subprogram/staffname.php";
		if($myRow["Estate"]==0){
			$SendDate=$myRow["SendDate"];
			$BillNumber="<a href='my_express_print.php?Id=$Id' target='_blank'>".$myRow["BillNumber"]."</a>";
			$ValueArray=array(
				array(0=>$SendDate,1=>"align='center'"),
				array(0=>$Forshort,1=>"align='center'"),
                array(0=>$expressType,1=>"align='center'"),
				array(0=>$BillNumber,1=>"align='center'"),
				array(0=>$Receiver),
				array(0=>$Company),
				array(0=>$Pieces,1=>"align='center'"),
                array(0=>$Contents),
				array(0=>$SendContent),	
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
				);
			}
		else{
			$ValueArray=array(
                array(0=>$Forshort,1=>"align='center'"),
                array(0=>$expressType,1=>"align='center'"),
				array(0=>$Receiver),
				array(0=>$Company),
				array(0=>$Pieces,1=>"align='center'"),
				array(0=>$Contents),
				array(0=>$SendContent),
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
				);
			}
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
