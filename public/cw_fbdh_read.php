<style type="text/css">
.sc {width:170px;list-style:none;}
.sc ul{ padding:0px; margin:0px;  } 
.sc li {list-style:none;width:100%;margin:0px 0px 0px 0px;}
.span1 {width:90px; text-align:left; display:inline-block;margin:0px 0px 0px 0px;}
.span2 {width:70px; text-align:right; display:inline-block;margin:0px 0px 0px 0px; }
</style>

<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw5_fbdh D
$DataPublic.currencydata 
二合一已更新
*/
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");

$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 货币汇兑记录");
$funFrom="cw_fbdh";
$nowWebPage=$funFrom."_read";
$sumCols="5,9";			//求和列,需处理
$Th_Col="选项|40|序号|40|日期|80|转出银行|100|转出<br>货币|40|转出金额|70|汇率|80|转入银行|100|转入<br>货币|40|转入金额|70|结汇凭证|120|核销单号(USD)|170|报关金额(USD)|80|备注|380|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;

if($Login_P_Number=='10871'||$Login_P_Number=='10006' ||$Login_P_Number=='10341'){$ActioToS="1,2,3,4,7,8";}
		else{$ActioToS="1,2";}
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$date_Result = mysql_query("SELECT PayDate FROM $DataIn.cw5_fbdh WHERE 1 GROUP BY DATE_FORMAT(PayDate,'%Y-%m') ORDER BY PayDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["PayDate"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo  "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and  DATE_FORMAT(D.PayDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo  "<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//计算列
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT D.Id,D.PayDate,D.OutCurrency,D.OutAmount,D.Rate,D.InCurrency,D.InAmount,D.BillNumber,D.Bill,D.Remark,D.Locks,D.Operator,C.Symbol AS OutCurrency,I.Title AS InTitle,O.Title AS OutTitle
FROM $DataIn.cw5_fbdh D
LEFT JOIN $DataPublic.currencydata C ON C.Id=D.OutCurrency
LEFT JOIN $DataPublic.my2_bankinfo I ON I.Id=D.InBankId
LEFT JOIN $DataPublic.my2_bankinfo O ON O.Id=D.OutBankId
WHERE 1 $SearchRows ORDER BY D.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$PayDate=$myRow["PayDate"];
		$OutCurrency=$myRow["OutCurrency"];
		$OutAmount=$myRow["OutAmount"];
		$OutBankName=$myRow["OutTitle"]==""?"&nbsp;":$myRow["OutTitle"];
		$InBankName=$myRow["InTitle"]==""?"&nbsp;":$myRow["InTitle"];
		$Rate=$myRow["Rate"];
		$InCurrency=$myRow["InCurrency"];
		$checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol FROM $DataPublic.currencydata WHERE Id='$InCurrency' LIMIT 1",$link_id));
		$InCurrency=$checkCurrency["Symbol"]==""?"&nbsp;":$checkCurrency["Symbol"];
		$InAmount=$myRow["InAmount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$BillNumber=$myRow["BillNumber"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/fbdh/",$SinkOrder,$motherSTR);

			
			
		$CertificateStr="&nbsp;";
		$SumCertificateNoAmount=0;
		$StockResult = mysql_query("SELECT M.Id,M.CertificateNo,M.DeclarationAmount  FROM $DataIn.cw5_customsfbdh K  
			LEFT JOIN  $DataIn.cw13_customsmain M  ON M.CertificateNo=K.CertificateNo 
			WHERE  K.BillNumber='$BillNumber'
		",$link_id);
		
		if($StockRows = mysql_fetch_array($StockResult)){
			//$i=1;
			$CertificateStr="";
			//$InvoiceStr="<table width='200' border='0' cellspacing='0'> ";
			do{
				
				$CertificateNo=$StockRows["CertificateNo"];
			    $Amount=$StockRows["DeclarationAmount"];
				$SumCertificateNoAmount=$SumCertificateNoAmount+$Amount;
				$Amount=round($Amount,2);
			   // $InvoiceNO=str_pad($InvoiceNO,35,' ',STR_PAD_RIGHT);
				if($CertificateStr==""){

					$CertificateStr="<span class='span1'>$CertificateNo</span> <span class='span2'>$Amount</span> ";
					
					}
				else{

					$CertificateStr=$CertificateStr." <span class='span1'>$CertificateNo</span> <span class='span2'>$Amount</span> ";
					
				}
				

				//$i++;
				}while($StockRows = mysql_fetch_array($StockResult));

				$SumCertificateNoAmount=round($SumCertificateNoAmount,2);
				
			}	
		$SumCertificateNoAmount=$SumCertificateNoAmount==0?"&nbsp;":$SumCertificateNoAmount;
		
		if($Bill==1){
			$Bill="DH".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			//$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			$BillNumber="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>$BillNumber</span>";
			}				

		$ValueArray=array(
			array(0=>$PayDate,		1=>"align='center'"),
			array(0=>$OutBankName),
			array(0=>$OutCurrency,	1=>"align='center'"),
			array(0=>$OutAmount,	1=>"align='right'"),
			array(0=>$Rate,			1=>"align='center'"),
			array(0=>$InBankName),
			array(0=>$InCurrency,	1=>"align='center'"),
			array(0=>$InAmount,		1=>"align='right'"),
			array(0=>$BillNumber,		1=>"align='left'"),
			array(0=>$CertificateStr,1=>"align='left'"),
			array(0=>$SumCertificateNoAmount,1=>"align='right'"),			
			array(0=>$Remark),
			array(0=>$Operator, 	1=>"align='center'")
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