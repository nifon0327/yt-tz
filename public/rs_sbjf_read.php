<?php 
//电信-ZX  2012-08-01
/*
$DataIn.sbpaysheet
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=14;				
$tableMenuS=600;
$sumCols="9,10,11";		//求和列
ChangeWtitle("$SubCompany 社保公积金缴费记录");
$funFrom="rs_sbjf";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|60|类型|60|员工ID|50|员工姓名|60|部门|60|职位|60|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|结付|80|登记日期|100|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8,14,175";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	//选择公司
   $cSignTB="S";$SelectFrom=5;
  include "../model/subselect/cSign.php"; 
  
//缴费类型
   $TypeId=$TypeId==""?1:$TypeId;
   $TypeSTR="TypeId".$TypeId;
   $$TypeSTR="selected";
   echo"<select name='TypeId' id='TypeId' onchange='document.form1.submit()'>";
   echo"<option value='1' $TypeId1>社保</option>";
   echo"<option value='2' $TypeId2>公积金</option>";
   echo"<option value='3' $TypeId3>意外险</option>";
   echo"<option value='4' $TypeId4>商业险</option>";
   echo"</select>";
   if($TypeId!="")$SearchRows.=" AND S.TypeId='$TypeId'";
	$date_Result = mysql_query("SELECT S.Month FROM $DataIn.sbpaysheet S WHERE 1 $SearchRows GROUP BY S.Month order by S.Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}

	$B_Result=mysql_query("SELECT B.Id,B.Name FROM $DataIn.sbpaysheet S
     LEFT JOIN   $DataPublic.branchdata B ON B.Id=S.BranchId
     WHERE 1 $SearchRows GROUP BY B.Id ORDER BY B.Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
	  echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND S.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
	$orderStr="ORDER BY S.BranchId";
}else {
	$orderStr="ORDER BY S.Month desc";
}
	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

echo "<span id='uploadSpan'>  &nbsp; &nbsp;单据:<input style='width: 150px;' name='Attached' type='file' id='Attached' onchange='checkFileType(this.value);'><input type='button' name='Submit' value='开始上传' onClick='UpLoad()'></span>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,
S.Date,S.Operator,S.Estate,S.Mid,P.Name,S.TypeId,J.Picture,P.OffStaffSign,S.cSign
	 FROM $DataIn.sbpaysheet S
	LEFT JOIN $DataIn.staffmain P ON S.Number=P.Number
	LEFT JOIN $DataIn.rs_sbjf_picture  J ON J.Mid = S.Id 
	WHERE 1 $SearchRows $orderStr ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];	
		
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$mAmount =$myRow["mAmount"];
			$cAmount =$myRow["cAmount"];
			$Amount=sprintf("%.2f",$mAmount +$cAmount);
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchId=$myRow["BranchId"];				
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$Mid=$myRow["Mid"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			$cSignFrom=$myRow["cSign"];
		    include"../model/subselect/cSign.php";
			switch ($myRow["TypeId"]){
				case 1:$TypeName="社保"; break;
				case 2:$TypeName="公积金"; break;
				case 3:$TypeName="意外险"; break;
				case 4: $TypeName="商业险";break;
					
			}
			$Estate=$myRow["Estate"];
			switch($Estate){
				case 1:
					if($Mid==0){
						$Estate="<span class='redB'>未处理</span>";$LockRemark="";}
					else{
						$Estate="<span class='redB'>错误(状态为未处理但已付)</span>";
						$LockRemark="错误,需核查并请IT处理.";
						}
				break;
				case 2:
					$Estate="<span class='yellowB'>请款中</span>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 3:
				$Estate="<span class='yellowB'>请款通过</span>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 0:
					$Estate="<span class='greenB'>已结付</span>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
				break;
				}
				
        $OffStaffSign=$myRow["OffStaffSign"];
        
        if($OffStaffSign==1){
	        $Name = "<span class='blueB'>$Name</span>"; //编外人员
        }
        	

        $Picture=$myRow["Picture"];
        if($Picture!=""){
		    $Dir=anmaIn("download/sbjf_List/",$SinkOrder,$motherSTR);
			$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
			$Month="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>$Month</span>";
            }
            				
		$ValueArray=array(
		    array(0=>$cSign,		1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Month, 	1=>"align='center'"),
			array(0=>$mAmount,	1=>"align='center'"),
			array(0=>$cAmount,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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

<script language = "JavaScript"> 
  function checkFileType(str){   
        var pos = str.lastIndexOf(".");  
        var lastname = str.substring(pos,str.length);  
        var resultName=lastname.toLowerCase();
        var jpg = ".jpg";
        
        if (jpg != resultName.toString() ){
            alert("请上传JPG格式");
             //resetFile();  
           }  
    } 

	function UpLoad(){
	
	   var Attached= document.getElementById("Attached");
	   //var TypeId = document.getElementById("TypeId").value;
	   //var chooseMonth = document.getElementById("chooseMonth").value;
	   var sr=Attached.value;
	   var upId="";
	   for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;
			if (e.type=="checkbox" && Name!=-1){
				if(e.checked){
						upId=upId==""? e.value : upId + "|" + e.value;
					} 
				}
			}
	  
	   
	   if(sr.length==0  || upId =="" ){
	   
			  alert("请选择要上传单据,或者相应记录");
			  return false;
		}
	   else{
	
			    document.form1.action="rs_sbjf_other_up.php?Action=200&upId="+upId;
				document.form1.submit();
			}
	}
</script>