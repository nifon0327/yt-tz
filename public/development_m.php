<?php 
/*电信---yang 20120801
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
$DataPublic.staffmain
$DataIn.stuffdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=16;
$tableMenuS=600;
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 开发项目审核列表");
$funFrom="development";
$Th_Col="选项|60|序号|40|客户|60|项目编号|60|项目名称|250|产品效果图|70|AI图档|70|数量|60|开发负责人|70|登记时间|70|样品交期|70|备注|60|登记人|60|审核状态|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,15,17";		
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//过滤条件
if($From!="slist"){
	$SearchRows="";
	/*$MonthResult = mysql_query("SELECT D.StartDate  FROM $DataIn.development D WHERE 1 $SearchRows group by DATE_FORMAT(D.StartDate ,'%Y-%m') order by D.StartDate DESC",$link_id);
	if ($MonthRow = mysql_fetch_array($MonthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit();'>";
		do{
			$MonthValue=date("Y-m",strtotime($MonthRow["StartDate"]));
			$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
			if($chooseMonth==$MonthValue){
				echo"<option value='$MonthValue' selected>$MonthValue</option>";
				$SearchRows.=" and DATE_FORMAT(D.StartDate ,'%Y-%m')='$MonthValue'";
				}
			else{
				echo"<option value='$MonthValue'>$MonthValue</option>";					
				}
			}while($MonthRow = mysql_fetch_array($MonthResult));
		echo"</select>&nbsp;";
		}*/
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";

//步骤5：需处理数据记录处理
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.Plan,D.StartDate,D.EndDate,D.Locks,D.Operator,C.Forshort,P.Name,D.Qty,D.Developer,D.Estate ,D.sFrom,D.Gfile
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator WHERE 1 AND D.Estate='1' and D.sFrom='0' $SearchRows ORDER BY D.ItemId desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"]==0?"&nbsp;":$myRow["Qty"];
		$ItemId=$myRow["ItemId"];
		$Plan=$myRow["Plan"]==""?"":"开发进度:".$myRow["Plan"];
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
		//如果权限非最高，则锁定非自己的项目
        if($Estate==1){$Locks=0;}
		else {$Locks=1;}
		$sFrom=$myRow["sFrom"];
		if($sFrom==1){
		      $sFrom="<div align='center' class='greenB' title='新产品开发审核通过'>√</div>";
			  $LockRemark="记录已经审核通过，强制锁定操作！修改需退回。";
			  $Locks=0;
			  }
	     else{
		      $sFrom="<div align='center' class='yellowB' title='新产品未审核'>×</div>";
			  $LockRemark="";
			 }
			 $showPurchaseorder="<img onClick='Showaddstuff(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏新增配件明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;
			</div><br></td></tr></table>";
		$ValueArray=array(
			0=>array(0=>$Forshort,1=>"align='center'"),
			1=>array(0=>$ItemId,1=>"align='center'"),
			2=>array(0=>$ItemName),
			3=>array(0=>$Attached,1=>"align='center'"),
			4=>array(0=>$Gfile,1=>"align='center'"),
			5=>array(0=>$Qty,1=>"align='center'"),
			6=>array(0=>$Operator,1=>"align='center'"),
			7=>array(0=>$StartDate,1=>"align='center'"),
			8=>array(0=>$EndDate,1=>"align='center'"),
			9=>array(0=>$Content,1=>"align='center'"),
			10=>array(0=>$Name,1=>"align='center'"),
			11=>array(0=>$sFrom,1=>"align='center'")
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
