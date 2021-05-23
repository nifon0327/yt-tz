<?php 
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=10;
$tableMenuS=500;
ChangeWtitle("$SubCompany 开发项目列表");
$funFrom="development";
$From=$From==""?"read":$From;
if($Estate==1){
$Th_Col="选项|55|序号|40|客户|60|项目编号|60|项目名称|250|产品效果图|70|AI图档|70|数量|60|开发负责人|70|登记时间|70|样品交期|70|备注|60|登记人|60|审核状态|70";
$ActioToS="15,95";
}
else{
$Th_Col="选项|55|序号|40|客户|60|项目编号|60|项目名称|250|产品效果图|70|AI图档|70|数量|60|开发负责人|70|登记时间|70|样品交期|70|备注|60|登记人|60";
$ActioToS="1,2,3,4,93,7,8";
}

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
			//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
if($From!="slist"){
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$SearchRows=$Estate==""?" and D.Estate=0":" and D.Estate=$Estate";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='0' $EstateSTR0>未开发项目</option>
	<option value='2' $EstateSTR2>开发中项目</option>
	<option value='1' $EstateSTR1>已开发项目</option>
	</select>&nbsp;";
	//登记人
	$OperatorSql = mysql_query("SELECT D.Operator,P.Name FROM $DataIn.development D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator 
				   WHERE 1  $SearchRows GROUP BY D.Operator ORDER BY D.Operator",$link_id);
	/*echo "SELECT D.Operator,P.Name FROM $DataIn.development D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator 
				   WHERE 1 GROUP BY D.Operator ORDER BY D.Operator";*/
	if($OperatorRow = mysql_fetch_array($OperatorSql)){
	    
		echo"<select name='Number' id='Number' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部登记人</option>";
		do{ 
			$OperatorId=$OperatorRow["Operator"];
			$Name=$OperatorRow["Name"];
			if ($Number==$OperatorId){
				  echo "<option value='$OperatorId' selected>$Name</option>";
				  $SearchRows.=" AND D.Operator='$OperatorId'";
				}
			else{
				  echo "<option value='$OperatorId'>$Name</option>";
				}
			}while ($OperatorRow = mysql_fetch_array($OperatorSql));
		echo"</select>&nbsp;";
		}
	//开发负责人
   $DeveloperSql= mysql_query("SELECT D.Developer,P.Name FROM $DataIn.development D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer 
				   WHERE 1 $SearchRows AND D.Developer!=''  GROUP BY D.Developer ORDER BY D.Developer",$link_id);
	if($DeveloperRow = mysql_fetch_array($DeveloperSql)){
		echo"<select name='Number1' id='Number1' onchange='document.form1.submit();'>";
		echo"<option value='' selected>开发负责人</option>";
		do{ 
			$DeveloperId=$DeveloperRow["Developer"];
			$Name=$DeveloperRow["Name"];
			if ($Number1==$DeveloperId){
				  echo "<option value='$DeveloperId' selected>$Name</option>";
				  $SearchRows.=" AND D.Developer='$DeveloperId'";
				}
			else{
				  echo "<option value='$DeveloperId'>$Name</option>";
				}
			}while ($DeveloperRow = mysql_fetch_array($DeveloperSql));
		echo"</select>&nbsp;";
		}
		//客户
	$ClientSql= mysql_query("SELECT D.CompanyId,C.Forshort FROM $DataIn.development D
	                         LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
							 WHERE 1  $SearchRows GROUP BY D.CompanyId",$link_id);
	if($ClientRow = mysql_fetch_array($ClientSql)){
		echo"<select name='Number2' id='Number2' onchange='document.form1.submit();'>";
		echo"<option value='' selected>全部客户</option>";
		do{ 
			$CompanyId=$ClientRow["CompanyId"];
			$Forshort=$ClientRow["Forshort"];
			if ($Number2==$CompanyId){
				  echo "<option value='$CompanyId' selected>$Forshort</option>";
				  $SearchRows.=" AND D.CompanyId='$CompanyId'";
				}
			else{
				  echo "<option value='$CompanyId'>$Forshort</option>";
				}
			}while ($ClientRow = mysql_fetch_array($ClientSql));
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
$mySql="SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.Plan,D.StartDate,D.EndDate,D.Locks,D.Operator,C.Forshort,P.Name,D.Qty,D.Developer,D.Estate ,D.sFrom,D.Gfile
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator WHERE 1 $SearchRows ORDER BY D.ItemId desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"]==0?"&nbsp;":$myRow["Qty"];
		$ItemId=$myRow["ItemId"];
		$Plan=$myRow["Plan"]==""?"开发进度:"."":"开发进度:".$myRow["Plan"];
		$ItemName="<span title='$Plan'>$myRow[ItemName]</span>";
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"]=="0000-00-00"?"&nbsp;":$myRow["EndDate"];
		$Operator=$myRow["Developer"]==""?"&nbsp;":$myRow["Developer"];
		include "../model/subprogram/staffname.php";	
		$Name=$myRow["Name"]==""?"&nbsp;":$myRow["Name"];
		$Content=trim($myRow["Content"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Content]' width='16' height='16'>";
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/kfimg/",$SinkOrder,$motherSTR);
		if($Attached!=0){
		$f=anmaIn($Attached,$SinkOrder,$motherSTR);//加密字串
		//$Attached=$myRow["Attached"]==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
		$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
		}
		else{
		$Attached="&nbsp;";
		}
		$Gfile=$myRow["Gfile"];
		if($Gfile!=0){
		$f1=anmaIn($Gfile,$SinkOrder,$motherSTR);//加密字串
		$Gfile="<a href=\"openorload.php?d=$d&f=$f1&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' style='' alt='ai图档' width='18' height='18' style='border:0'></a>";}
		else{
		$Gfile="&nbsp;";
		}
		$Locks=$myRow["Locks"];
        if($Estate==2){$Locks=0;}
		else {$Locks=1;}
		$sFrom=$myRow["sFrom"];
		if($sFrom==1){
		      $sFrom="<div align='center' class='greenB' title='新产品开发审核通过'>√</div>";
			  //$LockRemark="记录已经审核通过，强制锁定操作！修改需退回。";
			  }
	     else{
		      $sFrom="<div align='center' class='yellowB' title='新产品未审核'>×</div>";
			  //$LockRemark="";
			 }
		//已添加配件的任务突出显示
		$staffSql="SELECT * FROM $DataIn.developsheet WHERE  ItemId='$ItemId'";
		$staffResult=mysql_query($staffSql,$link_id);
		if($staffRow=mysql_fetch_array($staffResult)){
		$ItemColor="style='color:#FF3366'";}
		else{$ItemColor="&nbsp;";}
		$showPurchaseorder="<img onClick='Showaddstuff(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏新增配件明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;
			</div><br></td></tr></table>";
	if($Estate==1){
		$ValueArray=array(
			0=>array(0=>$Forshort,1=>"align='center'"),
			1=>array(0=>$ItemId,1=>"align='center'"),
			2=>array(0=>$ItemName,1=>"align='left' $ItemColor"),
			3=>array(0=>$Attached,1=>"align='center'"),
			4=>array(0=>$Gfile,1=>"align='center'"),
			5=>array(0=>$Qty,1=>"align='center'"),
			6=>array(0=>$Operator,1=>"align='center'"),
			7=>array(0=>$StartDate,1=>"align='center'"),
			8=>array(0=>$EndDate,1=>"align='center'"),
			9=>array(0=>$Content,1=>"align='center'"),
			10=>array(0=>$Name,1=>"align='center'"),
			11=>array(0=>$sFrom,1=>"align='center'")
			);}
	else{
	    $ValueArray=array(
			0=>array(0=>$Forshort,1=>"align='center'"),
			1=>array(0=>$ItemId,1=>"align='center'"),
			2=>array(0=>$ItemName,1=>"align='left' $ItemColor"),
			3=>array(0=>$Attached,1=>"align='center'"),
			4=>array(0=>$Gfile,1=>"align='center'"),
			5=>array(0=>$Qty,1=>"align='center'"),
			6=>array(0=>$Operator,1=>"align='center'"),
			7=>array(0=>$StartDate,1=>"align='center'"),
			8=>array(0=>$EndDate,1=>"align='center'"),
			9=>array(0=>$Content,1=>"align='center'"),
			10=>array(0=>$Name,1=>"align='center'")
			);}
		  
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
