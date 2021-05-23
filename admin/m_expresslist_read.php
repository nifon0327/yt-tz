<?php   
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
//include "../basic/functions.php";
//include "../basic/Parameter_CSS.inc";
?>
<html>
<head>
<?php   
include "../model/characterset.php";
?>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<link rel="stylesheet" href="../model/style/ReadCss.css">
<script src="../basic/functions.js" type=text/javascript></script>
<title>行政费用列表</title>
<body >
<form name="form1" method="post" action="">
<table border="0" cellpadding="0" cellspacing="0" width="980"> 
  <tr>
   <td width="7" ><img name="maintable_r1_c1" src="../images/maintable_r1_c1.gif" width="7" height="26" /></td>
   <td width="800" background="../images/maintable_r1_c2.gif">
   	 
	 <?php   
	echo"<select name='CompanyId' id='CompanyId' size='1' style='width: 100px;' onchange='document.form1.submit()'>";
	$forward_result = mysql_query("SELECT * FROM $DataPublic.freightdata  order by Id",$link_id);
	if($CompanyId==""){
		echo"<option value='' selected>所有货运公司</option>";
		$CompanySTR="";
		}
	else{
		echo"<option value=''>所有货运公司</option>";
		$CompanySTR="and expresssheet.CompanyId=$CompanyId";
		}
	if($forward_myrow = mysql_fetch_array($forward_result)){
		do{
			if($CompanyId==$forward_myrow[CompanyId]){
				echo"<option value='$forward_myrow[CompanyId]' selected>$forward_myrow[Forshort]</option>";}
			else{ 
				echo"<option value='$forward_myrow[CompanyId]'>$forward_myrow[Forshort]</option>";}
			} while ($forward_myrow = mysql_fetch_array($forward_result));
		}
	echo"</select>&nbsp;&nbsp;&nbsp;"; 
	 
	echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		$date_Result = mysql_query("SELECT Date FROM $DataIn.expresssheet WHERE 1  group by DATE_FORMAT(Date,'%Y-%m') order by Date DESC",$link_id);
		
		if ($dateRow = mysql_fetch_array($date_Result)) {
			do{
				$dateValue=date("Y-m",strtotime($dateRow["Date"]));
				$dateText=date("Y年m月",strtotime($dateRow["Date"]));
				if($chooseDate==$dateValue){
					echo  "<option value='$dateValue' selected>$dateText</option>";
					$PEADate="and  DATE_FORMAT(expresssheet.Date,'%Y-%m')='$dateValue'";
					}
				else{
					echo  "<option value='$dateValue'>$dateText</option>";					
					}
				}while($dateRow = mysql_fetch_array($date_Result));
					if($PEADate==""){
					$PEADate="and  DATE_FORMAT(expresssheet.Date,'%Y-%m')='$dateValue'";
					}
			}
	  echo"</select>&nbsp;&nbsp;&nbsp;";
	  if($cwEstate!=2){
	  	echo"<select name='cwEstate' id='cwEstate' onchange='document.form1.submit()'>";
	  	}
	switch($cwEstate){
		case "3"://未结付
			echo"<option value='3' selected>未结付</option><option value='0'>已结付</option>";
			echo"</select>";
			$EstateSTR="and expresssheet.Estate=3";
			$Actions="<span onClick='ChooseAdd(4)' style='CURSOR: pointer;color:#FF6633''>全选</span>&nbsp;&nbsp;
					<span onClick='ChooseMinus(4)' style='CURSOR: pointer;color:#FF6633''>反选</span>&nbsp;&nbsp;
					<span onClick='checkform(0)' style='CURSOR: pointer;color:#FF6633'>结付</span>
					";
			break;
		case "0"://已结付
			echo"<option value='3'>未结付</option><option value='0' selected>已结付</option>";
			echo"</select>";
			$EstateSTR="and expresssheet.Estate=0";
			$Actions="<span  style='CURSOR: pointer;color:#FF6633'>更新结付资料</span>";
			break;
		}
	echo"</select>";
			 ?> 
   </td>
   <td width="35"><img name="maintable_r1_c3" src="../images/maintable_r1_c3.gif" width="35" height="26"/></td>
   <td width="90" align="center" background="../images/maintable_r1_c4.gif" >
		<table border="0" align="center" cellspacing="0" >
   			<tr>
				<td class="readlink" >
					<nobr>&nbsp;&nbsp;
					<?php    echo $Actions;?>
					</nobr>
				</td>
			</tr>
	 </table>
   </td>
   <td width="36" ><img name="maintable_r1_c5" src="../images/maintable_r1_c5.gif" width="34" height="26"/></td>
   <td width="100" background="../images/maintable_r1_c6.gif" >&nbsp;</td>
   <td width="7"><img name="maintable_r1_c7" src="../images/maintable_r1_c7.gif" width="7" height="26"/></td>
  </tr>
  <tr>
   <td background="../images/maintable_r2_c1.gif"></td>
   <td colspan="5">
	<table width="100%" height="37" border="0" align="center" cellspacing="1"  id="ListTable">
	<?php   
	$my_Result = mysql_query("SELECT  S.Id,S.Date,S.ExpressNO,S.CompanyId,S.Pieces,S.Weight,
	S.Amount,S.Type,S.HandledBy,S.Remark,S.Estate,S.Locks,S.Operator,F.Forshort,M.Date AS PayDate
	 FROM $DataIn.expresssheet  S
	 LEFT JOIN $DataIn.expressmain M ON S.MainNumber=M.Number 
	 LEFT JOIN $DataPublic.freightdata  F ON F.CompanyId=S.CompanyId 
	 WHERE 1 $PEADate $EstateSTR $CompanySTR",$link_id);
	$Th_Col="|选项|30|Id|序号|30|Date|寄件日期|80|CompanyId|快递公司|60|ExpressNo|提单号码|100|Pieces|件数|40|Weight|重量|40|Amount|金额|50|Type|寄/到付|60|Type|经手人|60|Remark|备注|210|Estate|费用状态|80|Payee|汇单|60|payDate|结付日期|80|Operator|操作|60";
Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);	
$k=1;
$SUMA0=0;//总金额
$i=1;
if($my_Rows=mysql_fetch_array($my_Result)){
	do{
		$Id=$my_Rows["Id"];
		$Date=$my_Rows["Date"];
		$CompanyId=$my_Rows["CompanyId"];		
		$Pieces=$my_Rows["Pieces"];
		$Weight=$my_Rows["Weight"];
		$Amount=$my_Rows["Amount"];
		$Type=$my_Rows["Type"]==1?"到付":"寄付";		
		$HandledBy=$my_Rows["HandledBy"];		
		$Remark=$my_Rows["Remark"];
		$Estate=$my_Rows["Estate"];
		$Locks=$my_Rows["Locks"];
		$Operator=$my_Rows["Operator"];
		$Forshort=$my_Rows["Forshort"];
		$ExpressNO=$my_Rows["ExpressNO"]==""?"-":"<span onClick='payeeview(\"expressbill\",\"$my_Rows[ExpressNO].jpg\")' style='CURSOR: pointer;color:#FF6633'>$my_Rows[ExpressNO]</span>";
		$Bill=$my_Rows["Bill"]==""?"-":"<span onClick='View(\"billdir\",\"$my_Rows[Bill]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
		$PayDate=$my_Rows["PayDate"]=="0000-00-00"?"-":$my_Rows["PayDate"];
		$Payee=$my_Rows["Payee"]==""?"-":$my_Rows["Payee"];
		//统计
		$SUMA0=sprintf("%.2f",$SUMA0+$Amount);
		//$Payee=$my_Rows["Payee"]==""?"-":"<span onClick='View(\"cwfkout\",\"$my_Rows[Payee]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
		//$Receipt=$my_Rows["Receipt"]==""?"-":"<span onClick='View(\"cwfkout\",\"$my_Rows[Receipt]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
		switch($Estate){
		case "1":
			$Estate="<div align='center' class='rmbB'>未请款</div>";break;
		case "3":
			$Estate="<div align='center' class='yellowB'>未结付</div>";break;
		case "0":
			$Estate="<div align='center' class='greenB'>已结付</div>";break;
			}
		echo"<tr onmousedown='chooseRow(this,$i,\"click\",\"#FFFFFF\",\"#FFE9D2\",\"#FFCC99\",\"express_read\");' onmouseover='setPointer(this,$i,\"over\",\"#FFFFFF\",\"#FFE9D2\",\"#FFCC99\");' onmouseout='setPointer(this,$i,\"out\",\"#FFFFFF\",\"#FFE9D2\",\"#FFCC99\");'>";
		echo"<td class='A0111'><input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id' disabled></td>";
		echo"<td class='A0101' align='center'>$i</td>";
		echo"<td class='A0101' align='center'>$Date</td>";
		//快递公司
		echo"<td class='A0101'>$Forshort</td>";
		echo"<td class='A0101' onmousedown='window.event.cancelBubble=true;'>$ExpressNO</td>";
		echo"<td class='A0101'  align='right'>$Pieces</td>";
		echo"<td class='A0101'  align='right'>$Weight</td>";
		echo"<td class='A0101' align='right'><input type='text' size='8' name='A$i' value='$Amount' class='readonlyINPUT' readonly></td>";
		echo"<td class='A0101' align='center'>$Type</td>";
		echo"<td class='A0101' align='center'>$HandledBy</td>";
		echo"<td class='A0101' align='left'>$Remark</td>";			
		echo"<td class='A0101' align='center'>$Estate</td>";
		echo"<td class='A0101' align='center'>$Bill</td>";		
		echo"<td class='A0101' align='center'>$PayDate</td>";
		echo"<td class='A0101' align='center'>$Operator</td>";
		echo"</tr>";
		$i++;
		}while ($my_Rows=mysql_fetch_array($my_Result));
	echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
	Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);
	//统计
	$SUMA1=0;
	$SUMA2=$SUMA0-$SUMA1;
	echo"<tr bgcolor='#FFCC99' height='30'><td colspan='7'>合 计</td><td align='right'>
	<input type='text' size='8' name='SUMA0' value='$SUMA0' class='readonlyINPUT' readonly></td>
	<td colspan='7'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	选定记录合计：<input type='text' size='8' name='SUMA1' value='0' class='textINPUT' readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	未选定记录合计：<input type='text' size='8' name='SUMA2' value='$SUMA2' class='textINPUT' readonly>
	</td></tr>";
	}
