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
ChangeWtitle("$SubCompany 测试项目登记");
$funFrom="producttest";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|客户|60|编号|60|项目名称|400|备注|70|登记日期|70|登记人|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
if($From!="slist"){

$SearchRows="";
$OperatorSql = mysql_query("SELECT D.Operator,P.Name FROM $DataIn.producttest D 
	               LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator 
				   WHERE 1 GROUP BY D.Operator ORDER BY D.Operator",$link_id);
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
		
			//客户
	$ClientSql= mysql_query("SELECT D.CompanyId,C.Forshort FROM $DataIn.producttest D
	                         LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
							 WHERE 1 GROUP BY D.CompanyId",$link_id);
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

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.ItemId,D.ItemName,D.Content,D.Date,D.Operator,C.Forshort,P.Name,D.Locks 
FROM $DataIn.producttest D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Operator WHERE 1 $SearchRows ORDER BY D.ItemId desc";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Forshort=$myRow["Forshort"];
		$ItemName=$myRow["ItemName"];
		$Name=$myRow["Name"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$ItemId=$myRow["ItemId"];
		$Locks=$myRow["Locks"];
		$Content=trim($myRow["Content"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Content]' width='16' height='16'>";
		
		$ValueArray=array(
			0=>array(0=>$Forshort,
					 1=>"align='center'"),
			1=>array(0=>$ItemId),
			2=>array(0=>$ItemName),
			3=>array(0=>$Content,
					 1=>"align='center'"),
			4=>array(0=>$Date,
					 1=>"align='center'"),
			5=>array(0=>$Name,
					 1=>"align='center'")
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