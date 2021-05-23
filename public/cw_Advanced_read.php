<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="3";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 预结付取款明细");
$funFrom="cw_advanced";
$Th_Col="选项|40|序号|40|取款日期|70|取款人|60|取款金额|70|货币|40|银行|80|备注|400|状态|45|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$monthResult = mysql_query("SELECT A.Date FROM $DataIn.cw_advanced A GROUP BY DATE_FORMAT(A.Date,'%Y-%m') order by A.Date DESC",$link_id);
	$SearchRows.=$Estate==""?"":" and A.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(A.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(A.Date,'%Y-%m')='$FirstValue'";
			}
		echo"</select>&nbsp;";
		}
		$SearchRows.=$PEADate;
	//月份
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未消帐</option>
	<option value='0' $EstateSTR0>已消帐</option>
	</select>&nbsp;";
	$FBResult = mysql_query("SELECT B.Id,B.Symbol FROM $DataIn.cw_advanced A LEFT JOIN $DataPublic.currencydata B ON B.Id=A.Currency WHERE 1 $SearchRows GROUP BY A.Currency order by B.Id",$link_id);
	if($FBRow = mysql_fetch_array($FBResult)) {
		echo"<select name='FB' id='FB' onchange='document.form1.submit()'><option value=''>全部币别</option>";
		do{
			$FBId=$FBRow["Id"];
			$FBSymbol=$FBRow["Symbol"];
			if($FB==$FBId){
				echo "<option value='$FBId' selected>$FBSymbol</option>";
				$SearchRows=" AND A.Currency='$FBId'";
				}
			else{
				echo "<option value='$FBId'>$FBSymbol</option>";					
				}
			}while($FBRow = mysql_fetch_array($FBResult));
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
$mySql="SELECT A.Id,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,
	B.Title AS Bank,
	C.Symbol AS Currency,
	D.Name AS Teller
 	FROM $DataIn.cw_advanced A 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
	LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Teller
	WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Teller=$myRow["Teller"];
		$Amount=$myRow["Amount"];
		$SUMAmount+=$Amount;
		$Currency=$myRow["Currency"];		
		$Bank=$myRow["Bank"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
	
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];		
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB'>未消帐</div>";
				$LockRemark="";
				break;
			case "0":
				$Estate="<div align='center' class='greenB'>已消帐</div>";
				$LockRemark="记录已经结付，强制锁定操作！";
				$Locks=0;
				break;
			}
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$Teller, 1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Bank,),
			array(0=>$Remark,),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$ReturnReasons)
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		$m=1;
		$ValueArray=array(
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;"),
			array(0=>$SUMAmount,1=>"align='right'"),
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;"),
			array(0=>"&nbsp;")
			);
		$ShowtotalRemark="合计";
		$isTotal=1;
		include "../model/subprogram/read_model_total.php";			
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