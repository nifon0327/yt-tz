	function searchStuffId(Action){
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=stuffcombox_pand&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
        if(!BackData){ 
        if(document.getElementById('SafariReturnValue')){
                var SafariReturnValue=document.getElementById('SafariReturnValue');
                BackData=SafariReturnValue.value;
                SafariReturnValue.value="";
                }
        }
        if(BackData){
            var FieldArray=BackData.split("^^");
            document.getElementById('mStuffId').value=FieldArray[0];
            document.getElementById('mStuffCname').value="("+FieldArray[0]+")"+FieldArray[1]; 
        }
}

function CPandsViewStuffId(Action){
        if (document.getElementById('mStuffId').value=="")
        {
            alert("请选行选择母配件");
            return false;
        }
        if(document.getElementById('SafariReturnValue')){
        document.getElementById('SafariReturnValue').value="";
        }

	var num=Math.random();
		BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=stuffcombox_pand&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
		if(!BackData){  
		if(document.getElementById('SafariReturnValue')){
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度即领料记录数
		
		if(document.getElementById("TempMaxNumber")){
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
				if(FieldArray[1]==SIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[2]+" 已存在!跳过继续！";
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
				oTD.width="70";
				oTD.height="20";
				//第2列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				//第3列:类别
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.width="90";
				//第4列:配件ID
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="50";
				//第5列:配件名称
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="360";
				
				//第7列:对应数量
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+tmpNumQty+"' size='8' class='noLine' value='1' onchange='checkNum(this)' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				 //第8列:采购
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第9列:供应商
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[4]+"";				
				oTD.className ="A0101";
				oTD.width="119";
				//form1.hfield.value=tmpNum;
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


function checkNum(obj){
	var oldScore=document.form1.TempValue.value;
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
	var firstChar=TempScore.substring(0,1); 
	if(firstChar==0){
		reBackSign=0;
		}
	else{
		var ScoreArray=TempScore.split("/");
		var LengthScore=ScoreArray.length;
		if(LengthScore>2){
			reBackSign=0;
			}
		else{
			if(LengthScore==1){
				//检查数字格式
				var NumTemp=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp,"");//1是数字，0不是数字
				}
			else{
				var NumTemp0=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp0,"");//1是数字，0不是数字
				if(reBackSign==1){
					var NumTemp1=ScoreArray[1];
					reBackSign=fucCheckNUM(NumTemp1,"");//1是数字，0不是数字
					}
				}		
			}
		}
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value=oldScore;
		return false;
		}
}

function getinnerText(e) { 
    //若浏览器支持元素的innerText属性，则直接返回该属性 
    if(e.innerText) { return e.innerText; } 
     var t = ""; 
     e = e.childNodes || e ; 
     //遍历子元素的所有子元素 
     for(var i=0; i<e.length; i++) { 
         //若为文本元素，则累加到字符串t中。 
         if(e[i].nodeType == 3) { t += e[i].nodeValue; } 
         //否则递归遍历元素的所有子节点 
          else { t += getText(e[i].childNodes); } 
     } 
     return t; 
} 

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function downMove(tt){   	
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
  		ShowSequence();
		}
	}
	
function swapNode(node1,node2)
{
	var parent = node1.parentNode;//父节点
	var t1 = node1.nextSibling;//两节点的相对位置
	var t2 = node2.nextSibling;
	if(t1) parent.insertBefore(node2,t1);
	else parent.appendChild(node2);
	if(t2) parent.insertBefore(node1,t2);
	else parent.appendChild(node1);

}	
	
function upMove(tt){
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
		ShowSequence();
		}
	}  
  
function ShowSequence(TableTemp){   
	for(i=0;i<TableTemp.rows.length;i++)   
  		{var j=i+1;
		TableTemp.rows[i].cells[1].innerHTML=j;
		}
  }   
  
function deleteRow (tt){
	//var rowIndex=tt.parentElement.rowIndex; 
	var rowIndex;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		rowIndex=tt.parentNode.rowIndex;
	}
	else{
		rowIndex=tt.parentElement.rowIndex;
	}		
	
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}


function deletenewRow(rowIndex){
	newListTable.deleteRow(rowIndex);
	ShowSequence(newListTable);
	}


function AddnewRow(){
	oTR=newListTable.insertRow(newListTable.rows.length);
	tmpNum=oTR.rowIndex+1;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='AddnewRow(this.parentNode.parentNode.rowIndex)' title='新增行'>+</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='#' onclick='deletenewRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0101";
	oTD.align="center";
	oTD.height="30";
	oTD.width="70";
				
	//第二列:序号
	oTD=oTR.insertCell(1);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="50";
				
	//三、配件名称
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='newStuffCname[]' type='text' id='newStuffCname[]' size='70'  >";
	oTD.className ="A0101";

	//第四列:对应关系
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='newQty[]' type='text' id='newQty[]' size='18'  value='1' onchange='checkNum(this)' onfocus='toTempValue(this.value)' >";
	oTD.className ="A0100";
	oTD.align="center";
	oTD.width="150";
	}


function checkInput(){
	//检查对应数量是否正确
	var Message="";
	if(document.form1.mStuffId.value==""){
		alert("没有选择母配件！");
		return false;
		}
		
	 var DataSTR="";
	 var Qty=document.getElementsByName('Qty[]');
	 for(var i = 0; i<Qty.length; i++) {
	    var thisType=getinnerText(ListTable.rows[i].cells[2]);
		var thisData=getinnerText(ListTable.rows[i].cells[3]);
		thisData=thisData+"^"+Qty[i].value;
		if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
		  }
	 }
   var  newDataSTR  =  "";
	 var newQty=document.getElementsByName('newQty[]');
	 var newStuffCname=document.getElementsByName('newStuffCname[]');

	 for(var i = 0; i<newQty.length; i++) {
      if(newStuffCname[i].value==""){
                   alert("未填写子配件名称!");
                    break;  return  false;
               } 
      if(newQty[i].value==""){
                   alert("未填写子配件对应的关系!");
                    break;  return  false;
               } 

		if(newDataSTR==""){
				newDataSTR=newQty[i].value+"^"+newStuffCname[i].value;
				}
			else{
				newDataSTR=newDataSTR+"|"+newQty[i].value+"^"+newStuffCname[i].value;
		  }
	 }

	 if(DataSTR!="" || newDataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.newList.value=newDataSTR;
			document.form1.action="stuffcombox_pand_updated.php";
			document.form1.submit();
			}
 }
