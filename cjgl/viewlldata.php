<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transition al.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR />
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
</head>
<body>
<form name='form1' id='form1' enctype='multipart/form-data' method='post' action=''>
<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//OK
$Th_Col="选项|30|序号|40|分类|80|配件Id|60|配件名称|300|需求单流水号|100|订单需求数|75|领料数量|60|领料日期|70|领料员|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}

//查询条件
$Action=$Action==""?"2":$Action;
$GysList="";
$nowInfo="当前:配件领料资料";
$SearchRows="";
$GysList1.=" <input name='searchSTR' type='text' id='searchSTR' size='28' value='$searchSTR'>";
$GysList1.=" <input name='qSearch' type='button' id='qSearch' value='查 询' onClick='document.form1.submit();'>";
//步骤5：
$checkAllstr="<input name='chooseAll' type='button' id='chooseAll' value='全选' onclick='checkAll()'>";
echo"<table  cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7'>
	<tr>
	<td colspan='7' height='40px' class=''> $GysList1 $checkAllstr</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
    echo "<tr>";
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$i=1;
switch($Action){
	case "1":
	  $SearchRows.=" AND  S.Estate=0 AND D.StuffCname like '%$searchSTR%' ";	
	  break;
   case "2":
      $SearchRows.=" AND  S.Estate=0 AND D.StuffCname like '%$searchSTR%' ";	
	  break; //
	case "3":
	  $SearchRows.=" AND  S.Estate=0 AND S.sPOrderId = '$searchSTR'";
	  break;
}	


$mySql="
SELECT S.Received,S.Id,S.StockId,S.StuffId,SUM(S.Qty) AS Qty, SUM(S.Qty*S.Price)/SUM(S.Qty)  AS avgPrice,
S.Locks,D.StuffCname,D.TypeId,D.Picture,D.SendFloor,T.TypeName,
			A.Name AS Operator,IFNULL(G.OrderQty,GM.OrderQty) AS OrderQty,S.sPOrderId,S.Date
			FROM $DataIn.ck5_llsheet S
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			LEFT JOIN $DataIn.staffmain A ON A.Number=S.Receiver 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
			LEFT JOIN $DataIn.cg1_stuffcombox GM ON GM.StockId = S.StockId 
			WHERE 1 $SearchRows   GROUP BY S.StockId ORDER BY S.StockId";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		    $m=1;
			$LlId=$myRow["Id"];
		    $Date=$myRow["Date"];
		    $Operator=$myRow["Operator"];
		    $Number=$myRow["Number"];
		    $StuffId=$myRow["StuffId"];	
			$StockId=$myRow["StockId"];	
			$StuffCname=$myRow["StuffCname"];
			$TypeName=$myRow["TypeName"];
			$Qty=$myRow["Qty"];
			$OrderQty =$myRow["OrderQty"];
			$sPOrderId =$myRow["sPOrderId"];
			$Locks=$myRow["Locks"];
			$Estate=$myRow["Estate"];
		    $avgPrice=$myRow["avgPrice"];
			$ListFlag=1;
			$StuffCname = str_replace('"','', $StuffCname);
			$StuffCname = str_replace(',','', $StuffCname);
			switch($Action){
			case "1":
				$Bdata=$StuffId."^^".$StuffCname."^^".$LlId."^^".$StockId."^^".$OrderQty."^^".$Qty."^^".$sPOrderId;
				break;
		   case "2":
		   case "3":
				$Bdata=$StuffId."^^".$StuffCname."^^".$StockId."^^".$Qty."^^".$sPOrderId."^^".$avgPrice;
				break;
				}	
				
			$Locks=1;
		    $checkidValue=$Bdata;
			$Picture=$myRow["Picture"];
			$TypeId=$myRow["TypeId"];
			$Date=$myRow["Date"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			echo"<tr onclick='selChanged(document.getElementById(\"checkid$i\"),$StuffId,$Action);'>
			<td class='A0111' align='center' id='theCel$i' height='25' onclick='return false;'>
			<input name='checkid[]' type='checkbox' id='checkid$i'  value='$checkidValue' onclick='this.checked=!this.checked;'></td>
				 <td class='A0101' align='center'>$i</td>";
			echo"<td class='A0101' align='center'>$TypeName</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='center'>$StockId</td>";
			echo"<td class='A0101' align='right'>$OrderQty</td>";	
			echo"<td class='A0101' align='right'>$Qty</td>";		
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"</tr>";
			$i++;		
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='12' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "<input type='hidden' id='Allid' name='Allid' value='$i'>";
echo "</table>";
	?>
</form>
</body>
</html>
<script language = "JavaScript">
var oldselId="<?php    echo $oldStuffId?>";
var selId=oldselId;
var selSum=0;
function selChanged(e,StuffId,Action){
 switch(Action){
   case 1:
	if (selId=="") selId=StuffId;
    if (e.checked){
	   selSum=selSum-1;
	   if (selSum==0) selId=oldselId;
	   e.checked=false;
	}
	else{
		if (selId==StuffId){
		    selSum=selSum+1;
		    e.checked=true;
		    }
		else{
		    e.checked=false;
			alert ("错误！只能选择同一配件进行置换。");
		}
	  }
	  break;
   case 2:
   case 3:
     if (e.checked) e.checked=false; else e.checked=true;
	 break;
  }
}

function checkAll(){
var k=document.getElementById("Allid").value;
  for(i=1;i<k;i++){
       document.getElementById("checkid"+i).checked=true;
         }
}

function clearAll(){
/*var k=document.getElementById("Allid").value;
  for(i=1;i<k;i++){
       document.getElementById("checkid"+i).checked=false;
         }*/
}
</script>