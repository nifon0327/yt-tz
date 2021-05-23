<?php
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
$DataIn.ch2_packinglist
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"wait":$From;
//需处理参数
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 当前出货单列表");
$funFrom="ch_shippinglist";
$nowWebPage=$funFrom."_wait";
$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|产品数量|80|出货方量|100|出货日期|80|货运信息|120|运输车辆|80|操作员|50";
//$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="29,3,26,28,35,7,8";
$sumCols="7";	//求和列,需处理

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";
	$SearchRows=" and M.Estate='1'";
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY  M.Date  ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$dateValue=$dateRow["Date"];
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and  M.Date='$dateValue' ";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部</option>";
		do{
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
echo "<span class='ButtonH_25' onclick='shipment()'>出货</span>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort,C.PayType,S.InvoiceModel,M.CarNo
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
WHERE 1 $SearchRows
ORDER BY M.Date DESC";

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$CarNo=$myRow["CarNo"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		//$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">查看</a>";
		if($CompanyId==1001){
			$d2=anmaIn("invoice/mca",$SinkOrder,$motherSTR);
			//$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>★</span>";
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=6\" target=\"download\">★</a>";
			}
                $InvoiceModel=$myRow["InvoiceModel"];
		if ($InvoiceModel==5){ //出MCA
                    $d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
                    $InvoiceFile.="&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
                }
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"]);
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table  border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i'>&nbsp;</div><br></td></tr></table>";
		if($myRow["PayType"]==1){
			$BoxLable="<span class=\"redB\">未收款</span>";
			$OrderSignColor="bgColor='#F00'";
			}
      // 出货数量+方量
      $sListSql = "SELECT sum(S.Qty) AS Qty,sum(P.Weight) AS Weight
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'";
      $sListResult = mysql_query($sListSql,$link_id);
      if($ret = mysql_fetch_array($sListResult)){
          $tQty = $ret['Qty'];
          $tWeight = $ret['Weight'];
      };
		$ValueArray=array(
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$tQty,1=>"align='center'"),
			array(0=>$tWeight,1=>"align='center'"),
//			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Wise,1=>"align='center'"),
			array(0=>$CarNo,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
<script>
    function shipment() {

        var choosedRow=0;
        var Ids;
        jQuery('input[name^="checkid"]:checkbox').each(function() {
            if (jQuery(this).prop('checked') ==true) {

                choosedRow=choosedRow+1;
                if (choosedRow == 1) {
                    Ids = jQuery(this).val();
                } else {
                    Ids = Ids + "," + jQuery(this).val();
                }
            }
        });

        if (choosedRow == 0) {
            alert("该操作要求选定记录！");
            return;
        }
        document.form1.action="ch_shippinglist_updated.php?ActionId=299&ids="+Ids;
        document.form1.submit();
    }
</script>
