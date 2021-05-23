<?php 
/*
mc 验厂文件 ewen 2013-08-03 OK
验厂模式2013-05后的记录
工作日：加班时间截止20:00,超点与直落单独计算，不在此页面显示
周六：加班截止时间为20:00，超点与直落单独计算，不在此页面显示
周日：独立计算，不在此页面显示
临时班：工作时间超过10小时，按10小时计，超点不在此页面显示
要求：每周加班时间不超过60小时，超出的另计

原模式：
全部显示，直浇工时累加至加班工时中

修改：
#1、签退分上述两类处理
#2、验厂模式过滤周日记录
#3、直落记录只在原模式进行处理
*/
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("../model/modelhead.php");
	include_once("$path/public/kqClass/Kq_pbSet.php");
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_totleItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");


$ActioToS="";
if($CheckDate==""){
	$CheckDate=date("Y-m-d");
	}
$CheckMonth=substr($CheckDate,0,7);
$SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
$selStr="selFlag" . $KqSign;
$$selStr="selected";
$SelectCode=$SelectCode. "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
      <option  value=''   $selFlag>--全部--</option> 
      <option  value='1'  $selFlag1>考勤有效</option>
	   <option value='2' $selFlag2>考勤参考</option>
	  </select>";
$KqSignStr="";	
if ($KqSign!="") {
	$KqSignStr=" AND M.KqSign='$KqSign'";
	}
$SaveFun="&nbsp;";
$CustomFun="<a href='kq_checkio_print.php?CheckDate=$CheckDate' target='_blank' $onClickCSS>列印</a>&nbsp;&nbsp;";
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：星期处理
$NowToDay=date("Y-m-d");//
//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));	 
$weekInfo="星期".$Darray[$weekDay];

echo"<input name='kqList' type='hidden' id='kqList'>";
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>
<tr><td colspan='27' class='A0011'>当天是：$weekInfo</td></tr>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='60' rowspan='2' class='A1101'><div align='center'>小组</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>现职</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='120' colspan='3' class='A1101'><div align='center'>加点加班工时(+直落)</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='160' colspan='9' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
 	</tr>
  	<tr class=''>
		<td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='38' class='A0101' align='center'>1.5倍</td>
		<td width='38' class='A0101' align='center'>2倍</td>
		<td width='38' class='A0101' align='center'>3倍</td>
		<td width='20' class='A0101' align='center'>事</td>
		<td width='20' class='A0101' align='center'>病</td>		
		<td width='20' class='A0101' align='center'>无</td>
		<td width='20' class='A0101' align='center'>年</td>
		<td width='20' class='A0101' align='center'>补</td>
		<td width='20' class='A0101' align='center'>婚</td>
		<td width='20' class='A0101' align='center'>丧</td>
		<td width='20' class='A0101' align='center'>产</td>
		<td width='20' class='A0101' align='center'>工</td>
	</tr>";
//3		读取需统计的有效的员工资料
/*有效员工的条件：
	需要考勤且没有调动记录的（即入职起就需要考勤）；
	或有调动记录,则取考勤月份比调动生效月份大的最小那个月份的调入状态；
	同时员工的入职月份要少于或等于考勤那个月份
	且员工不在离职日期少于考勤月份的员工

*/
	$pbSet = new KqPbSet();
	$MySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE M.cSign='$Login_cSign' $KqSignStr AND
