<?
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>"; 

echo "
<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
//echo "checktranstable:$checktranstable <br>";
//echo "Table_Size:$Table_Size <br>";
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/kq_YearHolday.php";
if( ($BeginSign=='') || ($chooseYear=='')  ) {  //点击开始，开始倒入数据
	
	echo "<select name='chooseYear' id='chooseYear'  style='width: 100px;'  >";
	
		$ChooseYear=$ChooseYear==""?date("Y")-1:$ChooseYear;
		 for ($m=date("Y"),$n=2;$n>=0;$m--,$n--){
			 if ($ChooseYear==$m){
				  echo "<option value='$m' selected>$m" . "</option>";
			 }
		 else{
			 echo "<option value='$m'>$m" . "</option>";
			}
		 }
	echo "</select>";	
	echo" <input type='button' id='Begin' name='Begin' value='点击生成工龄扣除：' onClick='BeginS()'>";
	echo "<input name='BeginSign' id='BeginSign'  type='hidden' value='1'>";	
	
	echo "<br> <br>";
	
}
else {
	

?>
<?

$Operator=$Login_P_Number;
echo "Login_P_Number: $Login_P_Number ";
$DateTime=date("Y-m-d");
$Month=($chooseYear+1).'-01';   //计算去年请假，今年1月开始扣工龄;  可能要改生成工资时
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign
	FROM $DataPublic.staffmain M
	where M.Estate=1   ;
     ";
	

$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	$Number=$myRow["Number"];
	
	$NextYear=$chooseYear+1;
	$cmySql="SELECT S.Id,S.Month,S.Months,S.Remark,S.Date,S.Estate,S.Locks,S.Operator
	FROM $DataPublic.rs_kcgl S  
	WHERE  S.Number='$Number' and substring(S.month,1,4)='$NextYear'";  //查询今年是存在记录
	//echo "$cmySql";
	$cmyResult = mysql_query($cmySql,$link_id);
	if($cmyRow = mysql_fetch_array($cmyResult )){	
	    continue;  
	}
	
	
	$qjAllHours=0;$n=0;//$dataArray=array('date');
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number'  AND Type=1 and substring(StartDate,1,4)='$chooseYear' order by StartDate",$link_id);  //取得已批准的年假
	//echo "SELECT StartDate,EndDate,Type,bcType FROM $DataPublic.kqqjsheet WHERE Number='10369'  AND Type<>4 and substring(StartDate,1,4)='$chooseYear' order by StartDate";
    
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假
			
		do {
	
		//做成一个函数，更好	
		$StartDate=$qjRow1["StartDate"];
		$EndDate=$qjRow1["EndDate"];
		$bcType=$qjRow1["bcType"];
		//array_push($dataArray,$StartDate);

		$ThisHoldDays=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
		
		$qjAllHours=$qjAllHours+$ThisHoldDays;
		$n++;
		//$qjAllHours=$qjAllHours+$qjHours+$qjSHours+$qjEHours+$MidHours;	
		//echo "$qjHours+$qjSHours+$qjEHours+$MidHours <br>";
		}while ($qjRow1 = mysql_fetch_array($qjResult1));
		
	}

	$qjAllHours=round($qjAllHours);
	
	$Mod=$qjAllHours%8;
	if($Mod>0 && $Mod!=4){
		$Mod=$Mod<4?4-$Mod:8-$Mod;
	}
	else{
		$Mod=0;
	}
    $qjAllHours=$qjAllHours+$Mod;
	if($qjAllHours>120) {
		$Remark="$chooseYear 年请事假  $qjAllHours 小时; " ;
		$Months=1; //大于15天，小于等于一个月(30天)扣一个月
		if($qjAllHours>240 && $qjAllHours<=720) {
			$Months=3;
		}
		else {
			if($qjAllHours>720) {
				$Months=5;
			}
		}
	//插入到表中
		$inRecode="INSERT INTO $DataPublic.rs_kcgl (Id,Number,Month,Months,Remark,Date,Estate,Locks,Operator)
			VALUES  (NULL,$Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator' ) ";
		//echo "$inRecode";	
		$inResult=@mysql_query($inRecode);
		if($inResult){
			//$Log.="&nbsp;&nbsp;员工(".$Ids.")的".$TitleSTR."成功!</br>";
			echo "&nbsp;&nbsp; $Name ($Number ) --$Month 开始扣工龄 成功 <br> ";
			}
		else{
			//$Log.="<div class='redB'>&nbsp;&nbsp;员工(".$Ids.")的".$TitleSTR."失败! $inRecode </div></br>";
			echo "&nbsp;&nbsp; $Name ($Number ) --$Month 开始扣工龄 失败! $inRecode <br> ";
			
			}	
		
		
	}
	//break;	
		
}while ($myRow = mysql_fetch_array($myResult));		
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
		document.getElementById('chooseYear').readOnly='readOnly';
		//alert ("a df bb dd");
		document.form1.action="rs_kcgl_change.php";
		document.form1.submit();
}



</script>