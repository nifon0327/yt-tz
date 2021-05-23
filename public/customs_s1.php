<style type="text/css">
.sc {width:170px;list-style:none;}
.sc ul{ padding:0px; margin:0px;  } 
.sc li {list-style:none;width:100%;margin:0px 0px 0px 0px;}
.span1 {width:90px; text-align:left; display:inline-block;margin:0px 0px 0px 0px;}
.span2 {width:70px; text-align:right; display:inline-block;margin:0px 0px 0px 0px; }
</style>

<?php 
//电信-zxq 2012-08-01
/*/////////////////////
$DataIn.pands
$DataIn.productdata
$DataIn.trade_object
$DataIn.producttype
$DataPublic.packingunit
二合一已更新
*/
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
/*
$Th_Col="选项|60|序号|40|报关方式|80|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|付款账号|100|How to Ship|90|备注|200|操作员|50";
*/
$Th_Col="选项|40|序号|40|报关日期|75|报关INVOICES|170|金额|80|报关单号|120|核销单号|100|核实|40|货币类型|60|报关金额|80|出口发票|100|发票日期|75|备注|40|操作|80";
//$Th_Col="选项|40|序号|40|报关日期|75|金额|80|报关单号|120|核销单号|100|核实|40|货币类型|60|报关金额|80|出口发票|100|发票日期|75|备注|40|操作|80";

$ColsNumber=12;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";
switch($Action){
	case "2"://来自新增订单
		//if($From!=slist){//$CompanyIdSTR=" and P.CompanyId=$Bid";}
		//$CompanyIdSTR.=" AND P.Estate=1 AND P.ProductId IN(SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY ProductId)";

	break;
	}   
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
//$uTypeSTR=$uType==""?"":"and P.TypeId=$uType";
include "../model/subprogram/s1_model_3.php";
//步骤3：
$DateTime=date("Y-m-d");   //从现在开始，到上一年的。采购的，其它就不显示了  add mby zx 2010-12-30
$StartDate=date("Y-m-d",strtotime("$DateTime-1 years"));
$StartMonth=date("Y-m",strtotime("$DateTime-1 years"));
//echo "$sSearch:$$sSearch";
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录

	$SearchRows=" ";  //只显示已核实的
	$monthResult = mysql_query("SELECT M.DeclarationDate as Date FROM $DataIn.cw13_customsmain M WHERE 1 $SearchRows group by DATE_FORMAT(M.DeclarationDate,'%Y-%m') order by M.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($dateValue==$chooseMonth){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(M.DeclarationDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";	
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(M.Date,'%Y-%m')='$FirstValue'";
			}			
		}
		$SearchRows.=$PEADate;	


}

//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处

/////
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT M.Id,M.DeclarationNo,M.DeclarationDate,M.DeclarationEstate,M.CertificateNo,M.CertificateEstate,C.Symbol,M.DeclarationAmount,M.exportinvoiceNo,M.exportinvoiceDate,M.BillNumber,M.DeclarationFile,M.CertificateFile,M.exportinvoiceFile,M.Remark,M.Estate,M.Locks,M.Date,M.Operator
 	FROM $DataIn.cw13_customsmain M
	LEFT JOIN $DataPublic.currencydata C ON C.ID=M.DeclarationCurrency
	WHERE 1  $SearchRows $sSearch order by M.DeclarationDate DESC";
