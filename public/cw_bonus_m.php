<?php 
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=500;
$sumCols="8";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 其它奖金列表");
$funFrom="cw_bonus";
$Th_Col="选项|40|序号|40|所属公司|60|请款日期|70|部门|70|职位|70|员工姓名|80|货币|40|金额|70|说明|400|票据|45|状态|45|操作人|60|审核退回原因|300";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="15,17";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cw20_bonussheet S WHERE 1  $SearchRows AND S.Estate=2 group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	$SearchRows.=$Estate==""?"":" and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}
		echo"</select>&nbsp;";
		}
		$SearchRows.=$PEADate;
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

$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,D.Name,B.Name AS BranchName,J.Name AS JobName,S.cSign
 	FROM $DataIn.cw20_bonussheet  S 
    LEFT JOIN $DataIn.staffmain  D ON D.Number=S.Number
	LEFT JOIN $DataIn.branchdata B ON B.Id=D.BranchId
	LEFT JOIN $DataIn.jobdata J ON J.Id=D.JobId
	LEFT JOIN $DataIn.currencydata C ON C.Id=S.Currency
	WHERE 1  $SearchRows and S.Estate=2 order by S.Date DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$BranchName=$myRow["BranchName"];
		$JobName=$myRow["JobName"];
		$Name=$myRow["Name"];
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cw_bonus/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="C".$Id.".jpg";
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
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$ReturnReasons)
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