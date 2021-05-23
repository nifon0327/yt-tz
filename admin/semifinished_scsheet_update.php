<?php 
include "../model/modelhead.php";
include "../model/sweetalert.php";
//步骤2：
ChangeWtitle("$SubCompany 生产工单设置");//需处理
$nowWebPage ="semifinished_scsheet_update";	
$toWebPage  ="semifinished_scsheet_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
if ($Id=="" && $Mid!=""){
	$StockId=$Mid;
}

if ($funFrom=="pt_order"){
	$fromWebPage="../pt/" . $fromWebPage;
}
$Parameter="StockId,$StockId,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,CompanyId,$CompanyId,ActionId,72,OrderAction,$OrderAction";
//步骤3：

$tableWidth=850;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
?>
<table width='<?php  echo $tableWidth?>' border='0' cellspacing='0' bgcolor='#FFFFFF'>
	<tr>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
	<td colspan="7">1、半成品加工信息</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr <?php  echo $Fun_bgcolor?>>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
	<td width='100' class='A1111' align='center'>客户</td>
	<td width='100' class='A1101' align='center'>订单PO</td>
	<td width='300' class='A1101' align='center'>半成品名称</td>
	<td width='70' class='A1101' align='center'>加工数量</td>
	<td width='70' class='A1101' align='center'>备料数量</td>
	<td width='70' class='A1101' align='center'>已生产数</td>
	<td width='70' class='A1101' align='center'>入库数量</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
