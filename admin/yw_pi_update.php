<?php   
//电信-zxq 2012-08-01
//已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新PI");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
//$_SESSION["nowWebPage"]=$nowWebPage; 
$_SEESION["nowWebPage"] = $nowWebPage;
//步骤3：//需处理
/*
$upData =mysql_fetch_array(mysql_query("SELECT P.CompanyId,P.PaymentTerm, P.Notes, P.Terms,P.Date,C.Forshort,P.OtherNotes,P.ShipTo  
FROM $DataIn.yw3_pisheet P,$DataIn.trade_object C WHERE P.CompanyId=C.CompanyId AND P.PI='$Id' LIMIT 1",$link_id));
*/
$upData =mysql_fetch_array(mysql_query("SELECT P.CompanyId,P.PaymentTerm, P.Notes, P.Terms,P.Date,C.Forshort,P.OtherNotes,P.ShipTo,P.SoldTo,D.InvoiceModel
										FROM $DataIn.yw3_pisheet P
										LEFT JOIN $DataIn.trade_object C ON P.CompanyId=C.CompanyId
										LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId 
										WHERE P.PI='$Id' AND D.PiSign=1 
										UNION ALL 
										SELECT P.CompanyId,P.PaymentTerm, P.Notes, P.Terms,P.Date,C.Forshort,P.OtherNotes,P.ShipTo,P.SoldTo,D.InvoiceModel
										FROM $DataIn.yw3_pisheet P
										LEFT JOIN $DataIn.trade_object C ON P.CompanyId=C.CompanyId
										LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId 
										WHERE P.PI='$Id'  LIMIT 1										
										",$link_id));
echo 
$PaymentTerm=$upData["PaymentTerm"];
$Forshort=$upData["Forshort"];
$OtherNotes=$upData["OtherNotes"];
$Notes=$upData["Notes"];
$Terms=$upData["Terms"];
$ShipTo=$upData["ShipTo"];
$SoldTo=$upData["SoldTo"];
$CompanyId=$upData["CompanyId"];
if($InvoiceModel==""){
	$InvoiceModel=$upData["InvoiceModel"];
}
//echo "InvoiceModel:$InvoiceModel <br>";	

$ModelStr="Model" . $InvoiceModel;
$$ModelStr="selected";
//echo "Mode1:$Model1,Model2:$Model2";

$oNotesDisplay="display:none;";
if ($CompanyId==1004 || $CompanyId==1059){
    $oNotesDisplay="";
}
//步骤4：
$CheckFormURL="thisPage";
$tableWidth=1330;$tableMenuS=500;$spaceSide=15;
//$tableWidth=1100;$tableMenuS=500;$spaceSide=15;
$CustomFun="<span onClick='ViewOrderId(7)' $onClickCSS>加入订单</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,TempValue,";
//步骤4：需处理
?>
<table border="0" width="1100" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
     <tr>
      <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1101' align='center'  width='50%'  height='30'>
                PI NO.:<input name="PINO" type="text" id="PINO" size="40" value="<?php    echo $Id?>" readonly="readonly" >
                &nbsp;&nbsp;
                模板选择：<select name='InvoiceModel' id='InvoiceModel' onchange='ResetPage(this.name)'>
    					 	<option value='1' <?php echo $Model1 ?>>英文</option>
        					<option value='2' <?php echo $Model2 ?>>中文</option>
      					</select
                ></td>
                <td align='center' class='A1100' >PaymentTerm:<input name="PaymentTerm" type="text" id="PaymentTerm" size="40" value="<?php    echo $PaymentTerm?>" dataType="Require" msg="没有填写Payment term"> </td>
           
            </tr>    
          
        </table>
      </tr>
	 <tr>
        <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A0001' width='50%' align='center' height='30'>Notes</td>
                <td align='center' class='A0000' >Terms</td>
           
            </tr>    
            <tr>  
            
                <td class='A1101' width='50%' align='center'><textarea name="Notes" cols="47" rows="3" id="Notes"  ><?php    echo $Notes?></textarea>
                    </br><div id="oNotesDiv" name="oNotesDiv" style="<?php    echo $oNotesDisplay?>">Other Notes:<input name="OtherNotes"  id="OtherNotes"  value="<?php    echo $OtherNotes?>" style="width:270px;margin-top:10px;"/>(CEL专用)</div>
                </td>
                <td align='center' class='A1100'><textarea name="Terms" cols="47" rows="3" id="Terms"><?php    echo $Terms?></textarea></td>
              
            </tr>   
            <!--<php  if ($CompanyId==1088 || $CompanyId==1036 || $CompanyId==1046){?> -->
	                <tr> 
                  <td class='A0000'   height='30' colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;SHIP TO: <input name="ShipTo" id="ShipTo" value="<?php    echo $ShipTo?>"  style="width:600px;"/>注意：默认在PI模板设置！临时收货地址才在此指定！ <br />
                  &nbsp;&nbsp;&nbsp;&nbsp;SOLD TO: <input name="SoldTo" id="SoldTo" value="<?php    echo $SoldTo?>"  style="width:600px;"/>注意：默认在PI模板设置！临时收货地址才在此指定！
                  
                  </td>

                   
            </tr>  
            <!--  <php }?>  -->
        </table>
 	</tr>    
<!--   
 	 <tr>
        <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1110' width='50%' align='center' height='10'></td>
                <td align='center' class='A1101' ></td>
        </table>
 	</tr>     
    
  -->   
 <tr>
    <td colspan="2" align="center" valign="top" class='A0010'>
	  
<table border="0"   cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="80" height="25" align="center" class="A0101">操作</td>
		<td class="A0101" width="40" align="center">序号</td>
		<td class="A0101" width="100" align="center">订单PO</td>
		<td class="A0101" width="260" align="center">产品名称</td>
		<td class="A0101" width="210" align="center">Code</td>
		<td class="A0101" width="70" align="center">售价</td>
		<td class="A0101" width="80" align="center">订单数量</td>
		<td class="A0101" width="200" align="center">交货期</td>
        <td class="A0101" width="280" align="center">Remark</td>
		<td width="0" style="display:none">&nbsp;</td>
	</tr>
	<tr>
		<td height="150" colspan="10" class="A0101">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="OrderList">
			<?php   
			//已有订单
		$sheetResultP = mysql_query("SELECT S.Id,S.POrderId,S.OrderPO,P.cName,P.eCode,S.Qty,S.Price,I.Leadtime,I.Id AS PIId,I.condition,I.Remark 
				FROM $DataIn.yw3_pisheet I
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.Id=I.oId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE I.PI='$Id' ORDER BY I.Id",$link_id);		
		$k=1;
		if($sheetRowP = mysql_fetch_array($sheetResultP)){			
			do{
				$tempId=$sheetRowP["Id"];
				$PIId=$sheetRowP["PIId"];
				$POrderId=$sheetRowP["POrderId"];
				$OrderPO=$sheetRowP["OrderPO"]==""?"&nbsp;":$sheetRowP["OrderPO"];
				$Price=$sheetRowP["Price"];
				$Date=$sheetRowP["Date"];
				$Qty=$sheetRowP["Qty"];
				$cName=$sheetRowP["cName"];
				$eCode=$sheetRowP["eCode"];
				$Leadtime=$sheetRowP["Leadtime"];
				//$condition=$sheetRowP["condition"];
				$Remark =$sheetRowP["Remark"];
				
				$Leadtime=str_replace("*", "", $Leadtime);
				 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
                 $PIWeek=$dateResult["PIWeek"];
                 $week=substr($PIWeek, 4,2);
          
				echo"<tr>
				<td width='80' class='A0101' align='center' height='20'><a href='#' onclick='deleteRow(this.parentNode,OrderList,$tempId)' title='取消此项目'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode,OrderList)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode,OrderList)' title='当前行下移'>∨</a><input type='hidden' name='oId$k' id='oId$k' value='$tempId'</td>
				<td width='40' class='A0101' align='center'>$k</td>
				<td width='100' class='A0101' align='center'>$OrderPO</td>
				<td width='260' class='A0101'>$cName</td>
				<td width='210' class='A0101'>$eCode</td>
				<td width='70' class='A0101' align='right'>$Price</td>
				<td width='80' class='A0101' align='center'>$Qty</td>
				<td  width='200' class='A0101'>
				<input name='Leadtime[]' type='text' id='Leadtime$k' style='width:100px;' value='$Leadtime' class='I0000L' onFocus='WdatePicker({el:\"Leadtime$k\",minDate:\"%y-%M-%d\",isShowWeek:true,onpicked:function(){document.getElementById(\"PIWeek$k\").innerHTML=$" ."dp.cal.getP(\"W\",\"WW\")+\"周\";}})' readonly>&nbsp;&nbsp;<span id='PIWeek$k' style='margin-left:5px;color:#0000FF'>$week周</span>
				</td>
				<td  width='280' class='A0100'>
				<input name='Remark[]' type='text' id='Remark$k' style='width:280px;' value='$Remark' class='I0000L' >
				</td>			
				<td width='0' style='display:none'>$tempId</td>
				</tr>";
				$k++;
				}while($sheetRowP = mysql_fetch_array($sheetResultP));
				$j=$k-1;
			}		
		?>
			</table>
		</div>		
		</td>
	</tr>
</table>	  
</td>
    </tr>
    
    
	<!--<tr valign="bottom">
    	<td height="27" colspan="2" class='A0011'>&nbsp;&nbsp;&nbsp;追加至PI的订单 :</td>
  </tr>
    <tr>
      <td colspan="2" align="center" valign="top" class='A0011'>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="80" height="25" align="center" class="A1111">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="100" align="center">订单PO</td>
		<td class="A1101" width="250" align="center">产品名称</td>
		<td class="A1101" width="210" align="center">Code</td>
		<td class="A1101" width="70" align="center">售价</td>
		<td class="A1101" width="80" align="center">订单数量</td>
		<td class="A1101" width="250" align="center">交货期</td>
	</tr>
	<tr>
		<td height="100" colspan="8" align="center" class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php   
			//追加项目
			?>
			</table>
		</div>		
		</td>
	</tr>
</table>	  
</td>
    </tr>    -->
<tr align="center">
      <td colspan="2" valign="middle" class='A0011'>总订单数:<input name="Rows" type="text" id="Rows" value="<?php    echo $j?>" size="3" class="INPUT0000" readonly></td>
    </tr>
</table>
<input name="SIdList" type="hidden" id="SIdList"><input name="PI" type="hidden" id="PI" value="<?php    echo $Id?>">
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>


function CheckForm(){
		var DataSTR="";
		PI=document.getElementById("PI").value;
		var LeadTime=document.getElementsByName('Leadtime[]');
		var Remark=document.getElementsByName('Remark[]');
		for(i=0;i<OrderList.rows.length;i++){
  			//var thisData=OrderList.rows[i].cells[8].innerHTML+"^^"+LeadTime[i].value;
			var thisData=OrderList.rows[i].cells[9].innerHTML+"^^"+LeadTime[i].value+"^^"+Remark[i].value;
			if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
				}
			}
		if(DataSTR!=""){
		     //alert(DataSTR);
			document.form1.SIdList.value=DataSTR;
			document.form1.action="yw_pi_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何订单！请先加入订单");
			return false;
			}
	}
	
