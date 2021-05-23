<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
//电信-zxq 2012-08-01
$Th_Col="编号|50|置换日期|70|配件ID|50|配件名称|240|置换原因|200|置换人|45|序号|40|需求单流水号|90|所属订单PO|70|原领料数|55|现领料数|55";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}
$SearchRows="";
$GysList="";
$nowInfo="当前:置换备料记录";
$funFrom="item5_10";
$addWebPage=$funFrom . "_add.php";

	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.sc_stuffrepmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		$GysList="<select name='chooseDate' id='chooseDate' onchange='ResetPage(4,5)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			//$StartDate=$dateValue."-01";
			//$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateValue</option>";
				//$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				$GysList.="<option value='$dateValue'>$dateValue</option>";
				}
		 }while($dateRow = mysql_fetch_array($date_Result));
		$GysList.="</select>&nbsp;";
	}

//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
	$GysList1="<input class='ButtonH_25' type='button'  id='addBtn' value='新 增' onclick=\"openWinDialog(this,'$addWebPage',820,560,'center')\" $addBtnDisabled/>";

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList </td><td colspan='3' class=''>$GysList1</td><td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
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

$mySql="SELECT M.Id AS Mid,M.Date,M.StuffId,M.Estate,M.Remark,M.Operator,
		S.Id,S.StockId,S.oldQty,S.Qty,S.Locks,
		D.StuffCname,D.TypeId,D.Picture,P.cName,Y.OrderPO
FROM $DataIn.sc_stuffrepmain M
LEFT JOIN $DataIn.sc_stuffrepsheet S ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=M.StuffId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	  $newMid="";
	do{
		   $m=1;
		//主单信息
		    $Mid=$mainRows["Mid"];
		    $Date=$mainRows["Date"];
		    $Operator=$mainRows["Operator"];
			include "../admin/subprogram/staffname.php";
		    $StuffId=$mainRows["StuffId"];
			$StuffCname=$mainRows["StuffCname"];
		    $Estate=$mainRows["Estate"];
			$Remark=$mainRows["Remark"];
		//明细单信息
			$checkidValue=$mainRows["Id"];
			$Qty=$mainRows["Qty"];
			$oldQty =$mainRows["oldQty"];
			$StockId=$mainRows["StockId"];
			$Locks=$mainRows["Locks"];
			$cName=$mainRows["cName"];
			$OrderPO=$mainRows["OrderPO"];
			$Picture=$mainRows["Picture"];
			$TypeId=$mainRows["TypeId"];
			//检查是否有图片
			 $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		     include "../model/subprogram/stuffimg_model.php";

		//输出主单信息
		    if ($newMid!=$Mid){
			   $newMid=$Mid;$j=1;
			   if ($i!=1) {echo"</table></td></tr></table>";}
		       echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' >$Mid</td>";//编号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";	//置换日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";	//配件Id
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$StuffCname</td>";	//配件名称
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";//置换原因
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";//置换人
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
		       }
			else{
				$m=13;
			}
			   //输出明细信息
			   	echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo "<tr height='30'>";
				$unitFirst=$Field[$m]-1;
			    echo"<td class='A0001' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//需求流水号
			    $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderPO</td>";//订单PO
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$oldQty</div></td>";//原领料数量
				$m=$m+2;
				echo"<td class='A0000' width='' align='right'>$Qty</td>";//置换后数量
				echo "</tr>";
				$i++;$j++;
		}while($mainRows = mysql_fetch_array($mainResult));
     echo"</table></td></tr></table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script src='showkeyboard.js' type=text/javascript></script>
<script language = "JavaScript">
var keyboard=new KeyBoard();

