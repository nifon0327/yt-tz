<?php 
include "../model/modelhead.php";
include "../model/sweetalert.php";
//步骤2：
ChangeWtitle("$SubCompany 生产工单设置");//需处理
$nowWebPage =$funFrom."_scSet";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
if ($Id=="" && $Mid!=""){
	$Id=$Mid;
}
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,CompanyId,$CompanyId,ActionId,72";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
?>
<table width='<?php  echo $tableWidth?>' border='0' cellspacing='0' bgcolor='#FFFFFF'>
	<tr>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
	<td colspan="7">1、业务订单数据</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr <?php  echo $Fun_bgcolor?>>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
	<td width='100' class='A1111' align='center'>客户</td>
	<td width='100' class='A1101' align='center'>订单PO</td>
	<td width='300' class='A1101' align='center'>产品名称</td>
	<td width='70' class='A1101' align='center'>订单数量</td>
	<td width='70' class='A1101' align='center'>备料数量</td>
	<td width='70' class='A1101' align='center'>已生产数</td>
	<td width='70' class='A1101' align='center'>出货数量</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
<?php 
$sheetResult=mysql_query("SELECT S.OrderPO,S.POrderId,S.Qty,P.cName,P.eCode,M.OrderDate,S.Estate ,P.ProductId,C.Forshort 
    FROM $DataIn.yw1_ordersheet S 
    INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
    INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
    INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
    WHERE 1 and S.Id='$Id' LIMIT 1",$link_id);
if ($sheet_Row = mysql_fetch_array($sheetResult)){
   $OrderPO=$sheet_Row["OrderPO"]; 
   $POrderId=$sheet_Row["POrderId"];
   $OrderDate=$sheet_Row["OrderDate"]; 
   $ProductId=$sheet_Row["ProductId"]; 
   $Qty=$sheet_Row["Qty"];
   $cName=$sheet_Row["cName"];
   $eCode=$sheet_Row["eCode"];
   $Estate=$sheet_Row["Estate"];
   $Forshort=$sheet_Row["Forshort"];
   
   $scResult=mysql_fetch_array(mysql_query("SELECT SUM(S.scQty) AS scQty,SUM(IF(S.scFrom=2 OR S.Estate=0,Qty,0)) AS blQty 
    FROM yw1_scsheet S 
    LEFT JOIN $DataIn.cg1_stocksheet  G ON S.StockId=G.StockId 
    WHERE S.POrderId='$POrderId' and G.Level=1  ",$link_id));
   $scQty=$scResult["scQty"]==""?0:$scResult["scQty"];
   $blQty=$scResult["blQty"]==""?0:$scResult["blQty"];
   
   $chResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS chQty FROM  $DataIn.ch1_shipsheet WHERE POrderId=$POrderId",$link_id));
   $chQty=$chResult["chQty"]==""?0:$chResult["chQty"];           
 		//客户/产品名称
		echo "<input name='POrderId' type='hidden' id='POrderId' value='$POrderId'>";
		echo "<input name='OrderQty' type='hidden' id='OrderQty' value='$Qty'>";
		echo "<tr>";	
		echo "<td class='A0010' height='25'>&nbsp;</td>";
		echo "<td class='A0111' align='center'>$Forshort</td>";
		echo "<td class='A0101' align='center'>$OrderPO</td>";
		echo "<td class='A0101'>$cName</td>";
		echo "<td class='A0101' align='center'>$Qty</td>";
		echo "<td class='A0101' align='center'>$blQty</td>";
		echo "<td class='A0101' align='center'>$scQty</td>";
		echo "<td class='A0101' align='center'>$chQty</td>";
		echo"<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>";
		echo "</tr>";
		
  }
  
    $Relation=0;		
	$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P 
		 INNER JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
		 INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		 WHERE  P.ProductId='$ProductId' and T.TypeId=" . $APP_CONFIG['CARTON_TYPE'],$link_id);
	if($BoxRows = mysql_fetch_array($BoxResult)){
	      $Relation=$BoxRows["Relation"];
	      $RelationArray=explode("/",$Relation);
	      if($RelationArray[1]!="") $Relation=$RelationArray[1]; else $Relation=$RelationArray[0];
	 }
	 $RelationStr=$Relation==0?"未设置":$Relation . " / 箱";
	  echo "<input name='Relation' type='hidden' id='Relation' value='$Relation'>";
?>		
	<tr <?php  echo $Fun_bgcolor?>>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
		<td bgcolor='#FFFFFF' colspan='7'>2、生产工单数据  (<span style='color:#00F;'>装箱数量:<?php echo $RelationStr;?></span>)</td>
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
		<td width='70' class='A1101' align='center'>生产线</td>
		<td width='70' class='A1101' align='center'>工单数量</td>
		<td width='70' class='A1101' align='center'>备料状态</td>
		<td width='70' class='A1101' align='center'>已生产数</td>
		<td width='70' class='A1101' align='center'>拆分后锁定</td>
		<td width='70' class='A1101' align='center'>操作员</td>
	</tr>
			<?php 
			$i=1;$j=0;
			$sheetResult2=mysql_query("SELECT S.sPOrderId,S.WorkShopId,S.Qty,S.scFrom,S.Estate,S.scQty,S.Operator,
			    A.Name AS ActionName,L.Name AS LineName,SUM(IFNULL(C.Qty,0)) AS blQty    
			    FROM $DataIn.yw1_scsheet S 
			    LEFT JOIN $DataIn.cg1_stocksheet  G ON S.StockId=G.StockId 
			    LEFT JOIN $DataIn.workorderaction A ON A.ActionId=S.ActionId 
			    LEFT JOIN $DataIn.workscline L ON L.Id=S.scLineId 
			    LEFT JOIN $DataIn.ck5_llsheet C ON C.sPOrderId=S.sPOrderId 
			    WHERE 1 and S.POrderId='$POrderId' and G.Level=1 GROUP BY S.sPOrderId",$link_id); 
			while($sheetRow = mysql_fetch_array($sheetResult2)){
			    $delSign=1;
			    $sPOrderId= $sheetRow['sPOrderId'];
			    $Qty= $sheetRow['Qty'];
			    $scQty= $sheetRow['scQty'];
			    $scForm= $sheetRow['scFrom'];
			    $Estate= $sheetRow['Estate'];
			    $blQty = $sheetRow['blQty'];
			    $blText=$blQty==0?"未备料":"<div class='greenB'>已备料</div>";
			    $qtyStauts=($Estate==0 || $scQty>0 || $scForm==2)?"readonly":" onchange='changeQty(this,$scQty,$Qty)' ";
			    if ($Estate==0 || $scQty>0 || $scForm!=1) $delSign=0;
			    
			    $Operator= $sheetRow['Operator'];
			    include "../model/subprogram/staffname.php";
			    
			    $ActionName= $sheetRow['ActionName'];
			    $WorkShopId= $sheetRow['WorkShopId'];
			    $LineName= $sheetRow['LineName']==""?"未设置":$sheetRow['LineName'];
			    
			    $WorkShopStr="";
			    $WorkShopResult = mysql_query("SELECT Id,Name FROM $DataIn.workshopdata WHERE Estate=1",$link_id);
		        while($WorkShopRow = mysql_fetch_array($WorkShopResult)){
			         $W_Id=$WorkShopRow['Id']; 
			         $W_Name=$WorkShopRow['Name'];   
                     $SelectedStr=$WorkShopId==$W_Id?" selected ":"";
                     $WorkShopStr.="<option value='$W_Id' $SelectedStr>$W_Name</option>";
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
			    
			    $CheckLockSql=mysql_query("SELECT Id,Remark FROM $DataIn.yw1_sclock WHERE sPOrderId ='$sPOrderId' AND Locks=0 LIMIT 1",$link_id);
			    $lockChecked ="";
			    if($CheckLockRow = mysql_fetch_array($CheckLockSql)){
				    $lockChecked ="checked";
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
						<td class='A0110' align='center'>$LineName</td>
						<td class='A0110' align='center'>
						<input name='Qty[]' type='text' id='Qty$j' value='$Qty' style='width:70px;' $qtyStauts>
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
</table>
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
	   var totalqty=0;
	   var checkCarton=false;
	   var tempLockSign = 0 ;
	   for (var i=0;i<Qtys.length;i++){
	      totalqty+=Qtys[i].value*1;
	      if (Qtys[i].readOnly==false){
			   if (Qtys[i].value<=0){
				   //alert("工单数量不能为0或空");
				   swal("工单数量不能为0或空");
				   return false;
			   }
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
			  
			  if (Relation>0){
				  var BoxNum=Qtys[i].value%Relation;
				  if(BoxNum>0){
				     checkCarton=true;
				  }
			  }
		  }
	   }
      
	   var orderQty=document.getElementById('OrderQty').value*1;
	   if (totalqty!=orderQty || sumqty<=0){
	      //alert("工单数量和:"+sumqty + " 不等于订单数量:"+orderQty);
	      swal("工单数量和:"+sumqty + " 不等于订单数量:"+orderQty);
	      return false;
	   }
	   
	   var confirmSign = true;
	   if (checkCarton==true){
		   confirmSign=confirm("有单输入的数量是不是整箱数!是否继续?");
	   }
	   
	   
	   
	   
	   if (confirmSign==true && qtyList!=""){
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
		  
		  document.form1.action="yw_order_updated.php";
		  document.form1.submit();
		  return true;
	   }
   
      return false;
}

function getParent (el, parentTag) {
    do {
        el = el.parentNode;
    } while (el && el.tagName !== parentTag);
    return el;
}

function changeQty(e,scqty,oldVal){

   var  rowLength = parseInt(ListTable.rows.length)-1;
   var el = getParent(e, 'TR');
   var rowIndex = parseInt(el.rowIndex);


   if (fucCheckNUM(e.value,'Price')==0){
	   //alert("请填入正确的数字");
	   swal("请填入正确的数字");
	   e.value=oldVal;
	   return false;
   }
   else{
	  if (e.value*1<scqty){
		 //alert("工单数量不能小于已生产数量"); 
		 swal("工单数量不能小于已生产数量");
		 e.value=oldVal;
		 return false;
	  }
   }
   
   var Relation=document.getElementById("Relation").value;
   
   if(Relation!="" && Relation!=0 && (e.value%Relation!=0)  && rowLength>rowIndex){  //最后一张工单可以不用整箱
   
	     //alert("请按整数箱拆工单!"); 
	     swal("请按整数箱拆工单!");
		 e.value=oldVal;
		 return false;
	   
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
		
		oTD=oTR.insertCell(5);
		oTD.innerHTML="<?php echo $LineName;?>";
		oTD.className ="A0110";
		oTD.align="center";
		
		var ids=document.getElementById("IdCount").value;
		oTD=oTR.insertCell(6);
		oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+ids+"' value='0' style='width:70px;' onchange='changeQty(this,0,0)' >"+"<input name='SPID[]' type='hidden' id='SPID"+ids+"' value='0'>";
		oTD.className ="A0110";
		document.getElementById("IdCount").value=ids*1+1;
		
		oTD=oTR.insertCell(7);
		oTD.innerHTML="&nbsp;";
		oTD.className ="A0110";

		oTD=oTR.insertCell(8);
		oTD.innerHTML="&nbsp;";
		oTD.className ="A0110";
		
		oTD=oTR.insertCell(9);
		oTD.innerHTML="<input type='checkbox' id='lockSign"+ids+"' name='lockSign[]' >";
		oTD.className ="A0110";
		oTD.align="center";
		
		oTD=oTR.insertCell(10);
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