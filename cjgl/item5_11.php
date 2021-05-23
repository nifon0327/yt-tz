<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
//电信-zxq 2012-08-01
$checkResult = mysql_query("SELECT JobId,GroupId FROM $DataPublic.staffmain WHERE Number=$Login_P_Number ORDER BY Number LIMIT 1",$link_id);
$checkRow = mysql_fetch_assoc($checkResult);
$JobId=$checkRow["JobId"];
$groupId = $checkRow["GroupId"];
//OK
$Th_Col="序号|30|客户|80|退料日期|70|配件Id|60|需求单流水号|100|配件名称|300|领料数量|60|退料数量|60|单位|30|库位|80|退料原因|80|操作员|60|审核|50";

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
$nowInfo="当前: 车间退料数据";
$funFrom="item5_11";
$addWebPage=$funFrom . "_add.php";
$updateWebPage=$funFrom . "_update.php";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname') ";
	$GysList="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'/>";
   }
else{
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.sc_tlsheet WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		$GysList.="<select name='tlDate' id='tlDate'  onchange='ResetPage(1,5)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$tlDate=$tlDate==""?$dateValue:$tlDate;
			if($tlDate==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Date>='$StartDate' and S.Date<='$EndDate'";
				}
			else{
				$GysList.="<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		$GysList.="</select>&nbsp;";
		}
	//分类
	   $selStr="selFlag" . $chooseType;
	   $$selStr="selected";
	   $GysList.="<select name='chooseType' id='chooseType' onchange='ResetPage(1,5)'>";
	   $GysList.= "<option value='' $selFlag>全部</option>";
	   $GysList.= "<option value='1' style= 'color: $Color;font-weight: bold' $selFlag1>不良品</option>";
	   $GysList.= "<option value='2' style= 'color: $Color;font-weight: bold' $selFlag2>取消生产</option>";
	   $GysList.= "<option value='3' style= 'color: $Color;font-weight: bold' $selFlag3>其它</option>";
	   $GysList.="</select>&nbsp;";
	   if($chooseType>0) $SearchRows.="AND S.Remark='$chooseType'";
	   $GysList.="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}
//有权限且不是仓管员
if($SubAction==31 && $groupId!=701) $addBtnDisabled=""; else $addBtnDisabled="disabled";
 $GysList1="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',820,560,'center')\" $addBtnDisabled>新 增</span>";

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr >
	<td colspan='".($Cols-7)."' height='40px' class=''>$GysList </td><td colspan='3' class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT S.Id,O.Forshort,S.Date,S.StuffId,S.StockId,S.oldQty,S.Qty,S.Remark,S.Type,S.Locks,S.Estate,D.StuffCname,D.Picture,A.Name AS Operator,U.Name AS UnitName,L.Identifier  
FROM $DataIn.sc_tlsheet S 
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.staffmain A ON A.Number=S.Operator 
LEFT JOIN $DataIn.ck_location L ON L.Id = S.LocationId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
WHERE 1 $SearchRows ORDER BY S.Date DESC,S.Id DESC";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow['Forshort'];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$Qty=$myRow["Qty"];
		$oldQty=$myRow["oldQty"];
		$StockId=$myRow["StockId"];
		$sPOrderId=$myRow["sPOrderId"];
		$Remark=$myRow["Remark"];
		$Identifier=$myRow["Identifier"]==""?"&nbsp;":$myRow["Identifier"];
		switch($Remark){
			case 1:
			  $Remark="不良品";
			  $RemarkColor="style='color:#F00'";
			  break;
			case 2:
			  $Remark="取消生产";
			   $RemarkColor="style='color:#03F'";
			  break;
			case 3:
			  $Remark="其它";
			  $RemarkColor="style='color:#FC3'";
			  break;
			default:
			  $Remark="&nbsp;";
			  break;
		}
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//检查是否有图片
		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		$Type=$myRow["Type"];

       		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="";
	    if ($Estate==1){//未审核
            if(($SubAction==31 && $groupId==701) || $Login_P_Number=='10871' || $Login_P_Number=='10868'){//有权限,同时须为仓管员
	           $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
		       $UpdateClick=" onclick='passdata(this,$i,$Id)'";
		      }
	        else{//有权限删除
		       if ($SubAction==31){
				  $UpdateIMG="<img src='../images/unPass.png' width='30' height='30'>";
			      $UpdateClick="onclick='deltldata(this,$Id)';";
			   }
			  else{//无权限
			      $UpdateClick="";
			      $UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
			   }
	        }
	     }
		  else{
			   $UpdateIMG="<div class='greenB'>已核</div>";
		  }
			echo"<tr><td class='A0111' align='center' height='25' >$i</td>";
			echo"<td class='A0101' align='center'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101' align='center'>$StockId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$oldQty</td>";
			echo"<td class='A0101' align='right'>$Qty</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101' align='center'>$Identifier</td>";
			echo"<td class='A0101' align='center' $RemarkColor>$Remark</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
    echo"</table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='".$Cols."' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
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


function passdata(e,n,Id){
   var msg = "请确定退料是否正确？";
   if(confirm(msg)){
	   var url="item5_11_ajax.php?Id="+Id+"&ActionId=4";
	    var ajax=InitAjax();
		    ajax.open("GET",url,true);
		    ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){// && ajax.status ==200
			      var retText=ajax.responseText;
				  retText=retText.replace(/^\s+|\s+$/g,"");
				  if(retText=="Y"){//更新成功
					 e.innerHTML="&nbsp;";
				     e.style.backgroundColor="#339900";
				     e.onclick="";
					}
				 else{
				    alert ("审核失败！");
				  }
			   }
			}
		   ajax.send(null);
	 }
}

