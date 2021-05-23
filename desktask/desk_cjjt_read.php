
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
/*
电信---yang 20120801
已更新
*/
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
include "kqcode/kq_function.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间津贴分析");
$funFrom="desk_cjrtj";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|55|日期|45|星期|60|上班人数|70|总工时|70|需求支出|70|实际支出|70|总津贴|70|班长津贴10%|80|员工津贴90%|80|说明|150";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3";
$salary=$salary==""?100:$salary;

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT T.Id,J.Name,T.JobId 
	FROM $DataIn.sc1_counttype T
	LEFT JOIN $DataPublic.jobdata J ON J.Id=T.JobId
	 WHERE T.StatisticalType=1 ORDER BY T.Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='Tid' id='Tid' onchange='ResetPage(this.name)'>";
		do{
			$theTid=$myrow["Id"];
			$theName=$myrow["Name"];
			$Tid=$Tid==""?$theTid:$Tid;
			if ($Tid==$theTid){
				echo "<option value='$theTid' selected>$theName</option>";
				$SearchRows=" AND S.Tid='$theTid'";
				$JobId=$myrow["JobId"];
				}
			else{
				echo "<option value='$theTid'>$theName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	$date_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month 
	FROM $DataIn.sc1_cjtj S WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY S.Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
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
	$days=$chooseMonth==date("Y-m")?date("j"):date("t",strtotime($DateNow));
	for($d=1;$d<=$days;$d++){
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$weekDay=date("w",strtotime($DateNow));	
		if($weekDay==0 || $weekDay==6){
			$theDefaultColor="#FFA6D2";
			}
		$weekInfo="星期".$Darray[$weekDay];
		$SearchDay=" AND S.Date='$DateNow'";
		$NeedPay=0;
		$WorksA=0;
		$WorksB=0;
		$Hours=0;
		$showPurchaseorder="";
		//读取该部门不用考勤的员工数量
		$checkKqSql=mysql_query("SELECT count(*) FROM $DataPublic.staffmain WHERE JobId=$JobId AND KqSign>1 AND Estate=1",$link_id);
		$WorksA = mysql_num_rows($checkKqSql);
		/////////////////////////////////////////////////////
		//如果是水贴，则分贴A和贴B
		if($JobId==21){
			$SearchRows=" AND S.Tid IN(7,8)";
			}
		$mySql="SELECT SUM(S.Qty*C.Price) AS NeedPay,T.JobId 
		FROM $DataIn.sc1_cjtj S
		LEFT JOIN $DataIn.sc1_counttype T ON T.Id=S.Tid
		LEFT JOIN $DataIn.cg1_stocksheet C ON C.POrderId=S.POrderId
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=C.StuffId
		WHERE 1 $SearchRows $SearchDay AND A.TypeId=T.TypeId GROUP BY T.JobId";
		$myResult = mysql_query($mySql." $PageSTR",$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			$NeedPay=sprintf("%.0f",$myRow["NeedPay"]); //需求金额
			}
		include "desk_cjrtj_work.php" ;  //调用考勤列表，每天统计，工作时间，及加班时间
		/////////////////////////////////////////////////////
		$Works=$WorksA+$WorksB;
		$HoursYG=$Hours;
		$Hours+=$WorksA*10;
		$FactPay=$Hours*10;//班长一天100元
		$Sum_Works+=$Works;
		$Sum_Hours+=$Hours;
		$Sum_NeedPay+=$NeedPay;
		$Sum_FactPay+=$FactPay;
		$SubValue=$NeedPay-$FactPay;
		$SubValue=$SubValue>0?$SubValue:0;//总津贴
		//班长津贴
		$JJA=sprintf("%.0f",$SubValue*0.1);
		$JJB=$SubValue-$JJA;
		$Sum_SubValue+=$SubValue;
		$Sum_JJA+=$JJA;
		$Sum_JJB+=$JJB;
		
		$RemarkA=$WorksA>0?"&nbsp;&nbsp;班长:".$WorksA."人":"";
		$RemarkB=$WorksB>0?"&nbsp;&nbsp;员工:".$WorksB."人":"";
		$Remark=$RemarkA.$RemarkB;
		//读取生产备注:Tid,Date,Remark
		$checkRemarkRow=mysql_fetch_array(mysql_query("SELECT Remark FROM $DataIn.sc1_Remark WHERE Tid=$Tid AND Date='$DateNow'",$link_id));
		$scRemark=$checkRemarkRow["Remark"]==""?"&nbsp;":$checkRemarkRow["Remark"];
		$TempJJ=sprintf("%.3f",($SubValue*0.9)/$HoursYG);
		$TempJJ=$TempJJ>0?$TempJJ:0;
		$SubValue=SpaceValue0($SubValue);
		$JJA=SpaceValue0($JJA);
		$JJB=SpaceValue0($JJB);
		$Hours=$WorksB>0?"<a href='desk_cjrtj_kq.php?Date=$DateNow&JobId=$JobId&JJ=$TempJJ' target='_black'>".$Hours."</a>":$Hours;
		$ValueArray=array(
			array(0=>$weekInfo,			1=>"align='center'"),
			array(0=>$Works,			1=>"align='right'"),
			array(0=>$Hours,			1=>"align='right'"),
			array(0=>$NeedPay,			1=>"align='right'"),
			array(0=>$FactPay,			1=>"align='right'"),
			array(0=>$SubValue,			1=>"align='right'"),
			array(0=>$JJA,			1=>"align='right'"),			
			array(0=>$JJB,			1=>"align='right'"),
			array(0=>$Remark),
			);
		$checkidValue=$i;
		
		////////////////////////////////////////////////
		if($WorksB>0){
			//传递月份
			$DivNum="a".$i;
			$TempId="$DateNow|$DivNum|$Tid";//日期/层参数/统计分类
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cjrtj_b\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif'' width='13' height='13' style='CURSOR: pointer'>";
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
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>
		<td class='A0111' width='160' height='20'>合计</td>
		<td class='A0101' width='70' align='right'>$Sum_Works</td>
		<td class='A0101' width='70' align='right'>$Sum_Hours</td>
		<td class='A0101' width='70' align='right'>$Sum_NeedPay</td>
		<td class='A0101' width='70' align='right'>$Sum_FactPay</td>
		<td class='A0101' width='70' align='right'>$Sum_SubValue</td>
		<td class='A0101' width='80' align='right'>$Sum_JJA</td>
		<td class='A0101' width='80' align='right'>$Sum_JJB</td>
		<td class='A0101' width='150' align='right'>&nbsp;</td>
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
	var tempTid=document.getElementById("Tid").value;
	var tempMonth=document.getElementById("chooseMonth").value;
	var tempscRemark=document.form1.scRemark.value;
	var Remark=encodeURIComponent(tempscRemark);
	myurl="desk_cjrtj_updated.php?Tid="+tempTid+"&Month="+tempMonth+"&Day="+tempTableId+"&Remark="+Remark;
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
