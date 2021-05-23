<?php   
//电信-zxq 2012-08-01
/*
在系统计划任务中增加任务列表运行
功能：每天按时统计生成最新数据
*/
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
$Login_cSign=7;
include "d:/website/ac/basic/parameter.inc";
include "d:/website/ac/model/modelfunction.php";
include "d:/website/ac/model/subprogram/sys_parameters.php";

$Today=date("Y-m-d");
$base_dir=dirname(__FILE__);
//USD汇率
	$checkCurrency=mysql_fetch_array(mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Symbol='USD' ORDER BY Id LIMIT 1",$link_id));
	$USDstr=$checkCurrency["Symbol"];
	$USDRate=sprintf("%.2f",$checkCurrency["Rate"]);
	$USDInfo=$USDstr."汇率:".$USDRate;
	$HzRate=$HzRate*100;
        
//生成桌面统计数据
/*
   case 120://现金结存
   case 121://审核通过未结付总额
   case 122://未收客户货款总额
   case 123://未出货订单总额
   case 189://订单净利分类统计
   case 190://产品净利分类统计
   case 198://本月已出货电子类产品所占比例
 */
$TResult = mysql_query("SELECT L.ItemId,L.Title,L.Extra 
              FROM $DataPublic.tasklistdata L 
              WHERE L.TypeId>1 AND L.Estate=1 AND L.TypeId<6 AND L.ItemId in (120,121,122,123,189,190,198) ORDER BY L.TypeId,L.Oby",$link_id);
    
if($TRow = mysql_fetch_array($TResult)){
	do{
		$ItemId=$TRow["ItemId"];
		$Title=$TRow["Title"];
		$Extra=$TRow["Extra"];
		
                $newSubtask=0;
                
                $subtask_File=$base_dir . "/subtask/subtask-" .$ItemId . ".inc";
               
                $contentSTR="";
		include "subtask/subtask-".$ItemId.".php"; 
                                  //$OutputInfo.=$contentSTR;
                $contentSTR="<?php   \$OutputInfo.=\"" . $contentSTR . "\";?>";
                $fp = fopen($subtask_File, "w");
                fwrite($fp, $contentSTR);
                fclose($fp); 
	    }while ($TRow = mysql_fetch_array($TResult));
	}
        
//加入当天生产小组成员

$checkYsql=mysql_query("SELECT Id FROM $DataIn.sc1_memberset WHERE Date='$Today'",$link_id);
if(!$checkYrow=mysql_fetch_array($checkYsql)){
    if ($DataIn=='ac'){
		$ChartinRecode="INSERT INTO $DataIn.sc1_memberset SELECT NULL,GroupId,Number,KqSign,'$Today','0','10002','1','0','10002',NOW(),'10002',NOW() FROM $DataPublic.staffmain WHERE BranchId>4 AND Estate=1 AND cSign='$Login_cSign'";
	}else{
		$ChartinRecode="INSERT INTO $DataIn.sc1_memberset SELECT NULL,GroupId,Number,KqSign,'$Today','0','10002' FROM $DataPublic.staffmain WHERE BranchId>4 AND Estate=1 AND cSign='$Login_cSign'";
	}
	$inAction=@mysql_query($ChartinRecode);
}

//更新年假、补假数据
/*
$checkHsql=mysql_query("SELECT Id FROM $DataPublic.staffholiday WHERE Date='$Today' LIMIT 1",$link_id);
//echo "SELECT Id FROM $DataPublic.staffholiday WHERE Date='$Today'";
echo mysql_num_rows($checkHsql);
if (mysql_num_rows($checkHsql)<=0){ 
*/
$chooseYear=date('Y');
$LastYear=$chooseYear-1;
include "d:/website/ac/model/kq_YearHolday.php";
$checkMsql=mysql_query("SELECT Number,ComeIn FROM $DataPublic.staffmain WHERE Estate>0",$link_id);
while($checkMrow=mysql_fetch_array($checkMsql)){
       $Number=$checkMrow["Number"];
	  //年假
	   $ComeIn=$checkMrow["ComeIn"];
	   $ComeInY=substr($ComeIn,0,4);
		//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
		$ValueY=$chooseYear-$ComeInY;
		if (substr($ComeIn,5,5)=="01-01") $ValueY+=1;	
				
		$DefaultLastM=$chooseYear."-12-01";
		$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$CountDays=date("z",strtotime($ThisEndDay))+1;	//年假当年总天数
				
		//计算休假工时  1~9年:5天,10~19:10天,20年以上的 15天
		//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,2) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
	
        //echo "SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')";
		if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
		    do{
		       $bcType=$qjTimeRow["bcType"];
			   $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   if($end_Year>$LastYear)$EndDate=$LastYear."-12-31 17:00:00";
			   
			   $HourTotal=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
		
			   $HourTotal=$HourTotal<0?0:$HourTotal;
				 
			   $sumQjTime+=$HourTotal/8;
		      }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays-$sumQjTime;
				$AnnualLeave=intval((5*8*$inDays)/$CountDays);
				if($ValueY>10){
					$AnnualLeave=intval((10*8*$inDays)/$CountDays);
					}
				if($ValueY>20){
					$AnnualLeave=intval((15*8*$inDays)/$CountDays);
					}			
				}
			else{
				if($ValueY==1){
					$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
					$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					}
				else{
					$AnnualLeave=0;
					$inDays=0;
					}
				}
	    $hasAnnual = getLastYearLeave($Number, $DataIn,$link_id);
		if($hasAnnual == 0)	{
			$AnnualLeave = 0;
		}
		$qjAllDays=HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;
	    $YearDays=intval($AnnualLeave/8)-$qjAllDays;
	    $YearDays=$YearDays<0?0:$YearDays;
	 // $YearDays=GetYearHolDays($Number,$Today,"",$DataIn,$DataPublic,$link_id);
      //$YearDays=$YearDays<0?0:$YearDays;
      //补休
      $bxCheckSql = "Select  Sum(hours) as hours From $DataPublic.bxSheet Where Number = '$Number'";
	  $bxCheckResult =mysql_fetch_array(mysql_query($bxCheckSql,$link_id));
       $bxHours=$bxCheckResult["hours"]*1;
       
     if ($bxHours>0){
	        $usedBxHours=0;
	        $bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$Number' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
			$bxQjCheckResult = mysql_query($bxQjCheckSql,$link_id);
			
			while($bxQjCheckRow = mysql_fetch_array($bxQjCheckResult))
			{
				$startTime = $bxQjCheckRow["StartDate"];
				$endTime = $bxQjCheckRow["EndDate"];
				$bcType = $bxQjCheckRow["bcType"];
				$usedBxHours+= GetBetweenDateDays($Number,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
				
			}
			// echo "$Number ----$bxHours-$usedBxHours </br>";
			$bxHours-=$usedBxHours;
	}
	
    //$bxHours=getTakeDeferredHolidays($Number,$DataIn,$DataPublic,$link_id);
    $bxDays=$bxHours>0?$bxHours/8:0;
     if (is_float($bxDays) && abs($bxDays-round($bxDays))>=0.1) {
            $bxDays=number_format($bxDays,1);
     }
     else{
          $bxDays=number_format($bxDays);
     }
    
     $IN_main="INSERT  INTO $DataPublic.staffholiday(Id,Number,YearDays,BxDays,Date)VALUES(NULL,'$Number','$YearDays','$bxDays','$Today') ON DUPLICATE KEY UPDATE YearDays='$YearDays',BxDays='$bxDays',Date='$Today'";
     //echo $IN_main . "</br>";
     $In_Result=@mysql_query($IN_main,$link_id);
}
//}

//更新损益表数据
$FromPage="System_dayautorun";
 include "d:/website/ac/iphoneAPI/ManagerApp/report/syb_report.php";

$OutputInfo=$Today . "数据系统已自动生成！\r\n";
$fp = fopen($base_dir . "/subtask/system_dayautorun.log", "a");
fwrite($fp, $OutputInfo);
fclose($fp);
?>