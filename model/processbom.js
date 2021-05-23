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
  
function ShowSequence(){   
	for(i=0;i<ListTable.rows.length;i++)   
  		{var j=i+1;
		//ListTable.rows[i].cells[1].innerText=j;
		ListTable.rows[i].cells[1].innerHTML=j;
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
	ShowSequence();
	}

function clearRow (){
   var lens=ListTable.rows.length-1;
   for(var i=lens;i>=0;i--) ListTable.deleteRow(i);
}

	
function addRow(tt){   //增加相同的行。
	
	var rowIndex;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		rowIndex=tt.parentNode.rowIndex;
	}
	else{
		rowIndex=tt.parentElement.rowIndex;
	}	
	//alert(ListTable.rows[rowIndex].cells(3).innerText);
	oTR=ListTable.insertRow(ListTable.rows.length);
	tmpNumQty=oTR.rowIndex;
	tmpNum=oTR.rowIndex+1;
	//第1列:隐藏的配件ID
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;&nbsp;<a href='#' onclick='addRow(this.parentNode)' title='当前行上移'>+</a>";
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
				//第3列:类别
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+ListTable.rows[rowIndex].cells(2).innerText+"";
				oTD.className ="A0101";
				oTD.width="90";
				//第4列:配件ID
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+ListTable.rows[rowIndex].cells(3).innerText+"";
				oTD.className ="A0101";
				oTD.width="50";
				//第5列:配件名称
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+ListTable.rows[rowIndex].cells(4).innerText+"";
				oTD.className ="A0101";
				oTD.width="290";
			
				//第6列:加工工序Id
				oTD=oTR.insertCell(5);
				oTD.innerHTML="<input name='ProcessName[]' type='text'  id='ProcessName"+tmpNumQty+"' value=''  size='6' onclick='SearchProcess(this,"+tmpNumQty+",1,1,event)' readonly/><input name='ProcessId[]' type='hidden'  id='ProcessId"+tmpNumQty+"' value='' size='6'/>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第7列:加工工序对应关系
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<input name='Relation[]' type='text' id='Relation"+tmpNumQty+"' size='6' value='' onchange='checkNum(this)'/>";				
				oTD.className ="A0101";
				oTD.align="left";
				oTD.width="70";
				//form1.hfield.value=tmpNum;
				//第8列:供应商
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+ListTable.rows[rowIndex].cells(7).innerText+"";				
				oTD.className ="A0101";
				oTD.width="119";
	
}
	
	
function CheckForm(){
	//检查对应数量是否正确
	var Message="";
	var QtySTR="";
	if(document.getElementById('ProductId').value==""){
		Message="没有指定产品！";
		}
	if(Message!=""){
		alert(Message);
		return false;
		}
	else{
		var DataSTR="";
		for(i=0;i<ListTable.rows.length;i++){
  			var thisData=ListTable.rows[i].cells[3].innerText;
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
			alert("没有加入任何配件！请先加入配件！");
			return false;
			}
		}
	}

function checkNum(obj){
	var oldScore=document.form1.TempValue.value;
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
	/*var firstChar=TempScore.substring(0,1); 
	if(firstChar==0){
		reBackSign=0;
		}
	else{*/
		var ScoreArray=TempScore.split("/");
		var LengthScore=ScoreArray.length;
		if(LengthScore>2){
			 reBackSign=0;
		    }
		else{
			if(LengthScore==1){
				//检查数字格式
				var reBackSign=fucCheckNUM(TempScore,"Price");//1是数字，0不是数字
				}
			else{
				var NumTemp0=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp0,"Price");//1是数字，0不是数字
				if(reBackSign==1){
					var NumTemp1=ScoreArray[1];
					reBackSign=fucCheckNUM(NumTemp1,"Price");//1是数字，0不是数字
				  }
				}		
			}
	//	}
       
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value=oldScore;
		return false;
		}
	}

	
function SearchRecord(tSearchPage,fSearchPage,SearchNum,Action,Oevent){//读取产品资料
	var r=Math.random();
	var theName="";
	if(! window.event){  //firfox
	      event =Oevent; //处理兼容性，获得事件对象
	      theName=event.target.getAttribute('name');
	      event =""; 
	     }
	else {
		  theName=event.srcElement.getAttribute('name');
	     }
        document.getElementById('SafariReturnValue').value="";
	//var theName=event.srcElement.getAttribute('name');	
	var e=eval("document.form1."+theName);
	var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
        if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}
        if(BackData){ 
		var CL=BackData.split("^^");
                var ProductId=document.getElementById('ProductId').value;
                if (ProductId!=CL[0]) clearRow();
		document.getElementById('ProductId').value=CL[0];//记录产品ID
		document.getElementById(theName).value=CL[1];	//文本框显示产品名称
                CPandsViewStuffId(3);
		}
	}