<?php 
$sheetResult=mysql_query("SELECT G.POrderId,G.StockId,G.StuffId,(G.AddQty+G.FactualQty) AS Qty,
    D.StuffCname,D.Picture,D.FrameCapacity,S.OrderPO,C.Forshort 
    FROM $DataIn.cg1_semifinished E  
    INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId=E.mStockId 
    INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
    LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
    LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
    WHERE E.StockId='$StockId' LIMIT 1",$link_id);
  
if ($sheet_Row = mysql_fetch_array($sheetResult)){
   $POrderId=$sheet_Row["POrderId"]; 
   $OrderPO=$sheet_Row["OrderPO"]==""?"-":$sheet_Row["OrderPO"]; 
   $Forshort=$sheet_Row["Forshort"]==""?"特采单":$sheet_Row["Forshort"];
   
   
   $mStockId=$sheet_Row["StockId"]; 
   $StuffId=$sheet_Row["StuffId"];
   $Qty=$sheet_Row["Qty"];
   $StuffCname=$sheet_Row["StuffCname"];
   $Picture=$sheet_Row["Picture"];
   
   include "../model/subprogram/stuffimg_model.php";
   include"../model/subprogram/stuff_Property.php";//配件属性 
   
   
   $scResult=mysql_fetch_array(mysql_query("SELECT SUM(S.scQty) AS scQty,SUM(IF(S.scFrom=2 OR S.Estate=0,Qty,0)) AS blQty 
    FROM yw1_scsheet S 
    LEFT JOIN $DataIn.cg1_stocksheet  G ON S.StockId=G.StockId 
    WHERE S.StockId='$StockId'",$link_id));
   $scQty=$scResult["scQty"]==""?0:$scResult["scQty"];
   $blQty=$scResult["blQty"]==""?0:$scResult["blQty"];
   
   $chResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS rkQty FROM  $DataIn.ck1_rksheet  WHERE StockId='$StockId'",$link_id));
   $rkQty=$chResult["rkQty"]==""?0:$chResult["rkQty"];           
 		//客户/产品名称
		echo "<input name='POrderId' type='hidden' id='POrderId' value='$POrderId'>";
		echo "<input name='OrderQty' type='hidden' id='OrderQty' value='$Qty'>";
		echo "<tr>";	
		echo "<td class='A0010' height='25'>&nbsp;</td>";
		echo "<td class='A0111' align='center'>$Forshort</td>";
		echo "<td class='A0101' align='center'>$OrderPO</td>";
		echo "<td class='A0101'>$StuffCname</td>";
		echo "<td class='A0101' align='center'>$Qty</td>";
		echo "<td class='A0101' align='center'>$blQty</td>";
		echo "<td class='A0101' align='center'>$scQty</td>";
		echo "<td class='A0101' align='center'>$rkQty</td>";
		echo"<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>";
		echo "</tr>";
		
  }
  
	 $Relation=$sheet_Row["FrameCapacity"];
	 $RelationStr=$Relation==0?"未设置":$Relation . " / 框";
	 echo "<input name='Relation' type='hidden' id='Relation' value='$Relation'>";
?>		
	<tr <?php  echo $Fun_bgcolor?>>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
		<td bgcolor='#FFFFFF' colspan='7'>2、生产工单数据  (<span style='color:#00F;'>装框数量:<?php echo $RelationStr;?></span>)</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>

 <tr <?php  echo $Fun_bgcolor?>>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
	<td bgcolor='#FFFFFF' colspan='7'>	
      <table  border='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable'>
	 <tr>
		<td width='50' class='A1111' align='center'>操作</td>
		<td width='50' class='A1101' align='center'>序号</td>
		<td width='100' class='A1101' align='center'>工单流水号</td>
		<td width='70' class='A1101' align='center'>加工类型</td>
		<td width='100' class='A1101' align='center'>生产单位</td>
		<td width='70' class='A1101' align='center'>工单数量</td>
		<td width='70' class='A1101' align='center'>备料状态</td>
		<td width='70' class='A1101' align='center'>已生产数</td>
		<td width='70' class='A1101' align='center'>拆分后锁定</td>
		<td width='70' class='A1101' align='center'>操作员</td>
	</tr>
			<?php 
			$i=1;$j=0;
			$sheetResult2=mysql_query("SELECT S.sPOrderId,S.WorkShopId,S.Qty,S.scFrom,S.Estate,S.scQty,S.Operator,A.Name AS ActionName  
			    FROM $DataIn.yw1_scsheet S 
			    LEFT JOIN $DataIn.cg1_stocksheet  G ON S.StockId=G.StockId 
			    LEFT JOIN $DataIn.workorderaction A ON A.ActionId=S.ActionId 
			    WHERE 1 and S.StockId='$StockId' ",$link_id); 
			while($sheetRow = mysql_fetch_array($sheetResult2)){
			    $delSign=1;
			    $sPOrderId= $sheetRow['sPOrderId'];
			    $Qty= $sheetRow['Qty'];
			    $scQty= $sheetRow['scQty'];
			    $scForm= $sheetRow['scFrom'];
			    $Estate= $sheetRow['Estate'];
			    $checkLLResult=mysql_fetch_array(mysql_query("SELECT SUM(A.blQty) AS blQty,SUM(A.llQty) AS llQty 
			    FROM (
			      SELECT SUM(G.OrderQty*(S.Qty/M.OrderQty)) AS blQty,0 AS llQty 
					FROM  yw1_scsheet S 
                    LEFT JOIN  cg1_semifinished G ON G.mStockId=S.mStockId 
                    LEFT JOIN cg1_stocksheet M ON M.StockId=S.StockId 
					WHERE  S.sPOrderId='$sPOrderId' 
				UNION ALL 
					SELECT 0 AS blQty,IFNULL(SUM(Qty),0) AS llQty 
					FROM  ck5_llsheet 
				    WHERE  sPOrderId='$sPOrderId'
				 )A",$link_id));
			    $blQty=$checkLLResult['blQty'];
				$llQty=$checkLLResult['llQty'];
				$blSign=0;
				if ($blQty==$llQty){
					$blText="<div class='greenB'>已备料</div>";
					$blSign=1;
				}else{
					$blText=$llQty>0?"<div class='blueB'>部分备料</div>":"未备料";
					$blSign=$llQty>0?2:0;
				}
			   
			    $qtyStauts=($Estate==0 || $Qty==$scQty || $blSign>0)?"readonly":" onchange='changeQty(this,$scQty,$Qty)' ";
			    //$blText=$scForm==1?"未备料":"<div class='greenB'>已备料</div>";
			    //$qtyStauts=($Estate==0 || $Qty==$scQty)?"readonly":" onchange='changeQty(this,$scQty,$Qty)' ";
			    if ($Estate==0 || $scQty>0) $delSign=0;
			    $Operator= $sheetRow['Operator'];
			    include "../model/subprogram/staffname.php";
			    
			    $OrderActionId = $sheetRow['OrderActionId'];
			    $ActionName= $sheetRow['ActionName'];
			    $WorkShopId= $sheetRow['WorkShopId'];
		
			    $WorkShopStr="";
			    if ($OrderActionId!=105){
					    $WorkShopResult = mysql_query("SELECT Id,Name FROM $DataIn.workshopdata WHERE Estate=1",$link_id);
				        while($WorkShopRow = mysql_fetch_array($WorkShopResult))
				        {
					         $W_Id=$WorkShopRow['Id']; 
					         $W_Name=$WorkShopRow['Name'];   
		                     $SelectedStr=$WorkShopId==$W_Id?" selected ":"";
		                     $WorkShopStr.="<option value='$W_Id' $SelectedStr>$W_Name</option>";
					  }
				}else{
					   $WorkShopStr.="<option value='0'>--</option>";
				} 
				
			    $onClickStr="&nbsp;";
			    if ($j==0){
				    $onClickStr="<a href='#' onclick='addRows()' title='新增行数'>+</a></td>";
			    }
			    else{
				   if ($delSign==1){
					  $onClickStr="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>"; 
				    }
			    }
			    echo "<tr>
			                <td class='A0110' align='center'>
			                 $onClickStr 
							<td class='A0110' align='center'>$i</td>
							<td class='A0110' align='center'>$sPOrderId 
							<input name='SPID[]' type='hidden' id='SPID$j' value='$sPOrderId'>
							</td>
							<td class='A0110' align='center'>$ActionName</td>
							<td class='A0110' align='center'>
							 <select name='WorkShopId[]' id='WorkShopId$j' style='width:90px'>
							  $WorkShopStr
							 </select>
							</td>
							<td class='A0110' align='center'>
							<input name='Qty[]' type='text' id='Qty$j' value='$Qty' style='width:60px;' $qtyStauts>
							</td>
							<td class='A0110' align='center'>$blText</td>
							<td class='A0110' align='center'>$scQty</td>
							<td class='A0110' align='center'><input type='checkbox' id='lockSign$i' name='lockSign[]' $lockChecked ></td>
							<td class='A0111' align='center'>$Operator</td>
					 </tr>";
			   $i++;$j++;
			}
	?>
  </table>
  </td>
   <td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
  </tr>
<input name='QtyList' type='hidden' id='QtyList' value=''>
<input name='IdList' type='hidden' id='IdList' value=''>
<input name='WsList' type='hidden' id='WsList' value=''>
<input name='LockSignList' type='hidden' id='LockSignList' value=''>
<input name='DelList' type='hidden' id='DelList' value=''>
<input name='IdCount' type='hidden' id='IdCount' value='<?php  echo $j?>'>
<?php 
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
function CheckForm(ALType){
   var Qtys=document.getElementsByName("Qty[]");
   var PIds=document.getElementsByName("SPID[]");
   var WsId=document.getElementsByName("WorkShopId[]");
   var LockSignId =document.getElementsByName("lockSign[]");
   var Relation=document.getElementById("Relation").value;
   var qtyList="";
   var idList="";
   var WsIdList="";
   var LockSignList ="";
   var sumqty=0;
   var checkCarton=false;
   var tempLockSign = 0 ;
   for (var i=0;i<Qtys.length;i++){
   
     if(LockSignId[i].checked){
		  tempLockSign = 1;
	  }else{
		  tempLockSign =0;
	  }
      qtyList+=qtyList.length<=0?Qtys[i].value:"|"+Qtys[i].value;
	  idList+=idList.length<=0?PIds[i].value:"|"+PIds[i].value;
	  WsIdList+=WsIdList.length<=0?WsId[i].value:"|"+WsId[i].value;
	  LockSignList +=LockSignList.length<=0?tempLockSign:"|"+tempLockSign;
	  
	  sumqty+=Qtys[i].value*1;
		  
      if (Qtys[i].readOnly==false){
		   if (Qtys[i].value<=0){
			  // alert("工单数量不能为0或空");
			   swal("工单数量不能为0或空");
			   return false;
		   }
		  
		  if (Relation>0){
			  var BoxNum=Qtys[i].value%Relation;
			  if(BoxNum>0){
			     checkCarton=true;
			  }
		  }
	  }
   }
   
   var orderQty=document.getElementById('OrderQty').value*1;
   if (sumqty!=orderQty || sumqty<=0){
      //alert("工单数量和:"+sumqty + " 不等于订单数量:"+orderQty);
      swal("工单数量和:"+sumqty + " 不等于订单数量:"+orderQty);
      return false;
   }
   
   var confirmSign=true;
   if (checkCarton==true){
	   confirmSign=confirm("有单输入的数量是不是整箱数!是否继续?");
   }
   
   if (confirmSign==true && qtyList!=""){
	  // alert(qtyList);
	  var delList=document.getElementById('DelList').value;
	  var delIds=delList.split("|");
	  
	  if (delList.length>1 && delIds.length>0){
	      idList+="|"+delList;
		  for (var i=0;i<delIds.length;i++){
			 qtyList+="|-1";
			 WsIdList+="|0";
		  }
	  }
	  
	  document.getElementById('IdList').value=idList;
	  document.getElementById('QtyList').value=qtyList;
	  document.getElementById('WsList').value=WsIdList;
	  document.getElementById('LockSignList').value=LockSignList;
	  
	  document.form1.action="semifinished_scsheet_updated.php";
	  document.form1.submit();
	  return true;
   }
   
   return false;
}

function changeQty(e,scqty,oldVal){
   if (fucCheckNUM(e.value,'Price')==0){
	   //alert("请填入正确的数字");
	   swal("请填入正确的数字");
	   e.value=oldVal;
	   return false;
   }
   else{
	  if (e.value*1<scqty){
		// alert("工单数量不能小于已生产数量"); 
		 swal("工单数量不能小于已生产数量");
		 e.value=oldVal;
		 return false;
	  }
   }
}

function addRows(){
	   var oTR = ListTable.insertRow();
	    tmpNum=oTR.rowIndex;
	    var oTD;
		//1: 操作
		oTD=oTR.insertCell(0);		
		oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
		oTD.className ="A0110";
		oTD.align="center";
		
		//2：序号
		oTD=oTR.insertCell(1);
		oTD.innerHTML=""+tmpNum+"";
		oTD.className ="A0110";
		oTD.align="center";
		
		//3：流水号
		oTD=oTR.insertCell(2);
		oTD.innerHTML="新工单";
		oTD.className ="A0110";
		oTD.align="center";
		
		//3：流水号
		oTD=oTR.insertCell(3);
		oTD.innerHTML="<?php echo $ActionName;?>";
		oTD.className ="A0110";
		oTD.align="center";
		
		//3：流水号
		oTD=oTR.insertCell(4);
		oTD.innerHTML="<select name='WorkShopId[]' id='WorkShopId"+ids+"' style='width:90px'><?php echo $WorkShopStr;?></select>";
		oTD.className ="A0110";
		oTD.align="center";
		
		var ids=document.getElementById("IdCount").value;
		oTD=oTR.insertCell(5);
		oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+ids+"' value='0' style='width:70px;' onchange='changeQty(this,0,0)' >"+"<input name='SPID[]' type='hidden' id='SPID"+ids+"' value='0'>";
		oTD.className ="A0110";
		document.getElementById("IdCount").value=ids*1+1;
		
		oTD=oTR.insertCell(6);
		oTD.innerHTML="&nbsp;";
		oTD.className ="A0110";

		oTD=oTR.insertCell(7);
		oTD.innerHTML="&nbsp;";
		oTD.className ="A0110";
		
		oTD=oTR.insertCell(8);
		oTD.innerHTML="<input type='checkbox' id='lockSign"+ids+"' name='lockSign[]' >";
		oTD.className ="A0110";
		oTD.align="center";
		
		oTD=oTR.insertCell(9);
		oTD.className ="A0111";
        oTD.innerHTML="&nbsp;";

}
	

//序号重整
function ShowSequence(TableTemp,rowIndex){
	for(i=1;i<TableTemp.rows.length;i++){ 
  		TableTemp.rows[i].cells[1].innerText=i; 
  }   
}
  
//删除行 
function deleteRow (RowTemp,TableTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	
    var delIds=TableTemp.rows[rowIndex].cells[2].innerText;
    if (delIds.length>0){
	   var delList= document.getElementById('DelList').value; 
	   delList=delList.length>1?delList+'|'+delIds:delIds;
	   document.getElementById('DelList').value=delList;
    }
    
	TableTemp.deleteRow(rowIndex);
	ShowSequence(TableTemp,rowIndex);
}
</script>