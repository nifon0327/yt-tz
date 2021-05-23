<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php   
//电信-zxq 2012-08-01
$Th_Col="编号|40|领料人|45|领料日期|60|序号|30|客户|80|配件ID|50|配件名称|280|需领料数|55|本次领料|55|总领料|55|单位|30|需求单流水号|100|工单流水号|100|领料类型|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols = $Count/2;
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
	
//if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1; 
//}
$SearchRows="";
$GysList="";
$nowInfo="当前:车间领料数据";
	$SearchRows="";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND D.StuffCname LIKE '%$StuffCname%' ";
	$GysList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(4,5)'/>";
   }
else{

	$date2_Result = mysql_query("SELECT DATE_FORMAT(Received,'%Y-%m-%d') AS Received FROM $DataIn.ck5_llsheet WHERE 1 GROUP BY DATE_FORMAT(Received,'%Y-%m-%d')  ORDER BY DATE_FORMAT(Received,'%Y-%m-%d') DESC",$link_id);
		if($dateRow2 = mysql_fetch_array($date2_Result)) {
			$GysList.="<select name='selDate2' id='selDate2'  onchange='ResetPage(4,5)'>";
			do{			
				 $dateValue2=$dateRow2["Received"];
				 $selDate2=$selDate2==""?$dateValue2:$selDate2;
				if($selDate2==$dateValue2){
					$GysList.="<option value='$dateValue2' selected>$dateValue2</option>";
					$SearchRows.=" AND DATE_FORMAT(L.Received,'%Y-%m-%d')='$dateValue2' ";
					}
				else{
					$GysList.="<option value='$dateValue2'>$dateValue2</option>";					
					}
			 }while($dateRow2 = mysql_fetch_array($date2_Result));
			$GysList.="</select>&nbsp;";
		}
$GysList1="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";

}

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='".($Cols-8)."' height='40px' class=''>$GysList</td><td colspan='5' height='40px' class=''>$GysList1</td> <td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr></table>";
$DefaultBgColor=$theDefaultColor;
$i=1;

$SearchRows.=" AND L.Estate=0";


$mySql="SELECT L.Id,O.Forshort,L.StockId,L.StuffId,L.Qty AS llQty,L.Locks,DATE_FORMAT(L.Received,'%Y-%m-%d') AS Received ,L.sPOrderId,
D.StuffCname,D.TypeId,D.Picture,A.Name AS Receiver,A.Number,U.Name AS UnitName,
L.POrderId,L.Type,L.FromFunction,IFNULL(G.OrderQty,GM.OrderQty) AS OrderQty
FROM $DataIn.ck5_llsheet L
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = L.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = L.StockId
LEFT JOIN $DataIn.cg1_stuffcombox GM ON GM.StockId = L.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=L.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.staffmain A ON A.Number=L.Receiver 
WHERE 1 $SearchRows ORDER BY DATE_FORMAT(L.Received,'%Y-%m-%d') DESC , L.Receiver DESC";
/*
,SC.Qty,SC.mStockId,(CG.addQty+CG.FactualQty) AS xdQty,
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId= L.sPOrderId
LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId= SC.mStockId
*/
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	  $newMid="";
	do{
		$m=1; 
		//主单信息
		$Received=$mainRows["Received"];
		$Operator=$mainRows["Operator"];
		$Number=$mainRows["Number"];
		$Receiver=$mainRows["Receiver"];
		$Forshort=$mainRows['Forshort'];
		//明细资料
		$StuffId=$mainRows["StuffId"];		
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$POrderId=$mainRows["POrderId"];
			$mStockId = $mainRows["mStockId"];
			$Qty=$mainRows["Qty"];
			$thisllQty=$mainRows["llQty"];
			$sPOrderId=$mainRows["sPOrderId"];
			$StockId=$mainRows["StockId"];
			$llType=$mainRows["Type"];
			$FromFunction=$mainRows["FromFunction"];
			$OrderQty =$mainRows["OrderQty"];
			
			/*if($mStockId>0){
				$xdQty=$mainRows["xdQty"];
			    $Relation=$Qty/$xdQty;
			    $checkSemiRow = mysql_fetch_array(mysql_query("SELECT ROUND(OrderQty*$Relation,1) AS OrderQty FROM $DataIn.cg1_semifinished WHERE mStockId='$mStockId' AND StockId='$StockId'",$link_id));
			    $OrderQty =$checkSemiRow["OrderQty"];
		    
		    }else{
			      
			     $checkFinishRow = mysql_fetch_array(mysql_query("SELECT 
			     ROUND(G.OrderQty*(S.Qty/Y.Qty),1) AS OrderQty 
			     FROM  $DataIn.yw1_scsheet  S 
			     LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId= S.POrderId
				 LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId = S.POrderId
				 WHERE  G.StockId = '$StockId'",$link_id));
			     $OrderQty =$checkFinishRow["OrderQty"];
		    }*/
		    
		    $UnitName=$mainRows["UnitName"];
			
			$Locks=$mainRows["Locks"];
			$Estate=$mainRows["Estate"];
			$cName=$mainRows["cName"];
			$OrderPO=$mainRows["OrderPO"];
			$Picture=$mainRows["Picture"];
			$TypeId=$mainRows["TypeId"];
			//检查是否有图片
			 $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		     include "../model/subprogram/stuffimg_model.php";
		     include"../model/subprogram/stuff_Property.php";//配件属性   
			//领料总数
			$UnionSTR4=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND sPOrderId='$sPOrderId'",$link_id);
			$llQty=mysql_result($UnionSTR4,0,"llQty");
			$llQty=$llQty==""?0:$llQty;
			if($llQty>$OrderQty){//领料总数大于订单数,提示出错
				$llBgColor="class='redB'";
				}
			else{
				if($llQty==$OrderQty){//刚好全领，绿色
					$llBgColor="class='greenB'";
					}
				else{				//未领完
					$llBgColor="class='blueB'";
					}
			}
				
		//输出主单信息
		if ($newReceiver!=$Receiver){
			   $newReceiver=$Receiver;$j=1;
			   if ($i!=1) {echo"</table></td></tr></table>";}
		       echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' >$i</td>";//编号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Receiver</td>";	//领料日期				
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Received</td>";		//领料人				
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
		       }
			else{
				$m=7;
			}
			   //输出明细信息
			   	echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo "<tr height='30'>";
				$unitFirst=$Field[$m]-1;
			    echo"<td class='A0001' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
				$m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";	//配件ID
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";				//配件
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$OrderQty </td>";	//需领料数
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$thisllQty</td>";	//本次领料
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'><div $llBgColor>$llQty</div></td>";	
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//需求流水号	
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$sPOrderId</td>";	//工单流水号	
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$FromFunction</td>";						
				echo "</tr>";
				$i++;$j++;
		   }
		}while($mainRows = mysql_fetch_array($mainResult));
     echo"</table></td></tr></table>";
  }		
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
</html>
<script language = "JavaScript">
function CnameChanged(e){
	var StuffCname=e.value;
	if (StuffCname.length>=1){
	   e.style.color='#000';
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  e.style.color='#DDD';
	  document.getElementById("stuffQuery").disabled=true;
	}
}
</script>