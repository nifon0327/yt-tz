
<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.dimissiondata
$DataPublic.gradesubsidy
$DataPublic.paybase
$DataIn.sys1_baseset
$DataPublic.jobdata
$DataPublic.sbdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=18;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 薪资基础数据");
$funFrom="payset";
$nowWebPage=$funFrom."_read";
$sumCols="8,9,10,11,12,13,14,15";		//求和列
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|分类|70|职位|50|入职日期|80|员工<br>等级|40|支付<br>货币|40|底薪|50|预设<br>奖金|40|交通<br>补助|40|岗位<br>津贴|40|生活<br>补助|40|住宿<br>补助|40|小计|60|社保<br>扣款|40|个税<br>扣款|40|实计|60|加班<br>计算|40";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,4";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
      
	 //选择公司
	  $cSignTB="M";$SelectFrom=5;
	   include "../model/subselect/cSign.php"; 
	   
      //加入货币选择
		$SelectFrom=1;
		$CurrencyOther=" AND A.Id IN(1,4)";//只显示RMB/TWD
	  	include "../model/subselect/currency.php";
		if($Currency!=""){
			$SearchRows.=" AND M.Currency='".$Currency."'";
	  }
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
	if($Login_P_Number=="10620" || $Login_P_Number=="10002"){
		echo "&nbsp;固定薪的预设奖金扣减(调薪时使用)<input type='text' name='ReducedValue' id='ReducedValue'/><input type='button' name='ToAction' id='ToAction' value='确定' onclick='ToSave()'/>";
		}
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

//先检查是否有没插入员工

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$NowM=date("m")-1;
$PreM=$NowM==0?12:$NowM;
$PreY=$NowM==0?date("Y")-1:date("Y");
$PreDate=date("$PreY-$PreM-01");
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.Grade,M.KqSign,M.Estate,M.Currency,C.Symbol 
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.currencydata C ON C.ID=M.Currency 
WHERE 1 $SearchRows 
AND M.Number NOT IN (SELECT Number FROM $DataPublic.dimissiondata WHERE outDate<'$PreDate')
ORDER BY M.Estate DESC,M.BranchId,M.JobId,M.ComeIn";
//echo $mySql;AND M.cSign='$Login_cSign' 
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Name=$myRow["Estate"]==1?"<span class='greenB'>$Name</span>":"<span class='redB'>$Name</span>";
		$ComeIn=$myRow["ComeIn"];
		$Grade=$myRow["Grade"];		
		$KqSign=$myRow["KqSign"];

		//津贴计算
		if($Grade>0){
			$jtResult = mysql_fetch_array(mysql_query("SELECT Subsidy FROM $DataPublic.gradesubsidy where 1 and Grade=$Grade LIMIT 1",$link_id));
			$Gwjt=$jtResult["Subsidy"];
			}
		else{
			$Gwjt=0;
			}
		$Currency=$myRow["Currency"];
		
		//预设奖金
		$baseResult = mysql_fetch_array(mysql_query("SELECT Dx,Jj,Jtbz,Sbkk,Taxkk FROM $DataPublic.paybase WHERE 1 AND Number=$Number LIMIT 1",$link_id));
		$Jj=$baseResult["Jj"];

		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset WHERE KqSign='$KqSign' LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];
		$Dx=$B_Result["Dx"];
		$Shbz=$B_Result["Shbz"];
		$Zsbz=$B_Result["Zsbz"];
		$Jtbz=$B_Result["Jtbz"];
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		
		$Symbol=$myRow["Symbol"];	
		
		//社保读取
		if ($Currency==1){
			 $sbCheck= mysql_fetch_array(mysql_query("SELECT T.mAmount FROM $DataPublic.sbdata S LEFT JOIN $DataPublic.rs_sbtype  T ON T.Id=S.Type WHERE 1 and S.Number='$Number' ORDER BY S.Id DESC LIMIT 1",$link_id));
			$Sb=$sbCheck["mAmount"]==""?0:$sbCheck["mAmount"];
			
			$Taxkk=0;
		}
		else{
		      $Jtbz=$baseResult["Jtbz"];
			  $Sb=$baseResult["Sbkk"];
			  $Taxkk=$baseResult["Taxkk"];
			  $Dx=$baseResult["Dx"];
			  
			  $Shbz=0;$Zsbz=0;$Gwjt=0;
		}
		
		$Symbol=$myRow["Symbol"]==""?"RMB":$myRow["Symbol"];
		$Symbol=$Currency>1?"<div class='redB'>$Symbol</div>":$Symbol;
		
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		//小计=底薪+预设奖金+岗位津贴+生活补助+住宿补助-社保
		$Amount=SpaceValue0($Dx+$Jj+$Gwjt+$Shbz+$Zsbz+$Jtbz);
		$dAmount=SpaceValue0($Amount-$Sb-$Taxkk);
		$Grade=SpaceValue0($Grade);
		$Dx=SpaceValue0($Dx);
		$Jj=SpaceValue0($Jj);			
		$Gwjt=SpaceValue0($Gwjt);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);	
		$Jtbz=SpaceValue0($Jtbz);
		$Sb=SpaceValue0($Sb);
		$Taxkk=SpaceValue0($Taxkk);
		$ValueArray=array(
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$ComeIn,	1=>"align='center'"),
			array(0=>$Grade,	1=>"align='center'"),
			array(0=>$Symbol,		1=>"align='center'"),
			array(0=>$Dx,		1=>"align='center'"),
			array(0=>$Jj,		1=>"align='center'"),
			array(0=>$Jtbz,		1=>"align='center'"),
			array(0=>$Gwjt,		1=>"align='center'"),
			array(0=>$Shbz,		1=>"align='center'"),
			array(0=>$Zsbz,		1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Sb,		1=>"align='center'"),
			array(0=>$Taxkk,		1=>"align='center'"),
			array(0=>$dAmount,	1=>"align='center'"),
			array(0=>$KqSign,	1=>"align='center'")
			);
		$checkidValue=$Number;
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
<script>
function ToSave(){
	var TempAmount=document.getElementById("ReducedValue").value;
	if(TempAmount!=""){
		var TempValue=fucCheckNUM(TempAmount,"Price");
		if(TempValue==1){
			document.form1.action="payset_save.php";
			document.form1.submit();
			}
		else{
			alert("格式不对");
			}
		}
	else{
		alert("没填写");
		}
	}
</script>