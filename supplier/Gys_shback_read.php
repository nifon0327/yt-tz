<?php 
//电信-zxq 2012-08-01
session_start();
$Login_WebStyle="default";
include "../model/modelhead.php";
//步骤2：需处理

$tableMenuS=500;
ChangeWtitle("$SubCompany 退回送货单");
$funFrom="gys_shback";
$From=$From==""?"read":$From;
$ColsNumber=12;
$sumCols="5";	//求和列,需处理
$MergeRows=3;	//主单列
$Th_Col="选项|30|序号|30|退回日期|70|配件ID|50|配件名称|250|QC图|40|品检报告|55|送货数量|60|单位|30|退回原因|150|送货日期|70|送货单号|80|需求单流水号|100";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
$Keys=12;
//$ActioToS="1,2,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" AND M.CompanyId=$myCompanyId";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.gys_shback WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		//echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";

		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND M.Date>='$StartDate' AND M.Date<='$EndDate' ";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//增加快带查询Search按钮
$searchfile="Quicksearch_ajax.php";
$searchtable="stuffdata|D|StuffCname|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.BillNumber,M.SendDate,M.StockId,M.StuffId,M.Qty,M.Date,M.Remark,
        D.TypeId,D.StuffCname,D.Picture,G.FactualQty+G.AddQty AS cgQty,U.Name AS UnitName
FROM $DataIn.gys_shback M 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId= M.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId= M.StockId
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC,M.Id";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Date=$mainRows["Date"];
                $SendDate=$mainRows["SendDate"];
		$BillNumber=$mainRows["BillNumber"];
		$StuffId=$mainRows["StuffId"];			
                $StuffCname=$mainRows["StuffCname"];
                $UnitName=$mainRows["UnitName"];
                $Qty=$mainRows["Qty"]; 
                $StockId=$mainRows["StockId"];
                $TypeId=$mainRows["TypeId"];
		$Remark=$mainRows["Remark"];
                $Picture=$mainRows["Picture"];
                $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			
                $ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
                include "../model/subprogram/stuffimg_model.php";			
               		
                //配件QC检验标准图
                include "../model/subprogram/stuffimg_qcfile.php";
                //配件品检报告qualityReport
                include "../model/subprogram/stuff_get_qualityreport.php";
                
                $ValueArray=array(
				array(0=>$Date,		1=>"align='center'"),
                                array(0=>$StuffId,	1=>"align='center'"),
				array(0=>$StuffCname),
                                array(0=>$QCImage, 	1=>"align='center'"),
				array(0=>$qualityReport, 1=>"align='center'"),
                                array(0=>$Qty,		1=>"align='right'"),
				array(0=>$UnitName,	1=>"align='center'"),
				array(0=>$Remark),
				array(0=>$SendDate,    1=>"align='center'"),
				array(0=>$BillNumber,  1=>"align='center'"),
				array(0=>$StockId,     1=>"align='center'"),
				);
			$checkidValue=$StuffId;
			include "../admin/subprogram/read_model_6.php";	
              
	  }while($mainRows = mysql_fetch_array($mainResult));
	}
else{
	noRowInfo($tableWidth);
	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../admin/subprogram/read_model_menu.php";
?>