else{
	echo"<tr bgcolor='#FFFFFF' height='60'><td colspan='17' scope='col' height='60' class='A0111' align='center'><p>没有资料。</td></tr>";
	Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);
	}
  ?>
</table>
<?php   
//表尾
$Form="Yes";
Tabletail($i-1,$Page,$Page_count,$Form,$TypeSTR);
WinTitle("快递费用列表");
?>
<script type=text/javascript>
function checkform(temp){
	var chooseID=0;
	switch(temp){
		case 2://请款
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.disabled=false;
					if(e.checked==true){chooseID=chooseID+1;}
					} 
				}
			if(chooseID==0){
				for (var i=0;i<form1.elements.length;i++){
					var e=form1.elements[i];
					if (e.type=="checkbox"){
						e.disabled=true;					
						} 
					}
				alert("没有选取记录!");return false;
				}
			else{
			SignUdates("expresslist","AskPay","");}
			
			break;
		case 3://通过审核
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.disabled=false;
					if(e.checked==true){chooseID=chooseID+1;}
					} 
				}
			if(chooseID==0){
				for (var i=0;i<form1.elements.length;i++){
					var e=form1.elements[i];
					if (e.type=="checkbox"){
						e.disabled=true;					
						} 
					}
				alert("没有选取记录!");return false;
				}
			else{
				SignUdates("expresslist","AskPay_pass","");}
			break;
		case 0://财务结付
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.disabled=false;
					if(e.checked==true){chooseID=chooseID+1;}
					} 
				}
			if(chooseID==0){
				for (var i=0;i<form1.elements.length;i++){
					var e=form1.elements[i];
					if (e.type=="checkbox"){
						e.disabled=true;					
						} 
					}
				alert("没有选取记录!");return false;
				}
			else{
				SignUdates("expresslist","Payment","");}
			break;
		}
	}

function express_read(theROW,Action){
	if(Action==1){
		document.form1.SUMA1.value=FormatNumber(document.form1.SUMA1.value*1+eval("document.form1.A"+theROW).value*1,2);
		}
	else{
		document.form1.SUMA1.value=FormatNumber(document.form1.SUMA1.value*1-eval("document.form1.A"+theROW).value*1,2);

		}
	document.form1.SUMA2.value=FormatNumber(document.form1.SUMA0.value-document.form1.SUMA1.value,2);
	}
function ChooseAlladd(){
		document.form1.SUMA1.value=document.form1.SUMA0.value;
		document.form1.SUMA2.value=0;
	}
function ChooseAllMinus(){
		var temp1=document.form1.SUMA1.value;
		document.form1.SUMA1.value=document.form1.SUMA2.value;
		document.form1.SUMA2.value=temp1;
	}
</script>