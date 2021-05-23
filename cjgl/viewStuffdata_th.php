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
$Th_Col="选项|40|序号|40|配件Id|50|配件名称|400|在库|60|规格|30|备注|30|分类|150";
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
//$Action=$Action==""?"9":$Action;
$selModel=$selModel=""?"1":$selModel;
$GysList="";
$nowInfo="当前:配件资料";
$SearchRows="";
$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 AND mainType<2 order by Letter",$link_id);
if($myrow = mysql_fetch_array($result)){
	    $GysList.="<select name='StuffType' id='StuffType' onchange='document.form1.submit();'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			//$StuffType=$StuffType==""?$theTypeId:$StuffType;
			if ($StuffType==$theTypeId){
				$GysList.= "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND T.TypeId='$theTypeId' ";
				}
			else{
				$GysList.= "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			$GysList.= "</select>&nbsp;";
		}


switch($Action){
   case "9":
	  $SearchRows.= " AND G.CompanyId='$Cid' ";
	  break;
   }
  
 if ($searchSTR!="") $SearchRows.= " AND S.StuffCname like '%$searchSTR%' ";
 
    $GysList1.=" <input name='searchSTR' type='text' id='searchSTR' size='28' value='$searchSTR'>";
    $GysList1.=" <input name='qSearch' type='button' id='qSearch' value='查 询' onClick='document.form1.submit();'>";
//步骤5：
echo"<table  cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7'>
	<tr>
	<td colspan='4' height='40px' class=''>$GysList &nbsp;&nbsp;  $GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
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
$mySql="SELECT 
	S.Id,S.StuffId,S.StuffCname,S.Spec,S.Remark,K.tStockQty,T.TypeName
	FROM $DataIn.cg1_stocksheet G
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	WHERE 1 AND G.Mid>0   $SearchRows 
	AND NOT EXISTS (SELECT B.StockId FROM $DataIn.cg1_stuffcombox B WHERE B.mStockId=G.StockId ) 
	GROUP BY G.StuffId 
UNION ALL 
	SELECT  S.Id,S.StuffId,S.StuffCname,S.Spec,S.Remark,K.tStockQty,T.TypeName
	FROM $DataIn.cg1_stuffcombox  SG 
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=SG.mStockId  
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=SG.StuffId
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=SG.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	WHERE 1 AND  (G.FactualQty >0  OR G.AddQty >0)  $SearchRows GROUP BY SG.StuffId 
	ORDER BY StuffId DESC
	";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$TypeName=$myRow["TypeName"];
		$tStockQty=$myRow["tStockQty"];
		$Bdata=$StuffId."^^".$StuffCname."^^".$tStockQty;
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];		
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=1;
		$LockRemark="";
		$ListFlag=1;
		if($tStockQty<=0){$ListFlag=0;}
	    $checkidValue=$Bdata;
		if ($ListFlag==1){
			echo"<tr onclick='if(checkid$i.checked)checkid$i.checked=false;else checkid$i.checked=true;'>
			<td class='A0111' align='center' id='theCel$i' height='25' >
			<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue'></td>
				 <td class='A0101' align='center'>$i</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101'  align='center'>$tStockQty</td>";	
			echo"<td class='A0101'>$Spec</td>";	
			echo"<td class='A0101'  align='center'>$Remark</td>";	
			echo"<td class='A0101'>$TypeName</td>";
			echo"</tr>";
			$i++;
		}			
		}while ($myRow = mysql_fetch_array($myResult));
	  if ($i==1){
		echo"<tr><td colspan='8' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	  }
	}
else{
	echo"<tr><td colspan='8' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
</html>