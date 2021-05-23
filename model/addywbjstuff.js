function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function downMove(tt){   
	var nowRow=tt.parentElement.rowIndex;
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	ListTable.rows[nowRow].style.backgroundColor="#999999";

 	var nextRow=nowRow+1;
  	if(ListTable.rows[nextRow]!=null){
 		ListTable.rows[nowRow].swapNode(ListTable.rows[nextRow]);
  		ShowSequence();
		}
	}
	
function upMove(tt){
	var nowRow=tt.parentElement.rowIndex;
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	ListTable.rows[nowRow].style.backgroundColor="#999999";
  	var preRow=nowRow-1;
	if(preRow>=0){
		ListTable.rows[nowRow].swapNode(ListTable.rows[preRow]); 
		ShowSequence();
		}
	}  
  
function ShowSequence(){   
	for(i=0;i<ListTable.rows.length;i++)   
  		{var j=i+1;
		ListTable.rows[i].cells[1].innerText=j;}
  }   
  
function deleteRow (tt){
	var rowIndex=tt.parentElement.rowIndex; 
	ListTable.deleteRow(rowIndex);
	ShowSequence();
	}

function CheckForm(){
	//检查对应数量是否正确
	var Message="";
	var QtySTR="";
	if(document.form1.Pid.value==""){
		Message="没有指定产品！";
		}
	if(Message!=""){
		alert(Message);
		return false;
		}
	else{
		var DataSTR="";
		for(i=0;i<ListTable.rows.length;i++){
  			var thisData=ListTable.rows[i].cells[2].innerText;
			if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
				}
			}
		if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="ywbj_pands_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何配件！请先加入配件！");
			return false;
			}
		}
	}
function ViewStuffId(Action){
	var num=Math.random();  
	BackData=window.showModalDialog("ywbj_stuff_s1.php?r="+num+"&tSearchPage=ywbj_stuff&fSearchPage=pands&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度即领料记录数
		
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];					//拆分后的记录
			var FieldArray=FieldTemp.split("^^");	//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[2].innerText;
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
				oTD.height="20";	
				oTD.width="100";
							
				//第2列:序号100/60/60/400/90/110
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				//第3列:配件ID
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				//第4列:配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="400";
				//第6列:对应数量
				oTD=oTR.insertCell(4); 
				oTD.innerHTML="<input name='Price[]' type='text' id='Price"+tmpNumQty+"' size='9' class='noLine' value='"+FieldArray[2]+"' onchange='checkNum(this)' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				//oTD.width="115";
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
	var reBackSign=fucCheckNUM(TempScore,"Price");//1是数字，0不是数字
	if(reBackSign==0){
		alert("输入的数字不正确！");
		obj.value=oldScore;
		return false;
		}
	}