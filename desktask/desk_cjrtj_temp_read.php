<?php   
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//include "kqcode/kq_function.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 试用期月人工分析");
$funFrom="desk_cjrtj";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|55|日期|45|星期|60|上班人数|70|总工时|70|需求支出|70|预估支出|70|差值|70|差值率%|70|说明|180";
//$ActioToS="1,3";
$Keys=31;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
//月份
	$date_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month FROM $DataIn.sc1_cjtj S WHERE 1 AND S.Date>='2011-06-01' GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY S.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" AND DATE_FORMAT(K.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
	

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
if($chooseMonth==""){
	echo"没有资料";
	}
else{
	$i=1;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",0);
	$DefaultBgColor=$theDefaultColor;
	$Sum_Works=0;
	$Sum_Hours=0;
	$Sum_FactPay=0;
	$DateNow=$chooseMonth."-01";//当月首日
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$days=$chooseMonth==date("Y-m")?date("j")-1:date("t",strtotime($DateNow));
	for($d=1;$d<=$days;$d++){
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$weekDay=date("w",strtotime($DateNow));	
		if($weekDay==0 || $weekDay==6){
			$theDefaultColor="#FFA6D2";
			}
		$weekInfo="星期".$Darray[$weekDay];
		$NeedPay=0;
		$WorksA=0;	//不考勤员工总数
		$WorksB=0;	//考勤员工总数
		$Hours=0;
		
		$SearchDay=$SearchRows." AND K.Date='$DateNow'";
		
		$checkTWFlag="D";
		include "desk_cjrtj_tempwtime.php";
		$WorksB=$WorkNumber;
		$Hours=$kqWorkTime;
		$FactPay=$kqygAmount;
		/////////////////////////////////////////////////////
		$Works=$WorksA+$WorksB;
		$HoursYG=$Hours;
		$Sum_Works+=$Works;
		$Sum_Hours+=$Hours;
		$SubValue=$NeedPay-$FactPay;
		$NeedPaySTR=$NeedPay;//用于页面显示
		$Sum_NeedPay+=$NeedPay;
		if($FactPay>0){//过滤之前无效数据
			$Sum_FactPay+=$FactPay;
			}
		
		$SubPer=sprintf("%.0f",($SubValue*100)/$FactPay)."%";
		$RemarkA=$WorksA>0?"&nbsp;&nbsp;非考勤:".$WorksA."人":"";
		$RemarkB=$WorksB>0?"&nbsp;&nbsp;考勤:".$WorksB."人":"";
		$Remark=$RemarkA.$RemarkB;
	
		$TempJJ=sprintf("%.1f",($SubValue*0.9)/$HoursYG);
		$TempJJ=$TempJJ>0?$TempJJ:0;
		$Remark=$Remark==""?"&nbsp;":$Remark;
		$Hours=$WorksB>0?"<a href='desk_cjrtj_temp_kq.php?Date=$DateNow&JJ=$TempJJ' target='_black'>".$Hours."</a>":$Hours;
		$Locks=1;
		$ValueArray=array(
			array(0=>$weekInfo,			1=>"align='center'"),
			array(0=>$Works,			1=>"align='right'"),
			array(0=>$Hours,			1=>"align='right'"),
			array(0=>$NeedPaySTR,			1=>"align='right'"),
			array(0=>$FactPay,			1=>"align='right'"),
			array(0=>$SubValue,			1=>"align='right'"),
			array(0=>$SubPer,			1=>"align='right'"),			
			array(0=>$Remark)
			);
		$checkidValue="$DateNow|$NeedPay|$FactPay";
		////////////////////////////////////////////////
		include "../model/subprogram/read_model_6.php";
		$DateNow=date("Y-m-d",strtotime("$DateNow + 1 day"));
		}//end for
		//统计
	$Sum_SubValue=$Sum_NeedPay-$Sum_FactPay;
	$TempJQ=$TempJQ>0?$TempJQ:0;
	$Sum_SubPer=sprintf("%.0f",($Sum_SubValue*100)/$Sum_FactPay)."%";
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>
		<td class='A0111' width='160' height='20'>合计</td>
		<td class='A0101' width='70' align='right'>$Sum_Works</td>
		<td class='A0101' width='70' align='right'>$Sum_Hours</td>
		<td class='A0101' width='70' align='right'>$Sum_NeedPay</td>
		<td class='A0101' width='70' align='right'>$Sum_FactPay</td>
		<td class='A0101' width='70' align='right'>$Sum_SubValue</td>
		<td class='A0101' width='70' align='right'>$Sum_SubPer</td>
		<td class='A0101' width='180' align='right'>&nbsp;</td>
		</tr></table>";
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
