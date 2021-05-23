<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch5_sampsheet
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=14;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 随货样品列表");
$funFrom="ch_shipsamp";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|客户|70|样品ID|50|PO|80|中文注释|200|Description|200|装箱|50|数量|50|单价|70|单品重量(G)|80|金额|60|状态|50|更新日期|70|操作|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
$sumCols="8,10,11";
$TypeId=$TypeId==""?0:$TypeId;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ch5_sampsheet WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((S.Date>'$StartDate' and S.Date<'$EndDate') OR S.Date='$StartDate' OR S.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$selectedStr="strType".$TypeId;
	$$selectedStr="selected";
	echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
	echo "<option value='0' $strType0>样品</option>
	      <option value='1' $strType1>代购货款项目</option>
		  </select>&nbsp;";
	$SearchRows.=" AND S.TypeId='$TypeId'";

	//客户//状态
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
S.Id,S.SampId,S.CompanyId,S.SampPO,S.SampName,S.Description,S.Qty,S.Price,S.Weight,S.Date,S.Type,S.Estate,S.Locks,S.Operator,
C.Forshort 
FROM $DataIn.ch5_sampsheet S
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
WHERE 1 $SearchRows
ORDER BY S.Estate DESC,S.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$SampId=$myRow["SampId"];
		$SampName=$myRow["SampName"]==""?"&nbsp;":$myRow["SampName"];
                $SampPO=$myRow["SampPO"]==""?"&nbsp;":$myRow["SampPO"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Qty=$myRow["Qty"];	
		$Type=$myRow["Type"]==1?"装箱":"不装箱";	
		$Price=$myRow["Price"];
		$Weight=$myRow["Weight"];
		$Date=$myRow["Date"]; 
		$Locks=$myRow["Locks"]; 
		$Operator=$myRow["Operator"];
		$Amount=sprintf("%.2f",$Qty* $Price);
		include "../model/subprogram/staffname.php";
		switch($Estate=$myRow["Estate"]){
			case "0":
				$Estate="<div class='greenB'>已出</div>";
				break;
			case"1":
				$Estate="<div class='redB'>未处理</div>";
				break;
			default:
				$Estate="<div class='yellowB'>待出</div>";
				break;
			}
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$SampId,1=>"align='center'"),
                        array(0=>$SampPO),
			array(0=>$SampName),
			array(0=>$Description),
			array(0=>$Type,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Price,1=>"align='right'"),
			 array(0=>$Weight, 1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
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