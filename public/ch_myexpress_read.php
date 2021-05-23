<?php 
//代码、数据库共享-zx
//电信-zxq 2012-08-01
/*
$DataPublic.my3_express
$DataPublic.my3_express
$DataPublic.my3_exadd 
$DataPublic.freightdata
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
$funFrom="ch_myexpress";
$nowWebPage=$funFrom."_read";
if($From=="slist"){
	$Estate=0;
	}
	//划分权限:如果没有最高权限，则只显示自己的记录
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany.$Log_Item."已寄快递记录");$ColsNumber=13;
		$Th_Col="选项|40|序号|40|寄出日期|70|寄件人|60|快递公司|60|快递类型|60|寄/到付|60|付款帐号|100|快递单号|80|收件人|100|收件公司|200|件数|60|外箱尺寸(CM)|80|重量(KG)|60|物品说明|250|托寄内容|200";
		$EstateSTR0="selected";
		$ActioToS="1,42,15";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."待寄出快递记录");$ColsNumber=12;
		$Th_Col="选项|40|序号|30|寄件人|50|快递公司|50|快递类型|50|寄/到付|60|付款帐号|100|登记日期|70|收件人|100|收件公司|200|件数|40|外箱尺寸(CM)|80|重量(KG)|60|物品说明|300|托寄内容|200";
		$Estate=1;
		$EstateSTR1="selected";
		$ActioToS="42";//1,,10
		break;
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows=" AND E.Estate='$Estate'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
		<option value='1' $EstateSTR1>待寄</option>
		<option value='0' $EstateSTR0>已寄</option></select>&nbsp;";
	//月份
	if($Estate==0){
		$date_Result = mysql_query("SELECT E.SendDate FROM $DataPublic.my3_express E 
		LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Shipper
		WHERE 1 $SearchRows  
		GROUP BY DATE_FORMAT(E.SendDate,'%Y-%m') ORDER BY E.SendDate DESC",$link_id);
		if($dateRow = mysql_fetch_array($date_Result)) {
			echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
			do{			
				$dateValue=date("Y-m",strtotime($dateRow["SendDate"]));
				$StartDate=$dateValue."-01";
				$EndDate=date("Y-m-t",strtotime($dateRow["SendDate"]));
				$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
				if($chooseDate==$dateValue){
					echo"<option value='$dateValue' selected>$dateValue</option>";
					$SearchRows.=" and ((E.SendDate>'$StartDate' and E.SendDate<'$EndDate') OR E.SendDate='$StartDate' OR E.SendDate='$EndDate')";
					}
				else{
					echo"<option value='$dateValue'>$dateValue</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
			}
		}
	///
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT E.Id,E.SendDate,M.Name AS Shipper,E.BillNumber,E.CompanyId,E.Contents,E.Pieces,E.Length,E.Width,E.Height,E.cWeight,E.dWeight,E.Amount,E.Estate,E.Date,
A.Name AS Receiver,A.Company AS Company,F.Forshort,E.SendContent,E.expressType,E.PayType,E.PayerNo
FROM $DataPublic.my3_express E
LEFT JOIN $DataPublic.my3_exadd A ON A.Id=E.Receiver 
LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=E.CompanyId
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Shipper
WHERE 1 $SearchRows 
ORDER BY E.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Shipper=$myRow["Shipper"];
		$Receiver=$myRow["Receiver"];
		$Company=$myRow["Company"];
		$Pieces=$myRow["Pieces"];
		$Contents=$myRow["Contents"];
                $Length=$myRow["Length"];
                $Width=$myRow["Width"];
                $Height=$myRow["Height"];
                if ($Length>0){
                    $boxSize=$Length . "*" . $Width . "*" . $Height;
                }else{
                    $boxSize="&nbsp;"; 
                }
                $cWeight=$myRow["cWeight"]==0?"&nbsp;":$myRow["cWeight"];
		$Date=$myRow["Date"];
		$SendContent=$myRow["SendContent"]==""?"&nbsp;":$myRow["SendContent"];
		$Locks=$myRow["Locks"];
       $Forshort=$myRow["Forshort"]==""?"&nbsp;":$myRow["Forshort"];
        $expressType=$myRow["expressType"]==1?"正规":"代理";
		$PayType=$myRow["PayType"]==0?"到付":"寄付";	
		$PayerNo=$myRow["PayerNo"]==0?"&nbsp;":$myRow["PayerNo"];
		
		if($myRow["Estate"]==0){
			$SendDate=$myRow["SendDate"];
			$BillNumber="<a href='my_express_print.php?Id=$Id' target='_blank'>".$myRow["BillNumber"]."</a>";
			$ValueArray=array(
				array(0=>$SendDate,1=>"align='center'"),
				array(0=>$Shipper,1=>"align='center'"),
				array(0=>$Forshort,1=>"align='center'"),
                array(0=>$expressType,1=>"align='center'"),
				array(0=>$PayType,1=>"align='center'"),
				array(0=>$PayerNo,1=>"align='left'"),
				array(0=>$BillNumber,1=>"align='center'"),
				array(0=>$Receiver),
				array(0=>$Company),
				array(0=>$Pieces,1=>"align='center'"),
                array(0=>$boxSize,1=>"align='center'"),
                array(0=>$cWeight,1=>"align='center'"),
				array(0=>$Contents),
				array(0=>$SendContent)
				);
			}
		else{
			$ValueArray=array(
				array(0=>$Shipper,1=>"align='center'"),
                array(0=>$Forshort,1=>"align='center'"),
                array(0=>$expressType,1=>"align='center'"),
				array(0=>$PayType,1=>"align='center'"),
				array(0=>$PayerNo,1=>"align='left'"),				
				array(0=>$Date,1=>"align='center'"),
				array(0=>$Receiver),
				array(0=>$Company),
				array(0=>$Pieces,1=>"align='center'"),
                array(0=>$boxSize,1=>"align='center'"),
                array(0=>$cWeight,1=>"align='center'"),
				array(0=>$Contents),
				array(0=>$SendContent)
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