function CnameChanged(){
	StuffCname=document.getElementById("StuffCname").value;
	if (StuffCname.length>=2){
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  document.getElementById("stuffQuery").disabled=true;
	}
}
function CheckForm(){
	var Message="";
	var tabLen=ListTable.rows.length;
	if(tabLen<1){
		Message="没有设置换备料数据的记录!";
		alert(Message);return false;
	   }
	else{
	   if(tabLen<3){
		  Message="置换备料数据不能少于两条记录!";
		  alert(Message);return false;
		}
	}

	 for(var i=0;i<tabLen-1;i++){
	  	    var tempVal6=ListTable.rows[i].cells[6].innerHTML;
			var tempVal8=ListTable.rows[i].cells[8].innerHTML;
		    if(tempVal8==tempVal6 || tempVal8==""){
			   var j=i+1;
			   Message=Message+j+",";
		    }
	    }
	   if(Message!=""){
		  Message="记录序号:"+Message.substring(0,Message.length-1)+"未参与置换，请删除！";
		  alert(Message);
		  return false;
	   }

	var tmpTotal1=document.getElementById("tempTotal").value;
	var tmpTotal2=document.getElementById("tempTotal2").value;
	if (tmpTotal1!=tmpTotal2){
	   Message="错误，置换前的领料总数与置换后的领料总数不相等!";
	   alert(Message);return false;
	}

	var Remark=document.getElementById("Remark").value;
	Remark=Remark.replace(/^\s+|\s+$/g,"");//去除两边空格
	if (Remark==""){
	   Message="请填写置换原因!";
	   alert(Message);return false;
	}

	var tempValue="";
	for(var i=0;i<tabLen-1;i++){
	  tempValue=tempValue+ListTable.rows[i].cells[0].data+"|"+ListTable.rows[i].cells[2].innerHTML+"|"+ListTable.rows[i].cells[6].innerHTML+"|"+ListTable.rows[i].cells[8].innerHTML;
	  if (i<tabLen-2){tempValue=tempValue+",";}
	 }
   document.getElementById("TempValue").value=tempValue;
   return true;
}


function viewStuffdata() {
	var diag = new Dialog("live");
	var StuffCname=document.getElementById("StuffCname").value;
	var StuffId=document.getElementById("StuffId").value;
	if (StuffCname=="") return false;
	diag.Width = 950;
	diag.Height = 600;
	diag.Title = "配件领料记录";
	diag.URL = "viewlldata.php?Action=1&oldStuffId="+StuffId+"&searchSTR="+StuffCname;
	diag.ShowMessageRow = false;
	diag.MessageTitle ="";
	diag.Message = "";
	diag.ShowButtonRow = true;
	diag.selModel=2; //1只选一条；2多选；
	diag.OKEvent=function(){
		var backData=diag.backValue();
		if (backData.length>0){
			editTabRecord(backData);
		    diag.close();
		   }
		};
	diag.show();
}

function editTabRecord(BackStuffId){
  		var Rowstemp=BackStuffId.split(",");
		var Rowslength=Rowstemp.length;
		var addFlag=0;
		var tabLen=ListTable.rows.length;
		if (tabLen>1) {ListTable.deleteRow(tabLen-1);addFlag=1;}
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldArray=Rowstemp[i].split("^^");				//$StuffId."^^".$StuffCname."^^".$LlId."^^".$StockId."^^".$OrderQty."^^".$Qty."^^".$OrderPO
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StockIdtemp=ListTable.rows[j].cells[0].data;;
				if(FieldArray[2]==StockIdtemp){//如果领料ID号存在
					Message="需求单流水号: "+FieldArray[3]+"的领料记录已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				addFlag=1;
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.data=FieldArray[2];
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50px";
				oTD.height="20";

				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40px";

				//三、需求流水号
				oTD=oTR.insertCell(2);
				//oTD.innerHTML=""+FieldArray[0]+"";
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100px";

				//四：订单PO
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";

				//五：配件名称
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="210px";

			   	//六：需备料数
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";


            	//七：原领料数
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";

				//八:退换数量
				var addStr="<option value='1'>增加</option>";
				var operStr=1;
				if (parseInt(FieldArray[5])>=parseInt(FieldArray[4])) {
					addStr="";
					operStr=-1;
				}
				if (parseInt(FieldArray[5])>0){
				   addStr=addStr+"<option value='-1'>减少</option>";
				}
				oTD=oTR.insertCell(7);
				oTD.innerHTML="<select name='operator' id='operator' onchange='operChange(this)'>"+addStr+"</select><input type='text' name='thQTY' id='thQTY' size='5' class='I0000L' value='' onfocus=showKeyboard(this) readonly/>";
				//oTD.innerHTML="<input type='text' name='thQTY[]' id='thQTY' size='6' class='I0000L' value='' onblur='Indepot(this,"+FieldArray[2]+")' onfocus='toTempValue(this.value)'>";
				oTD.data="";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="150px";

				//九:置换后数量
				oTD=oTR.insertCell(8);
				oTD.data=operStr;
				oTD.innerHTML="";
				oTD.className ="A0100";
				oTD.style.color="#F00";
				oTD.align="center";
				oTD.width="";
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)

	 if(addFlag>0) addTotalRow();
	 document.getElementById("StuffId").value=FieldArray[0];
	 document.getElementById("StuffCname").value=FieldArray[1];
}

