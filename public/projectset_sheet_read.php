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
$ColsNumber=15;
$tableMenuS=500;
ChangeWtitle("$SubCompany 研发项目列表");
$funFrom="projectset_sheet";
$From=$From==""?"read":$From;
$Th_Col="选项|55|序号|35|项目编号|60|项目名称|200|项目类型|70|项目描述|300|档案|40|负责人|50|参与人|100|开始日期|60|预估完成日期|80|进度目标|60|备注|50|登记人|50|登记时间|60|状态|60";
$ActioToS="1,2,3,4,5,6,7";

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
	$SearchRows=strlen($Estate)>0?" and P.Estate=$Estate":'';
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>所有项目</option>
	<option value='1' $EstateSTR1>开发中项目</option>
	<option value='0' $EstateSTR0>已开发项目</option>
	<option value='2' $EstateSTR2>未审批项目</option>
	<option value='3' $EstateSTR3>未验收项目</option>
	</select>&nbsp;";

	//开发负责人
   $DeveloperSql= mysql_query("SELECT P.Principal,M.Name 
                   FROM $DataIn.projectset_sheet P
	               LEFT JOIN $DataPublic.staffmain M ON M.Number=P.Principal  
				   WHERE 1 $SearchRows GROUP BY P.Principal ORDER BY Principal",$link_id);
	if($DeveloperRow = mysql_fetch_array($DeveloperSql)){
		echo"<select name='Principal' id='Principal' onchange='document.form1.submit();'>";
		echo"<option value='' selected>开发负责人</option>";
		do{ 
			$PrincipalId=$DeveloperRow["Principal"];
			$Name=$DeveloperRow["Name"];
			if ($Principal==$PrincipalId){
				  echo "<option value='$PrincipalId' selected>$Name</option>";
				  $SearchRows.=" AND P.Principal='$PrincipalId'";
				}
			else{
				  echo "<option value='$PrincipalId'>$Name</option>";
				}
			}while ($DeveloperRow = mysql_fetch_array($DeveloperSql));
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

$colspan=count($Th_Col)/2;
$mySql="SELECT P.Id,P.ItemId,P.ItemName,P.Principal,P.Participant,P.Description,P.Attached,P.Amount,P.StartDate,P.EstimatedDate,
P.Estate,P.Date,P.Locks,P.Remark,T.Name AS TypeName,M.Name 
FROM $DataIn.projectset_sheet P
LEFT JOIN $DataIn.projectset_Type T ON T.Id=P.TypeId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=P.Operator WHERE 1 $SearchRows ORDER BY ItemId desc";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
     $d=anmaIn("download/projectset/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$ItemId=$myRow["ItemId"];
		$ItemName=$myRow["ItemName"];
		$Description=$myRow["Description"];
		$Amount = number_format($myRow["Amount"]);
		$StartDate=$myRow["StartDate"];
		$EstimatedDate=$myRow["EstimatedDate"];
		$TypeName=$myRow["TypeName"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		
		$Operator = $myRow["Principal"];
		include "../model/subprogram/staffname.php";
		$Principal = $Operator;
		
		$Participants = explode(',', $myRow["Participant"]);
		$Participant = '';
		foreach($Participants as $Operator){
			include "../model/subprogram/staffname.php";
			$Participant.=$Participant==''?$Operator:','.$Operator;
		}
		
		$Attached=$myRow["Attached"];
		if ($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
		}else{
			$Attached='&nbsp;';
		}
		
		$Operator=$myRow["Name"];
		$Date=$myRow["Date"];
		
		$Locks = $myRow["Locks"];
		$Estate = $myRow["Estate"];
		switch($Estate){
		    case 1:$EstateStr='开发中';break;
			case 2:$EstateStr='未审批';break;
			case 3:$EstateStr='未验收';break;
			case 0:$EstateStr='已完成';break;
		}
		
		//进度目标
		$progressStr="<a href='projectset_progress_read.php' style='CURSOR: pointer' title='编辑进度目标' width='13' height='13' target='_blank'>View</a>";
		
		//已有填写开发进度显示
		$logResult=mysql_query("SELECT * FROM $DataIn.projectset_log WHERE  Mid='$Id'",$link_id);
		if($logRow=mysql_fetch_array($logResult)){
		      $ItemColor="style='color:#FF3366'";
		}
		else{
		       $ItemColor="&nbsp;";
		}
		
		$showPurchaseorder="<img onClick='ShowDropTable(ProcessTable_$Id,showtable_$Id,ProcessDiv_$Id,\"projectset_ajax\",\"$Id\",\"public\");'  src='../images/showtable.gif' title='显示或隐藏开发进度信息.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable_$Id'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='ProcessTable_$Id' name='ProcessTable_$Id' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='ProcessDiv_$Id'  name='ProcessDiv_$Id' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$ItemId,   1=>"align='center'"),
			array(0=>$ItemName, 1=>"$ItemColor"),
			array(0=>$TypeName, 1=>"align='center'"),
			array(0=>$Description),
			array(0=>$Attached, 1=>"align='center'"),
			array(0=>$Principal,1=>"align='center'"),
			array(0=>$Participant),
			array(0=>$StartDate,    1=>"align='center'"),
			array(0=>$EstimatedDate,1=>"align='center'"),
			array(0=>$progressStr,  1=>"align='center'"),
			array(0=>$Remark,   1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$EstateStr,1=>"align='center'")
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
