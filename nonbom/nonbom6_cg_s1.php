<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|下单日期|80|采购|50|供应商|100|采购单号|70|采购备注|150|货款|70|已请款|70|发票|80";
$ColsNumber=18;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
$SearchSTR=0;		//不允许搜索
$SearchRows=" ";
$CompanyId=$uType!=''?$uType:$CompanyId;

//步骤3：
include "../model/subprogram/s1_model_3.php";


$checkResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m') AS Month FROM $DataIn.nonbom6_cgmain A  
GROUP BY DATE_FORMAT(A.Date,'%Y-%m') ORDER BY A.Date DESC",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择月份</option>";
		do{			
			$Temp_Month=$checkRow["Month"];
			if($Temp_Month==$chooseDate){
				echo"<option value='$Temp_Month' selected>$Temp_Month</option>";
				$SearchRows=" AND DATE_FORMAT(E.Date,'%Y-%m')='$Temp_Month'";
				}
			else{
				echo"<option value='$Temp_Month'>$Temp_Month</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	//采购
	$checkResult = mysql_query("SELECT E.BuyerId,C.Name 
							   FROM $DataIn.nonbom6_cgmain E 
							   LEFT JOIN $DataPublic.staffmain C ON C.Number=E.BuyerId 
							   WHERE 1 $SearchRows GROUP BY E.BuyerId ORDER BY C.Name",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择采购</option>";
		do{			
			$Temp_BuyerId=$checkRow["BuyerId"];
			$Temp_Name=$checkRow["Name"];
			if($Temp_BuyerId==$BuyerId){
				echo"<option value='$Temp_BuyerId' selected>$Temp_Name</option>";
				$SearchRows.=" AND E.BuyerId='$Temp_BuyerId'";
				}
			else{
				echo"<option value='$Temp_BuyerId'>$Temp_Name</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
		
	//供应商
	$checkResult = mysql_query("SELECT Ee.Letter,Ee.Forshort,Ee.CompanyId 
							   FROM $DataIn.nonbom6_cgmain E 
							   LEFT JOIN $DataPublic.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId
							   WHERE 1 $SearchRows GROUP BY Ee.Forshort ORDER BY Ee.Letter",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择供应商</option>";
		do{			
			$Temp_CompanyId=$checkRow["CompanyId"];
			$Temp_Name=$checkRow["Letter"] . "-" . $checkRow["Forshort"];
			if($Temp_CompanyId==$CompanyId){
				echo"<option value='$Temp_CompanyId' selected>$Temp_Name</option>";
				//$SearchRows.=" AND Ee.CompanyId='$Temp_CompanyId'";
				$SearchRows.=" AND E.CompanyId='$Temp_CompanyId'";
				}
			else{
				echo"<option value='$Temp_CompanyId'>$Temp_Name</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}

$OrderByField = $fSearchPage=='nonbom6'?'PurchaseID':'Date';
//步骤4：需处理-可选条件下拉框
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT E.Id,E.Date AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,E.Attached,Ee.Forshort,Ee.CompanyId
		FROM $DataIn.nonbom6_cgmain E
		LEFT JOIN $DataIn.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId 
		LEFT JOIN $DataIn.currencydata C ON C.Id=Ee.Currency 
		WHERE 1 $SearchRows  GROUP BY E.Id ORDER BY $OrderByField DESC   ";
//echo $mySql;
$InvoicePath=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$cgDate=substr($myRow["cgDate"], 0,10);
		$Attached=$myRow["Attached"];
		$Forshort=$myRow["Forshort"];
		
		if ($Attached==1){
			$f=anmaIn("$Mid".'.pdf',$SinkOrder,$motherSTR);
			$cgDate="<a href=\"../admin/openorload.php?d=$DCPath&f=$f&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>$cgDate</a>";
		}
		
		$PurchaseID=$myRow["PurchaseID"];
	    $Operator=$myRow["BuyerId"];
		include "../model/subprogram/staffname.php";
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";		
		$mainRemark=$myRow["mainRemark"]==""?"&nbsp;":$myRow["mainRemark"];
		
		$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS HKAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$Id' ",$link_id));		
		$HKAmount=$checkHk["HKAmount"];   //货款
		$HKAmount = sprintf("%.2f", $HKAmount);
		
		$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(Id,-1) as HkID,IFNULL(SUM(Amount),0) AS HavedAmount,IFNULL(SUM(IF(Estate=3,Amount,0)),0) AS HavePassAmount ,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS CWAmount  
		FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Id' ",$link_id));		
		
		$HavedAmount=$checkHavedHk["HavedAmount"];  //已请款：连接请款记录
		$HkID=$checkHavedHk["HkID"];  //已结付ID，有些是0的，所以要判定
		$HavedAmount = sprintf("%.2f", $HavedAmount);
       		
		if($CWAmount==$HKAmount && $HkID>0) {  //表示全部结付完成
			$HKAmount="<div class='greenB'>$HKAmount</div>";
			$HavedAmount="<a href='nonbom6_qkview.php?Mid=$Id' target='_blank'>"."<div class='greenB'  >$HavedAmount</div>"."</a>";  //已请款：连接请款记录
			
		}else {
			if($CWAmount>0){
				$HKAmount="<div class='yellowB' title='已结付:$CWAmount' >$HKAmount </div>"; //表示部分结付
				
			}
		}
		
		
		$CheckRow = mysql_fetch_array(mysql_query("SELECT InvoiceNo,InvoiceFile FROM $DataIn.nonbom6_invoice 
			     WHERE cgMid ='$Id'",$link_id));
		$InvoiceFile = $CheckRow["InvoiceFile"];
		$InvoiceNo= $CheckRow["InvoiceNo"];
		if($InvoiceFile!=""){
		    $f2=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceFile="<a href=\"../admin/openorload.php?d=$InvoicePath&f=$f2&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>$InvoiceNo</a>";
		}else{
			$InvoiceFile ="&nbsp;";
		}
		
		$Locks = 1;
		$BackValue=$Id . "^^" . $myRow["PurchaseID"] . "^^" . $HKAmount;
		$ValueArray=array(
		    array(0=>$cgDate,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$PurchaseID,1=>"align='center'"),
			array(0=>$mainRemark,1=>"align='center'"),
			array(0=>$HKAmount,1=>"align='right'"),
			array(0=>$HavedAmount,1=>"align='right'"),
			array(0=>$InvoiceFile,1=>"align='center'"),
			);
		$checkidValue=$BackValue;
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