<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php 
include "../model/modelhead.php";
$sumCols="7";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 助学小孩费用申请");
$funFrom="childstudyfee";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属公司|60|申请月份|70|员工姓名|100|小孩姓名|100|性别|40|申请金额|60|凭证|60|备注|250|目前就读年级|180|状态|40|更新日期|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,4,14";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$DefaultMonth="2014-02-01";
	$NewMonth=date("Y-m");
	$Months=intval(abs((date("Y")-2014)*12+date("m")));
	for($i=$Months-2;$i>=0;$i--){
		$dateValue=date("Y-m",strtotime("$i month", strtotime($DefaultMonth))); 
		$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$optionStr.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				$optionStr.="<option value='$dateValue'>$dateValue</option>";					
				}
		}
	}
	echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>$optionStr</select>&nbsp;";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT   S.Id,S.Amount,S.Remark,S.Month,S.Attached,S.Date,S.Estate,S.Locks,S.Operator,M.Name,B.Name AS Branch,J.Name AS Job,A.Number,A.ChildName,A.Sex,C.Name AS ClassName,M.Estate AS mEstate,S.cSign
FROM  $DataIn.cw19_studyfeesheet   S 
LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
LEFT JOIN $DataPublic.childclass C ON C.Id=S.NowSchool
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$Dir=anmaIn("download/childinfo/",$SinkOrder,$motherSTR);
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];		
		$ChildName=$myRow["ChildName"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Amount=$myRow["Amount"];
		$ClassName=$myRow["ClassName"];
		$Remark=$myRow["Remark"];
		$Attached=$myRow["Attached"];
		$Month=$myRow["Month"];
	    $JobName=$myRow["Job"];
		$BranchName=$myRow["Branch"];
		$Estate=$myRow["Estate"];
	switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$checkPay= mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.cw19_studyfeemain WHERE Id='$Mid' LIMIT 1",$link_id));
				$PayDate=$checkPay["PayDate"];
				$Estate="<div align='center' class='greenB' title='已结付,结付日期：$PayDate'>√</div>";
				$LockRemark="记录已经结付，强制锁定操作！";
				$Locks=0;
				break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];

		$Attached=$myRow["Attached"];
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			//$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
               $Attached="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"childstudyfee_file\",\"$Id\")' src='../images/edit.gif' title='上传凭证' width='13' height='13'>&nbsp;&nbsp;&nbsp;<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
			}
		else{
               $Attached="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"childstudyfee_file\",\"$Id\")' src='../images/edit.gif' title='上传凭证' width='13' height='13'>";
			}

 if($Remark!=""){
               $Remark="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"childstudyfee_Remark\",\"$Id\")' src='../images/edit.gif' title='更新备注' width='13' height='13'>&nbsp;&nbsp;&nbsp;$Remark";
             }
     else{
               $Remark="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"childstudyfee_Remark\",\"$Id\")' src='../images/edit.gif' title='更新备注' width='13' height='13'>";
          }

		if($myRow["mEstate"]==0){
			$Name="<div class='redB'>$Name</div>";
			}
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$ChildName,1=>"align='center'"),
			array(0=>$Sex,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Attached),
			array(0=>$Remark),
			array(0=>$ClassName,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,9,3,$Id,\"$Month\",\"$ChildName\")' style='CURSOR: pointer'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script>
function updateJq(TableId,RowId,toObj,sId,Month,ChildName){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 3:
				InfoSTR=Month+ChildName+"&nbsp;就读年级&nbsp;&nbsp;<select id='ClassId' name='ClassId' style='width:180px;'><option value='' 'selected'>请选择</option>";		
				<?PHP 
					$ChildResult = mysql_query("SELECT Id,Name FROM $DataPublic.childclass WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($ChildRow = mysql_fetch_array($ChildResult)){
				  do{
					           $echoInfo.="<option value='$ChildRow[Id]'>$ChildRow[Name]</option>";
					  } while($ChildRow = mysql_fetch_array($ChildResult));
			      }
				?>
				 InfoSTR=InfoSTR+"<?PHP echo $echoInfo; ?>"+"</select><br>";
				break;
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate("+sId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(sId){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	switch(ObjId){
			case "3":		
			var tempClassId=document.form1.ClassId.value;
			myurl="childstudyfee_ajax.php?Id="+sId+"&tempClassId="+tempClassId;
			var ajax=InitAjax(); 
			 ajax.open("GET",myurl,true);
			 ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){// && ajax.status ==200
					  eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=ajax.responseText;
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		}
	}
</script>