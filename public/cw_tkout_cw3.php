<?php 
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows=" and S.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未结付货款</option><option value='0' >已结付货款</option></select>&nbsp;";		
	//月份
	$MonthResult = mysql_query("SELECT S.Month FROM $DataIn.cw1_tkoutsheet S WHERE 1 $SearchRows GROUP BY S.Month ORDER BY S.Month",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{
			$MonthValue=$MonthRow["Month"];
			$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
			if($chooseMonth==$MonthValue){
				echo"<option value='$MonthValue' selected>$MonthValue</option>";
				$SearchRows.=" and S.Month='$MonthValue'";
				}
			else{
				echo"<option value='$MonthValue'>$MonthValue</option>";					
				}
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
		}
	else{
		//无月份记录
		$SearchRows.=" and S.Month='无效'";
		}

   //客户
	$clientSql= mysql_query("SELECT 
	M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.cw1_tkoutsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	WHERE 1  $SearchRows   GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
	
	if($clientRow = mysql_fetch_array($clientSql)){
		echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		do{
			$Letter=$clientRow["Letter"];
			$Forshort=$clientRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$clientRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($clientRow = mysql_fetch_array($clientSql));
		echo"</select>&nbsp;";
		}
	}
else{
	$SearchRows.=" AND S.Estate='3'";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,SM.Name,D.StuffCname,U.Name AS UnitName,H.Date as OutDate,D.TypeId,H.InvoiceNO,H.InvoiceFile,Count(*) AS ShipCount ,H.cwSign
 	FROM $DataIn.cw1_tkoutsheet S 
	
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.staffmain SM ON SM.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	
	WHERE 1 $SearchRows GROUP BY S.StockId ORDER BY H.InvoiceNO DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	do{
		$m=1; $LockRemark=""; $ShipSign=0;
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];//采购流水号
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称
		$Buyer=$myRow["Name"];//采购		
		$Forshort=$myRow["ForshortName"];//供应商
		$OrderQty=$myRow["OrderQty"];		//订单数量		
		$StockQty=$myRow["StockQty"];	//需求数量
		$FactualQty=$myRow["FactualQty"];	//需求数量
		$AddQty=$myRow["AddQty"];			//增购数量	
		$Qty=$FactualQty+$AddQty;	//采购总数
		$TypeId=$myRow["TypeId"];
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    
		
		$POrderId=$myRow["POrderId"];
		$ShipCount=$myRow["ShipCount"];
		if ($ShipCount>1){
				//分批出货
				$InvoiceNOSTR="";
				$chResult=mysql_query("SELECT H.InvoiceNO,H.InvoiceFile FROM $DataIn.ch1_shipsheet E 
			                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
			                               WHERE E.PorderId='$POrderId' order by H.Date",$link_id);
			  while($chRow = mysql_fetch_array($chResult)){
				    $InvoiceNO=$chRow["InvoiceNO"];
	                $InvoiceFile=$chRow["InvoiceFile"];
			        $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			        $InvoiceNOSTR.=$InvoiceFile==0?"":"<div><a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a></div>";
				} 
				  $InvoiceNO=$InvoiceNOSTR==""?"&nbsp;":$InvoiceNOSTR;
			}
       else{
	        $InvoiceNO=$myRow["InvoiceNO"];
	        $InvoiceFile=$myRow["InvoiceFile"];
			$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			$InvoiceNO=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
		}
		$Estate="<div class='redB'>未付</div>";
		//统计
		$Amount=sprintf("%.2f",$Qty*$Price);//本记录金额合计	
		$cwSign=$myRow["cwSign"];
         if($cwSign!=0)$ShipSign=1;
           $ShipSign=$InvoiceNO=="&nbsp;"?1:$ShipSign;
           if($ShipSign==1){//全部货款已结付，可以请款
                     $LockRemark="对应的INVOICE货款未结付完，不能结付退款!";
               }

		$Locks=1;
		$ValueArray=array(
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$OrderQty,1=>"align='center'"),
			array(0=>$StockQty,1=>"align='center'"),
			array(0=>$FactualQty,1=>"align='center'"),
			array(0=>$AddQty,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$OutDate,1=>"align='center'"),
			array(0=>$InvoiceNO,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Buyer,1=>"align='center'")
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
if ($myResult )  $RecordToTal= mysql_num_rows($myResult); else $RecordToTal=0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function zhtj(obj){
	switch(obj){
		case "chooseMonth":
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}
		break;
		}
	document.form1.action="";
	document.form1.submit();
	}
</script>