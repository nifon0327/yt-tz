<?php 
//电信-ZX   2012-08-01
//代码、数据库合并后共享-EWEN 2012-08-20
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 来访登记列表");
$funFrom="info_visitor";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|来访日期|60|来访分类|60|来访单位|120|人数|30|来访起始时间|110|来访结束时间|110|来访说明|300|状 态|60|登记人|80|登记日期|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
$sumCols="10";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$type_Result = mysql_query("SELECT C.Id,C.Name AS TypeName FROM $DataPublic.come_type C WHERE C.Estate=1",$link_id);
	if($typeRow = mysql_fetch_array($type_Result)) {
		echo"<select name='chooseType' id='chooseType' onchange='document.form1.submit()'>";
		echo"<option value='' selected>全部</option>";
		do{			
			$TypeId=$typeRow["Id"];
			$TypeName=$typeRow["TypeName"];
			if($chooseType==$TypeId){
				echo"<option value='$TypeId' selected>$TypeName</option>";
				$SearchRows.=" and I.TypeId='$TypeId'";
				}
			else{
				echo"<option value='$TypeId'>$TypeName</option>";				
				}
			}while($typeRow = mysql_fetch_array($type_Result));
		echo"</select>&nbsp;";
		}
	
		$date_Result = mysql_query("SELECT I.ComeDate FROM $DataPublic.come_data I WHERE 1 $SearchRows GROUP BY DATE_FORMAT(I.ComeDate,'%Y-%m') ORDER BY I.ComeDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		//echo"<option value='' selected>全部</option>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["ComeDate"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and DATE_FORMAT(I.ComeDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
		
	
		echo"<select name='chooseEstate' id='chooseEstate' onchange='document.form1.submit()'>";
		$EstateSelectSTR="EstateSelect" . $chooseEstate;
		$$EstateSelectSTR="selected";
		echo"<option value='' $EstateSelect>全部</option>";
		echo"<option value='1' $EstateSelect1>未到访</option>";
		echo"<option value='2' $EstateSelect2>来访中</option>";
		echo"<option value='0' $EstateSelect0>已来访</option>";
		echo"</select>&nbsp;";
        $SearchRows.=$chooseEstate==""?"":" and I.Estate='$chooseEstate' ";
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
$mySql="SELECT I.Id,I.Name,I.ComeDate,I.InTime,I.OutTime,I.Persons,I.Remark,I.Date,C.Name AS TypeName,I.Estate,P.Forshort,M.Name AS Operator     
FROM $DataPublic.come_data I 
LEFT JOIN $DataPublic.come_type C ON C.Id=I.TypeId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=I.CompanyId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=I.Operator  
WHERE  1 $SearchRows ORDER BY I.Estate DESC,I.ComeDate DESC ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
        $Name=$myRow["Name"];
        $Date=$myRow["Date"];
        $ComeDate=$myRow["ComeDate"];
        $InTime=$myRow["InTime"];
        $OutTime=$myRow["OutTime"];
        $Remark=$myRow["Remark"];
        $TypeName=$myRow["TypeName"];
        $Persons=$myRow["Persons"];
        $Operator=$myRow["Forshort"]==""?$myRow["Operator"]:$myRow["Forshort"]; 
        
        //$Remark="[$TypeName]:" . $Operator . ";" . $Remark;
        $Estate=$myRow["Estate"];
        switch($Estate){
             case 1:$EstateSTR="<div class='redB'>未到访</div>";break;
             case 2:$EstateSTR="<div class='greenB'>来访中</div>";break;
             case 0:$EstateSTR="<b>已来访</b>";break;
        }
	
		$Locks=$Estate==0?0:1;
		$ValueArray=array(
	  	    array(0=>$ComeDate,1=>"align='center'"),
	  	    array(0=>$TypeName,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Persons,1=>"align='center'"),
			array(0=>$InTime,1=>"align='center'"),
			array(0=>$OutTime,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$EstateSTR,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
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