function deltldata(e,Id){

  msgStr="您确认要删除该记录吗？";
  if(confirm(msgStr)) {
        var url="item5_11_ajax.php?Id="+Id+"&ActionId=3";
        var ajax=InitAjax();
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			     e.innerHTML="已删除";
			     e.style.color="#FF0000";
			     e.onclick="";
				//document.form1.submit();
				}
			}
		 }
	   ajax.send(null);
     }
}

function viewStuffdata(ActionId) {
	var diag = new Dialog("live");
	var StuffId=document.getElementById("StuffId").value;
	switch(ActionId){
	   case 1://配件名称查询
	        var StuffCname=document.getElementById("StuffCname").value;
	        if (StuffCname=="") return false;
		    diag.URL = "viewlldata.php?Action=2&oldStuffId="+StuffId+"&searchSTR="+StuffCname;
		 break;
		case 3://订单流水号查询
		    var tlReson=document.getElementById("tlReson").value;
            if(tlReson=="" || tlReson==undefined){alert("请选择退料原因!");return false;}
            else{
                  if(tlReson=="2"){alert("因取消生产的配件退料,请整单退料!");}
                  }
		    var sPOrderId=document.getElementById("sPOrderId").value;
			diag.URL = "viewlldata.php?Action=3&oldStuffId="+StuffId+"&searchSTR="+sPOrderId;
		 break;
	  }
	diag.Width = 950;
	diag.Height = 600;
	diag.Title = "配件领料记录";
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
        var thisQty,lastQty;
		var tlReson=document.getElementById("tlReson").value;
  		var Rowstemp=BackStuffId.split(",");
		var Rowslength=Rowstemp.length;
		var tabLen=ListTable.rows.length;
               switch(tlReson){
                     case "1":lastReson="不良品";break;
                     case "2":lastReson="取消生产";break;
                     case "3":lastReson="其它";break;
                                }

		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldArray=Rowstemp[i].split("^^");				//$StuffId."^^".$StuffCname."^^".$StockId."^^".$Qty."^^".$OrderPO
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StockIdtemp=ListTable.rows[j].cells[2].innerHTML;
				if(FieldArray[2]==StockIdtemp){//如果领料ID号存在
					Message="需求单流水号: "+FieldArray[2]+"的领料记录已在列表!跳过继续！";
					break;
					}
			 }
			if (parseInt(FieldArray[3])==0){
				    Message="需求单流水号: "+FieldArray[2]+"的领料数量为零!跳过继续！";
				}
			if(Message==""){
                if(tlReson=="2"){thisQty=FieldArray[3];lastQty=0;}
               else { thisQty=""; lastQty="";}
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				document.getElementById("sPOrderId").value = ""+FieldArray[4]+"";
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
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
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100px";

				//四，配件ID
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";

				//五：配件名称
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.data=FieldArray[0];
				oTD.className ="A0101";
				oTD.width="210px";

			   	//六：已领料数
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";


            	//七：退料数
				oTD=oTR.insertCell(6);
				oTD.data="";
				oTD.innerHTML="<input type='text' name='thQTY"+tmpNum+"' id='thQTY"+tmpNum+"' size='5' class='I0000L' value='"+thisQty+"' onblur=checkNumber(this,"+tmpNum+") /><input type='hidden' name='thPrice"+tmpNum+"' id='thPrice"+tmpNum+"' value='"+FieldArray[5]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60px";

				//八:退料原因
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+lastReson+"";
				oTD.data="1";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="150px";

				//九:退料后数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML=""+lastQty+"";
				oTD.className ="A0100";
				oTD.style.color="#F00";
				oTD.align="center";
				oTD.width="";
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
}



