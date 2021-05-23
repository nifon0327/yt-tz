<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw11_staffjj
$DataPublic.branchdata
$DataPublic.jobdata
$DataPublic.staffmain
二合一已更新
*/
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
echo "<link rel='stylesheet' href='../model/shadow.css'>";
include "../model/modelhead.php";

$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=18;
$sumCols="11,13,14,15";
$tableMenuS=600;
ChangeWtitle("$SubCompany 奖金列表");
$funFrom="cw_jj";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|所属公司|60|奖金项目|120|部门|70|职位|60|员工ID|50|员工姓名|60|工龄<br>Y(M)|40|计算月份|110|比率参数|60|总金额|80| 结付比率|60|本次结付金额|80|个税|40|实付|80|状态|40|请款月份|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,3,4,7,8,14";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT ItemName FROM $DataIn.cw11_jjsheet WHERE 1 GROUP BY ItemName order by ItemName DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["ItemName"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
			    $ChooseItemName=$dateValue;
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="AND S.ItemName='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}	  	
		$TimeResult=mysql_query("SELECT S.JfTime FROM $DataIn.cw11_jjsheet S WHERE  1 $SearchRows GROUP  BY S.JfTime  ",$link_id);
		if($TimeRow=mysql_fetch_array($TimeResult)){
			echo"<select name='JfTime' id='JfTime' onchange='document.form1.submit()'>";
		   do{
		          $thisJfTime=$TimeRow["JfTime"];
		          $timeValue="第". $thisJfTime."次结付";
		          if($JfTime=="")$JfTime= $thisJfTime;
		          if($JfTime==$thisJfTime){
				       echo"<option value='$thisJfTime' selected>$timeValue</option>";
				          $SearchRows.="AND S.JfTime='$thisJfTime'";
			     	     }
			    else{
				    echo"<option value='$thisJfTime'>$timeValue</option>";					
				      }
			    }while ($TimeRow=mysql_fetch_array($TimeResult));
			    echo"</select>&nbsp;";
		    }
		    
		    	//选择员工类别
       $kqSelectSign="kqSelectSign" .$chooseKqSign ;
       $$kqSelectSign=" selected ";
       echo"<select name='chooseKqSign' id='chooseKqSign' onchange='document.form1.submit()'>";
       echo"<option value='' $kqSelectSign>全部</option>";
       echo"<option value='1' $kqSelectSign1>固定薪</option>";
       echo"<option value='2' $kqSelectSign2>非固定薪</option>";
       echo"</select>&nbsp;";
    	 
    	switch($chooseKqSign){
    	   case 1:$SearchRows.="AND P.kqSign>'1'";break; 
    	   case 2:$SearchRows.="AND P.kqSign='1'";break; 
    	}
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.ItemName,B.Name AS Branch,W.Name AS Job,S.Number,P.Name,P.ComeIn,S.Month,S.MonthS,S.MonthE,S.Divisor,S.Rate,S.Amount,S.Estate,S.Locks,S.Date,P.Name AS Operator,P.Estate AS PEstate,F.Idcard ,S.JfRate,S.JfTime,S.RandP,S.jjAmount,S.cSign 
FROM $DataIn.cw11_jjsheet S 
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=S.JobId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
LEFT JOIN $DataPublic.staffsheet F ON F.Number=S.Number 
WHERE 1 $SearchRows ORDER BY CONVERT(P.Name USING gbk),P.BranchId,P.JobId,P.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemName=$myRow["ItemName"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$PEstate=$myRow["PEstate"];
		$staffName=$PEstate==0?"<div class='redB'>$Name</div>":$Name;
		
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
       $Idcard=$myRow["Idcard"];
		$Rate=$myRow["Rate"]*100/100;
		
		 $RandP = $myRow["RandP"];
		$Amount=$myRow["Amount"];
		$jjAmount=$myRow["jjAmount"];
		
		$JfRate=$myRow["JfRate"];
		$JfTime=$myRow["JfTime"];
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
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator="&nbsp;";
		//$Operator=$myRow["Operator"];
		//include "../model/subprogram/staffname.php";
		$ComeIn=$myRow["ComeIn"];
		$chooseMonth="";
		include "subprogram/staff_model_gl.php";
		$TotalResult=mysql_query("SELECT Amount FROM $DataIn.cw11_jjsheet_frist WHERE ItemName='$ItemName' AND Number='$Number'",$link_id);
		//echo "SELECT Amount FROM $DataIn.cw11_jjsheet_frist WHERE ItemName='$ItemName' AND Number='$Number'";
		if (mysql_num_rows($TotalResult)>0){
		$TotalAmount =mysql_result($TotalResult,0,"Amount");
		}
		else{
			$TotalAmount=0;
		}
		$ItemName=$ItemName."--".$JfTime;
		
		$TableCellId= 'ListTable' . $i;
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$ItemName, 1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$staffName, 	1=>"align='center'"),
			array(0=>$Gl, 			1=>"align='right'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$Rate."%", 	1=>"align='center'"),
			array(0=>$TotalAmount, 	1=>"align='center'"),
			array(0=>$JfRate, 	1=>"align='center'"),
			array(0=>$jjAmount, 	1=>"align='center'"),
			array(0=>$RandP, 	1=>"align='center'" ,2=>" onmousedown='window.event.cancelBubble=true;' onclick='updateValue(\"$TableCellId\",$Id,\"$ItemName / $Name\")' style='CURSOR: pointer;'  style='CURSOR: pointer'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Month,	 	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
    
                $TempId="$Number|$ChooseItemName";//日期/层参数/统计分类
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cw_jj\",\"public\");' id='ThisImg_$DivNum$i' title='$TempId' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
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
		include "../model/subprogram/read_model_6.php";
                echo $HideTableHTML;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);

echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0>
     <input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
	
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
	
?>

<script  src='../model/IE_FOX_MASK.js'  type="text/javascript"></script>
<script language="JavaScript" type="text/JavaScript">

function updateValue(TableCellId,runningNum,staffName){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var RandP = eval(TableCellId).rows[0].cells[14].innerHTML;
	
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(theDiv.style.visibility=="hidden"){
		//document.form1.ActionTableId.value=TableCellId;//表格名称
		InfoSTR=staffName + "个税金额:<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='30' class='TM0000' style='display:none;'>&nbsp;<input name='RandP' type='text' id='RandP' size='50' value='"+RandP+"' class='INPUT0100'>&nbsp;&nbsp;&nbsp;<br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateValue("+TableCellId+")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'></div>";

		infoShow.innerHTML=InfoSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function aiaxUpdateValue(TableCellId){
	//var tempTableId=document.form1.ActionTableId.value;
	var temprunningNum=document.form1.runningNum.value;
	var tempmyRandP=document.form1.RandP.value;

   
	if (fucCheckNUM(tempmyRandP,'Price')=="0"){
		alert("请输入正确的金额!");
		return;
	}
	myurl="../public/cw_jj_updated.php?Id="+temprunningNum+"&RandP="+tempmyRandP+"&ActionId=909";

	var ajax=InitAjax(); 
	ajax.open("GET",myurl,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
		    if (ajax.responseText.substr(ajax.responseText.length-1, 1)=='Y'){
		           eval(TableCellId).rows[0].cells[14].innerHTML=tempmyRandP;
		           var payAmount = eval(TableCellId).rows[0].cells[13].innerHTML *1;
		           eval(TableCellId).rows[0].cells[15].innerHTML= payAmount - tempmyRandP ;
		    }
		    else{
			      alert("更新个税失败!");
		    }
		    
	        CloseDiv();
			}
		}
	ajax.send(null); 	
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
	
</script>

