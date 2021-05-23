<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#EEEEEE;margin:10px auto;}
.in {background:#FFFFFF;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
-->
</style>
<?php
//电信---yang 20120801
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增加工工序BOM资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=950;$tableMenuS=600;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewPorcess(2)' $onClickCSS>加入工序</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
$SelectCode="生产类配件名称 <input name='gStuffIdName' type='text' id='gStuffIdName' size='60' onclick='searchStuffId(9)' readonly >
<input name='HZ' type='hidden' id='HZ' value='$pcValue'>
<input name='gStuffId' type='hidden' id='gStuffId'>
<input name='SelTypeId' type='hidden' id='SelTypeId'>";
include "../model/subprogram/add_model_pt.php";

//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0"  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                    <td width="100" height="25" class="A1101" align="center"> 操作</td>
                    <td width="50" class="A1101" align="center">序号</td>
                    <td width="50" class="A1101" align="center">工序ID</td>
                    <td width="160" class="A1101" align="center">工序名称</td>
                    <td width="290" class="A1101" align="center">工序说明</td>
                    <td width="70" class="A1101" align="center">对应关系</td>
                    <td width="100" class="A1101" align="center">约束工序</td>
                    <td width=" " class="A1100" align="center">排序号</td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td   height="336" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                            <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>

			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
  
</table>
<input name="TempValue" type="hidden" id="TempValue">
<input name="SIdList" type="hidden" id="SIdList">
<?php
//步骤5：
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
include "../model/subprogram/add_model_ps.php";
?>

<script language = "JavaScript">
//窗口打开方式修改为兼容性的模态框 by ckt 2018-01-14
function searchStuffId(Action){
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[1]) {
        var num=Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'searchStuffId("",true)';
        var url = "/admin/stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=processbom&SearchNum=1&Action="+Action;
        openFrame(url, 980, 650);//url需为绝对路径
        return false;
    }
    if(SafariReturnValue.value){
        var FieldArray=SafariReturnValue.value.split("^^");
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
        document.getElementById('gStuffId').value=FieldArray[0];
        document.getElementById('gStuffIdName').value="("+FieldArray[0]+")"+FieldArray[1];
        document.getElementById('SelTypeId').value=FieldArray[2];
    }
}

function CPandsViewPorcess(Action){
        if (document.getElementById('gStuffId').value=="")
        {
            alert("请先行选择生产类配件");
            return false;
        }
        else{
		        if(document.getElementById('SafariReturnValue')){
		           document.getElementById('SafariReturnValue').value="";
		       }
        }
     var SelTypeId=document.getElementById('SelTypeId').value;
	var num=Math.random();
		BackData=window.showModalDialog("process_data_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=processbom&SearchNum=2&Action="+Action+"&SelTypeId="+SelTypeId,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
		if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度即领料记录数
		
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
		
                
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[3].innerText;				
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[1]+" 已存在!跳过继续！";
					break;
					}
				}			
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				
				//第1列:隐藏的配件ID
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";
				oTD.height="20";
				//第2列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";

				//第4列:配件ID
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.width="50";
				oTD.align="center";
				
				//第5列:配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="160";
				
				//第6列:采购
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.width="290";
				
				//第6列:对应数量
                                
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+tmpNumQty+"' size='8' class='noLine' value='"+FieldArray[2]+"' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb"+tmpNumQty+"' value='"+FieldArray[6]+"'><input name='sPrice[]' type='hidden' id='sPrice"+tmpNumQty+"' value='"+FieldArray[5]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				
                oTD=oTR.insertCell(6);
				oTD.innerHTML="<input name='BeforeProcessId[]' type='text'  id='BeforeProcessId"+tmpNumQty+"' value=''  size='12' onclick='updateJq(this,"+tmpNumQty+",1)'  readonly/>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";

				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[4]+"";				
				oTD.className ="A0100";
				oTD.width="";
				oTD.align="center";
				}
			else{
				alert(Message);
				}
			}//end for
			
			return true;
		}
	else{
		alert("没有选到配件！");
		return false;
		}
	}

function checkInput(){
	//检查对应数量是否正确
	var Message="";
	if(document.form1.gStuffId.value==""){
		alert("没有指定半成品配件！");
		return false;
		}
		
	 var DataSTR="";
	 var Qty=document.getElementsByName('Qty[]');
	 var BeforeProcessId = document.getElementsByName('BeforeProcessId[]');
	  for(var i = 0; i<Qty.length; i++) {
		var thisData=getinnerText(ListTable.rows[i].cells[2]);
		thisData=thisData+"^"+Qty[i].value+"^"+BeforeProcessId[i].value;
		if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
		  }
	 }

	 if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="processbom_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何工序！请先加入工序！");
			return false;
			}
 }
  
function updateJq(e,RowId,toObj){
	var InfoSTR="";
	var buttonSTR="";
	var runningNum="";
	
	var theDiv=document.getElementById("Jp");
	var infoShow=document.getElementById("infoShow");
	
	var ObjId=document.form1.ObjId.value;
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId ){
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//选择表中配件
			   var rows=ListTable.rows.length;
			   
			    theDiv.style.width=400;
			    theDiv.style.height=rows*25+20;
			   
			   infoShow.style.width=400;
			   infoShow.style.height=rows*25+20;

			   var eValue=e.value.split(",");
			   for(var j=0;j<ListTable.rows.length;j++){
			         if (j<RowId){
				           var processId=ListTable.rows[j].cells[2].innerText;
				           var processName=ListTable.rows[j].cells[3].innerText;
				           var checkSign="";
				           for(n=0;n<eValue.length;n++){
					           if (eValue[n]==processId) checkSign=" checked ";
				           }
				          InfoSTR+="&nbsp;<input type='checkbox' name='processCheckId[]'  id='processCheckId' value='"+processId+"' "+checkSign+">&nbsp;&nbsp;"+processId+"—"+processName+"</br>"; 
			         }
			   } 
				break;
		}
		var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='清  空' onclick=' clearValue("+RowId+","+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+RowId+","+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'>";

		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	    theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
       
		//theDiv.className="moveRtoL";
		theDiv.style.visibility = "";
		theDiv.style.display="";
	}
}


function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}
	
function setValue(RowId,toObj)
{
    switch(toObj){
	    case 1:
	       var returnId="";
	       var processCheckId=document.getElementsByName("processCheckId[]");
	        for(var j=0;j<processCheckId.length;j++){
	            if (processCheckId[j].checked){
		            returnId+=returnId==""?processCheckId[j].value:","+processCheckId[j].value;
	            }
	        }
	       var BeforeProcessId="BeforeProcessId"+RowId; 
	       document.getElementById(BeforeProcessId).value=returnId;
	       break;
    }
     CloseDiv();
}


function  clearValue(RowId,toObj){
	var BeforeProcessId="BeforeProcessId"+RowId; 
	    document.getElementById(BeforeProcessId).value=0;
	    CloseDiv();
}
</script>