function SearchProcess(e,index,SearchNum,Action,Oevent){//读取加工工序资料
	var r=Math.random();
	var theName="";
	if(! window.event){  //firfox
	      event =Oevent; //处理兼容性，获得事件对象
	      theName=event.target.getAttribute('id');
	      event =""; 
	     }
	else {
		  theName=event.srcElement.getAttribute('id');
	     }
        document.getElementById('SafariReturnValue').value="";
        
	//var theName=event.srcElement.getAttribute('name');	
	var e=eval("document.form1."+theName);
	var BackData=window.showModalDialog("process_data_s1.php?r="+r+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
        if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
	 }
        if(BackData){
		var CL=BackData.split("^^");
                var IdName="ProcessId"+index;
				var theName="ProcessName"+index;
                document.getElementById(IdName).value=CL[0];//记录产品ID
                document.getElementById(theName).value=CL[1];
		//document.getElementById('ProductId').value=CL[0];//记录产品ID
		//document.getElementById(theName).value=CL[1];	//文本框显示产品名称
               // CPandsViewStuffId(3);
		}
	}
        
function CPandsViewStuffId(Action){
        ProductId=document.getElementById('ProductId').value;
        if (ProductId=="") {
            alert('请先选择产品名称');
            return;
        }
        document.getElementById('SafariReturnValue').value="";
	//alert (ProductId);
	var num=Math.random();  
	var BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=processbom&SearchNum=2&Action="+Action+"&ProductId="+ProductId,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
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
		var Rowslength=Rows.length;//数组长度
		
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
		  //给add by zx firfox and  safari不能用javascript生成的元素
			  
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			/*for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[3].innerText;				
				if(FieldArray[1]==SIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[2]+" 已存在!跳过继续！";
					break;
					}
				}	*/		
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				//alert(tmpNumQty);
				
				//第1列:隐藏的配件ID
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>&nbsp;&nbsp;<a href='#' onclick='addRow(this.parentNode)' title='当前行上移'>+</a>";
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
				oTD.width="290";
			
				//第6列:加工工序Id
				oTD=oTR.insertCell(5);
				oTD.innerHTML="<input name='ProcessName[]' type='text'  id='ProcessName"+tmpNumQty+"' value=''  size='6' onclick='SearchProcess(this,"+tmpNumQty+",1,1,event)' readonly/><input name='ProcessId[]' type='hidden'  id='ProcessId"+tmpNumQty+"' value='' size='6'/>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第7列:加工工序对应关系
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<input name='Relation[]' type='text' id='Relation"+tmpNumQty+"' size='6' value='' onchange='checkNum(this)'/>";				
				oTD.className ="A0101";
				oTD.align="left";
				oTD.width="70";
				//form1.hfield.value=tmpNum;
				 //第8列:上传图片
				/*oTD=oTR.insertCell(7);
				oTD.innerHTML="<input name='Picture[]' type='file' id='Picture"+tmpNumQty+"' size='32' DataType='Filter' Accept='pdf' Msg='格式不对,请重选'>";
				oTD.className ="A0101";
				oTD.align="left";
				oTD.width="339";*/
				//第10列:供应商
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
		}
	else{
		alert("没有选到配件！");
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

 function checkInput(){
	//检查对应数量是否正确
	var Message="";
	if(document.getElementById('ProductId').value==""){
		alert("没有指定产品！");
		return false;
		}
	//alert();	
	 var DataSTR="";
	 //var Qty=document.getElementsByName('Qty[]');
	 var ProcessName=document.getElementsByName('ProcessName[]');
         var ProcessId=document.getElementsByName('ProcessId[]');
	 var Relation=document.getElementsByName('Relation[]');
	// var Picture=document.getElementsByName('Picture[]');
	for(var i = 0; i<ProcessName.length; i++) {
		 var strPname=ProcessName[i].value;
                 var strPid=ProcessId[i].value;
		 strPname=strPname.replace(/(^\s*)|(\s*$)/g, ""); 
		 if (strPname!=""){
			 if (Relation[i].value<=0 || Relation[i].value==""){
			    alert ('对应关系不能为空！');
			    return false;
			 }
		 }
		
		if (strPname=="" || strPid==""){
		     alert ('请选择加工工序！');
		     return false; 
	        }
		var thisData=getinnerText(ListTable.rows[i].cells[3]);
		thisData=thisData+"^"+strPid+"^"+Relation[i].value;
		//alert (thisData);
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
                        //alert (DataSTR);
			}
		else{
			alert("没有加入任何配件！请先加入配件！");
			return false;
			}
   // CheckForm();
  }