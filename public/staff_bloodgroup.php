<?php 
//代码共享 EWEN 2012-10-29
include "../model/modelhead.php";
echo"
<table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
<tr class=''>
<td class='A1111' width='50' align='center' rowspan='2'>序号</td>
<td class='A1101' width='60' align='center' rowspan='2'>部门</td>
<td class='A1101'  align='center' colspan='5' height='25'>血型类型</td></tr>
<tr align='center' class=''>
    <td height='25' width='60' class='A0101'>Ｏ型</td>
    <td width='60' class='A0101'>A型</td>
    <td width='60' class='A0101'>B型</td>
    <td width='60' class='A0101'>AB型</td>
    <td width='60' class='A0101'>未设置</td>
  </tr>";
//部门
$NumSum0=$NumSum1=$NumSum2=$NumSum3=$NumSum4=0;
$checkSql = mysql_query("SELECT Id,Name FROM $DataPublic.branchdata WHERE Estate=1 AND (cSign='$Login_cSign' OR cSign='0') ORDER BY SortId",$link_id);
if($checkRow = mysql_fetch_array($checkSql)) {
	$i=1;
	do{
		$Num0=$Num1=$Num2=$Num3=$Num4=0;
		$Id=$checkRow["Id"];
		$Name=$checkRow["Name"];
		
		echo"<tr align='center'><td height='25'  class='A0111'>$showPurchaseorder$i</td><td class='A0101'>$Name</td>";
		 $checkNumSql=mysql_query("SELECT count(*) AS Num,B.BloodGroup 
												FROM $DataPublic.staffmain A 
												LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number 
												WHERE A.BranchId='$Id' AND A.Estate=1 AND (A.cSign='$Login_cSign' OR A.cSign='0') GROUP BY B.BloodGroup",$link_id);
		 if($checkNumRow=mysql_fetch_array($checkNumSql)){
			 do{
				 $Num=$checkNumRow["Num"];
				 $BloodGroup=$checkNumRow["BloodGroup"];
				 $TempNum="Num".strval($BloodGroup); 
				 $$TempNum=$Num;	
				 $TempNumSum="NumSum".strval($BloodGroup); 
				 $$TempNumSum+=$Num;	
				 }while($checkNumRow=mysql_fetch_array($checkNumSql));
			 }
		 $Num0=$Num0==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','$Id','0')>".$Num0."</span>";
		 $Num1=$Num1==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','$Id','1')>".$Num1."</span>";
		 $Num2=$Num2==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','$Id','2')>".$Num2."</span>";
		 $Num3=$Num3==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','$Id','3')>".$Num3."</span>";
		 $Num4=$Num4==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','$Id','4')>".$Num4."</span>";
    	echo"<td class='A0101' >$Num1</td><td class='A0101'>$Num2</td><td class='A0101'>$Num3</td><td class='A0101'>$Num4</td><td class='A0101'>$Num0</td></tr>";
		echo"<tr id='TrShow$i' style='display:none;background:#cccccc;'><td colspan='7' style=' valign='top' class='A0111'><div id='DivShow$i' style='display:none;'></div><br></td></tr>";//隐藏的行
		$i++;
		}while($checkRow = mysql_fetch_array($checkSql));
	//小计
	$NumSum0=$NumSum0==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','','0')>".$NumSum0."</span>";
	$NumSum1=$NumSum1==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','','1')>".$NumSum1."</span>";
	$NumSum2=$NumSum2==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','','2')>".$NumSum2."</span>";
	$NumSum3=$NumSum3==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','','3')>".$NumSum3."</span>";
	$NumSum4=$NumSum4==0?"&nbsp;":"<span style='CURSOR: pointer;color:#FF3300' onClick=ShowSheet('TrShow$i','DivShow$i','','4')>".$NumSum4."</span>";
	echo"<tr align='center' class=''><td class='A0111' align='center' colspan='2' height='25'>合计</td>
	<td class='A0101'>$NumSum1</td>
	<td class='A0101'>$NumSum2</td>
	<td class='A0101'>$NumSum3</td>
	<td class='A0101'>$NumSum4</td>
	<td class='A0101'>$NumSum0</td></tr>
	";
	echo"<tr id='TrShow$i' style='display:none;background:#cccccc;'><td colspan='7' style=' valign='top' class='A0111'><div id='DivShow$i' style='display:none;'></div><br></td></tr>";//隐藏的行
	}
echo"</table>";
?>
<script language="JavaScript" type="text/JavaScript">
function ShowSheet(TrId,DivId,BID,BGID){//隐藏行ID,隐藏行DIV,部门ID,血型ID
 ShowDiv=eval(DivId);
 ShowTr=eval(TrId);
 ShowTr.style.display=(ShowTr.style.display=="none")?"":"none";
 ShowDiv.style.display=(ShowDiv.style.display=="none")?"":"none";
var url="staff_bloodgroup_ajax.php?BID="+BID+"&BGID="+BGID;
 var ajax=InitAjax();
 ajax.open("GET",url,true);
 ajax.onreadystatechange =function(){
 　　if(ajax.readyState==4 && ajax.status ==200 && ajax.responseText!=""){
 　　　 var BackData=ajax.responseText;
   			ShowDiv.innerHTML=BackData;
   			}
  		}
 	ajax.send(null); 
 	}
</script>