<?php 
include "../model/modelhead.php";
$ColsNumber=18;
$tableMenuS=500;
$sumCols="14";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 员工离职补助记录审核");
$funFrom="staffout_subsidy";
$Th_Col="选项|40|序号|40|所属公司|60|补助类型|70|请款日期|70|姓名|60|部门|60|入职日期|70|在职时间|80|离职日期|70|离职类别|80|离职原因|60|月均工资|60|补助比例|60|补助次数|60|补助金额|70|货币|40|单据|45|状态|45|说明|300";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
$ActioToS="1,17,15";	
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$cztest = ' AND S.Estate=2 ';
if ($_REQUEST["cztest"]=='cz') {
	$cztest = '';
}
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,S.TotalRate,S.Time,M.Name,B.Name AS Branch,M.ComeIn,D.outDate,S.AveAmount,S.Number,T.Name AS TypeName,S.PaySign,D.Reason AS LeaveReason,S.TypeId,S.cSign
 	FROM $DataIn.staff_outsubsidysheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
	WHERE 1 $SearchRows $cztest order by S.Date DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Number=$myRow["Number"];
		$Amount=$myRow["Amount"];
       $AveAmount=$myRow["AveAmount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
        $TypeName=$myRow["TypeName"];
		$ComeIn=$myRow["ComeIn"];
        $TypeName=$myRow["TypeName"];
		$LeaveReason=$myRow["LeaveReason"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[LeaveReason]' width='16' height='16'>";
         /*********************************************/
		 //工龄计算
		 $ComeInYM=substr($ComeIn,0,7);
		 include "subprogram/staff_model_gl.php";
       $outDate=$myRow["outDate"];
       $TotalRate =$myRow["TotalRate"];
       $Time ="第".$myRow["Time"]."次";
       $PaySign =$myRow["PaySign"];
       if($PaySign==1)$Time="<span class='redB'>一次性支付</span>";
       $Rate =$TotalRate."个月";
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/staff_subsidy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill=$Number.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];		
		switch($Estate){
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				break;
			}
       $TypeId=$myRow["TypeId"]==1?"离职补助":"辞退赔偿金";	
       $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";		
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$TypeId, 1=>"align='center'"),
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Branch,1=>"align='center'"),
			array(0=>$ComeIn,1=>"align='center'"),
			array(0=>$Gl_STR,1=>"align='center'"),
			array(0=>$outDate,1=>"align='center'"),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$LeaveReason,1=>"align='center'"),
			array(0=>$AveAmount, 1=>"align='center'"),
			array(0=>$Rate, 1=>"align='center'"),
			array(0=>$Time, 1=>"align='center'"),
			array(0=>$Amount, 1=>"align='center'"),
			array(0=>$Currency, 1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$Content,	3=>"...")
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