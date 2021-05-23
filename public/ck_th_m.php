<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=600;
$sumCols="6,9";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 已入库物料退换审核列表");
$funFrom="ck_th";
$Th_Col="选项|40|序号|30|退换日期|60|配件Id|45|配件名称|250|历史<br>订单|40|退换数量|60|实物库存|60|单价|50|单位|45|小计|60|退换原因|250|供应商|80|采购员|40|状态|50|操作|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量	
$ActioToS="17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$SearchRows=" and F.Estate=1";
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ck2_thsheet F 
	LEFT JOIN  $DataIn.ck2_thmain M ON M.Id=F.Mid  
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo  "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and  DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo  "<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
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
$mySql="
SELECT 
F.Id,F.StuffId,F.Qty,F.Remark,M.Date,F.Estate,F.Locks,M.Operator,D.StuffCname,U.Name AS UnitName,D.Price,D.Price*F.Qty AS Amount,P.Forshort,B.BuyerId,K.tStockQty   
FROM $DataIn.ck2_thsheet F
LEFT JOIN $DataIn.ck2_thmain M ON M.Id=F.Mid   
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = F.StuffId
LEFT JOIN $DataIn.bps B ON B.StuffId=F.StuffId 
WHERE 1 $SearchRows ORDER BY F.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$LockRemark ="";
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"];
		$tStockQty=$myRow["tStockQty"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];		
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='redB'>未核</div>":"<div class='greenB'>已核</div>";
		
		if($tStockQty<$Qty){
			$LockRemark ="库存不够，不能审核!";
		}
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$OperatorSTR=$Operator;
		
		$Operator=$myRow["BuyerId"];
		include "../model/subprogram/staffname.php";
		$Buyer=$Operator;
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		$ValueArray=array(
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$StuffId, 	1=>"align='center'"),
			array(0=>$StuffCname),
            array(0=>$OrderQtyInfo, 	1=>"align='center'"),
			array(0=>$Qty, 		1=>"align='right'"),
			array(0=>$tStockQty, 		1=>"align='right'"),
			array(0=>$Price,	1=>"align='right'"),
			array(0=>$UnitName,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Remark,	3=>"..."),
			array(0=>$Forshort),
			array(0=>$Buyer,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$OperatorSTR,	1=>"align='center'")
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