function saveQty(){
	var Message="";
	var tabLen=ListTable.rows.length;
	if(tabLen<1){
		Message="没有设置退料数据的记录!";
		alert(Message);return false;
	   }

	 for(var i=0;i<tabLen;i++){
	  	    var tempVal5=ListTable.rows[i].cells[5].innerHTML;
			var tempVal8=ListTable.rows[i].cells[8].innerHTML;
		    if(tempVal8==tempVal5 || tempVal8==""){
			   var j=i+1;
			   Message=Message+j+",";
		    }
	    }
	   if(Message!=""){
		  Message="记录序号:"+Message.substring(0,Message.length-1)+"未设置退料数量，请删除！";
		  alert(Message);
		  return false;
	   }
    var sPOrderId=document.getElementById("sPOrderId").value;
    var tlReson=document.getElementById("tlReson").value;
    var ReturnCkSign=document.getElementById("ReturnCkSign").value;
	var TempValue="";
	for(var i=0;i<tabLen;i++){
      var index=i+1;
      var tempQty =document.getElementById("thQTY"+index).value;
      var tempPrice = document.getElementById("thPrice"+index).value;
	  TempValue=TempValue+sPOrderId+"|"+ListTable.rows[i].cells[2].innerHTML+"|"+ListTable.rows[i].cells[3].innerHTML+"|"+ListTable.rows[i].cells[5].innerHTML+"|"+tempQty+"|"+tlReson+"|"+tempPrice+"|"+ReturnCkSign;
	  if (i<tabLen-1){TempValue=TempValue+",";}
	 }
    document.getElementById("TempValue").value=TempValue;
    if(TempValue!=""){
	   var url="item5_11_ajax.php?TempValue="+TempValue+"&ActionId=1";
	    var ajax=InitAjax();
		    ajax.open("GET",url,true);
		    ajax.onreadystatechange =function(){
			 if(ajax.readyState==4){// && ajax.status ==200
			      var retText=ajax.responseText;
			      alert(retText)
				  retText=retText.replace(/^\s+|\s+$/g,"");
				  if(retText=="Y"){//更新成功
				     alert ("退料数据添加成功！");
				     document.form1.submit();
					}
				 else{
				    alert ("退料数据添加失败,检查改工单是否已生产完毕！");
				  }
				  closeWinDialog();
			   }
			}
		   ajax.send(null);
     }
}




function operChange(e){
    var i=e.parentNode.parentNode.rowIndex;
    ListTable.rows[i].cells[7].data=e.value;
}



function  checkNumber(e,index){
	 var i=e.parentNode.parentNode.rowIndex;
	 if (isnumber(e.value) && e.value!="" ){
	      document.getElementById("thQTY"+index).value=e.value;
		  var tempVal5=parseInt(ListTable.rows[i].cells[5].innerHTML);
	      var tempVal6=parseInt(e.value);
	      if(tempVal6>tempVal5){
		      alert("超出范围!");
		      e.value = "";
		      return false;
	      }
		  ListTable.rows[i].cells[8].innerHTML=tempVal5- tempVal6;

	  }else{
		  alert("不是规范的数字");
		  e.value = "";
	  }
}


function isnumber(str){
    var digits=".1234567890";
    var i=0;
    var strlen=str.length;
    while((i<strlen)){
        var char=str.charAt(i);
        if(digits.indexOf(char)==-1)return false;
		i++;
    }
    return true;
}



function showKeyboard(e,index){
	var i=e.parentNode.parentNode.rowIndex;
	var limitData=ListTable.rows[i].cells[5].innerHTML;
    var addQtyFun=function(){
	  if (e.value!=""){
	     document.getElementById("thQTY"+index).value=e.value;
		  var tempVal5=parseInt(ListTable.rows[i].cells[5].innerHTML);
	      var tempVal6=parseInt(e.value);
		  ListTable.rows[i].cells[8].innerHTML=tempVal5- tempVal6;
	  }
	 }
    keyboard.show(e,limitData,'<=','',addQtyFun);
}

//删除指定行
function deleteRow(rowIndex){
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j;
		}
	}
</script>