<?php 
//内部模式:OK ewen2013-07-30
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=23;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 考勤月统计");
$funFrom="kq_stat";
$nowWebPage=$funFrom."_read";
$sumCols="";		//求和列
$Th_Col="选项|40|序号|30|月份|50|部门|50|职位|50|员工ID|50|姓名|50|应到<br>工时|38|实到<br>工时|38|标|30|超|30|直|30|标|30|超|30|直|30|标|30|超|30|直|30|迟到<br>次数|38|迟到<br>次数|38|事假|30|病假|30|有薪|30|无薪|30|缺勤工时|38|无效工时|38|旷工工时|38|有薪工时|38|夜班次数|38";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,4,7,8,11";//26,
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	$date_Result = mysql_query("SELECT Month FROM $DataIn.kqdata WHERE 1 group by month order by Id DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.="and K.Month='$chooseMonth'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
		}	
		$FormalSign=$FormalSign==""?0:$FormalSign;
		$selStr="selFlag" . $FormalSign;
		$$selStr="selected";
		echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
		     <option value='0' $selFlag0>全部</option>
			 <option value='1' $selFlag1>正式工</option>
			 <option value='2' $selFlag2>试用期</option>";
		echo "</select>&nbsp;";
		if($FormalSign>0)$SearchRows.=" AND M.FormalSign='$FormalSign'";	
		
	    $SelectTB="M";$SelectFrom=1; 
	    //选择地点
	    include "../model/subselect/WorkAdd.php"; 	

		$selStr="selFlag" . $KqSign;
		$$selStr="selected";
		echo "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();''>
			  <option  value=''   $selFlag>--全部--</option> 
			  <option  value='1'  $selFlag1>考勤有效</option>
			   <option value='2' $selFlag2>考勤参考</option>
			  </select>";
		$KqSignStr="";	
		if ($KqSign!="") {
			$KqSignStr=" AND M.KqSign='$KqSign'";
		}	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//List_Title($Th_Col,"1",0);
?>
 <table border='0'    cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border-top-width:0px' >
    <tr height='20' class='' align='center'>
      <td width='40' rowspan='2' class='A1111'>选项</td>
      <td width='30' rowspan='2' class='A1101'>序号</td>
      <td width='50' rowspan='2' class='A1101'>月份</td>
      <td width='50' rowspan='2' class='A1101'>部门</td>
      <td width='50' rowspan='2' class='A1101'>职位</td>
      <td width='50' rowspan='2' class='A1101'>员工ID</td>
      <td width='50' rowspan='2' class='A1101'>姓名</td>
      <td width='38' rowspan='2' class='A1101'>应到<br>工时</td>
      <td width='38' rowspan='2' class='A1101'>实到<br>工时</td>
      <td colspan="3" class='A1101'>1.5倍薪工时</td>
     <td colspan="3" class='A1101'>2倍薪工时</td>
     <td colspan="3" class='A1101'>3倍薪工时</td>
      <td width='38' rowspan='2' class='A1101'>迟到<br>次数</td>
      <td width='38' rowspan='2' class='A1101'>早退<br>次数</td>
      <td colspan='4' class='A1101'>请、休假工时</td>
      <td width='38' rowspan='2' class='A1101'>缺勤<br>工时</td>
      <td width='38' rowspan='2' class='A1101'>无薪<br>工时</td>
      <td width='38' rowspan='2' class='A1101'>旷工<br>工时</td>
      <td width='38' rowspan='2' class='A1101'>有薪<br>工时</td>        
      <td width="38" rowspan='2' class='A1101'>夜班<br>次数</td>
    </tr>
    <tr class='' align='center'>
    <td width='30' class='A0101' height='20'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
    <td width='30' class='A0101'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
    <td width='30' class='A0101'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
    <td width='30' class='A0101'>事假</td>
    <td width='30' class='A0101'>病假</td>
    <td width='30' class='A0101'>有薪</td>
    <td width='30' class='A0101'>无薪</td>
    </tr>
  </table>

	<?php 
