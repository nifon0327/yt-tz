<?php 
//步骤1 $DataIn.cwdyfsheet  二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 测试费用列表");
$funFrom="prayfortest";
//$Th_Col="选项|40|序号|40|项目ID|60|费用说明|450|费用|60|备注|40|单据|40|供应商|140|状态|40|申请人|50|请款日期|75";
$Th_Col="选项|40|序号|40|客户|60|项目ID|60|项目名称|400|费用分类|80|请款日期|75|请款金额|60|货币类型|60|请款说明|450|凭证|40|请款人|50|状态|40|供应商|140|备注|40";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//非必选,过滤条件
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
    $SearchRows=" and S.TypeID='15' ";
	if(!($Keys & mLOCK)){
		 $SearchRows.=" and S.Operator=$Login_P_Number";
		}
	else {
		$OperatorSql = mysql_query("SELECT S.Operator,P.Name FROM $DataIn.cwdyfsheet S
						   LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator 
						   WHERE 1 $SearchRows   GROUP BY S.Operator ORDER BY S.Operator",$link_id);
			if($OperatorRow = mysql_fetch_array($OperatorSql)){		
		echo"<select name='Number' id='Number' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部登记人</option>";
		do{ 
			$OperatorId=$OperatorRow["Operator"];
			$Name=$OperatorRow["Name"];
			if ($Number==$OperatorId){
				  echo "<option value='$OperatorId' selected>$Name</option>";
				  $SearchRows.=" AND S.Operator='$OperatorId'";
				}
			else{
				  echo "<option value='$OperatorId'>$Name</option>";
				}
			}while ($OperatorRow = mysql_fetch_array($OperatorSql));
		echo"</select>&nbsp;";
		}		
	}
	//$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cwdyfsheet S WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($dateValue==$chooseMonth){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";	
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}			
		}
		$SearchRows.=$PEADate;	

		/*$kftypedata_Result = mysql_query("SELECT Id,Name FROM $DataPublic.kftypedata WHERE Estate=1 order by Id",$link_id);
		if($kftypedata_Row = mysql_fetch_array($kftypedata_Result)){
			 echo "<select name='TypeID' id='TypeID' onchange='zhtj(this.name)' >";
			 echo "<option value='' >全  部</option>";
			do{
				$KName=$kftypedata_Row["Name"];
				$KId=$kftypedata_Row["Id"];
				if($KId==$TypeID){
					echo"<option value='$KId' selected>$KName</option>";
					$SearchRows.=" and K.id='$KId'";
					}
				else{	
					echo"<option value='$KId'>$KName</option>";
					}
				}while ($kftypedata_Row = mysql_fetch_array($kftypedata_Result));
			echo"</select>&nbsp;";	
			}*/
			//客户
	$ClientSql= mysql_query("SELECT S.CompanyId,C.Forshort FROM $DataIn.cwdyfsheet S
	                         LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
							 WHERE 1   $SearchRows GROUP BY S.CompanyId",$link_id);
	if($ClientRow = mysql_fetch_array($ClientSql)){
		echo"<select name='Number2' id='Number2' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部客户</option>";
		do{ 
			$CompanyId=$ClientRow["CompanyId"];
			$Forshort=$ClientRow["Forshort"];
			if ($Number2==$CompanyId){
				  echo "<option value='$CompanyId' selected>$Forshort</option>";
				  $SearchRows.=" AND S.CompanyId='$CompanyId'";
				}
			else{
				  echo "<option value='$CompanyId'>$Forshort</option>";
				}
			}while ($ClientRow = mysql_fetch_array($ClientSql));
		echo"</select>&nbsp;";
		}			

	switch($Estate){
		case "0":			$EstateSTR0="selected";			break;
		case "1":			$EstateSTR1="selected";			break;
		case "2":			$EstateSTR2="selected";			break;
		case "3":			$EstateSTR3="selected";			break;
		default:			$EstateSTR4="selected";			break;
		}	
	echo"<select name='Estate' id='Estate' onchange='zhtj(this.name)'>
	<option value='' $EstateSTR4>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	if ($Estate!=""){
      $SearchRows.=" and S.Estate=$Estate";
	}

		
}
/*
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	if(!($Keys & mLOCK)){
		 $SearchRows=" and S.Operator=$Login_P_Number";
		}
	switch($Estate){
		case "0":			$EstateSTR0="selected";			break;
		case "1":			$EstateSTR1="selected";			break;
		case "2":			$EstateSTR2="selected";			break;
		case "3":			$EstateSTR3="selected";			break;
		default:			$EstateSTR4="selected";			break;
		}
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.cwdyfsheet S WHERE 1 $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	$SearchRows=$Estate==""?"":"and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$optionStr.="<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$optionStr.="<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}			
		}
		$SearchRows.=$PEADate;
	}

//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>$optionStr</select>&nbsp;";
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR4>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
*/	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,D.Forshort,S.ItemId,S.ItemName,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	WHERE 1 AND S.TypeID='15' $SearchRows order by S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$Forshort=$myRow["Forshort"];
		$ItemName=$myRow["ItemName"];
		$KName=$myRow["KName"];
		$Description=$myRow["Description"]==""?"&nbsp":$myRow["Description"];
		$Amount=$myRow["Amount"];
		$CName=$myRow["CName"];
		$ModelDetail=$myRow["ModelDetail"]==""?"&nbsp":$myRow["ModelDetail"];
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];			
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
					$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
					break;
				}
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		//财务强制锁:非未处理皆锁定
		//array(0=>$ModelDetail,3=>"..."),
		$ValueArray=array(
		    array(0=>$Forshort,1=>"align='center'"),		
			array(0=>$ItemId,1=>"align='center'"),
			array(0=>$ItemName),
			array(0=>$KName,3=>"..."),
			array(0=>$Date,1=>"align='center'"),			
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$CName,1=>"align='center'"),
			array(0=>$Description,3=>"..."),
			array(0=>$Bill,1=>"align='center'"),			
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Provider,3=>"..."),			
			array(0=>$Remark,1=>"align='center'")			
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

<script language="JavaScript" type="text/JavaScript">
function zhtj(obj){
	switch(obj){
		case "chooseMonth"://改变采购
			if(document.all("TypeID")!=null){
				document.forms["form1"].elements["TypeID"].value="";
				}
			if(document.all("Estate")!=null){
				document.forms["form1"].elements["Estate"].value="";
				}
		break;
		case "TypeID":
			if(document.all("Estate")!=null){
				document.forms["form1"].elements["Estate"].value="";
				}

		break;

		}
	//document.form1.action="cg_cgdmainR_read.php";
	document.form1.submit();
}
</script>