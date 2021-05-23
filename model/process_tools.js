function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}


function ShowSequence(i,j){   
    ListTable = document.getElementById("ListTable"+i);
	for(i=0;i<ListTable.rows.length;i++)   
  		{var j=i+1;
		//ListTable.rows[i].cells[1].innerText=j;
		ListTable.rows[i].cells[1].innerHTML=j;
		}
  }   
   
function addRow(tt,i,j,ProcessId){

    ListTable = document.getElementById("ListTable"+i);
	oTR=ListTable.insertRow(ListTable.rows.length);
	tmpNum=parseInt(oTR.rowIndex)+1;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='addRow(this.parentNode,"+i+","+tmpNum+")' title='当前行上移'>+</a>&nbsp;&nbsp;&nbsp;<a href='#' onclick='deleteRow(this.parentNode,"+i+","+tmpNum+")' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0001";
	oTD.align="center";
	oTD.height="25";
				
	//第二列:序号
	oTD=oTR.insertCell(1);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0001";
	oTD.align="center";
				
	
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='toolsName[]'  id='toolsName"+i+tmpNum+"'  type='text'  size='35' value='' onclick='addtoolsName(this,"+i+","+tmpNum+")'><input type='hidden' name='toolsId[]' id='toolsId"+i+tmpNum+"' value='' >";
	oTD.className ="A0001";
	oTD.align="center";

	
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+i+tmpNum+"' size='8' value='0' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='tempProcessId[]' type='hidden' id='tempProcessId"+i+tmpNum+"' value='"+ProcessId+"'>";
	oTD.className ="A0001";
	oTD.align="center";
	}
	
	
function deleteRow (tt,i,j){

	var rowIndex;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  
		rowIndex=tt.parentNode.rowIndex;
	}
	else{
		rowIndex=tt.parentElement.rowIndex;
	}		
	
	ListTable = document.getElementById("ListTable"+i);
	ListTable.deleteRow(rowIndex,i,j);
	ShowSequence(i,j);
}
	
	
function addtoolsName(e,i,j){
//读取产品资料
	var r=Math.random();  
	var BackData=window.showModalDialog("../public/fixturetool_s1.php?r="+r+"&tSearchPage=fixturetool&fSearchPage=fixturetool&SearchNum=1"+"&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	if(BackData){
		var CL=BackData.split("^^");
		var toolsIdStr="toolsId"+i+j;
		var toolsId=document.getElementById(toolsIdStr);
		toolsId.value=CL[0];//记录ID
		e.value=CL[1];	
	}
 }
 
 function checkInput(){
 	
	 var DataSTR="";
	 var Relation=document.getElementsByName('Qty[]');
	 var tempProcessId = document.getElementsByName('tempProcessId[]');
	 var toolsId = document.getElementsByName('toolsId[]');
	 
	 var nullSign = 0 ;
	 for(var i = 0; i<tempProcessId.length; i++) {
		var thisData=tempProcessId[i].value+"^"+toolsId[i].value+"^"+Relation[i].value;
		
		if(toolsId[i].value =="" || Relation[i].value ==0 || Relation[i].value==""){
			
			nullSign = 1;
		}
		
		if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
		  }
	  }


	  if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="process_relate_updated.php";
			document.form1.submit();
			}
 }
 
 
 function checkNum(obj){
	var oldScore=document.form1.TempValue.value;
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
	var ScoreArray=TempScore.split("/");
	var LengthScore=ScoreArray.length;
	if(LengthScore>2){
		reBackSign=0;
		}
	else{
		if(LengthScore==1){
			//检查数字格式
			var NumTemp=ScoreArray[0];
			var reBackSign=fucCheckNUM(NumTemp,"Price");//1是数字，0不是数字
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
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value=oldScore;
		return false;
		}
}
 