function ChangeThis(thisE,sId){
	var oldValue=document.form1.TempValue.value;
	var Id=document.form1.Id.value;
	var CompanyIdTemp=document.form1.CompanyId.value;
	var thisValue=thisE.value;
	if(thisValue!=""){
		myurl="yw_pi_updated.php?PIId="+sId+"&ActionId=935&NewLeadtime="+thisValue+"&PI="+Id+"&CompanyId="+CompanyIdTemp;
		retCode=openUrl(myurl);
		if (retCode=="-2"){
			alert("出货期更新失败！");
			thisE.value=oldValue;
			return false;
			}
		else{
			alert("出货期更新成功");
			}
		}
	}

function ViewOrderId(Action){
	var Message="";
	var num=Math.random();  
	var ClientTemp=document.form1.CompanyId.value;
	if(ClientTemp!=""){
		BackData=window.showModalDialog("yw_pi_s1.php?num="+num+"&Action="+Action+"&Jid="+ClientTemp,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
		//拆分
		if(BackData){
			var Rows=BackData.split("``");//分拆记录:
			var Rowslength=Rows.length;//数组长度即订单数
			for(var i=0;i<Rowslength;i++){
				var Message="";
				var FieldTemp=Rows[i];		//拆分后的记录
				var FieldArray=FieldTemp.split("^^");
				//过滤相同的产品订单ID号
				for(var j=0;j<OrderList.rows.length;j++){
					var OrderIdtemp=OrderList.rows[j].cells[0].data;//隐藏ID号存于操作列	
					if(FieldArray[0]==OrderIdtemp){//如果流水号存在
						Message="待出产品订单: "+FieldArray[2]+" 已存在!跳过继续！";
						break;
						}
					}
				//$Id."^^".$OrderPO."^^".$cName."^^".$eCode."^^".$Price."^^".$Qty;
				//	0			1				2			3			4			5
				if(Message==""){
					oTR=OrderList.insertRow(OrderList.rows.length);
					//表格行数
					tmpNum=oTR.rowIndex+1;
					//第一列:操作
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,OrderList)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode,OrderList)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode,OrderList)' title='当前行下移'>∨</a>";
					oTD.data=""+FieldArray[0]+"";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="79";
					oTD.height="20";
					
					//第二列:序号
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+tmpNum+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="40";
					//三、PO
					oTD=oTR.insertCell(2);
					oTD.innerHTML=""+FieldArray[1]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="100";
					
					//四：中文名
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[2]+"";
					oTD.className ="A0101";
					oTD.width="260";
					
					//五:Product Code
					oTD=oTR.insertCell(4); 
					oTD.innerHTML=""+FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.width="210";
	
					//六：信价
					oTD=oTR.insertCell(5);
					oTD.innerHTML=""+FieldArray[4]+"";
					oTD.className ="A0101";
					oTD.align="right";
					oTD.width="70";
	
					//七：数量
					oTD=oTR.insertCell(6);
					oTD.innerHTML=""+FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="80";
					
					//八：交货期
					oTD=oTR.insertCell(7);
					oTD.innerHTML="<input type='text' name='Leadtime[]' id='Leadtime"+tmpNum+"' style='width:100px;'  class='I0000L' value='' onFocus='WdatePicker({el:\"Leadtime"+tmpNum+"\",minDate:\"%y-%M-%d\",isShowWeek:true,onpicked:function(){document.getElementById(\"PIWeek"+tmpNum+"\").innerHTML=$dp.cal.getP(\"W\",\"WW\")+\"周\";}})' readonly>&nbsp;&nbsp;<span id='PIWeek"+tmpNum+"' style='margin-left:5px;color:#0000FF'></span>";
					oTD.className ="A0101";
					oTD.width="200";
					
					//十：备注
					oTD=oTR.insertCell(8);
					oTD.innerHTML="<input type='text' name='Remark[]' id='Remark"+tmpNum+"' style='width:280px;'  class='I0000L' value=''>";
					oTD.className ="A0100";
					oTD.width="280";					
					
					oTD=oTR.insertCell(9);
					oTD.innerHTML=""+FieldArray[0]+"";
					oTD.className ="";
					oTD.style.display="none";
					}//end if(Message=="")
				else{
					alert(Message);
					}
				}//end for
				document.form1.Rows.value=OrderList.rows.length;
				return true;
			}
		else{
			alert("没有选取待出订单！");
			return false;
			}
		}
	else{
		alert("未选择客户");
		}
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1;
		TableTemp.rows[i].cells[1].innerHTML=j;  
		}
	document.form1.Rows.value=OrderList.rows.length;
	}

  
