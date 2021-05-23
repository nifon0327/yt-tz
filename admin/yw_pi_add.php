<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增PI");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
//$_SESSION["nowWebPage"]=$nowWebPage; 
$_SEESION["nowWebPage"] = $nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$CheckFormURL="thisPage";
$tableWidth=850;$tableMenuS=500;
$CustomFun="<span onClick='ViewOrderId(7)' $onClickCSS>加入订单</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
//步骤4：需处理

?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'><input name="Ids" type="hidden" id="Ids" value="<?php    echo $Ids?>"></td>
	</tr>
    <tr>
    	<td width="131" height="30" valign="middle" class='A0010' align="right">Client </td>
	    <td width="699" valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 337px;" dataType="Require" onchange='selChange(this)' msg="未选择或该客户无需PI">
			<?php   
			$checkSql = "SELECT P.CompanyId,C.Forshort FROM $DataIn.trade_object C,$DataIn.yw3_pimodel P
			WHERE C.CompanyId=P.COmpanyId AND (C.cSign=$Login_cSign or C.cSign=0) AND C.Estate=1";

			$checkResult = mysql_query($checkSql);
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value='' selected>-Select-</option>";
				do{
					$CompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					echo "<option value='$CompanyId'>$Forshort</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		  </select></td>
    </tr>
    <!--
    <tr>
    	<td height="30" valign="middle" class='A0010' align="right">PI NO. </td>
	    <td valign="middle" class='A0001'><input name="PI" type="text" id="PI" size="60" dataType="Require" msg="没有填写PI文件名"></td>
    </tr>
    -->
    <tr>
      <td height="30" align="right" valign="middle" class='A0010'>&nbsp;</td>
      <td valign="middle" class='A0001'>注：PI NO请使用统一格式(<SPAN style="COLOR: #ff6633" >不统一的例子：CEL PI 20080018 / CEL PI20080018 / CEL  20080018</SPAN>)<br>
      &nbsp;&nbsp;&nbsp;&nbsp;英文PI文件禁止使用中文或中文标点符号,否则会出现乱码.</td>
    </tr>
   
     <tr>
      <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="830" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1111' align='center'  width='50%'  height='30'>PI NO.:<input name="PI" type="text" id="PI" size="60" dataType="Require" msg="没有填写PI文件名"></td>
                <td align='center' class='A1101' >PaymentTerm:<input name="PaymentTerm" type="text" id="PaymentTerm" size="40" value="<?php    echo $PaymentTerm?>" dataType="Require" msg="没有填写Payment term"> </td>
           
            </tr>    
          
        </table>
      </tr>   
   
 	 <tr>
        <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="830" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1111' width='50%' align='center' height='10'></td>
                <td align='center' class='A1101' ></td>
           

        </table>
 	</tr>  
    
 	<tr>
        <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="830" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1111' width='50%' align='center' height='30'>Notes</td>
                <td align='center' class='A1101' >Terms</td>
           
            </tr>    
            <tr>  
            
                <td class='A1111' width='50%' align='center'><textarea name="Notes" cols="47" rows="3" id="Notes"  ><?php    echo $Notes?></textarea>
                 </br><div id="oNotesDiv" name="oNotesDiv" style="display:none;"> Other Notes:<input name="OtherNotes"  id="OtherNotes"  style="width:270px;margin-top:10px;"/>(CEL专用)</div>
                </td>
                <td align='center' class='A1101'><textarea name="Terms" cols="47" rows="3" id="Terms"><?php    echo $Terms?></textarea></td>
              
            </tr>   
        </table>
 	</tr>   

 	 <tr>
        <td colspan="2" align="center" valign="top" class='A0011'>
        <table border="0" width="830" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr> 
           
                <td class='A1111' width='50%' align='center' height='10'></td>
                <td align='center' class='A1101' ></td>
           

        </table>
 	</tr>  

    
    <tr>
      <td colspan="2" align="center" valign="top" class='A0011'>
<table border="0" width="830" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="80" height="25" align="center" class="A1111">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="100" align="center">订单PO</td>
		<td class="A1101" width="250" align="center">产品名称</td>
		<td class="A1101" width="210" align="center">Code</td>
		<td class="A1101" width="70" align="center">售价</td>
		<td class="A1101" width="80" align="center">订单数量</td>
		<td width="0" style="display:none"></td>
	</tr>
	<tr>
		<td height="200" colspan="8" align="center" class="A0111">
		<div style="width:830;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='830' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php   
			//入库明细列表
			?>
			</table>
		</div>		
		</td>
	</tr>
</table>	  
</td>
    </tr>
    <tr align="center">
      <td colspan="2" valign="middle" class='A0011'>订单数
        <input name="Rows" type="text" id="Rows" value="0" size="3" class="INPUT0000"  dataType="Range" msg="没有选择订单" min="0" max="100"></td>
    </tr>
</table><input name="SIdList" type="hidden" id="SIdList">
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>


function CheckForm(){
		var DataSTR="";
		var PI=document.getElementById("PI").value;
		var PaymentTerm=document.getElementById("PaymentTerm").value;
		if(PI==""){alert("PI不能为空");return false;}
		if(PaymentTerm==""){alert("PaymentTerm不能为空");return false;}
		for(i=0;i<ListTable.rows.length;i++){
  			var thisData=ListTable.rows[i].cells[7].innerHTML;
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
			document.form1.action="yw_pi_save.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何订单！请先加入订单");
			return false;
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
				for(var j=0;j<ListTable.rows.length;j++){
					var OrderIdtemp=ListTable.rows[j].cells[1].data;//隐藏ID号存于操作列	
					if(FieldArray[0]==OrderIdtemp){//如果流水号存在
						Message="待出产品订单: "+FieldArray[2]+" 已存在!跳过继续！";
						break;
						}
					}
				//$Id."^^".$OrderPO."^^".$cName."^^".$eCode."^^".$Price."^^".$Qty;
				//	0			1				2			3			4			5
				if(Message==""){
					oTR=ListTable.insertRow(ListTable.rows.length);
					//表格行数
					tmpNum=oTR.rowIndex+1;
					//第一列:操作
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a><input type='hidden' name='oId[]' id='oId' value='"+FieldArray[0]+"'>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode,ListTable)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode,ListTable)' title='当前行下移'>∨</a>";
					oTD.data=""+FieldArray[0]+"";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="80";
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
					oTD.width="99";
					
					//四：中文名
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[2]+"";
					oTD.className ="A0101";
					oTD.width="249";
					
					//五:Product Code
					oTD=oTR.insertCell(4); 
					oTD.innerHTML=""+FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.width="209";
	
					//六：信价
					oTD=oTR.insertCell(5);
					oTD.innerHTML=""+FieldArray[4]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="70";
	
					//七：数量
					oTD=oTR.insertCell(6);
					oTD.innerHTML=""+FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="78";
					
					oTD=oTR.insertCell(7);
					oTD.innerHTML=""+FieldArray[0]+"";
					oTD.className ="";
					oTD.style.display="none";
					}//end if(Message=="")
				else{
					alert(Message);
					}
				}//end for
				document.form1.Rows.value=ListTable.rows.length;
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
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	document.form1.Rows.value=ListTable.rows.length;
	}