((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
	M.Number IN(
		SELECT K.Number FROM $DataPublic.redeployk K 
			INNER JOIN(
				SELECT Number,max(Month) as Month FROM $DataPublic.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month 	
			WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
	M.Number IN(
		SELECT Ka.Number FROM $DataPublic.redeployk Ka 
			INNER JOIN(
				SELECT Number,min(Month) as Month FROM $DataPublic.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
			WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
ORDER BY M.BranchId,M.GroupId,M.Number";

//echo "$MySql";    /////////////////////////////////////////////////////////////
	$i=1;
	$Qty_SUM=0;
	$Amount_SUM=0;
	$Result = mysql_query($MySql,$link_id);
	if($myrow = mysql_fetch_array($Result)) 
	{
		do{
	
			$Name=$myrow["Name"];
			$Number=$myrow["Number"];
			include_once("../model/subprogram/factoryCheckDate.php");
			if(skipStaff($Number))
			{
				continue;
			}
			
			$GroupName=$myrow["GroupName"];
			$Job=$myrow["Job"];
			
			$ioResultSql = "SELECT CheckTime,CheckType,KrSign 
			FROM $DataIn.checkinout 
			WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
			order by CheckTime";
			$ioResult = mysql_query($ioResultSql,$link_id);
			$inTime = "";
			$outTime = "";
			while($ioResultRow = mysql_fetch_assoc($ioResult))
			{
				$checkTime=$ioResultRow["CheckTime"];
				$checkType=$ioResultRow["CheckType"];
				$krSign=$ioResultRow["KrSign"];
			
				switch($checkType)
				{
					case "I":
					{
						$inTime = $checkTime; 
					}
					break;
					case "O":
					{
						$outTime = $checkTime;
					}
					break;
				}	
			}
			
			$dayItem = new KqDailyItem($inTime, $outTime, $CheckDate);
			$dayItem->setupDateType($CheckDate, $Number,$DataIn, $DataPublic, $link_id);
			
			$otHours = new KqOtHourSet($Number,$CheckDate, $DataIn, $DataPublic, $link_id);
		
			//开始计算加班时间
			if(($otHours->getOtHours($dayItem->dateType) != 0 && ($dayItem->dateType == "X" || $dayItem->dateType == "Y")) || $dayItem->dateType == "G")
			{
				$dayItem->calculateHours($Number, $otHours->getOtHours($dayItem->dateType), $otHours->zlHours, $pbSet, $DataIn, $DataPublic, $link_id);
			}
			else
			{
				if($dayItem->dateType == "X" || $dayItem->dateType == "Y")
				{
					$dayItem->checkInTime = "";
					$dayItem->checkOutTime = "";
				}
			}

			
		//////////////////////////////////////////////////////////////////////////////////////////////////////////

		
			echo"<tr align='center'><td class='A0111'>$i</td>";
			echo"<td class='A0101'>$Number</td>";
			echo"<td class='A0101' align='left'>$Name</td>";
			echo"<td class='A0101' align='left'>$GroupName</td>";
			echo"<td class='A0101' align='left'>$Job</td>";
			echo"<td class='A0101' align='left'><div $DateTypeColor>".$dayItem->dateType.$dayItem->dayInfomation." </div></td>";
			echo"<td class='A0101' align='center'><span $AIcolor>".$dayItem->returnCheckInTime()."</span></td>";
			echo"<td class='A0101' align='center'><span $AOcolor>".$dayItem->returnCheckOutTime()." </span></td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->workTime)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->realWorkTime)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->jbTime)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->sxTime)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->jrTime)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->beLate)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->leaveEarly)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->privateLeave)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->sickLeave)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->noWageLeave)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->annualLeave)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->notBusyLeave)." </td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->marriageLeave)." </td>";// $test
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->funeralLeave)."</td>";
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->maternityLeave)."</td>";// $qjTest
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->injuryLeave)."</td>";// $qjTest
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->absenteeismHours)."</td>";
			echo "<td class='A0101' align='center'>".zerotospace($dayItem->queQingHours)."</td>";	
			//echo"<td class='A0101' align='center'>$KGTime</td>";	
			echo"<td class='A0101' align='center'>".zerotospace($dayItem->nightShit)."</td>";
			echo"</tr>";
			
		$i++;
		}while ($myrow = mysql_fetch_array($Result));
	    
	}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='25' scope='col' height='60' class='A0111' align='center'><p>暂时还没有资料。</td></tr>";
	}  
	  
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
		echo"
			<tr class=''>
				<td rowspan='2' class='A0111'><div align='center'>序号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>编号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
				<td rowspan='2' class='A0101'><div align='center'>小组</div></td>
				<td rowspan='2' class='A0101'><div align='center'>现职</div></td>
				<td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
				<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
				<td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
				<td colspan='3' class='A0101'><div align='center'>加点加班工时(+直落)</div></td>
				<td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
				<td rowspan='2' class='A0101'><div align='center'>早退</div></td>
				<td colspan='9' class='A0101'><div align='center'>请、休假工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>夜班</div></td>
			</tr>
			<tr class=''>
				<td heigh38t='20'  align='center' class='A0101'>签到</td>
				<td class='A0101' align='center'>签退</td>
				<td class='A0101' align='center'>1.5倍</td>
				<td class='A0101' align='center'>2倍</td>
				<td class='A0101' align='center'>3倍</td>
				<td class='A0101' align='center'>事</td>
				<td class='A0101' align='center'>病</td>		
				<td class='A0101' align='center'>无</td>
				<td class='A0101' align='center'>年</td>
				<td class='A0101' align='center'>补</td>
				<td class='A0101' align='center'>婚</td>
				<td class='A0101' align='center'>丧</td>
				<td class='A0101' align='center'>产</td>
				<td class='A0101' align='center'>工</td>
			</tr></table>";
//步骤5：
include "../model/subprogram/add_model_b.php";
echo"<br>";
//include "../model/subprogram/read_model_menu.php";
?>
<input name="CheckNote" type="hidden" id="CheckNote" value="<?php  echo $CheckNote?>"/>
<script language="javascript" type="text/javascript">
function SaveDaytj(){
        var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message){
		   var kqList="";
		   for(var m=3; m<kqTable.rows.length-2; m++){
		         for(var n=9;n<13;n++){
				 var s=kqTable.rows[m].cells[n].innerText;
				      s=s.replace(/(^\s*)/g,"");
				   if(s.length>0){
		               kqList=kqList+","+
				      kqTable.rows[m].cells[1].innerHTML+"^^"+
					  kqTable.rows[m].cells[9].innerText+"^^"+
					  kqTable.rows[m].cells[10].innerText+"^^"+
					  kqTable.rows[m].cells[11].innerText+"^^"+
					  kqTable.rows[m].cells[12].innerText;
					  break;
					  }
				    }
                }	
		    //alert(kqList);
			document.getElementById("kqList").value=kqList;
			document.form1.action="kq_checkio_updated.php?ActionId=kq";
			document.form1.submit();
			}
		else{
			return false;
			}

    
}
</script>
