<?php   
include "../model/modelhead.php";
$tableMenuS=750;
ChangeWtitle("$SubCompany 报关订单");
$funFrom="ch_shiping";
$nowWebPage=$funFrom."_BG";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$Th_Col="选项|60|序号|40|出货流水号|70|客户|90|Invoice名称|110|Invoice|80|外箱标签|60|出货金额|80|出货日期|70|货运信息|120|报关方式|80|How to Ship|90|出货分类|60|备注|140|操作员|50";
$sumCols="7";			//求和列,需处理
$ColsNumber=14;	
 $ActioToS=""; 
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";   
//步骤5：

//步骤6：需处理数据记录处理
	$date_Result = mysql_query("SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') AS Month 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
WHERE  M.Estate='0' AND T.Type=1 ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=$dateRow["Month"];
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
                   $SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,S.InvoiceModel   
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
WHERE 1   AND M.Estate='0'  $SearchRows AND T.Type=1
ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$OrderSignColor="";
		//$theDefaultColor=$DefaultBgColor;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
            $PId=$PackingRow["Id"];
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
        else $PId="";
		//Invoice查看
		$Sign=$myRow["Sign"];//收支标记
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a>";
		if($CompanyId==1001  && $Sign!=-1){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
			}    
        $InvoiceModel=$myRow["InvoiceModel"];
		
		//if ($InvoiceModel==5){ //出MCA
		if ($InvoiceModel==5 || $CompanyId==1064){ //出MCA
                    $d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
                    $InvoiceFile.="&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
                }	
		 if($CompanyId==1079 && file_exists("../download/songbill/".$InvoiceNO.".pdf")){
              $d3=anmaIn("download/songbill/",$SinkOrder,$motherSTR);	
              $Forshort="<a href=\"openorload.php?d=$d3&f=$f1&Type=&Action=7\" target=\"download\">$Forshort</a>";
            }

		$incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		$incomeType="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Ch_shippinglist_upshipType\",\"$Id\")' src='../images/edit.gif' alt='更新报关方式' width='13' height='13'>
		<span class='yellowB'>$incomeType</span>";	

		
		$Ship=$myRow["Ship"];
		switch ($Ship){
			case '-1':$Ship=""; break;			
			case '0': $Ship="air"; break;
			case '1':$Ship="sea";break;
			case '7':$Ship="陆运";break;
			case '8':$Ship="库存";break;
			case '9':$Ship="UPS";break;
			case '10':$Ship="DHL";break;
			case '11':$Ship="SF";break;
			case '12':$Ship="Fedx";break;
		}
		$Ship=$Ship=""?"&nbsp;":$Ship;
		$Ship="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Ch_shippinglist_upship\",\"$Id\")' src='../images/edit.gif' alt='更新出货方式' width='13' height='13'>
		<span class='yellowB'>$Ship</span>";	
		
		$ShipType=$myRow["ShipType"];
		switch ($ShipType){
			case 'replen':
				$ShipType="补货"; 
				$shipColor=" bgcolor='#FEA085' ";
				break;			
			case 'credit': 
				$ShipType="扣款"; 
				$shipColor=" bgcolor='#FFFF93' ";
				break;
			case 'debit':
				$ShipType="其它收款"; 
				$shipColor=" bgcolor='#6ACFFF' ";
				break;
		    default:
			    $ShipType="出货"; 
				$shipColor="";
		}
		 $ColbgColor=$shipColor;//前面选项设置订单类型颜色
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Remark="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Ch_shippinglist_upmain\",\"$Id\")' src='../images/edit.gif' alt='更新备注' width='13' height='13'>
		<span class='yellowB'>$Remark</span>";	
		if($myRow["Wise"]==""  && $PId!="")$theDefaultColor="#FFA6D2";//未填货运信息以及未装箱的显示粉红色
       
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;
		$AmountSUM+=$Amount;
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
                
                include "subprogram/read_companyidSign.php";        
                $check_Id=$Id;
                $tempCheck_Sign = "";	  
		         
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//检查收款情况
		 $checkShipAmount=mysql_query("SELECT SUM(S.Amount) AS ShipAmount
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);
		if(mysql_num_rows($checkShipAmount) > 0)
		{
			$ShipAmount=mysql_result($checkShipAmount,0,"ShipAmount");
		}
		$ShipAmount=$ShipAmount==""?0:round($ShipAmount,2);
		if(sprintf("%.2f",$Amount)-sprintf("%.2f",$ShipAmount)>0){//出货金额与收款金额一致，则为已收款
			 if($myRow["PayType"]==1){
				$BoxLable="<span class=\"redB\">未收款</span>";
				$OrderSignColor="bgColor='#F00'";
				}
			}

		     $ValueArray=array(
			   array(0=>$Number,1=>"align='center' $NumberColor"),
			   array(0=>$Forshort),
			   array(0=>$InvoiceNO),
			   array(0=>$InvoiceFile,1=>"align='center'"),
			   array(0=>$BoxLable,1=>"align='center'"),
			   array(0=>$Amount,	1=>"align='right'"),
			   array(0=>$Date,1=>"align='center'"),
			   array(0=>$Wise),
			   array(0=>$incomeType),	
			   array(0=>$Ship),	
			   array(0=>$ShipType,1=>"align='center'"),
			   array(0=>$Remark),
			   array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
		$m=1;
		$ValueArray=array(
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>$AmountSUM,	1=>"align='right'"),
			  array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;"),
			   array(0=>"&nbsp;")
			);
		$ShowtotalRemark="合计";
		$isTotal=1;
		include "../model/subprogram/read_model_total.php";
		
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';//
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