//WHERE 1 AND M.CertificateEstate=0 $SearchRows $sSearch order by M.DeclarationDate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$tbDefalut=0;
	$midDefault="";
	$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);	
	
	do{
		$LockRemark="";
		$m=1;
		$Id=$myRow["Id"];
		$DeclarationDate=$myRow["DeclarationDate"];
		$DeclarationNo=$myRow["DeclarationNo"];
		$DeclarationEstate=$myRow["DeclarationEstate"];
		
			switch($DeclarationEstate){
				case "1":
					$DeclarationEstate="<div align='center' class='redB' title='未申报'>×</div>";
					$LockRemark="";
					break;
				case "0":
					$DeclarationEstate="<div align='center' class='greenB' title='已申报'>√</div>";
					//$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}			
		$tmpDeclarationNo=$DeclarationNo;
		
		$DeclarationFile=$myRow["DeclarationFile"];
		if($DeclarationFile!=""){
			$f2=anmaIn($DeclarationFile,$SinkOrder,$motherSTR);
			//$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);		
			$DeclarationNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$DeclarationNo</span>";
			}

			
		
		
		
		$CertificateNo=$myRow["CertificateNo"];
		$tempCertificateNo=$CertificateNo;
		$CertificateFile=$myRow["CertificateFile"];
		if($CertificateFile!=""){
			$f2=anmaIn($CertificateFile,$SinkOrder,$motherSTR);
			//$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);		
			$CertificateNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$CertificateNo</span>";
			}

		$CertificateEstate=$myRow["CertificateEstate"];
			switch($CertificateEstate){
				case "1":
					$CertificateEstate="<div align='center' class='redB' title='未核实'>×</div>";
					$LockRemark="";
					break;
				case "0":
					$CertificateEstate="<div align='center' class='greenB' title='已核实'>√</div>";
					//$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}		
		
		$Symbol=$myRow["Symbol"];
		$DeclarationAmount=$myRow["DeclarationAmount"];
		$exportinvoiceNo=$myRow["exportinvoiceNo"]==""?"&nbsp":$myRow["exportinvoiceNo"];
		$tmpexportinvoiceNo=$exportinvoiceNo;
		$exportinvoiceFile=$myRow["exportinvoiceFile"];
		if($exportinvoiceFile!=""){
			$f2=anmaIn($exportinvoiceFile,$SinkOrder,$motherSTR);
			//$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);		
			$exportinvoiceNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$exportinvoiceNo</span>";
			}

					
		$exportinvoiceDate=$myRow["exportinvoiceDate"]==""?"&nbsp":$myRow["exportinvoiceDate"];
		switch($Action){
		case "1"://选择产品以便进行操作
			//$Bdata=$ProductId;
			break;
		case"2"://来自新增订单
			$Bdata=$DeclarationDate."^^".$tmpDeclarationNo."^^".$tempCertificateNo."^^".$DeclarationAmount."^^".$tmpexportinvoiceNo."^^".$exportinvoiceDate;	
			break;
		case "6"://多选框
			//$Bdata=$ProductId."^^".$cName;
			break;
		case "7"://选择产品以便进行BOM操作
			//$Bdata=$ProductId."^^".$cName;
			break;
			}	
			
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];				
					
            $InvoiceStr="&nbsp;";
			$SumInvoiceAmount=0;
			$StockResult = mysql_query("SELECT M.Id,M.InvoiceNO,M.Sign  FROM $DataIn.cw13_customssheet K  
				LEFT JOIN  $DataIn.ch1_shipmain M  ON M.Number=K.shipmainNumber 
                WHERE  K.DeclarationNo='$tmpDeclarationNo'
			",$link_id);
	        
			if($StockRows = mysql_fetch_array($StockResult)){
				//$i=1;
				$InvoiceStr="";
				//$InvoiceStr="<table width='200' border='0' cellspacing='0'> ";
				do{
					$InvoiceId=$StockRows["Id"];
					$InvoiceNO=$StockRows["InvoiceNO"];
					$Sign=$StockRows["Sign"];
					$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$InvoiceId'",$link_id));         $Amount=$checkAmount["Amount"]*$Sign;
					$SumInvoiceAmount=$SumInvoiceAmount+$Amount;
					$Amount=sprintf("%.2f",$Amount);
                   // $InvoiceNO=str_pad($InvoiceNO,35,' ',STR_PAD_RIGHT);
				   if($InvoiceNO!=""){
						if($InvoiceStr=="" ){
							//$InvoiceStr=$InvoiceNO;
							//$InvoiceStr="<li><span>$InvoiceNO </span><span> $Amount</span> </li>";
							//$InvoiceStr="<span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
							$InvoiceStr="<span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
							
							}
						else{
							//$InvoiceStr=$InvoiceStr."<br>".$InvoiceNO;
							//$InvoiceStr=$InvoiceStr."<li> <span>$InvoiceNO</span><span>$Amount</span> </li>";
							//$InvoiceStr=$InvoiceStr." <span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
							$InvoiceStr=$InvoiceStr." <span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
						}
				    }
					
					/*
					$InvoiceStr=$InvoiceStr. "<tr >
					        <td width='120'>$InvoiceNO</td>
							<td>$Amount</td>
						  </tr>";
					*/	  
					//echo "InvoiceStr:$InvoiceStr";
					
					
					//$i++;
					}while($StockRows = mysql_fetch_array($StockResult));
					//$InvoiceStr=$InvoiceStr."<br>";
					//$InvoiceStr=$InvoiceStr. "</ul>";
					$SumInvoiceAmount=sprintf("%.2f",$SumInvoiceAmount);
				}
	
		//array(0=>$InvoiceStr,1=>"align='left'"),
		$ValueArray=array(
			array(0=>$DeclarationDate,1=>"align='center'"),
			array(0=>$InvoiceStr,1=>"align='left'"),
			array(0=>$SumInvoiceAmount,1=>"align='right'"),
			array(0=>$DeclarationNo,1=>"align='left'"),
			array(0=>$CertificateNo,1=>"align='left'"),			
			array(0=>$CertificateEstate,1=>"align='left'"),
			array(0=>$Symbol,1=>"align='center'"),			
			array(0=>$DeclarationAmount,1=>"align='right'"),
			array(0=>$exportinvoiceNo,1=>"align='left'"),
			array(0=>$exportinvoiceDate,1=>"align='center'"),			
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),			
			);
		$checkidValue=$Bdata;
		//$checkidValue=$Id;
		//include "../model/subprogram/read_model_6.php";
		include "../model/subprogram/s1_model_6.php";
		//echo $StuffListTB;
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
<script language="JavaScript" type="text/JavaScript">
function zhtj(obj){
	document.form1.submit();
}
</script>