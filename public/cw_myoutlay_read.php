<?php 
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/functions.php";
include "../basic/Parameter_CSS.inc";
$Page="No"; //是否显示分页码
$Form="Yes"; 
if ($PEADate==""){
	$PEADate="";}
else{
	//分解时间段,即在某一个时间段内
	$PEADateString=explode("/",$PEADate);
	$PEADateStat=$PEADateString[0];
	$PEADateEnd=$PEADateString[1];
	$PEADate="and myoutlaysheet.Date>='$PEADateStat' and myoutlaysheet.Date<='$PEADateEnd'";}
if($cwEstate==""){
	$cwEstate=1;}
?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<link rel="stylesheet" href="../model/style/ReadCss.css">
<script src="../basic/functions.js" type=text/javascript></script>
<title>员工报销资料管理</title>
<body >
<form name="form1" method="post" action="">
<table border="0" cellpadding="0" cellspacing="0" width="1040"> 
  <tr>
   <td width="7" ><img name="maintable_r1_c1" src="../images/maintable_r1_c1.gif" width="7" height="26" /></td>
   <td width="800" background="../images/maintable_r1_c2.gif">
      <select name="staff" id="staff" width="60" onchange='document.form1.submit()'>
	<?php  
		$result = mysql_query("SELECT * FROM $DataPublic.staffmain  WHERE staffType=\"办公室职员\" and Number>10001 order by Number ",$link_id);
	if ($staff==""){
		echo "<option value='' selected>全部职员</option>";
		}
	else{
		echo "<option value='' >全部职员</option>";
		}
	while ($myrow = mysql_fetch_array($result)){
					$Name=$myrow["Name"];
					if ($Name==$staff){
					echo "<option value='$Name' selected>$Name</option>";}
					else{
					echo "<option value='$Name'>$Name</option>";}
				} 
			  
	?>
     </select>

	<select name="cwEstate" id="cwEstate" onchange="document.form1.submit()">
	 <?php 
	 if($cwEstate==0){
 	 	echo"<option value='1'>未结付</option><option value='0' selected>已结付</option>";
		$cwEstateSTR="and myoutlaysheet.Estate=0";
		}
	 else{
	 	echo"<option value='0'>已结付</option><option value='1' selected>未结付</option>";
		$cwEstateSTR="and myoutlaysheet.Estate=3";
		}
	?> 
     </select>
	 
   <td width="35"><img name="maintable_r1_c3" src="../images/maintable_r1_c3.gif" width="35" height="26"/></td>
   <td width="90" align="center" background="../images/maintable_r1_c4.gif" >
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
					<nobr>&nbsp;&nbsp;
					<?php 
						if(($Keys & mUPDATE)&&($cwEstate==0)){
							echo"<span onClick='UpdataThisId(\"myoutlay\",\"cw\",\"\")' style='CURSOR: pointer;color:#FF6633'>更新附档</span>&nbsp;";}
						echo"<span onClick='checkall(this.form)' style='CURSOR: pointer;color:#FF6633''>全选</span>&nbsp;&nbsp;
							<span onClick='ReChoose(this.form)' style='CURSOR: pointer;color:#FF6633''>反选</span>&nbsp;&nbsp;";
						if(($Keys & mUPDATE)&&($cwEstate==1)){
						echo"<span onClick='SignUdates(\"myoutlay\",\"payment\",\"\")' style='CURSOR: pointer;color:#FF6633'>结付</span>&nbsp;";}
  					?>
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
	<table width="100%" height="37" border="0" align="center" cellspacing="1"  id="DataList">
	<?php 
	if($staff!=""){
		$staffSTR="and myoutlaysheet.Operator=\"$staff\"";}
	$my_Result = mysql_query("SELECT myoutlaysheet.Operator,myoutlaysheet.Number,myoutlaysheet.Content,myoutlaysheet.Money,myoutlaysheet.Bill,
	myoutlaysheet.Date,myoutlaysheet.Estate,myoutlaysheet.Type,myoutlaysheet.Locks,myoutlaymain.Payee,myoutlaymain.Date AS PayDate	
	 FROM myoutlaysheet
	LEFT JOIN myoutlaymain ON myoutlaysheet.MainNumber=myoutlaymain.Number 	where 1 $cwEstateSTR $staffSTR",$link_id);
	$Th_Col="|选项|30|Id|序号|30|Personnal|报帐人|60|Date|报帐日期|80|Money|报帐金额|80|Content|报帐原因|500|Type|费用分类|60|Bill|相关票据|60|Estate|请款状态|60|Payee|汇单|60|payDate|结付日期|80";
	Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);	$i=1;$k=1;
	$Amount_SUM=0;//总金额
	if($my_Rows=mysql_fetch_array($my_Result)){
		do{
			$Number=$my_Rows["Number"];
			$Content=$my_Rows["Content"];
			$Money=$my_Rows["Money"];
			$Bill=$my_Rows["Bill"]==""?"-":"<span onClick='View(\"billdir\",\"$my_Rows[Bill]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
			$Date=$my_Rows["Date"];
			$Estate=$my_Rows["Estate"];
			$Type=$my_Rows["Type"];
			$Operator=$my_Rows["Operator"];
			$Locks=$my_Rows["Locks"];
			$PayDate=$my_Rows["PayDate"]=="0000-00-00"?"-":$my_Rows["PayDate"];
			$Payee=$my_Rows["Payee"]==""?"-":$my_Rows["Payee"];
			$Amount_SUM=sprintf("%.2f",$Amount_SUM+$Money);
			//$Payee=$my_Rows["Payee"]==""?"-":"<span onClick='View(\"cwfkout\",\"$my_Rows[Payee]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
			//$Receipt=$my_Rows["Receipt"]==""?"-":"<span onClick='View(\"cwfkout\",\"$my_Rows[Receipt]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
			switch($Estate){
			case "1":
				$Estate="未请款";
				break;
			case "2":
				$Estate="请款中";
				break;
			case "3":
				$Estate="<div align='center' class='greenB'>请款通过</div>";
				break;
			case "0":
				$Estate="<div align='center' class='greenB'>已结付</div>";
				break;
				}
			if($Type==1){$Type="车费";}
			if($Type==2){$Type="快递费";}
			if($Type==3){$Type="其它";}
			echo"<tr 
				onmousedown='chooseRow(this,$i,\"click\",\"#FFFFFF\",\"$MouseOver_bgcolor\",\"#FFCC99\",\"cw_myoutlay_read\");' 
				onmouseover='setPointer(this,$i,\"over\",\"#FFFFFF\",\"$MouseOver_bgcolor\",\"#FFCC99\");' 
				onmouseout='setPointer(this,$i,\"out\",\"#FFFFFF\",\"$MouseOver_bgcolor\",\"#FFCC99\");'>";
			echo"<td class='A0111'><input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Number'></td>";
			echo"<td class='A0101'>$i</td>";
			echo"<td class='A0101'>$Operator</td>";
			echo"<td class='A0101'>$Date</td>";
			echo"<td class='A0101' align='right'>$Money&nbsp;&nbsp;</td>";
			echo"<td class='A0101' align='left'>$Content</td>";			
			echo"<td class='A0101' align='left'>$Type</td>";
			echo"<td class='A0101' align='center' onmousedown='window.event.cancelBubble=true;'>$Bill</td>";
			echo"<td class='A0101' align='center'>$Estate</td>";
			echo"<td class='A0101' align='center'>$Payee</td>";
			echo"<td class='A0101' align='center'>$PayDate</td>";
			echo"</tr>";
			$i++;
		}while ($my_Rows=mysql_fetch_array($my_Result));		
	echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
	Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);
	echo"<tr height='25'><td colspan='4'>合计</td><td align='right'><div align='center' class='greenB'>$Amount_SUM</div></td><td colspan='6'>&nbsp;</td></tr>";
	}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='17' scope='col' height='60' class='A0111' align='center'><p>没有资料。</td></tr>";
	Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);
	}
  ?>
</table>
<?php 
//表尾
$Form="Yes";
Tabletail($i-1,$Page,$Page_count,$Form,$TypeSTR);
WinTitle("员工报销明细");
?>
<script type=text/javascript>
function cw_myoutlay_read(){
	}
</script>