

function CPandsViewCut(Action){
        if(document.getElementById('SafariReturnValue')){
        document.getElementById('SafariReturnValue').value="";
        }

	var num=Math.random();

		BackData=window.showModalDialog("../pt/cut_data_s1.php?r="+num+"&tSearchPage=cut_data&fSearchPage=slice_cutdie&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");

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
				oTD.width="100";
				oTD.height="25";
				//第2列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第3列:刀模名称
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="420";
				
				//第4列:刀模尺寸
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[2]+"<input name='CutId[]' type='hidden' id='CutId"+tmpNum+"' value='"+FieldArray[0]+"'>";
				oTD.className ="A0101";
				oTD.width="";

				}
			else{
				alert(Message);
				}
			}//end for
			
			return true;
		}
	else{
		alert("没有选到刀模！");
		return false;
		}
}



function checkInput(){	
	 var DataSTR="";
	 var CutId=document.getElementsByName('CutId[]');
	 for(var i = 0; i<CutId.length; i++) {
			    if (CutId[i].value==""){
				 alert("序号:"+(i+1)+ " 的配件没有设定刀模关系！");
				return false;
			 }
			if(DataSTR==""){
					DataSTR=CutId[i].value;
					}
				else{
					DataSTR=DataSTR+"|"+CutId[i].value;
			  }
	 }

	 if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="slice_cutdie_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何刀模！请先加入刀模！");
			return false;
			}
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