//从财务数据表读取有效员工数据:入职日期必须在当月之前，否则未到职；离职日期不能少于当月第一天，否则已离职
//  C.CheckTime LIKE '$chooseMonth%'
$i=1;
$j=($Page-1)*$Page_Size+1;
$mySql = "SELECT K.Id,K.Month,K.Number,K.Dhours,K.Whours,
K.Ghours,K.GOverTime,K.GDropTime,
K.Xhours,K.XOverTime,K.XDropTime,
K.Fhours,K.FOverTime,K.FDropTime,
K.InLates,K.OutEarlys,K.SJhours,K.BJhours,K.YXJhours,K.WXJhours,K.QQhours,
K.WXhours,K.KGhours,K.dkhours,K.YBs,K.Locks,M.Name,M.Estate,B.Name AS Branch,J.Name AS Job
FROM $DataIn.kqdata K 
LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
LEFt JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows  $KqSignStr  ORDER BY K.Month DESC,M.Estate DESC,M.BranchId,M.JobId,K.Number";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Month=$myRow["Month"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];		
		$Job=$myRow["Job"];		
        

		$Dhours=zerotospace($myRow["Dhours"]);		//应到工时
		$Whours=zerotospace($myRow["Whours"]);		//实到工时
		$Ghours=zerotospace($myRow["Ghours"]);				//1.5倍薪工时-标
		$GOverTime=zerotospace($myRow["GOverTime"]);	//1.5倍薪工时-超
		$GDropTime=zerotospace($myRow["GDropTime"]);	//1.5倍薪工时-直
		
		$Xhours=zerotospace($myRow["Xhours"]);				//2倍薪工时-标
		$XOverTime=zerotospace($myRow["XOverTime"]);	//2倍薪工时-超
		$XDropTime=zerotospace($myRow["XDropTime"]);	//2倍薪工时-直
		
		$Fhours=zerotospace($myRow["Fhours"]);				//3倍薪工时-标
		$FOverTime=zerotospace($myRow["FOverTime"]);	//3倍薪工时-超
		$FDropTime=zerotospace($myRow["FDropTime"]);	//3倍薪工时-直
		if($Month<"2013-04"){//4月份之前按原来的方式去计算
			//2倍加班工时
			$XhoursResult=mysql_fetch_array(mysql_query("SELECT xHours FROM $DataIn.hdjbsheet WHERE Number=$Number and Month='$Month'",$link_id));
			$Xhours=$XhoursResult["xHours"];
			$Xhours=zerotospace($Xhours);		//2倍工时
			}
		$InLates=zerotospace($myRow["InLates"]);	//迟到次数
		$OutEarlys=zerotospace($myRow["OutEarlys"]);//早退次数
		$SJhours=zerotospace($myRow["SJhours"]);	//事假工时
		$BJhours=zerotospace($myRow["BJhours"]);	//病假工时
		$BXhours=zerotospace($myRow["BXhours"]);	//补休工时 
		$YXJhours=zerotospace($myRow["YXJhours"]);	//有薪假工时:婚、丧等有薪假
		$WXJhours=zerotospace($myRow["WXJhours"]);	//无薪假工时
		$QQhours=zerotospace($myRow["QQhours"]);	//缺勤工时
		$WXhours=zerotospace($myRow["WXhours"]);	//无效工时
		$KGhours=zerotospace($myRow["KGhours"]);	//旷工工时
		$dkhours=zerotospace($myRow["dkhours"]);	//有薪工时
		$YBs=zerotospace($myRow["YBs"]);			//夜班次数
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		//分离职和未离职
		if($Estate==1){//在职
			$Name="<a href='kq_stat_reportm.php?Number=$Number&CheckMonth=$Month&CountType=1' target='_blank'><span class='greenB'>$Name</span></a>";
			}
		else{			//离职
			$Name="<a href='kq_stat_reportm.php?Number=$Number&CheckMonth=$Month&CountType=1' target='_blank'><span class='redB'>$Name</span></a>";
			}
		//是否已生成薪资，是，则强制锁定
		$LockRemark="";
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.cwxzsheet WHERE Month='$chooseMonth' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
		if($checkRow = mysql_fetch_array($checkMonth)){
			$LockRemark="该月已生成薪资,禁止修改.";
			}
		$ValueArray=array(
			array(0=>$Month,	1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job, 		1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Dhours, 	1=>"align='center'"),
			array(0=>$Whours,	1=>"align='center'"),
			array(0=>$Ghours, 	1=>"align='center'"),
			array(0=>$GOverTime, 	1=>"align='center'"),
			array(0=>$GDropTime, 	1=>"align='center'"),		
			array(0=>$Xhours, 	1=>"align='center'"),
			array(0=>$XOverTime, 	1=>"align='center'"),
			array(0=>$XDropTime, 	1=>"align='center'"),		
			array(0=>$Fhours, 	1=>"align='center'"),
			array(0=>$FOverTime, 	1=>"align='center'"),
			array(0=>$FDropTime, 	1=>"align='center'"),
			array(0=>$InLates,	1=>"align='center'"),
			array(0=>$OutEarlys,1=>"align='center'"),
			array(0=>$SJhours,	1=>"align='center'"),
			array(0=>$BJhours,	1=>"align='center'"),
			array(0=>$YXJhours,	1=>"align='center'"),
			array(0=>$WXJhours,	1=>"align='center'"),
			array(0=>$QQhours,	1=>"align='center'"),
			array(0=>$WXhours,	1=>"align='center'"),
			array(0=>$KGhours,	1=>"align='center'"),
			array(0=>$dkhours,	1=>"align='center'"),
			array(0=>$YBs,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
?>
<table border='0'    cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border-top-width:0px' >
    <tr height='20' class='' align='center'>
      <td width='40' rowspan='2' class='A0111'>选项</td>
      <td width='30' rowspan='2' class='A0101'>序号</td>
      <td width='50' rowspan='2' class='A0101'>月份</td>
      <td width='50' rowspan='2' class='A0101'>部门</td>
      <td width='50' rowspan='2' class='A0101'>职位</td>
      <td width='50' rowspan='2' class='A0101'>员工ID</td>
      <td width='50' rowspan='2' class='A0101'>姓名</td>
      <td width='38' rowspan='2' class='A0101'>应到<br>工时</td>
      <td width='38' rowspan='2' class='A0101'>实到<br>工时</td>
      <td width='30' class='A0101' height='20'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
    <td width='30' class='A0101'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
    <td width='30' class='A0101'>标</td>
    <td width='30' class='A0101'>超</td>
    <td width='30' class='A0101'>直</td>
      
      <td width='38' rowspan='2' class='A0101'>迟到<br>次数</td>
      <td width='38' rowspan='2' class='A0101'>早退<br>次数</td>
      <td width='30' class='A0101'>事假</td>
    <td width='30' class='A0101'>病假</td>
    <td width='30' class='A0101'>有薪</td>
    <td width='30' class='A0101'>无薪</td>
      <td width='38' rowspan='2' class='A0101'>缺勤<br>工时</td>
      <td width='38' rowspan='2' class='A0101'>无薪<br>工时</td>
      <td width='38' rowspan='2' class='A0101'>旷工<br>工时</td>
      <td width='38' rowspan='2' class='A0101'>有薪<br>工时</td>        
      <td width="38" rowspan='2' class='A0101'>夜班<br>次数</td>
    </tr>
    <tr class='' align='center'>
    <td colspan="3" class='A0101'>1.5倍薪工时</td>
     <td colspan="3" class='A0101'>2倍薪工时</td>
     <td colspan="3" class='A0101'>3倍薪工时</td>
    <td colspan='4' class='A0101'>请、休假工时</td>
    </tr>
  </table><?php 
//步骤7：
echo '</div>';
$myResult0 = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult0);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