function addTotalRow(){
	  var j,tempQty;
	  var tempTotal2=0;
	  var tempTotal=0;
		// var changeQty=document.getElementsByName("changeQty");
		 var tabLen=ListTable.rows.length;
	     for(i=0;i<tabLen;i++){
		   tempTotal=tempTotal+parseInt(ListTable.rows[i].cells[6].innerHTML);
		   j=i+1;
		   tempQty=ListTable.rows[i].cells[8].innerHTML;
		   tempQty=tempQty==""?"0":tempQty;
	       tempTotal2=tempTotal2+parseInt(tempQty);
		 }
	       oTR=ListTable.insertRow(ListTable.rows.length);
		   oTD=oTR.insertCell(0);
		   oTD.colSpan="6";
		   oTD.innerHTML="合 计";
		   oTD.className ="A0101";
		   oTD.align="center";
		   oTD.width="520px";
		   oTD.height="20";

		   oTD=oTR.insertCell(1);
		   oTD.innerHTML="<input type='text' name='tempTotal' id='tempTotal' class='I0000L' size='5' value='"+tempTotal+"' readonly>";
		   oTD.className ="A0101";
		   oTD.align="center";
		   oTD.width="60px";

		   oTD=oTR.insertCell(2);
		   oTD.innerHTML="&nbsp;";
		   oTD.className ="A0101";
		   oTD.align="center";
		   oTD.width="150px";

		   oTD=oTR.insertCell(3);
		   oTD.innerHTML="<input type='text' name='tempTotal2' id='tempTotal2' class='I0000L' size='5' value='"+tempTotal2+"' readonly>";
		   oTD.className ="A0101";
		   oTD.align="center";
		   oTD.width="";
}

function showKeyboard(e){
    var addQtyFun=function(){
	   var i=e.parentNode.parentNode.rowIndex;
	   ListTable.rows[i].cells[7].data=e.value;
	   if (!operChangeVal(i)){
		   e.value="";
		   keyboard.show(e,'','','',addQtyFun);
	   }
	}
    keyboard.show(e,'','',e.value,addQtyFun);
}
/*
function checkNumber(e){
	var str=e.value;
	var i=e.parentNode.parentNode.rowIndex;
	var Lens= str.length;
	var patrn=/^[0-9]{1,10}$/;
	if (!patrn.exec(str) && Lens>0){
		if (Lens==1) {
			e.value=" ";
			ListTable.rows[i].cells[7].data="";
		   }
	  else {
		  str=str.substring(0,Lens-1);
		   e.value=str;
		  ListTable.rows[i].cells[7].data=str;
		  }
	}
}
*/
function operChange(e){
   var i=e.parentNode.parentNode.rowIndex;
   ListTable.rows[i].cells[8].data=e.value;
   operChangeVal(i);
}
/*
function qtyChange(e){
	var i=e.parentNode.parentNode.rowIndex;
	ListTable.rows[i].cells[7].data=e.value;
	operChangeVal(i);
}*/

function operChangeVal(i){
	var tempVal7=ListTable.rows[i].cells[7].data;
	var tempVal8=ListTable.rows[i].cells[8].innerHTML;
	if  (tempVal7=="" && tempVal8=="") return false;
	var tempVal5=parseInt(ListTable.rows[i].cells[5].innerHTML);
	var tempVal6=parseInt(ListTable.rows[i].cells[6].innerHTML);
	var operVal=parseInt(ListTable.rows[i].cells[8].data);
	var tempVal=tempVal7*operVal+tempVal6;
	if  (tempVal>tempVal5 || tempVal<0){
		alert ("错误！置换后的领料数量不能大于需备料数或小于零。");
		 ListTable.rows[i].cells[8].innerHTML="";
		 return false;
	    }
	else{
		 ListTable.rows[i].cells[8].innerHTML=tempVal;
	}
	//重新计算置换后的数量总数
		 var tmpTotal2=0;var tempQty=0;
	     var tabLen=ListTable.rows.length-1;
	     for(var k=0;k<tabLen;k++){
		   tempQty=ListTable.rows[k].cells[8].innerHTML;
		   tempQty=tempQty==""?"0":tempQty;
	       tmpTotal2=tmpTotal2+parseInt(tempQty);
		 }
		document.getElementById("tempTotal2").value=tmpTotal2;
		return true;
}


//删除指定行
function deleteRow(rowIndex){
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
    var tabLen=TableTemp.rows.length-1;
	  TableTemp.deleteRow(tabLen);//删除合计行
	  if (tabLen<=0){
		   document.getElementById("StuffId").value="";
	   }
	  else{
	    for(var i=0;i<tabLen;i++){
  		   var j=i+1;
		  TableTemp.rows[i].cells[1].innerText=j;

	    }//end for
		addTotalRow();
	 }
}
</script>