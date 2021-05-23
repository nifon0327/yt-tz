<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$tableMenuS=750;
ChangeWtitle("$SubCompany Delivery Bill");
$funFrom="orderstock";
$nowWebPage=$funFrom."_deliverying";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$Th_Col="&nbsp;|60|NO.|40|DeliveryDate|120|DeliveryNO.|220|DeliveryFile|100|DeliveryQty|100|DeliveryAmount|100|Remark|220";
$ColsNumber=6;	
include "../model/subprogram/read_model_3.php";
//步骤3：
//$CompanySTR=" AND M.CompanyId='$myCompanyId'";

if ($myCompanyId==1081 || $myCompanyId==1002 || $myCompanyId==1080 || $myCompanyId==1065 ) {
	$CompanySTR=" and M.CompanyId in ('1081','1002','1080','1065')";
}
else {
	$CompanySTR=" and M.CompanyId='$myCompanyId' ";
}

//$CompanySTR=" AND M.CompanyId='1002'";
$date_Result = mysql_query("SELECT DATE_FORMAT(M.DeliveryDate,'%Y-%m') AS DeliveryDate
	FROM $DataIn.ch1_deliverymain M 
	WHERE 1 $CompanySTR   GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=$dateRow["DeliveryDate"];
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$seaStr.=" and DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
include "../admin/subprogram/read_model_5.php";
$subTableWidth=$tableWidth-30;
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT  M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator ,M.Estate
FROM $DataIn.ch1_deliverymain  M
WHERE 1 $CompanySTR $seaStr  AND NOT EXISTS( SELECT H.DeliveryNumber FROM $DataIn.ch1_deliveryhidden H WHERE  H.DeliveryNumber=M.DeliveryNumber) 
ORDER BY M.DeliveryDate DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
	do{
		   $m=1;
		   $Id=$myRow["Id"];
           $DeliveryNumber=$myRow["DeliveryNumber"];
		   $Forshort=$myRow["Forshort"]; 
		   $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		   $DeliveryDate=$myRow["DeliveryDate"];
		   $Operator=$myRow["Operator"];
		   include "../model/subprogram/staffname.php";
           $Bill="&nbsp;"; 
		   $filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
           if(file_exists($filename)){
		   $f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
		   $Bill="<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">view</a>";
		   }
		 $Estate=$myRow["Estate"];
         $Keys=1;
		  if($myCompanyId==1091 && $Estate==2){
		      $Keys=31;
		    }
		   $DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS DeliveryAmount  FROM ch1_deliverysheet WHERE Mid='$Id'",$link_id);
		   $DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		   $DeliveryAmount =mysql_result($DeliveryResult,0,"DeliveryAmount"); 
		   $DeliveryAmount =sprintf("%.2f",$DeliveryAmount);
		//检查是否有装箱
		   $Packing="<div class='redB'>&nbsp;</div>";
		   $checkPacking=mysql_query("SELECT Id FROM $DataIn.ch1_deliverypacklist WHERE Mid='$Id' LIMIT 1",$link_id);
		  if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$Packing="<a href='../admin/ch_shipoutlist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>view</a>";
			} 
			$SumQty+=$DeliveryQty;
			$SumAmount+=$DeliveryAmount;
             	        
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='show delivery of bill' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";


		     $ValueArray=array(
			    array(0=>$DeliveryDate,1=>"align='center'"),
			   array(0=>$DeliveryNumber),
			   array(0=>$Bill,1=>"align='center'"),
			   array(0=>$DeliveryQty,1=>"align='right'"),
			   array(0=>$DeliveryAmount,	     1=>"align='right'"),
			   array(0=>$Remark)
			  );
		  
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}

//步骤7：
echo '</div>';//
if($myCompanyId==1091){
$Keys=31;
$ActioToS="141";
include "subprogram/client_menu.php";
}
List_Title($Th_Col,"0",0);
?>
<script language="javascript">
function sOrhOrder(e,f,Order_Rows,ShipId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ShipId!=""){			
			var url="ch_shipout_order_ajax.php?ShipId="+ShipId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			    ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
</script>