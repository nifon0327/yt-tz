<?
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>"; 

echo "
<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
//echo "checktranstable:$checktranstable <br>";
//echo "Table_Size:$Table_Size <br>";
include "../basic/parameter.inc";
if( ($BeginSign=='') || ($TheNumber=='') || ($TheName=='') || ($TheTime=='') ) {  //点击开始，开始倒入数据
	

	echo "&nbsp;&nbsp;工号：<input name='TheNumber' id='TheNumber'  type='text' style='width:80px' value=''>";
	echo "&nbsp;&nbsp;姓名：<input name='TheName' id='TheName'  type='text' style='width:80px' value=''>";
	
	echo "&nbsp;&nbsp;时间：<input name='TheTime' id='TheTime'  type='text' style='width:80px' value=''>";
	
	echo "
	<select name='FromCompany' id='FromCompany' style='width:80px' dataType='Require' msg='未选择'>
    <option value=''>请选择</option>
	";
	$Staff_Result = mysql_query("SELECT Id,Db,CShortName FROM $DataPublic.companys_group WHERE Csign>0 AND Estate=1 order by Id",$link_id);
	if($staff_Row = mysql_fetch_array($Staff_Result)){
	do{
		$Db=$staff_Row["Db"];
		$CShortName=$staff_Row["CShortName"];
		echo"<option value='$Db'>$CShortName</option>";
		}while ($staff_Row = mysql_fetch_array($Staff_Result));
	}	              
    echo "</select>";
	echo "&nbsp;&nbsp;调动到：";

	echo "
	<select name='ToCompany' id='ToCompany' style='width:80px' dataType='Require' msg='未选择'>
    <option value=''>请选择</option>
	";
	$Staff_Result = mysql_query("SELECT Id,Db,CShortName FROM $DataPublic.companys_group WHERE Csign>0 AND Estate=1 order by Id",$link_id);
	if($staff_Row = mysql_fetch_array($Staff_Result)){
	do{
		$Db=$staff_Row["Db"];
		$CShortName=$staff_Row["CShortName"];
		echo"<option value='$Db'>$CShortName</option>";
		}while ($staff_Row = mysql_fetch_array($Staff_Result));
	}	              
    echo "</select>";
    
	echo" <input type='button' id='Begin' name='Begin' value='点击考勤调动：' onClick='BeginS()'>";
	echo "<input name='BeginSign' id='BeginSign'  type='hidden' value='1'>";	
	
	echo "<br> <br>";
	
}
else {
	

?>
<?
    if (strlen($TheTime)<7) {
		echo "请输入正确的日期:$TheTime";
		$TheTime="1990-1234567";
		return false;
		
		
	}
	$mySql="SELECT *  FROM $DataPublic.staffmain WHERE Name='$TheName' AND Number='$TheNumber' ";
	//echo "$mySql";
	$myResult = mysql_query($mySql,$link_id) or die ("数据连接错误!");
	if($myRow = mysql_fetch_array($myResult)){
	
	
		$new_Recode="INSERT INTO `$ToCompany`.`checkinout`
		       (`Id`, `BranchId`, `JobId`, `Number`, `CheckTime`, `CheckType`, `dFrom`, `Estate`, `Locks`, `ZlSign`, `KrSign`, `Operator`)  
		SELECT NULL, `BranchId`, `JobId`, `Number`, `CheckTime`, `CheckType`, `dFrom`, `Estate`, `Locks`, `ZlSign`, `KrSign`, `Operator` FROM `$FromCompany`.`checkinout`  WHERE Number='$TheNumber' AND CheckTime like '$TheTime%' ";
		echo "$new_Recode <br>";
		$new_Result = mysql_query($new_Recode,$link_id);
		if($new_Result && mysql_affected_rows()>0){
			echo "考勤记录调动成功！";
			$query="delete FROM `$FromCompany`.`checkinout`  WHERE Number='$TheNumber' AND CheckTime like '$TheTime%' ";  
			$result=mysql_query($query,$link_id); //删掉原来数据		
			
			}
		else{
			echo "考勤记录调动失败或不存在数据！";
			} 
			
	}
	else {
		echo "工号或用户名有误！请重新输入";
	}
		
		
		
		
	
}  //else 

?>
<?

echo "
		</form>
	</body>
</html>
";
?>
<script language = "JavaScript">
function BeginS(){
		document.getElementById('Begin').disabled=true;
		document.getElementById('TheNumber').readOnly='readOnly';
		document.getElementById('TheName').readOnly='readOnly';
		document.getElementById('TheTime').readOnly='readOnly';
		
		document.form1.action="kq_change.php";
		document.form1.submit();
}



</script>