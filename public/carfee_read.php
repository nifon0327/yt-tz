<?php 
include "../model/modelhead.php";
$ColsNumber=15;
$tableMenuS=500;
$sumCols="6";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 车辆费用列表");
$funFrom="carfee";
$Th_Col="选项|40|序号|40|所属公司|60|会计科目|120|车辆|70|请款日期|70|金额|70|货币|40|说明|400|分类|100|票据|45|起始里程|70|结束里程|70|状态|45|审核退回原因|300|更新日期|70|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.carfee S WHERE 1  $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	$SearchRows.=$Estate==""?"":" and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			echo "<option value='' selected>全部</option>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$SearchRows.=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
	//全部车辆
     $carResult = mysql_query("SELECT S.CarId,D.CarNo FROM $DataIn.carfee S 
    LEFT JOIN $DataPublic.cardata  D ON D.Id=S.CarId
     WHERE 1  $SearchRows   group by S.CarId",$link_id);
	if($carRow = mysql_fetch_array($carResult)) {
		echo"<select name='CarId' id='CarId' onchange='document.form1.submit()'>";
				echo "<option value='' selected>全部</option>";
		do{
            $thisCarId=$carRow["CarId"];
            $thisCarNo=$carRow["CarNo"];
			if($CarId==$thisCarId){
				echo "<option value='$thisCarId' selected>$thisCarNo</option>";
				$SearchRows.=" AND S.CarId=$thisCarId ";
				}
			else{
				echo "<option value='$thisCarId'>$thisCarNo</option>";					
				}
			}while($carRow = mysql_fetch_array($carResult));
		echo"</select>&nbsp;";
		}

	//费用类别
     $carTypeResult = mysql_query("SELECT S.TypeId,T.Name FROM $DataIn.carfee S 
	LEFT JOIN $DataPublic.carfee_type T ON S.TypeId=T.Id
    LEFT JOIN $DataPublic.cardata  D ON D.Id=S.CarId
     WHERE 1  $SearchRows   group by S.TypeId",$link_id);
	if($carTypeRow= mysql_fetch_array($carTypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
				echo "<option value='' selected>全部</option>";
		do{
            $thisTypeId=$carTypeRow["TypeId"];
            $thisName=$carTypeRow["Name"];
			if($TypeId==$thisTypeId){
				echo "<option value='$thisTypeId' selected>$thisName</option>";
				$SearchRows.=" AND S.TypeId=$thisTypeId ";
				}
			else{
				echo "<option value='$thisTypeId'>$thisName</option>";					
				}
			}while($carTypeRow = mysql_fetch_array($carTypeResult));
		echo"</select>&nbsp;";
		}




	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.Symbol AS Currency,D.CarNo,S.sCourses,S.eCourses,F.ListName AS FirstName,S.cSign
 	FROM $DataIn.carfee S 
	LEFT JOIN $DataPublic.carfee_type T ON S.TypeId=T.Id
    LEFT JOIN $DataPublic.cardata  D ON D.Id=S.CarId
    LEFT JOIN $DataPublic.acfirsttype F ON F.FirstId=S.FirstId 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1  $SearchRows order by S.Date DESC";
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
		$Type=$myRow["Type"];
        $CarNo=$myRow["CarNo"];
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/carfee/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="C".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
       $sCourses=$myRow["sCourses"]==0?"&nbsp;":$myRow["sCourses"];			
       $eCourses=$myRow["eCourses"]==0?"&nbsp;":$myRow["eCourses"];	
       $FirstName=$myRow["FirstName"]==""?"&nbsp;":$myRow["FirstName"];	
 		
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];
		$Operator=$myRow["Operator"];
		//取操作员姓名
		include "../model/subprogram/staffname.php";		
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
				$checkPay= mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.carfeemain WHERE Id='$Mid' LIMIT 1",$link_id));
				$PayDate=$checkPay["PayDate"];
				$Estate="<div align='center' class='greenB' title='已结付,结付日期：$PayDate'>√</div>";
				$LockRemark="记录已经结付，强制锁定操作！";
				$Locks=0;
				break;
			}
     		//财务强制锁:非未处理皆锁定$CarNo
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
		    array(0=>$FirstName, 1=>"align='center'"),
			array(0=>$CarNo, 1=>"align='center'"),
			array(0=>$Date, 1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Type,	3=>"..."),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$sCourses, 1=>"align='center'"),
			array(0=>$eCourses, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$ReturnReasons),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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