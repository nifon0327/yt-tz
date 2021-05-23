<?
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>"; 
echo "<body  oncontextmenu='event.returnValue=false' onhelp='return false;'>
<form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
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


$Operator=$Login_P_Number;
$DateTime=date("Y-m-d");
$Month=($chooseYear+1).'-01';   //计算去年请假，今年1月开始扣工龄;  可能要改生成工资时
	 
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign
	FROM $DataPublic.staffmain M
	where M.Estate=1";	 
	

$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	$Number=$myRow["Number"];
	$LastYear=$chooseYear-1;
	$NextYear=$chooseYear+1;
	$cmySql="SELECT S.Id,S.Month,S.Months,S.Remark,S.Date,S.Estate,S.Locks,S.Operator
	FROM $DataPublic.rs_kcgl S  
	WHERE  S.Number='$Number' and substring(S.month,1,4)='$NextYear'";  //查询今年是存在记录
	$cmyResult = mysql_query($cmySql,$link_id);
	if($cmyRow = mysql_fetch_array($cmyResult )){	
	    continue;  
	}
	
	//2016-05-16 至 2016-07-31 淡季期间，请事假的不用扣除工龄     
    $NodeductDays=GetBetweenDateDays($Number,'2016-05-16','2016-07-31',$bcType,$DataIn,$DataPublic,$link_id);  
        
	$qjAllHours=0;$n=0;//$dataArray=array('date');
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number'  AND Type=1 and (substring(StartDate,1,4)='$chooseYear' OR substring(EndDate,1,4)='$chooseYear') order by StartDate",$link_id);  //取得已批准的年假
    
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假	
		do{
			$StartDate=$qjRow1["StartDate"];
			$EndDate=$qjRow1["EndDate"];
			
			//跨年的要分开
		    $frist_Year=substr($StartDate,0,4);
		    $end_Year=substr($EndDate,0,4);
		    if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
		    if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";		
	
			$bcType=$qjRow1["bcType"];
	
			$ThisHoldDays=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
			
			$qjAllHours=$qjAllHours+$ThisHoldDays;
			$n++;
		}while ($qjRow1 = mysql_fetch_array($qjResult1));
		
	}

	$qjAllHours=round($qjAllHours-$NodeductDays);
	

	if($qjAllHours>120) {
		$Remark="$chooseYear 年请事假  $qjAllHours 小时; " ;
		$Months=1; //大于15天，小于等于一个月(30天)扣一个月
		if($qjAllHours>240 && $qjAllHours<=720) {
			$BS=$qjAllHours/240;
			$Times=round($BS);
			$Months=3*$Times;
		}
		else {
			if($qjAllHours>720) {
				$BS=$qjAllHours/240;
				$Times=round($BS);
				$Months=5*$Times;				
			}
		}
	   //插入到表中
		$inRecode="INSERT INTO $DataPublic.rs_kcgl (Id,Number,Month,Months,Remark,Date,Estate,Locks,Operator)
			VALUES  (NULL,$Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator' ) ";
		$inResult=@mysql_query($inRecode);
		if($inResult){
			echo "&nbsp;&nbsp; $Name ($Number ) --$Month 开始扣工龄 成功 <br> ";
			}
		else{
			echo "&nbsp;&nbsp; $Name ($Number ) --$Month 开始扣工龄 失败! $inRecode <br> ";
		  }	
	}
		
    }while ($myRow = mysql_fetch_array($myResult));		
  }
}  

?>
</form>
</body>
</html>
<script language = "JavaScript">
function BeginS(){
		document.getElementById('Begin').disabled=true;
		document.getElementById('chooseYear').readOnly='readOnly';
		document.form1.action="rs_kcgl_change.php";
		document.form1.submit();
}
</script>