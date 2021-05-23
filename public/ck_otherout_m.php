<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=600;
$sumCols="6,7,8";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 物料其它出库审核列表");
$funFrom="ck_otherout";
$Th_Col="选项|40|序号|40|出库日期|70|配件|45|配件名称|250|历史<br>订单|40|在库|60|可用库存|60|出库数量|60|单价|50|单位|45|小计|60|单据|50|出库原因|320|分类|65|状态|50|操作|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量	
$ActioToS="15,17";				
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$SearchRows=" and F.Estate=1 AND F.OutSign =2";
	$date_Result = mysql_query("SELECT F.Date FROM $DataIn.ck8_bfsheet F WHERE 1 $SearchRows GROUP BY DATE_FORMAT(F.Date,'%Y-%m') ORDER BY F.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo  "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and  DATE_FORMAT(F.Date,'%Y-%m')='$dateValue'";
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
F.Id,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,F.Estate,F.Locks,F.Operator,D.StuffCname,U.Name AS UnitName,D.Price,D.Price*F.Qty AS Amount,C.TypeName,C.TypeColor,K.tStockQty,K.oStockQty,F.Bill,F.DealResult 
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck8_bftype  C ON C.id=F.Type 
WHERE 1 $SearchRows  AND F.OutSign =2 ORDER BY F.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$LockRemark ="";
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];		
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==0?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		$Type=$myRow["Type"];
		$TypeName=$myRow["TypeName"];
		$TypeColor =$myRow["TypeColor"];
		$TypeName="<span style=\"color:$TypeColor \">$TypeName</span>";
        //历史订单
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
        $tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
        if($Qty >$tStockQty || $Qty>$oStockQty){
            $Qty = "<span class='redB'>$Qty</span>"; 
	        $LockRemark = "库存不足，不能出库，请相关人员修改出库数量";
        }else{
	        $Qty = "<span class='greenB'>$Qty</span>";
        }
        
         $Bill=$myRow["Bill"];
		$Dir=anmaIn("download/ckbf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="B".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$ValueArray=array(
			array(0=>$Date, 	  1=>"align='center'"),
			array(0=>$StuffId, 	  1=>"align='center'"),
			array(0=>$StuffCname),
            array(0=>$OrderQtyInfo, 	1=>"align='center'"),
            array(0=>$tStockQty, 		1=>"align='right'"),
            array(0=>$oStockQty, 		1=>"align='right'"),
			array(0=>$Qty, 		  1=>"align='right'"),
			array(0=>$Price,	  1=>"align='right'"),
			array(0=>$UnitName,	  1=>"align='center'"),
			array(0=>$Amount,	  1=>"align='right'"),
			array(0=>$Bill,		1=>"align='center'"),
			array(0=>$Remark,	  3=>"..."),
			array(0=>$TypeName,	  1=>"align='center'"),
			array(0=>$Estate,	  1=>"align='center'"),
			array(0=>$Operator,	  1=>"align='center'")
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