function deleteRow (RowTemp,TableTemp,OrderIdTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	TableTemp.deleteRow(rowIndex);
	ShowSequence(TableTemp);
	}
        
function selChange(e){
   if(e==null)  return;
    for(i=0;i<e.length;i++){
       if(e[i].selected==true){
          var cVal= e[i].text;
          break;
       }
    }
    cVal=cVal.toUpperCase();
    var oNotesDiv=document.getElementById("oNotesDiv");
    if (cVal.indexOf("CEL")==-1){
        oNotesDiv.style.display="none";
        }
    else{
        oNotesDiv.style.display=""; 
    }
    
     var CompanyId=e.value;
	var url="yw_paymode_ajax.php?CompanyId="+CompanyId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
            　if(ajax.readyState==4 && ajax.status ==200){
	　        var BackData=ajax.responseText;
               document.getElementById("PaymentTerm").value=BackData;
		    }
        }
　	ajax.send(null);
    
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
	
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	ListTable.rows[nowRow].style.backgroundColor="#999999";

 	var nextRow=nowRow+1;
  	if(ListTable.rows[nextRow]!=null){
 		//ListTable.rows[nowRow].swapNode(ListTable.rows[nextRow]);
		swapNode(ListTable.rows[nowRow],ListTable.rows[nextRow]);
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
	
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	ListTable.rows[nowRow].style.backgroundColor="#999999";
  	var preRow=nowRow-1;
	if(preRow>=0){
		//ListTable.rows[nowRow].swapNode(ListTable.rows[preRow]); 
		swapNode(ListTable.rows[nowRow],ListTable.rows[preRow]);
		ShowSequence(TableTemp);
		}
	} 
</script>