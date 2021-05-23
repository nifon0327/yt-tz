<?php 
include "../model/modelhead.php";
include "../model/subprogram/read_datain.php";
include "public_appconfig.php";
include "../model/kq_YearHolday.php";
?>
<title>员工详细资料</title>
<style>
  img{
	  width:30px;
	  height:40:px;
	  margin:0;
	  padding:0;
	  }
body {
	background-color: #FFF;
	}
  </style>
<body onkeydown="unUseKey()"   oncontextmenu="event.returnValue=false"   onhelp="return false;">
  <table border="0" cellpadding="2" cellspacing="1" style="background-color:#666;-webkit-print-color-adjust:exact;">
    <tr bgcolor="#CCCCCC" align="center">
      <td style="width:35px;height:20px;">照片</td>
      <td style="width:50px;">姓名</td>
      <td style="width:60px;">入职时间</td>
      <td style="width:60px;">在职时间</td>
      <td style="width:80px;">电话</td>
      <td style="width:30px;">籍贯</td>
      <td style="width:35px;">请假</td>
      <td style="width:70px;">上次请假</td>
      <td style="width:65px;">月薪情况</td>
       <td style="width:55px;">2012年假</td>
       <td style="width:55px;">2013年假</td>
      <td style="width:50px;">介绍人</td>
    </tr>
    <?php
    $checkSql=mysql_query("SELECT A.Number,A.Name,A.ComeIn,B.Photo,B.Mobile,C.Name AS Rpr,D.Name AS Introducer
		FROM $DataPublic.staffmain A
		LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number
		LEFT JOIN $DataPublic.rprdata C ON C.Id=B.Rpr
		LEFt JOIN $DataPublic.staffmain D ON D.Number=A.Introducer
		WHERE A.Id>30 AND A.Id<40 AND A.Estate=1 ORDER BY A.ComeIn",$link_id);
if($checkRow=mysql_fetch_array($checkSql)){
	$i=1;
	do{
    	$imgPath="";
		$Number=$checkRow["Number"];
	   	$Photo=$checkRow["Photo"];	//照片
	   	if(	$Photo==1){
		   	$imgPath="$StaffPhotoPath/p".$Number.".jpg";
		   	}
	   	$Name=$checkRow["Name"];//姓名
	   	$ComeIn=$checkRow["ComeIn"];	//入职时间
		include "subprogram/staff_model_gl.php";
	   	$Mobile=$checkRow["Mobile"];		//电话
	   	$Rpr=$checkRow["Rpr"];				//籍贯
	   
		//薪资统计
     	$xzSql=mysql_query("
			SELECT MAX(Amount) MaxAmount,Min(Amount) AS MinAmount,avg(Amount) AS AvgAmount,Amount,SUM(Amount) AS AmountSUM FROM (
			SELECT SUM(Amount) AS Amount,Month FROM(
			SELECT (Amount+Jz+Sb+Otherkk+Kqkk+RandP-taxbz) AS Amount,Month FROM $DataIn.cwxzsheet WHERE Number=$Number
			UNION ALL
			SELECT Amount AS Amount,Month FROM $DataIn.hdjbsheet WHERE Number=$Number 
			)Z GROUP BY Month ORDER BY Month DESC
			) Y
			",$link_id);
		if($xzRow=mysql_fetch_array($xzSql)){
			$MaxAmount=sprintf("%.0f",$xzRow["MaxAmount"]);
			$MinAmount=sprintf("%.0f",$xzRow["MinAmount"]);
			$AvgAmount=sprintf("%.0f",$xzRow["AvgAmount"]);
			$Amount=sprintf("%.0f",$xzRow["Amount"]);
			$AmountSUM=sprintf("%.0f",$xzRow["AmountSUM"]);
			}
		//奖金计算
		//年休假计算####################
		//入职当年
		$ComeInY=substr($ComeIn,0,4);
		//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
		$chooseYear=2012;
		$LastYear=$chooseYear-1;
		$NextYear=$chooseYear+1;
		
		$ValueY=$chooseYear-$ComeInY;
				
		$DefaultLastM=$chooseYear."-12-01";
		$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
				
		//计算休假工时  1~9年:5天,10~19:10天,20年以上的 15天
		//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
		if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
		    do{
		       $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
			   	$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		        $Days=intval($HoursTemp/24);//取整求相隔天数
			    //分析请假时间段包括几个休息日/法定假日/公司有薪假日
			    $HolidayTemp=0;     //初始假日数
			    //分析是否有休息日
			     $isHolday=0;  //0 表示工作日
			     $DateTemp=$StartDate;
			     $DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			      for($n=0;$n<=$Days;$n++){
				         $isHolday=0;  //0 表示工作日
				         $DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				         $weekDay=date("w",strtotime("$DateTemp"));	 
				         if($weekDay==6 || $weekDay==0){
					             $HolidayTemp=$HolidayTemp+1;
					             $isHolday=1;
					             }
				         else{
					           //读取假日设定表
					              $holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					              if($holiday_Row = mysql_fetch_array($holiday_Result)){
						            $HolidayTemp=$HolidayTemp+1;
						            $isHolday=1;
						        }
					     }            
				         //分析是否有工作日对调
				         if($isHolday==1){  //节假日上班，所以其休息时间要减
					        $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					         if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							  $HolidayTemp=$HolidayTemp-1;
					          }				
			           	}			
				else{  //非节假日调班，则其休息时间要加,
					     $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					      if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					     }
			        }              
		      	}
			     //计算请假工时
			     $Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			     $HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
			     $HourTotal=$HourTotal<0?0:$HourTotal;   //有时假，只请半天，但调假一天，所以要去掉
			     $sumQjTime+=$HourTotal/8;
		       }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays-$sumQjTime;
				$AnnualLeave=intval((5*8*$inDays)/$CountDays);
				if($ValueY>=10){
					$AnnualLeave=intval((10*8*$inDays)/$CountDays);
					}
				if($ValueY>=20){
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
		$qjAllDays=HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;
		$qjAllDays2=HaveYearHolDayDays($Number,$NextYear."-01-01 00:00:00",$NextYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;
		//明年休假计算
		$ValueY=$NextYear-$ComeInY;
		$DefaultLastM=$NextYear."-12-01";
		$NextEndDay=$NextYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$NextCountDays=date("z",strtotime($NextEndDay));	//年假当年总天数
		    
			if($ValueY>1){	//年份间隔在2以上的
				$NextAnnualLeave=5*8;
				if($ValueY>=10){
					$NextAnnualLeave=10*8;
					}
				if($ValueY>=20){
					$NextAnnualLeave=15*8;
					}					
				}
			else{
				if($ValueY==1){
					$NextinDays=abs(strtotime($NextEndDay)-strtotime($ComeIn))/3600/24-$NextCountDays;
					$NextAnnualLeave=intval((5*8*$NextinDays)/$NextCountDays);
					}
				else{
					$NextAnnualLeave=0;
					}
				}					
		//年休假计算#######################
		$Introducer=$checkRow["Introducer"];
		$bgColor=$i%2==0?"bgcolor='#E8E8E8'":"bgcolor='#FFFFFF'";
		
		//请假统计
     	$qjSql=mysql_fetch_array(mysql_query("SELECT count(*) AS qjNum,left(StartDate,10) AS StartDate,left(EndDate,10) AS EndDate FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND StartDate>=DATE_SUB(now(), INTERVAL 12 Month) ORDER BY EndDate DESC",$link_id));
		$qjNum=$qjSql["qjNum"];
		$StartDate=$qjSql["StartDate"]."<br>/<br>".$qjSql["EndDate"];
		$qjNum=$qjNum>0?$qjNum."次":"&nbsp;";
		$AnnualLeave1=intval($AnnualLeave/8);
		if($AnnualLeave1>0){
			if($AnnualLeave1==$qjAllDays){
				$AnnualLeave1="<span class='redB'>$AnnualLeave1/$qjAllDays"."天</span>";
				}
			else{
				if($qjAllDays>0){
					$AnnualLeave1="<span class='blueN'>$AnnualLeave1/$qjAllDays"."天</span>";
					}
				else{
					$AnnualLeave1="<span class='greenN'>$AnnualLeave1/$qjAllDays"."天</span>";
					}
				}
			}
		else{
			$AnnualLeave1="&nbsp;";
			}
		$NextAnnualLeave2=intval($NextAnnualLeave/8);
		if($NextAnnualLeave2>0){
			if($NextAnnualLeave2==$qjAllDays2){
				$NextAnnualLeave2="<span class='redB'>$NextAnnualLeave2/$qjAllDays2"."天</span>";
				}
			else{
				if($qjAllDays2>0){
					$NextAnnualLeave2="<span class='blueN'>$NextAnnualLeave2/$qjAllDays2"."天</span>";
					}
				else{
					$NextAnnualLeave2="<span class='greenN'>$NextAnnualLeave2/$qjAllDays2"."天</span>";
					}
				}
			}
		else{
			$NextAnnualLeave2="&nbsp;";
			}
?>
    <tr <?php echo $bgColor;?>>
      <td align="center"><?php  echo "<img src='$imgPath'>";?></td>
      <td valign="bottom" ><?php  echo $Name;?></td>
      <td valign="bottom" ><?php  echo $ComeIn;?></td>
      <td valign="bottom" ><?php echo $Gl_STR;?></td>
      <td valign="bottom" ><?php  echo $Mobile;?></td>
      <td valign="bottom" ><?php echo $Rpr;?></td>
      <td valign="bottom"  align="right"><?php echo $qjNum;?></td>
      <td valign="bottom" align="center"><?php echo $StartDate;?></td>
      
      <td valign="bottom" align="center"><?php 
	  echo "<div style='width:60px;text-align:right'>$MaxAmount</div>";
	  echo "<div style='width:60px;text-align:center'>$AvgAmount</div>";
	  echo "<div style='width:60px;text-align:left'>$MinAmount</div>";
	  ?></td>
      <td valign="bottom"  align="right"><?php echo $AnnualLeave1?></td>
      <td valign="bottom"  align="right"><?php echo $NextAnnualLeave2;?></td>
      <td valign="bottom" ><?php echo $Introducer;?></td>
    </tr>
    <?php
		$i++;
		}while ($checkRow=mysql_fetch_array($checkSql));
	}
	?>
  </table>
</form>
</body>
</html>
