<?php   
//电信-EWEN
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|60|配件ID|70|配件名称|210|单价|60";
$ColsNumber=4;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Bid==""?"":" and M.CompanyId='$Bid'";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT D.Id,D.StuffId,D.StuffCname,D.Price 
FROM $DataIn.stuffdata D
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
 WHERE 1 AND T.mainType=3 AND Estate>0 ORDER BY TypeId,StuffCname";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$Bdata=$StuffId."^^".$StuffCname."^^".$Price;
		$Locks=1;
		$ValueArray=array(
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Price,1=>"align='center'")
			);
		$checkidValue=$Bdata;
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