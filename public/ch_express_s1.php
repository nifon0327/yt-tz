<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|40|寄出日期|70|寄件人|60|快递公司|60|快递单号|80|收件人|100|收件公司|200|件数|60|外箱尺寸(CM)|80|重量(KG)|60|物品说明|250|托寄内容|200";
$ColsNumber=14;
$tableMenuS=600;
$Page_Size = 100;
$isPage=1;
include "../model/subprogram/s1_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'>
     <option value='1' $Pagination1>分页</option>
	 <option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
include "../model/subprogram/s1_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$sSearch.=" AND E.Estate='0'";
$mySql="SELECT E.Id,E.SendDate,M.Name AS Shipper,E.BillNumber,E.CompanyId,E.Contents,E.Pieces,E.Length,E.Width,E.Height,E.cWeight,E.dWeight,E.Amount,E.Estate,E.Date,E.SendContent,
A.Name AS Receiver,A.Company AS Company,F.Forshort
FROM $DataPublic.my3_express E
LEFT JOIN $DataPublic.my3_exadd A ON A.Id=E.Receiver 
LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=E.CompanyId
LEFT JOIN $DataPublic.staffmain M ON M.Number=E.Shipper
WHERE 1 $sSearch ORDER BY E.SendDate DESC";
//echo  "$mySql";
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
		$SendContent=$myRow["SendContent"];
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
		$Locks=$myRow["Locks"];
		$BillNumber=$myRow["BillNumber"];
		   switch($Action){
		        case "1":
			    $Bdata=$BillNumber."^^".$Pieces."^^".$cWeight."^^".$SendContent;
		        break;
			}		
			$SendDate=$myRow["SendDate"];
			$Forshort=$myRow["Forshort"];
			$BillNumber="<a href='my_express_print.php?Id=$Id' target='_blank'>".$myRow["BillNumber"]."</a>";
			$ValueArray=array(
				array(0=>$SendDate,1=>"align='center'"),
				array(0=>$Shipper,1=>"align='center'"),
				array(0=>$Forshort),
				array(0=>$BillNumber,1=>"align='center'"),
				array(0=>$Receiver),
				array(0=>$Company),
				array(0=>$Pieces,1=>"align='center'"),
                array(0=>$boxSize,1=>"align='center'"),
                array(0=>$cWeight,1=>"align='center'"),
				array(0=>$Contents),
				array(0=>$SendContent)
				);
		$checkidValue=$Bdata;
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