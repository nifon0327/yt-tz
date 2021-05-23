<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch6_creditnote
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 扣款资料列表");
$funFrom="ch_creditnote";
$nowWebPage=$funFrom."_read";
$sumCols="7";			//求和列,需处理
$Th_Col="选项|40|序号|40|PO|80|扣款ID|60|Description|230|数量|50|单价|60|金额|60|状态|50|加入日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$TempEstateSTR="EstateSTR".strval($Estate); $$TempEstateSTR="selected";	
	$SearchRows.=$Estate==""?"":" AND K.Estate='$Estate'";
	$date_Result = mysql_query("SELECT K.Date FROM $DataIn.ch6_creditnote K WHERE 1 $SearchRows GROUP BY DATE_FORMAT(K.Date,'%Y-%m') ORDER BY K.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and DATE_FORMAT(K.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT K.CompanyId,C.Forshort 
		FROM $DataIn.ch6_creditnote K 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=K.CompanyId WHERE 1 $SearchRows GROUP BY K.CompanyId ORDER BY C.OrderBy DESC,C.CompanyId ",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='javascriot:document.form1.submit()'>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and K.CompanyId='$thisCompanyId' ";
				$ModelCompanyId=$thisCompanyId;
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未出</option>
	<option value='0' $EstateSTR0>已出</option>
	</select>&nbsp;";
	$otherAction="<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\",\"admin\")' $onClickCSS>生成出货单</span>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataIn.ch6_creditnote K WHERE 1 $SearchRows";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$Id=$myRow["Id"];
		$PO=$myRow["PO"]==""?"&nbsp;":$myRow["PO"];
		$Number=$myRow["Number"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$Date=$myRow["Date"]; 
		$Estate=$myRow["Estate"];
		$LockRemark=$Estate==0?"已出货":"";
		$Estate=$Estate==0?"<div class='greenB'>已出</div>":"<div class='redB'>未出</div>"; 
		$Locks=$myRow["Locks"]; 
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$PO),
			array(0=>$Number,
					 1=>"align='center'"),
			array(0=>$Description,
					 3=>"..."),
			array(0=>$Qty,
					 1=>"align='right'"),
			array(0=>$Price,
					 1=>"align='right'"),
			array(0=>$Amount,
					 1=>"align='right'"),
			array(0=>$Estate,					
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
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
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
//日期更新函数,对前缀+日期+编号的影响
function changeDate(){
	var oldValue=document.form1.TempValue.value;
	var shipDate=document.form1.ShipDate.value;
	var checkshipDate=ymdCheck(shipDate);
	//检查日期格式是否正确
	if(checkshipDate==false){
		alert("日期不对!自动恢复前次输入的日期.");
		document.form1.ShipDate.value=oldValue;return false;
		}
	else{
		//看前四位是否一致，不一致则改invoice
		var newDateSTR=myEngCheck(shipDate);
		var InvoiceNO=document.form1.InvoiceNO.value;
		var NoArray = InvoiceNO.split("-");
		var PreSTR= NoArray[0];
		var DateSTR= NoArray[1];//年月
		var NumStr= NoArray[2];
		if(newDateSTR!=DateSTR){	//如果月份有变化
			var InvoiceNO=PreSTR+"-"+newDateSTR+"-"+NumStr;
			alert("月份有变化,自动更改Invoice编号为:"+InvoiceNO);
			document.form1.InvoiceNO.value=InvoiceNO;
			}
		}	
	}
function ckeckForm(){
	//检查Invoice名称/日期
	var InvoiceNO=document.form1.InvoiceNO.value;
	var shipDate=document.form1.ShipDate.value;
	var checkshipDate=ymdCheck(shipDate);
	var Message="";
	if(InvoiceNO==""){
		Message="扣款单名称未填写!";
		}
	if(checkshipDate==false){
		Message="日期不对!";
		}
	if(Message!=""){
		alert(Message);return false;	
		}
	else{
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				} 
			}
		document.form1.action="ch_creditnote_toship.php";
		document.form1.submit();
		}
	}
</script>