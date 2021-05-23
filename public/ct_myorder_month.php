<?php 
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
$sumCols="5";		//求和列
$From=$From==""?"month":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=650;
ChangeWtitle("$SubCompany 员工每月点餐统计");
$funFrom="ct_myorder";
$nowWebPage=$funFrom."_month";
$Th_Col="选项|60|序号|40|员工姓名|100|员工部门|100|月点餐数量<br>(已审核)|80|月点餐金额<br>(已审核)|80|确认人|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
   $monthResult = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Date FROM $DataPublic.ct_myorder WHERE 1 group by DATE_FORMAT(Date,'%Y-%m') order by DATE_FORMAT(Date,'%Y-%m') DESC ",$link_id);
	if($monthResult && $monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			 $dateValue=$monthRow["Date"];
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and DATE_FORMAT(A.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
    }
}
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Number,M.Name,B.Name AS BranchName
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
WHERE 1 $SearchRows GROUP BY M.Number ORDER BY M.BranchId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
       $Number=$myRow["Number"];
       $Name=$myRow["Name"];
       $BranchName=$myRow["BranchName"];
       $CheckResult0=mysql_fetch_array(mysql_query("SELECT Count(*) AS Sum,SUM(Amount) AS SumAmount FROM $DataPublic.ct_myorder WHERE Operator='$Number' AND DATE_FORMAT(Date,'%Y-%m')='$chooseDate' AND Estate=0",$link_id));
      $SumQty=$CheckResult0["Sum"]==0?"&nbsp;":$CheckResult0["Sum"];
      $SumAmount=$CheckResult0["SumAmount"]==""?0:$CheckResult0["SumAmount"];

       $CheckmonthResult=mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Amount,Operator FROM $DataPublic.ct_monthamount WHERE Number='$Number' AND Month='$chooseDate'",$link_id));
       $RegisterAmount=$CheckmonthResult["Amount"]==""?0:$CheckmonthResult["Amount"];
       $Operator=$CheckmonthResult["Operator"];
		include "../model/subprogram/staffname.php";
      $UpdateIMG="";$UpdateClick="";
        if(($Number==$Login_P_Number   || $Login_P_Number==10620 || $Login_P_Number==10871)&& $SumAmount>0 && $SumAmount>$RegisterAmount ){
                   $UpdateIMG="<img src='../images/register.png' width='30' height='30'";
                   $UpdateClick="onclick='RegisterEstate(\"$Number\",\"$chooseDate\",\"$SumAmount\",this)'";
             }
     else{
                   $UpdateIMG="<span class='greenB'>$Operator</span>";
                   $UpdateClick="";
               }
		$URL="ct_myorder_month_ajax.php";
        $theParam="Number=$Number&Month=$chooseDate&ActionId=1";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$Name,1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$SumQty,1=>"align='right'"),
			array(0=>$SumAmount,1=>"align='right'"),
		//	array(0=>$wSumQty,1=>"align='right'"),
		//	array(0=>$wSumAmount,1=>"align='right'"),
			array(0=>$UpdateIMG,1=>"align='center' $UpdateClick")
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
function RegisterEstate(tempNumber,chooseDate,SumAmount,e){
var msgStr=chooseDate+" 你点餐消费金额为:"+SumAmount+"RMB\n请确认!";
  if(confirm(msgStr)) {
	    var url="ct_myorder_month_ajax.php?tempNumber="+tempNumber+"&chooseDate="+chooseDate+"&SumAmount="+SumAmount+"&ActionId=2";
	    var ajax=InitAjax();
　	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                if(ajax.responseText=="Y"){
			        //更新该单元格底色和内容
			        e.innerHTML="&nbsp;";
			        e.style.backgroundColor="#339900";
			        e.onclick="";
                  }
			 else{
			          alert ("确认失败！"); 
			         }
		    	}
		}
　	ajax.send(null);
      }
}
</script>