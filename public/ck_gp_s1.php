<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|配件Id|50|配件名称|400|规格|30|备注|30|供应商|150";
$ColsNumber=14;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
$Parameter.=",Jid,$Jid";
$sSearch1=" AND M.CompanyId='$Jid'";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
//echo "sSearch:$sSearch";
$mySql="
SELECT 
	B.StuffId,B.StuffCname,B.Spec,B.Remark,P.Forshort
    from  $DataIn.stuffdata B
	LEFt JOIN $DataIn.stufftype D ON D.TypeId=B.TypeId
	LEFt JOIN $DataIn.bps M ON M.StuffId=B.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	WHERE 1  $sSearch $sSearch1 ORDER BY B.StuffCname DESC";
//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Forshort=$myRow["Forshort"];
		
		$Bdata=$StuffId."^^".$StuffCname."^^".$unQty;
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Locks=1;
		$ValueArray=array(
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Spec),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Forshort)
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