<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:500px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php
//2014-01-07 ewen 修正OK
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
include "../public/kqcode/kq_function.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间小组月人工分析");
$funFrom="desk_cjrtj";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|55|日期|45|星期|60|上班人数|70|总工时|70|需求支出|70|预估支出|70|实际支出|70|差值<br>(需求-预估)|70|(差值/预估)%|70|说明|180|备注|350";
$ActioToS="1,3";
$Keys=31;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
//月份
	$date_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month FROM $DataIn.sc1_cjtj S WHERE 1 AND S.Date>='2010-09-01' GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY S.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='CheckMonth' id='CheckMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$CheckMonth=$CheckMonth==""?$dateValue:$CheckMonth;
			if($CheckMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" AND DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				//当月天数
					$checkDays=date("t",strtotime($dateValue."-01"));
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
//小组
	$result = mysql_query("SELECT G.GroupLeader,G.GroupId,G.GroupName
	FROM $DataIn.staffgroup G
	LEFT JOIN $DataIn.sc1_memberset S ON G.GroupId=S.GroupId
	 WHERE 1 $SearchRows AND G.TypeId>0 AND G.Estate=1 GROUP BY S.GroupId ORDER BY G.GroupId",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='GroupId' id='GroupId' onchange='document.form1.submit()'>";
	//	echo "<option value='0' selected>其他</option>";
		do{
			$theGroupId=$myrow["GroupId"];
			$theGroupName=$myrow["GroupName"];
			$theRemark=$myrow["Remark"];
			$GroupId=$GroupId==""?$theGroupId:$GroupId;
			if ($GroupId==$theGroupId){
				echo "<option value='$theGroupId' selected>$theGroupName</option>";
				$SearchRows=" AND S.GroupId='$theGroupId'";
				}
			else{
				echo "<option value='$theGroupId'>$theGroupName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	}
if($GroupId==0){
	$SearchRows=" AND (S.GroupId>600 AND S.GroupId<802)";//辅助组：品检、仓库和厂务
	}
$XZSign=0;//薪资表是否已经生成
//注意：时薪初始化，由于非考勤在职员工会计入统计，所当月无工资的员工（请长假的情况）需做时薪初始化置0值，即此类员工将不计算实际支出
$checkQJSql=mysql_query("SELECT Number FROM $DataPublic.kqqjsheet WHERE StartDate LIKE '$CheckMonth%' OR EndDate LIKE '$CheckMonth%' GROUP BY Number",$link_id);
if($checkQJRow=mysql_fetch_array($checkQJSql)){
	do{
		$sNumber=$checkQJRow["Number"];
		$TempSXSTR="SX".strval($sNumber); 
		$$TempSXSTR=0;								//初始化请假员工的时薪
		}while ($checkQJRow=mysql_fetch_array($checkQJSql));
	}
//预估时薪
include "../model/subprogram/onehoursalary.php";
//实际时薪
//只有当月工资以：6品检、7仓库、8车间三个部门来计算的员工才做统计
$checkKqSqlB=mysql_query("SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/(IFNULL(B.Whours,0)+IFNULL((B.Ghours+B.GOverTime+B.GDropTime)*1.5,0)+IFNULL((B.xHours+B.XOverTime+B.XDropTime)*2,0)+IFNULL((B.FHours+B.FOverTime+B.FDropTime)*3,0)) AS SX
		FROM $DataIn.cwxzsheet A 
		LEFT JOIN $DataIn.kqdata B ON B.Number=A.Number AND B.Month='$CheckMonth'
		WHERE A.Month='$CheckMonth' AND A.Kqsign=1 AND A.BranchId>5
	UNION ALL
	SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/( $checkDays*10) AS SX
		FROM $DataIn.cwxzsheet A 
		WHERE A.Month='$CheckMonth' AND A.KqSign>1 AND A.BranchId>5
",$link_id);
if($checkKqRowB=mysql_fetch_array($checkKqSqlB)){
	$XZSign=1;
	do{
		$sNumber=$checkKqRowB["Number"];
		$TempSXSTR="SX".strval($sNumber); 
		$$TempSXSTR=$checkKqRowB["SX"];	//记录每位员工的时薪
		}while($checkKqRowB=mysql_fetch_array($checkKqSqlB));
	}
			
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0>
	<input name='ActionTableId' type='hidden' id='ActionTableId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
if($CheckMonth==""){
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
	$DateNow=$CheckMonth."-01";//当月首日
	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	$days=$CheckMonth==date("Y-m")?date("j"):date("t",strtotime($DateNow));
	for($d=1;$d<=$days;$d++){//天循环
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$weekDay=date("w",strtotime($DateNow));	
		if($weekDay==0 || $weekDay==6){
			$theDefaultColor="#FFA6D2";
			}
		$weekInfo="星期".$Darray[$weekDay];
		$SearchDay=$SearchRows." AND S.Date='$DateNow'";
		$NeedPay=0;
		$WorksA=0;	//不考勤员工总数
		$WorksB=0;	//考勤员工总数
		$Hours=0;
		$showPurchaseorder="";
		//1､需求支出
		/////////////////////////////////////////////////////
		$mySql="SELECT SUM(S.Qty*C.Price) AS NeedPay 
		FROM $DataIn.sc1_cjtj S 
		LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=S.POrderId 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId 
		WHERE 1 AND S.GroupId='$GroupId' AND DATE_FORMAT(S.Date,'%Y-%m-%d')= '$DateNow' AND A.TypeId=S.TypeId";
		$myResult = mysql_query($mySql." $PageSTR",$link_id);
		if($myRow = mysql_fetch_array($myResult)){//注意：登记到别组的记录
			$NeedPay=sprintf("%.0f",$myRow["NeedPay"]); //需求金额
			}
		//2、预估和实际支出
		include "desk_cjrtj_worktime.php";		
		//非考勤人数和工时$WorkNumber1
		$WorksA=$WorkNumber2;
		//考勤人数和工时
		$WorksB=$WorkNumber1;
		//合计总工时
		$Hours=$kqWorkTime1+$kqWorkTime2;
		//合计小组人数
		$Works=$WorksA+$WorksB;
		$HoursYG=$Hours;
		$EstimatePay=$group1_ygAmount+$group2_ygAmount;					//预估支出
		$FactPay=sprintf("%.0f",$group1_sjAmount+$group2_sjAmount);		//实际支出
		
		$Sum_FactPay+=$FactPay;
		$Sum_Works+=$Works;
		$Sum_Hours+=$Hours;
		$SubValue=$NeedPay-$EstimatePay;
		$NeedPaySTR=$NeedPay;//用于页面显示
		$Sum_NeedPay+=$NeedPay;
		if($EstimatePay>0){//过滤之前无效数据
			$Sum_Estimate+=$EstimatePay;
			}
		else{
			$NeedPaySTR=$NeedPay==0?0:"<div class='redB' title='登记的是其它生产分类'>".$NeedPay."</div>";
			}
		if ($EstimatePay!=0)
		     $SubPer=sprintf("%.0f",($SubValue*100)/$EstimatePay)."%";
		 else
		    $SubPer=0;
		$RemarkA=$WorksA>0?"&nbsp;&nbsp;非考勤:".$WorksA."人":"";
		$RemarkB=$WorksB>0?"&nbsp;&nbsp;考勤:".$WorksB."人":"";
		$Remark=$RemarkA.$RemarkB;
		//读取生产备注:Tid,Date,Remark
		$checkRemarkRow=mysql_fetch_array(mysql_query("SELECT Remark FROM $DataIn.sc1_Remark WHERE GroupId=$GroupId AND Date='$DateNow'",$link_id));
		$scRemark=$checkRemarkRow["Remark"]==""?"&nbsp;":$checkRemarkRow["Remark"];
		if ($HoursYG!=0)
		   $TempJJ=sprintf("%.1f",($SubValue*0.9)/$HoursYG);
		else
		   $TempJJ=0;
		$TempJJ=$TempJJ>0?$TempJJ:0;
		$Remark=$Remark==""?"&nbsp;":$Remark;
		$Hours="<a href='desk_cjrtj_kq.php?Date=$DateNow&GroupId=$GroupId&JJ=$TempJJ' target='_black'>".$Hours."</a>";
		$Locks=1;
		$ValueArray=array(
			array(0=>$weekInfo,			1=>"align='center'"),
			array(0=>$Works,			1=>"align='right'"),
			array(0=>$Hours,			1=>"align='right'"),
			array(0=>$NeedPaySTR,			1=>"align='right'"),
			array(0=>$EstimatePay,			1=>"align='right'"),
			array(0=>$FactPay,			1=>"align='right'"),
			array(0=>$SubValue,			1=>"align='right'"),
			array(0=>$SubPer,			1=>"align='right'"),			
			array(0=>$Remark),
			array(0=>$scRemark,2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateScRemark($i)' style='CURSOR: pointer'",3=>"..."),
			);
		$checkidValue="$DateNow|$NeedPay|$EstimatePay";
		
		////////////////////////////////////////////////
		if($WorksB>0 || $DateNow==date("Y-m-d")){
			//传递月份
			$DivNum="a".$i;
			$TempId="$DateNow|$DivNum|$GroupId";//日期/层参数/统计分类
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cjrtj_b\");' id='ThisImg_$DivNum$i' title='$TempId' name='ThisImg_$DivNum$i' src='../images/showtable.gif'' width='13' height='13' style='CURSOR: pointer'>";
			}
			$HideTableHTML="
				<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
					<tr bgcolor='#B7B7B7'>
						<td class='A0111' height='30'>
							<br>
								<div id='HideDiv_$DivNum$i' align='right'>&nbsp;</div>
							<br>
						</td>
					</tr>
				</table>";
			
		////////////////////////////////////////////////
		include "../model/subprogram/read_model_6.php";
		echo $HideTableHTML;
		$DateNow=date("Y-m-d",strtotime("$DateNow + 1 day"));
		}//end for
		//统计
	$Sum_Estimate=sprintf("%.0f",$Sum_Estimate);
	$Sum_SubValue=$Sum_NeedPay-$Sum_Estimate;
	$TempJQ=$TempJQ>0?$TempJQ:0;
	$Sum_SubPer=$Sum_Estimate>0?sprintf("%.0f",($Sum_SubValue*100)/$Sum_Estimate)."%":0;
	$Th_Col="选项|55|日期|45|星期|60|上班人数|70|总工时|70|需求支出|70|预估支出|70|实际支出|70|差值<br>(需求-预估)|70|(差值/预估)%|70|说明|180|备注|350";
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>
		<td class='A0111' width='165' height='20'>合计</td>
		<td class='A0101' width='70' align='right'>$Sum_Works</td>
		<td class='A0101' width='70' align='right'>$Sum_Hours</td>
		<td class='A0101' width='70' align='right'>$Sum_NeedPay</td>
		<td class='A0101' width='70' align='right'>$Sum_Estimate</td>
		<td class='A0101' width='70' align='right'>$Sum_FactPay</td>
		<td class='A0101' width='70' align='right'>$Sum_SubValue</td>
		<td class='A0101' width='70' align='right'>$Sum_SubPer</td>
		<td class='A0101' width='180' align='right'>&nbsp;</td>
		<td class='A0101' width='349' align='right'>&nbsp;</td>
		</tr></table>";
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function updateScRemark(TableId){//行即表格序号
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var tempTableId=document.form1.ActionTableId.value;//原表格Id
		theDiv.style.top=event.clientY + document.body.scrollTop+'px';									//层的上坐标
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	//层的左坐标
	if(theDiv.style.visibility=="hidden" || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		var tempscRemark=eval("ListTable"+TableId).rows[0].cells[10].innerText;
		InfoSTR="更新"+TableId+"号的备注:<br><textarea name='scRemark' cols='60' id='scRemark' class='INPUT0100'>"+tempscRemark+"</textarea>";
		var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(){
	var tempTableId=document.form1.ActionTableId.value;//表格序号，即日期
	var tempGroupId=document.getElementById("GroupId").value;
	var tempMonth=document.getElementById("CheckMonth").value;
	var tempscRemark=document.form1.scRemark.value;
	var Remark=encodeURIComponent(tempscRemark);
	myurl="desk_cjrtj_updated.php?GroupId="+tempGroupId+"&Month="+tempMonth+"&Day="+tempTableId+"&Remark="+Remark;
	//document.form1.action=myurl;
	//document.form1.submit();
	retCode=openUrl(myurl);
	if (retCode!=-2){
		//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
		eval("ListTable"+tempTableId).rows[0].cells[10].innerHTML="<span onclick='updateScRemark("+tempTableId+")' style='CURSOR: pointer;color:#FF9966'>"+tempscRemark+"</sapn>";
		CloseDiv();
		}
	}
//-->
</script>
