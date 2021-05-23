<!-- MC、DP不同 MC加入毛利计算-->
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function downMove(tt){   
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
	GrossMargin();
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

function CheckForm(){
	//检查对应数量是否正确
	var Message="";
	var QtySTR="";
	if(document.form1.ProductId.value==""){
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
			document.form1.action="pands_updated.php";
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
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=pands&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度即领料记录数
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=1;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[1].innerText;				
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
				oTD.width="69";
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
				oTD.width="311";
				//第6列:对应数量
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+tmpNumQty+"' size='4' class='noLine' value='1' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb"+tmpNumQty+"' value='"+FieldArray[6]+"'><input name='sPrice[]' type='hidden' id='sPrice"+tmpNumQty+"' value='"+FieldArray[5]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="69";
				 //第7列:采购
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第8列:供应商
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
			//计算产品毛利
			GrossMargin();
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
	/*var firstChar=TempScore.substring(0,1); 
	if(firstChar==0){
		reBackSign=0;
		}
	else{
	*/
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
	//	}
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value=oldScore;
		return false;
		}
	else{
		GrossMargin();
		}
	}
function SearchRecord(tSearchPage,fSearchPage,SearchNum,Action,Oevent){//读取产品资料
	var r=Math.random();
	var theName="";
	if(! window.event){  //firfox
	  //alert("FirFox");
	  event =Oevent; //处理兼容性，获得事件对象
	  theName=event.target.getAttribute('name');
	  event =""; 
	  //alert ("FirFox"+theName);
	}
	else {
		//alert("IE");
		theName=event.srcElement.getAttribute('name');
		//alert ("IE"+theName);
	}
	
	
	//var theName=event.srcElement.getAttribute('name');	
	var e=eval("document.form1."+theName);
	var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.ProductId.value=CL[0];//记录产品ID
		document.form1.saleAmount.value=CL[1];//记录产品价格
		e.value=CL[2];	//文本框显示产品名称
		//计算产品毛利
		GrossMargin();
		}
	}
//毛利计算
function GrossMargin(){
	var buyAmountUSD=0;
	var buyAmountRMB=0
	var cbThis=0;
	var HZRMB=0;
	var HZ=document.form1.HZ.value*1;					//行政费用计算率
	var saleAmount=(document.form1.saleAmount.value)*1;//产品售价
	//计算配件总价
	Q=document.getElementsByName("Qty[]");//配件对应数量数组
	F=document.getElementsByName("Fb[]");//配件货币数组
	P=document.getElementsByName("sPrice[]");//配件成本数组
	//S=document.getElementsByName("StuffId[]");//配件成本数组
	for(var j=0;j<ListTable.rows.length;j++){	
	    //var tempStuffId=S[j].value;			
		var tempQty=Q[j].value;		//对应数量，以/来拆分前后两部分
		var tempFb=F[j].value*1;	//货币ID
		var tempPrice=P[j].value*1;	//配件成本
		var QtyArray=tempQty.split("/");
		
		if(QtyArray.length==2){
			cbThis=(QtyArray[0]*tempPrice)/(QtyArray[1]*1);
			}
		else{
			cbThis=QtyArray[0]*tempPrice;
			}
		if(tempFb==2){	//USD成本
			buyAmountUSD+=cbThis;
			}
		else{			//RMB成本
			buyAmountRMB+=cbThis;
			}
		}//end for
	
	//计算行政费用=非USD金额*7%
	HZRMB=Number(buyAmountRMB*HZ).toFixed(4);
	document.form1.cbUSD.value=buyAmountUSD;
	document.form1.cbRMB.value=buyAmountRMB;
	document.form1.cbHZ.value=HZRMB;
	document.form1.Maori.value=Number(saleAmount-buyAmountUSD-buyAmountRMB-HZRMB).toFixed(4);//计算毛利
}

	
function CPandsViewStuffId(Action){
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=pands&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
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
		  //给add by zx firfox and  safari不能用javascript生成的元素
			  
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
				//alert(tmpNumQty);
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
				oTD.width="310";
				//第6列:对应数量
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+tmpNumQty+"' size='4' class='noLine' value='1' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb"+tmpNumQty+"' value='"+FieldArray[6]+"'><input name='sPrice[]' type='hidden' id='sPrice"+tmpNumQty+"' value='"+FieldArray[5]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第7列:刀模
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<input name='Diecut[]' type='text'  id='Diecut"+tmpNumQty+"' value=''  size='6'/>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第8列:切割关系
				oTD=oTR.insertCell(7);
				oTD.innerHTML="<input name='Cutrelation[]' type='text' id='Cutrelation"+tmpNumQty+"' size='4' value='' onchange='checkNum(this)'/>";				
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//form1.hfield.value=tmpNum;
				oTD=oTR.insertCell(8);
				oTD.innerHTML="<input name='bpRateName[]' type='text' id='bpRateName"+tmpNum+"' size='4' value='' onclick='addbpRate(this, "+tmpNum+")'/><input name='bpRate[]' type='hidden' id='bpRate"+tmpNum+"'>";		
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				 //第9列:采购
				oTD=oTR.insertCell(9);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第10列:供应商
				oTD=oTR.insertCell(10);
				oTD.innerHTML=""+FieldArray[4]+"";				
				oTD.className ="A0101";
				oTD.width="190";
				//form1.hfield.value=tmpNum;
				}
			else{
				alert(Message);
				}
			}//end for
			//计算产品毛利
			GrossMargin();
			return true;
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

function addbpRate(e,tmpNumQty){
//读取产品资料
	var num=Math.random();  
	BackData=window.showModalDialog("standbyrate_s1.php?r="+num+"&tSearchPage=standbyrate&fSearchPage=pands&SearchNum=1&Action=1","BackData","dialogHeight =450px;dialogWidth=780px;center=yes;scroll=yes");
	//alert(tmpNumQty);
	//alert(BackData);
	if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	if(BackData){
		var CL=BackData.split("^^");
		var bpRateArray="bpRate"+tmpNumQty;
		var bpRate=document.getElementById(bpRateArray);
		//alert(CL[0]);
		bpRate.value=CL[0];//记录ID
		e.value=CL[1];	
		}
	}