<?php 
include "../model/modelhead.php";
$ColsNumber=11;
$tableMenuS=500;
$sumCols="8";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 其它奖金列表");
$funFrom="cw_xzbonus";
$Th_Col="选项|40|序号|40|所属公司|60|请款月份|70|部门|70|职位|70|员工姓名|80|货币|40|金额|70|说明|400|票据|45|状态|45|操作人|60|请款日期|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="2,4";
//$ActioToS="1,2,3,4,14,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT S.Month FROM $DataIn.cwxz_bonus S WHERE 1  $SearchRows group by S.Month order by Month DESC",$link_id);
	//$SearchRows.=$Estate==""?"":" and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			echo "<option value='' selected>全部</option>";
		do{
			$dateValue=$monthRow["Month"];
			if($chooseMonth==$dateValue){
				echo "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
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

$mySql="SELECT S.Id,S.Month,S.Content,S.Amount,S.Bill,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,D.Name,B.Name AS BranchName,J.Name AS JobName,S.cSign
 	FROM $DataIn.cwxz_bonus  S 
    LEFT JOIN $DataPublic.staffmain  D ON D.Number=S.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=D.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=D.JobId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1  $SearchRows order by S.Date DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Month=$myRow["Month"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$BranchName=$myRow["BranchName"];
		$JobName=$myRow["JobName"];
		$Name=$myRow["Name"];
		
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cw_bonus/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="X".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"ch\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$Month, 1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$JobName,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$Date)
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