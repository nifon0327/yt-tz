<?php   
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|分类|90|半成品Id|45|半成品名称|320|参考买价|60|单位|40|默认供应商|100|送货</br>楼层|40|采购|50|备注|30|状态|40|更新日期|70|操作|50";
$ColsNumber=14;
$tableMenuS=800;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页

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

$mySql="SELECT A.StuffId,A.StuffCname,A.Picture,A.Price,A.Estate,
M.Number,P.CompanyId,P.Forshort,P.Currency,M.Name,
A.Remark,A.SendFloor,A.Date,A.Operator,T.TypeName,U.Name AS Unit,A.TypeId
FROM $DataIn.semifinished_bom  S  
LEFT JOIN $DataIn.stuffdata A ON A.StuffId = S.mStuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFt JOIN $DataIn.bps B ON B.StuffId=A.StuffId
LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 	
WHERE 1  $sSearch GROUP BY S.mStuffId  ORDER BY S.Id DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$Unit=$myRow["Unit"];
		$StuffCname=$myRow["StuffCname"];
		$Forshort=$myRow["Forshort"];
		$TypeId=$myRow["TypeId"];
		$TypeName=$myRow["TypeName"];
		$Buyer=$myRow["Name"];
		$Price=$myRow["Price"];
		switch($Action){
			case "1":
				$Bdata=$StuffId."^^".$StuffCname;	
				break;			
			}		
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		
		$Picture=$myRow["Picture"];

		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$StuffCname=$myRow["StuffCname"];		
		$Date=substr($myRow["Date"],0,10);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
        include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        $Locks = 1;
		$ValueArray=array(
			array(0=>$TypeName."-".$Locks,
					 1=>"align='center'"),
			array(0=>$StuffId,
					 1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Price,
					 1=>"align='center'"),
			array(0=>$Unit,
					 1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$Buyer,
					 1=>"align='center'"),
			array(0=>$Remark,
					 1=>"align='center'"),
			array(0=>$Estate,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
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