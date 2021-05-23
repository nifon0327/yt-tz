<?php 
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|60|序号|40|客户|90|提货单号|120|提货日期|80|提货文档|80|外箱标签|60|提货数量|80|提货金额|80|件数|60|重量(KG)|100|操作人|60";
$ColsNumber=14;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$nowWebPage="ch_shipout_bill_s1";
include "../model/subprogram/s1_model_3.php";

if($chooseDate=="全部")$SelectStr="Selected";else $SelectStr="";
$date_Result = mysql_query("SELECT M.DeliveryDate AS Date 
   FROM $DataIn.ch1_deliverymain M 
   WHERE 1 GROUP BY DATE_FORMAT(M.DeliveryDate,'%Y-%m') ORDER BY M.DeliveryDate DESC",$link_id);
if($dateRow = mysql_fetch_array($date_Result)) {
	echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"ch_shipout_bill_s1\")'>";
	do{			
		$dateValue=date("Y-m",strtotime($dateRow["Date"]));
		$StartDate=$dateValue."-01";
		$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
		$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
		if($chooseDate==$dateValue){
			echo"<option value='$dateValue' selected>$dateValue</option>";
			$SearchRows.=" and ((M.DeliveryDate>'$StartDate' and M.DeliveryDate<'$EndDate') OR M.DeliveryDate='$StartDate' OR M.DeliveryDate='$EndDate')";
			}
		else{
			echo"<option value='$dateValue'>$dateValue</option>";					
			}
		}while($dateRow = mysql_fetch_array($date_Result));
	echo "<option value='全部' $SelectStr>全部</option>";
	echo"</select>&nbsp;";
	}
if($fSearchPage=="ch_freight_declaration")
    {
	 $DataSheet="ch4_freight_invoice";
     }
  else{
     $DataSheet="ch3_forward_invoice";
     }

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
include "../model/subprogram/s1_model_5.php";
$i=1;
$NewTotal=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id, M.DeliveryNumber,M.Remark,M.DeliveryDate,M.Operator ,C.Forshort
        FROM $DataIn.ch1_deliverymain M
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
        WHERE 1 $SearchRows  AND M.Id NOT IN (SELECT chId AS Id FROM $DataSheet WHERE TypeId='2')";
//echo $mySql;	
$d1=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult && $myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$Id=$myRow["Id"];
		$DeliveryNumber=$myRow["DeliveryNumber"];
		$Remark=$myRow["Remark"];
		$DeliveryDate=$myRow["DeliveryDate"];
		$Forshort =$myRow["Forshort"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $Bill="&nbsp;"; 
		$filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
        if(file_exists($filename)){
		$f1=anmaIn($DeliveryNumber,$SinkOrder,$motherSTR);
		$Bill="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a>";
		}
		$DeliveryResult=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty,SUM(DeliveryQty*Price) AS DeliveryAmount  FROM  $DataIn.ch1_deliverysheet WHERE Mid='$Id'",$link_id);
		$DeliveryQty =mysql_result($DeliveryResult,0,"DeliveryQty");
		$DeliveryAmount =mysql_result($DeliveryResult,0,"DeliveryAmount"); 
		$DeliveryAmount =sprintf("%.2f",$DeliveryAmount);
		//检查是否有装箱
		$Packing="<div class='redB'>未装箱</div>";
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch1_deliverypacklist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$Packing="<a href='ch_shipoutlist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			} 
			
			//出货重量和件数:从装箱明细中取
		$checkWG=mysql_fetch_array(mysql_query("SELECT SUM(BoxQty) AS BoxQty,SUM(P.WG) AS WG 
		FROM $DataIn.ch1_deliverypacklist P WHERE P.Mid='$Id'",$link_id));
		$WG=$checkWG["WG"]==0?0:$checkWG["WG"];
		$BoxQty=$checkWG["BoxQty"]==0?0:$checkWG["BoxQty"];
		$checkidValue=$Id."^^".$DeliveryNumber."^^".$WG."^^".$BoxQty;
		   $ValueArray=array(
			array(0=>$Forshort ,1=>"align='center'"),
			array(0=>$DeliveryNumber,1=>"align='center'"),
			array(0=>$DeliveryDate,1=>"align='center'"),
			array(0=>$Bill,	       1=>"align='center'"),
			array(0=>$Packing,	   1=>"align='center'"),
			array(0=>$DeliveryQty, 1=>"align='center'"),
			array(0=>$DeliveryAmount, 1=>"align='center'"),
			array(0=>$BoxQty,		   1=>"align='center'"),
			array(0=>$WG, 1=>"align='center'"),
			array(0=>$Operator,    1=>"align='center'")
			);
		  include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
//$RecordToTal= mysql_num_rows($myResult);
$RecordToTal= $NewTotal;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>