<?php 
//电信-EWEN
$ActioToS="";
if($CheckDate==""){
	$CheckDate=date("Y-m-d");
	}
$CheckMonth=substr($CheckDate,0,7);
$SelectCode="<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='javascript:document.form1.submit();'>&nbsp;
<select name='CountType' id='CountType' onchange='document.form1.submit()'><option value='0' $CountType0>日考勤统计</option><option value='1' $CountType1>月考勤统计</option></select>";
$SaveFun="<span onClick='SaveDaytj()' $onClickCSS>保存</span>&nbsp;";
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：星期处理
$ToDay=$CheckDate;//计算当天
$NowToDay=date("Y-m-d");//
//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));	 
$weekInfo="星期".$Darray[$weekDay];
$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
$jbTimes=0;
$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	$jbTimes=$holidayRow["jbTimes"];
	switch($holidayRow["Type"]){
	case 0:		$DateTypeTemp="W";		break;
	case 1:		$DateTypeTemp="Y";		break;
	case 2:		$DateTypeTemp="F";		break;
		}
	}
echo"<input name='kqList' type='hidden' id='kqList'>";
echo"<table width='$tableWidth'  border='0' cellspacing='0' bgcolor='#FFFFFF' id='kqTable'>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>编号</div></td>
		<td width='60' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='70' rowspan='2' class='A1101'><div align='center'>小组</div></td>
		<td width='70' rowspan='2' class='A1101'><div align='center'>现职</div></td>
		<td width='60' rowspan='2' class='A1101'><div align='center'>日期类别</div></td>
		<td height='15' width='160' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='60' rowspan='2' class='A1101'><div align='center'>实到工时</div></td>
 	</tr>
  	<tr class=''>
		<td width='80' height='15'  align='center' class='A0101'>签到</td>
		<td width='80' class='A0101' align='center'>签退</td>
	</tr>";

$MySql="SELECT M.Number,M.Name,J.Name AS Job,G.GroupName 
FROM $DataIn.lw_staffmain M 
LEFT JOIN $DataIn.jobdata J ON J.Id=M.JobId 
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE M.Estate=1";

$i=1;
$Qty_SUM=0;
$Amount_SUM=0;
$Result = mysql_query($MySql,$link_id);
if($myrow = mysql_fetch_array($Result)) {
	do{
		$test="";		$AIcolor="";	$AOcolor="";	
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		
		$aiTime=0;		$aoTime=0;	    $WorkTime=0;	
		$DateType=$DateTypeTemp;
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$GroupName=$myrow["GroupName"];
		$Job=$myrow["Job"];
		//读取班次
		include "../public/kqcode/checkio_model_pb.php";
		//读取签卡记录:签卡记录必须是已经审核的
		
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.lw_checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) order by CheckTime",$link_id);
		if($ioRow = mysql_fetch_array($ioResult)) {
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));			
						$aoTime=date("H:i",strtotime("$CheckTime"));						
						break;
					}					
				}while ($ioRow = mysql_fetch_array($ioResult));
			//读取当天的班次时间
			}

		

		
		
		echo"<tr><td class='A0111' align='center'>$i</td>";
		echo"<td class='A0101' align='center' $NumberColor >$Number</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'>$GroupName</td>";
		echo"<td class='A0101' align='center'>$Job</td>";
		echo"<td class='A0101' align='center'><div $DateTypeColor>$DateType</div></td>";
		echo"<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101' align='center'>$WorkTime</td>";
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
				<td rowspan='2' class='A0101'><div align='center'>日期类别</div></td>
				<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
				<td rowspan='2' class='A0101'><div align='center'>实到工时</div></td>
			</tr>
			<tr class=''>
				<td height='20'  align='center' class='A0101'>签到</td>
				<td class='A0101' align='center'>签退</td>
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
