<style type="text/css">
.sc {width:170px;list-style:none;}
.sc ul{ padding:0px; margin:0px;  } 
.sc li {list-style:none;width:100%;margin:0px 0px 0px 0px;}
.span1 {width:90px; text-align:left; display:inline-block;margin:0px 0px 0px 0px;}
.span2 {width:70px; text-align:right; display:inline-block;margin:0px 0px 0px 0px; }
</style>

<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.cwdyfsheet  二合一已更新
include "../model/modelhead.php";

//步骤2：需处理
$ColsNumber=17;
$tableMenuS=500;
$sumCols="4,10,15";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 报关出口明细列表");
$funFrom="customs";

$Th_Col="选项|40|序号|40|报关日期|75|报关INVOICES|170|INVOICES金额|80|报关单号|120|申报|40|核销单号|100|核实|40|货币类型|60|报关金额|80|出口发票|100|发票日期|75|结汇凭证|100|汇率|60|结汇金额(RMB)|90|结汇日期|75|备注|40|操作|80";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8,87";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件

if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录

	$SearchRows="";
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


	switch($DeclarationEstate){
		case "0":			$DEstateSTR0="selected";			break;
		case "1":			$DEstateSTR1="selected";			break;
		default:			$DEstateSTR4="selected";			break;
		}	
		
	echo"<select name='DeclarationEstate' id='DeclarationEstate' onchange='zhtj(this.name)'>
	<option value='' $DEstateSTR4>申报相关</option>
	<option value='1' $DEstateSTR1>未申报</option>
	<option value='0' $DEstateSTR0>已申报</option>
	</select>&nbsp;";
	if ($DeclarationEstate!=""){
      $SearchRows.=" and M.DeclarationEstate=$DeclarationEstate";
	}




	switch($CertificateEstate){
		case "0":			$EstateSTR0="selected";			break;
		case "1":			$EstateSTR1="selected";			break;
		default:			$EstateSTR4="selected";			break;
		}	
	echo"<select name='CertificateEstate' id='CertificateEstate' onchange='zhtj(this.name)'>
	<option value='' $EstateSTR4>核实相关</option>
	<option value='1' $EstateSTR1>未核实</option>
	<option value='0' $EstateSTR0>已核实</option>
	</select>&nbsp;";
	if ($CertificateEstate!=""){
      $SearchRows.=" and M.CertificateEstate=$CertificateEstate";
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
$mySql="SELECT M.Id,M.DeclarationNo,M.DeclarationDate,M.DeclarationEstate,M.CertificateNo,M.CertificateEstate,C.Symbol,M.DeclarationAmount,M.exportinvoiceNo,M.exportinvoiceDate,M.DeclarationFile,M.CertificateFile,M.exportinvoiceFile,M.Remark,M.Estate,M.Locks,M.Date,M.Operator,
D.Id as DId,D.PayDate,D.OutCurrency,D.OutAmount,D.Rate,D.InCurrency,D.InAmount,D.BillNumber,D.Bill
 	FROM $DataIn.cw13_customsmain M
	LEFT JOIN $DataIn.cw5_customsfbdh K  ON K.CertificateNo=M.CertificateNo
	LEFT JOIN $DataIn.cw5_fbdh D  ON D.BillNumber=K.BillNumber
	LEFT JOIN $DataPublic.currencydata C ON C.ID=M.DeclarationCurrency
	WHERE 1 $SearchRows order by M.DeclarationDate DESC";
//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$tbDefalut=0;
	$midDefault="";
	$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);
	do{
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
			//$DeclarationNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$DeclarationNo</span>";
			$DeclarationNo="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">$DeclarationNo</a>";
			}


		$CertificateNo=$myRow["CertificateNo"];
		$CertificateFile=$myRow["CertificateFile"];
		if($CertificateFile!=""){
			$f2=anmaIn($CertificateFile,$SinkOrder,$motherSTR);
			//$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);		
			//$CertificateNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$CertificateNo</span>";
			$CertificateNo="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">$CertificateNo</a>";
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
		
		$exportinvoiceFile=$myRow["exportinvoiceFile"];
		if($exportinvoiceFile!=""){
			$f2=anmaIn($exportinvoiceFile,$SinkOrder,$motherSTR);
			//$d2=anmaIn("download/DeclarationFile/",$SinkOrder,$motherSTR);		
			//$exportinvoiceNo="<span onClick='OpenOrLoad(\"$d2\",\"$f2\",6)' style='CURSOR: pointer;color:#FF6633'>$exportinvoiceNo</span>";
			$exportinvoiceNo="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">$exportinvoiceNo</a>";
			}

					
		$exportinvoiceDate=$myRow["exportinvoiceDate"]==""?"&nbsp":$myRow["exportinvoiceDate"];
		
		$BillNumber=$myRow["BillNumber"];  //存在相应的单号，才能显示
        if ($BillNumber!=""){
			$DId=$myRow["DId"];
			$PayDate=$myRow["PayDate"];
			$OutCurrency=$myRow["OutCurrency"];
			$checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol FROM $DataPublic.currencydata WHERE Id='$OutCurrency' LIMIT 1",$link_id));
			$InCurrency=$checkCurrency["Symbol"];				
			$OutAmount=$myRow["OutAmount"];
			//$Rate=$myRow["Rate"];
			$Rate=sprintf("%.4f",$myRow["Rate"]);
			$Rate="<div  title='$Rate*$OutAmount' >$Rate</div>";
			$InCurrency=$myRow["InCurrency"];
			$InAmount=$myRow["InAmount"];
			$checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol FROM $DataPublic.currencydata WHERE Id='$InCurrency' LIMIT 1",$link_id));
			$InCurrency=$checkCurrency["Symbol"];			
			
			
			$Bill=$myRow["Bill"];
			if($Bill==1){
				$Dir=anmaIn("download/fbdh/",$SinkOrder,$motherSTR);
				$Bill="DH".$DId.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				//$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				$BillNumber="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
				//$BillNumber="<a href=\"openorload.php?d=$Dir&f=$Bill&Type=&Action=\" target=\"download\">$BillNumber</a>";
				}				
		}		
		else{
			$PayDate="&nbsp;";
			$BillNumber="&nbsp;";
			$OutCurrency="&nbsp;";
			$OutAmount="&nbsp;";
			$Rate="&nbsp;";
			$InCurrency="&nbsp;";
			$InAmount="&nbsp;";
			$Bill="&nbsp;";			
			
		}
		
		/*
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		*/	
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];			
			/*
			switch($Estate){
				case "1":
					$Estate="<div align='center' class='redB' title='未处理'>×</div>";
					$LockRemark="";
					break;
				case "2":
					$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "3":
					$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
					break;
				case "0":
					$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
            */
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
					if($InvoiceStr==""){
						//$InvoiceStr=$InvoiceNO;
						//$InvoiceStr="<li><span>$InvoiceNO </span><span> $Amount</span> </li>";
						$InvoiceStr="<span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
						
						}
					else{
						//$InvoiceStr=$InvoiceStr."<br>".$InvoiceNO;
						//$InvoiceStr=$InvoiceStr."<li> <span>$InvoiceNO</span><span>$Amount</span> </li>";
						$InvoiceStr=$InvoiceStr." <span class='span1'>$InvoiceNO</span> <span class='span2'>$Amount</span> ";
						
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
         
		$ValueArray=array(
			array(0=>$DeclarationDate,1=>"align='center'"),
			array(0=>$InvoiceStr,1=>"align='left'"),
			array(0=>$SumInvoiceAmount,1=>"align='right'"),
			array(0=>$DeclarationNo,1=>"align='left'"),
			array(0=>$DeclarationEstate,1=>"align='left'"),
			array(0=>$CertificateNo,1=>"align='left'"),			
			array(0=>$CertificateEstate,1=>"align='left'"),
			array(0=>$Symbol,1=>"align='center'"),			
			array(0=>$DeclarationAmount,1=>"align='right'"),
			array(0=>$exportinvoiceNo,1=>"align='left'"),
			array(0=>$exportinvoiceDate,1=>"align='center'"),			
			array(0=>$BillNumber,1=>"align='left'"),
			array(0=>$Rate,1=>"align='right'"),
			array(0=>$InAmount,1=>"align='right'"),
			array(0=>$PayDate,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),			
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
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="JavaScript" type="text/JavaScript">
function zhtj(obj){
	switch(obj){
		case "chooseMonth"://改变采购
			if(document.all("CertificateEstate")!=null){
				document.forms["form1"].elements["CertificateEstate"].value="";
				}
			if(document.all("BillNumberEstate")!=null){
				document.forms["form1"].elements["BillNumberEstate"].value="";
				}				
		break;
		case "CertificateEstate"://改变采购

			if(document.all("BillNumberEstate")!=null){
				document.forms["form1"].elements["BillNumberEstate"].value="";
				}				
		break;
		


		}
	//document.form1.action="cg_cgdmainR_read.php";
	document.form1.submit();
}
</script>