function deleteRow (tt,TableTemp){
	//var rowIndex=tt.parentElement.rowIndex; 
	var rowIndex;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		rowIndex=tt.parentNode.rowIndex;
	}
	else{
		rowIndex=tt.parentElement.rowIndex;
	}		
	
	OrderList.deleteRow(rowIndex);
	ShowSequence(TableTemp);
	}
        
function downMove(tt,TableTemp){   
	//var nowRow=tt.parentElement.rowIndex;
	
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}	
	
	for(i=0;i<TableTemp.rows.length;i++){
		TableTemp.rows[i].style.backgroundColor="#ffffff";
		}
	TableTemp.rows[nowRow].style.backgroundColor="#999999";

 	var nextRow=nowRow+1;
  	if(TableTemp.rows[nextRow]!=null){
 		//OrderList.rows[nowRow].swapNode(OrderList.rows[nextRow]);
		swapNode(TableTemp.rows[nowRow],TableTemp.rows[nextRow]);
  		ShowSequence(TableTemp);
		}
	}
	
function upMove(tt,TableTemp){
	//var nowRow=tt.parentElement.rowIndex;
	
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}	
	
	for(i=0;i<TableTemp.rows.length;i++){
		TableTemp.rows[i].style.backgroundColor="#ffffff";
		}
	TableTemp.rows[nowRow].style.backgroundColor="#999999";
  	var preRow=nowRow-1;
	if(preRow>=0){
		//OrderList.rows[nowRow].swapNode(OrderList.rows[preRow]); 
		swapNode(TableTemp.rows[nowRow],TableTemp.rows[preRow]);
		ShowSequence(TableTemp);
		}
	}  
	
function dateChange(index,sign){
     var LeadtimeName="Leadtime_"+index;
     var elLeadtime=document.getElementById(LeadtimeName);
     
     var LeaddayName="Leadday_"+index;
     var elLeadday=document.getElementById(LeaddayName);
     switch(sign){
	    case 0:
	       var Leadtime=elLeadtime.value;
	       if (Leadtime!=""){
	            var now=new Date();
		        Leadtime=new Date(Leadtime.replace(/\*/g,""));
		        elLeadday.value=parseInt((Leadtime-now)/(24*60*60*1000));
	       }
	       break;
	   case 1:
	        var days=parseInt(elLeadday.value);
	        var  Leadtime=new Date();
		     Leadtime.setDate(Leadtime.getDate()+days);
		     elLeadtime.value=Leadtime.getFullYear() + "-" + (Leadtime.getMonth() + 1) + "-" + Leadtime.getDate(); 
	      break;
    }
}
</script>