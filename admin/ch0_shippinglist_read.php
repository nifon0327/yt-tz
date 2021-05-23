<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";

echo"<html><head><SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script></head>";


$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 模拟已出订单列表");
$funFrom="ch0_shippinglist";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|100|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$sumCols="7";			//求和列,需处理

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";	
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch0_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch0_shipmain M,$DataIn.trade_object C 
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
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

/*
$mySqlXX="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,S.InvoiceModel,S.LabelModel  
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
WHERE 1 $SearchRows
ORDER BY M.Date DESC";
*/
$CompanyIdArray = array(1004,1066,1084,100359,100241,100262,1064,100361,100377,1064,100155,1024,100105);
  if(in_array($CompanyId, $CompanyIdArray)){
	       $ActioToS="3,4,26,28,38,153,174,114,180";//
   }
   else{
	        $ActioToS="3,4,26,28,38,153,174,180";//
   }
/*
switch ($CompanyId) {
	case "1004":
     case "1066":
     case "1084":
     case "100359":
     case "100241":
     case "100262":
     case "100361":
     case "100377":
     case "1064":
     case "100155":
     case "1024":
     case "100105":
		$ActioToS="3,4,26,28,38,153,174,114,180";//
		break;
	default:
		$ActioToS="3,4,26,28,38,153,174,180";//
		break;
}
*/



$mySql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort,D.LabelModel,D.OutSign  
FROM $DataIn.ch0_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
WHERE 1 $SearchRows
ORDER BY M.Date DESC";
//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice0/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch0_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//$BoxLable="<div class='greenB'>已装箱</div>";
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			//$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch0_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			$LabelModel=$myRow["LabelModel"];
			if ($LabelModel==36  || $LabelModel==46 || $LabelModel==48 || $LabelModel==39 ) { //add by zx 2014-08-12,CG_Asia Bigben有主标和侧边标
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch0_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=1' target='_blank'>正</a>
				<a href='ch0_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=2' target='_blank'>侧</a>";
				
			}else {	
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch0_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
			//$LabelModel=43;
			//echo $LabelModel;
			if ($LabelModel==43 ) {  //add  by zx 2015-09-14
				$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch0_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=1' target='_blank'>原标</a>
					<a href='ch0_shippinglist_print_zd.php?Parame1=$Parame1&Parame2=$Parame2&LablePos=2' target='_blank'>自定</a>";				
			}
			
			
		 }
		//Invoice查看
		//加密参数
		
		$Sign=$myRow["Sign"];//收支标记
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$f2=anmaIn($InvoiceNO.'_p',$SinkOrder,$motherSTR);
		//$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",7)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a> &nbsp; <a href=\"openorload.php?d=$d1&f=$f2&Type=&Action=7\" target=\"download\">PL</a>";
		
		$OutSign= $myRow["OutSign"];
        $OutPdfFile='';
        if ($OutSign==9){
	        $OutPdfFile= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"ch0_shippinglist_tooldpdf.php?Id=$Id\" target=\"_blank\">旧版</a>";
        }
        
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch0_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
		/*
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		*/
		$URL="ch0_shiporder_ajax.php";
        $theParam="ShipId=$Id";
		//echo "$theParam";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$InvoiceFile . $OutPdfFile,1=>"align='center'"),
			array(0=>$BoxLable,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Wise),
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