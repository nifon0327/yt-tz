//转到更新保存页面:11月确认
//10 				当前行无素  行数	 鼠标动作		非选定色		鼠标经过色	选定色(不使用)   着色列数
function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor,  theMerge){
    var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '')|| typeof(theRow.style) == 'undefined') {
        return false;
    	}

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
   		}
    else 
		if (typeof(theRow.cells) != 'undefined') {
        	theCells = theRow.cells;
    		}
    	else {
        return false;
    	}

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
	var lastCellsCnt =rowCellsCnt-1;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[lastCellsCnt].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[lastCellsCnt].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
			
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {			
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                //判断是否有并行，如果有，则首行前几列，不变色
				if(theMerge<rowCellsCnt){	//有并行
					var MergeCles=rowCellsCnt-theMerge;//并行的列
					if(c>=MergeCles){
						theCells[c].setAttribute('bgcolor', newColor, 0);
						}					
					}
				else{						//无并行
					theCells[c].setAttribute('bgcolor', newColor, 0);
					}
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function

//9		下拉选框
function OutputSelects(Character,Default_Str,Length)
{
    var Split_Character=Character.split("~");
    var Length_Character=Split_Character.length;
    if(Split_Character[Length_Character-1]==""){
		Length_Character--;}
    var i=0;
	while(i<Length_Character){
    var j=1;
	ValueStr="";
	while(j<Length){
		ValueStr=ValueStr+Split_Character[i+j];
		j++
		}
	document.write("<option value=\""+ValueStr+"\""+((Default_Str==ValueStr)?" selected":"")+">"+Split_Character[i]);
       i=i+Length;
    	}
	}

//8		转其它更新功能页面
function Update_Other(From){
	document.form1.action=From+"_other.php";
	document.form1.submit();

	}

//7		转到相应的记录删除页面:删除多条记录,Ids是多条记录标志 WebPage:目标页面 From：目标页面的前导页面 ALType：目标页面分类过滤
function Del_ManyRecords(WebPage,From,ALType){
	// 检查是否没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录!");
		}
	else{		
		var message=confirm("你确定要删除此记录吗？");
		if (message==true){
			//选项解锁为可用
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.disabled=false;
					} 
				}
			if (From!=""){
				From="&From="+From;}
			if (ALType!=""){
				ALType="&ALType="+ALType;}
			document.form1.action="../admin/"+WebPage+"_del.php?Id=Ids"+From+ALType;
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}

//6		转到更新记录页面
function Update_OneRecord(WebPage,From,ALType){
	// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>1){
				UpdataIdX=form1.elements.length;
				break;
				}
			}
	if (UpdataIdX!=1){
		alert("多选或未选记录,本操作只针对一条记录!");
		return (false);
		}
	else{
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		location.href="../admin/"+WebPage+"_update.php?Id="+Id+From+ALType;
		}
	}

//5		转新增记录页面
function Add_Record(WebPage,From,ALType){
	if (From!=""){
		From="From="+From;}
	if (ALType!=""){
		ALType="&ALType="+ALType;}	
	location.href="../admin/"+WebPage+"_Add.php?"+From+ALType;
	}

//4		全选记录行
function All_elects(theDefaultColor,thePointerColor,theMarkColor,theMerge){
	//if(ChooseTemp=="false"){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked==false){
					e.checked=true;
					var   s   =e.name;   
 					var   row   =   s.replace(/[^\d]/g,"");
					//改变底色:			对象		行	事件		非选定色     鼠标经过色      选定色        着色列数
					chooseRow(ListTable.rows[row],row,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge);
					}
				} 
			}
		ChooseTemp="true";
	}
	
//3		反选记录行
	function Instead_elects(theDefaultColor,thePointerColor,theMarkColor,theMerge){
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			var s=e.name;   
 			var row=s.replace(/[^\d]/g,"");
			if (e.checked){		//非选定
				e.checked=false;
				chooseRow(ListTable.rows[row],row,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge);
				}
			else{				//选定
				e.checked=true;
				chooseRow(ListTable.rows[row],row,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge);
				}
			}
		}
	}

//2	改变选定行状态	行元素    行号		 鼠标动作		非选定色	  鼠标经过色		选定色			   着色列数
function chooseRow(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom,theMerge){
	
	var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '')|| typeof(theRow.style) == 'undefined') {
        return false;
    	}
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    	}
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }
	
    var rowCellsCnt  = theCells.length;
	var lastCellsCnt =rowCellsCnt-1;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    if (typeof(window.opera) == 'undefined' && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[lastCellsCnt].getAttribute('bgcolor');//注意应该取最后一列的底色做判断
        domDetect    = true;
    	}
    else {
        currentColor = theCells[lastCellsCnt].style.backgroundColor;//注意应该取最后一列的底色做判断
        domDetect    = false;
    	}
    
	
	if (currentColor == '' || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {//全选
        newColor = theMarkColor;
        marked_row[theRowNum] = true;
		}
    else 
	if (currentColor.toLowerCase() == thePointerColor.toLowerCase()  && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
		
		if (theAction == 'click' && theMarkColor != '') {	//单击
			if((theFrom!="")&&(theFrom!="undefined")){
				//选定记录:底色为选定色
				eval("document.form1.checkid"+theRowNum).checked=true;
				eval(theFrom)(theRowNum,1);
				}
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
		}
    else 
		if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        	if (theAction == 'click') {			
				if((theFrom!="")&&(theFrom!="undefined")){
					//取消选定：底色为非选定色，如果鼠标在其上，则为鼠标经过色
					eval("document.form1.checkid"+theRowNum).checked=false;
					eval(theFrom)(theRowNum,-1);
					}
           		 newColor = (thePointerColor != '')? thePointerColor: theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])? true: null;
		  }
   	 }
if (newColor) {
        var c = null;
        if (domDetect) {
			
		   for (c = 0; c < rowCellsCnt; c++) {
		   
                //判断是否有并行，如果有，则首行前几列，不变色
				if(theMerge<rowCellsCnt){	//有并行
					var MergeCles=rowCellsCnt-theMerge;//并行的列
					if(c>=MergeCles){
						theCells[c].setAttribute('bgcolor', newColor, 0);
						}					
					}
				else{						//无并行
					theCells[c].setAttribute('bgcolor', newColor, 0);
					}
					
				}
      		}
        else {
            for (c = 0; c < rowCellsCnt; c++) {
				theCells[c].style.backgroundColor = newColor;
            }
        }
    }
    return true;
} 

//1		转更新标记页面：可用/禁用；锁定/解锁
function Update_Sign(WebPage,Action,From,ALType){
		//需检查是否有选记录，如果没有则返回
	//选项解锁为可用
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			e.disabled=false;
			} 
		}
	// 检查是否没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0){
		alert("没有选取记录");
		//选项改回禁用
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=true;
				} 
			}
		return false;
		}
	else{		
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		if (Action!=""){
			Action="Id="+Action;}
		document.form1.action="../admin/"+WebPage+"_Updated.php?"+Action+From+ALType;
		document.form1.submit();
		}
	}
//********************
//11月已确认的函数结束
//********************
function Addproduct(WebPage,From,ALType){
	if (From!=""){
		From="From="+From;}
	if (ALType!=""){
		ALType="&ALType="+ALType;}	
	location.href="../admin/"+WebPage+"_Add.php?"+From+ALType;
	}

function UpdataThisId(WebPage,From,ALType){
	// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>1){
				UpdataIdX=form1.elements.length;
				break;
				}
			}
	if (UpdataIdX!=1){
		alert("多选或未选记录,本操作只针对一条记录!");
		return (false);
		}
	else{
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		location.href="../admin/"+WebPage+"_updata.php?Id="+Id+From+ALType;
		}
	}

function  ForDight(Dight,How)  
{  
  Dight  =  Math.round  (Dight*Math.pow(10,How))/Math.pow(10,How);  
  return  Dight;  
} 
//更选货币时的提醒功能
function changeCurrency(){
	alert("注意！你已更改了货币符号，添加或保存之前请核实。");
	}

//检查请款月份格式
function checkAskMonth(theMonth){
	var d= new Date(); 
	var nowYear=d.getYear();
	var nowMonth=d.getMonth();
	var Message="";
	var theMonthlength=theMonth.length;
	if(theMonthlength!=6){
		Message="请款月份格式不正确.";
		}
	else{
		var theYear=theMonth.substring(0,4);
		if(nowYear-theYear==0 || nowYear-theYear==1){
			var theMonth=theMonth.substring(4,6);
			if(theMonth>13){
				Message="月份格式不正确.";				
				}
			else{
				if(nowYear-theYear==0 && theMonth*1>nowMonth+1){
					Message="输入了未来月份！";
					}
				}
			}
		else{
			Message="年份格式或年份(只允许今年和上一年)不正确.";
			}
		}
	return Message;
	}

//检查中文输入
function CnWordRegCheck(str)
 {
  var reg=/^[\u4e00-\u9fa5](\s*[\u4e00-\u9fa5])*$/;
  var flag = reg.test(str);
  return flag;
 }

//行背景色变换
var marked_row = new Array;
/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   interger  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default background color
 * @param   string    the color to use for mouseover
 * @param   string    the color to use for marking a row
 *
 * @return  boolean  whether pointer is set or not
 */


function isDate(str){
if(!str.match(/^\d{4}\-\d\d?\-\d\d?$/)){return false;}
//str=str.replace(/[^\d \:\-]+/g,"");
window.tmp=false;
window.execScript('tmp=IsDate("' + str + '")', "vbs");
return tmp;
}
function openUrl(url){ 
var objxml=new ActiveXObject("Microsoft.XMLHttp") 
objxml.open("GET",url,false); 
objxml.send(); 
retInfo=objxml.responseText; 
if (objxml.status=="200"){ 
return retInfo; 
} 
else{ 
return "-2"; 
} 
} 
//显示或隐藏配件采购单列表
function ShowOrHide(e,f,Order_Rows){
e.style.display=(e.style.display=="none")?"":"none";
var yy=f.src;
if (yy.indexOf("showtable")==-1){
	f.src="../images/showtable.gif";
	Order_Rows.myProperty=true;
	}
else{
	f.src="../images/hidetable.gif";
	Order_Rows.myProperty=false;
	}
}

//窗口标题
function WinTitle(Titles){
top.document.title=Titles; }

//返回按钮函数 WebPage:转回的目标页面 From：目标页面的前导页面 ALType：目标页面分类过滤
function GoBack(WebPage,From,ALType){
	if (From!=""){
		if (WebPage=="Invoice_Read"){
		From="Client="+From;
		}
	else{
		From="From="+From;}
	}
	if (ALType!=""){
		ALType="ALType="+ALType;}
		if (WebPage=="mcmain"){
			WebPage="../mcmain";
			}
	location.href=WebPage+".php?"+From+ALType;
	}
	
//下拉列表OutputSelect(选项值字符串，默认项字符串)
function OutputSelect(ValueStr,Default_Str)
{
    var Split_ValueStr=ValueStr.split("~");
    var Length_ValueStr=Split_ValueStr.length;
    if(Split_ValueStr[Length_ValueStr-1]==""){
		Length_ValueStr--;}
    var i=0;
    while(i<Length_ValueStr){
        document.write("<option value=\""+Split_ValueStr[i]+"\""+((Default_Str==Split_ValueStr[i])?" selected":"")+">"+Split_ValueStr[i]);
        i++;
    	}
	}
	

//转到保存页面
function SaveRecord(Action){
	if(Action=="Product"){
		if (form1.CompanyId.value!=""){
			if (form1.OrderNumber.value!=""){
				document.form1.action="ClientOrder_Save.php?from=ClientOrder_Add&RecordCount="+AddOrderItem+"";
				document.form1.submit();}
			else{
				alert("没有输入订单编号!");}
			}
		else{
			alert("请选择一个客户!");}
	}
	else{//无订单采购
		if (form1.CompanyId.value!=""){
			document.form1.action="noorderpurchase_save.php?RecordCount="+AddOrderItem+"";
			document.form1.submit();}
		else{
			alert("请选择一个供应商！");}
		}
	}



//转到相应的记录删除页面:删除一条记录
function DelThisId(WebPage,Id,From){
	if (From==""){
		From=WebPage;
		}
	location.href="../admin/"+WebPage+"_del.php?Id="+Id+"&From="+From;
	}

//转到相应的记录删除页面:删除多条记录,Ids是多条记录标志 WebPage:目标页面 From：目标页面的前导页面 ALType：目标页面分类过滤
function DelIds(WebPage,From,ALType){
	var message=confirm("你确定要删除此记录吗？");
   	if (message==true){
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		document.form1.action="../admin/"+WebPage+"_del.php?Id=Ids"+From+ALType;
		document.form1.submit();
		}
	else{
		return false;
		}
	}

function DelNoChoose(WebPage,From,ALType){
	//选项解锁
	//多选项可用
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			e.disabled=false;
			} 
		}
	// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>0){
				break;
				}
			}
	if(UpdataIdX==0){
		alert("没有选取记录");
		//多选项可用
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=true;
				} 
			}
		return false;
		}
	else{
		//检查是否选定记录
		var message=confirm("你确定要删除记录吗？");
		if (message==true){
			if (From!=""){
				From="&From="+From;}
			if (ALType!=""){
				ALType="&ALType="+ALType;}
			document.form1.action="../admin/"+WebPage+"_del.php?Id=Ids"+From+ALType;
			document.form1.submit();
			}
		else{
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.disabled=true;
					} 
				}
			return false;
			}
		}
	}

//转到添加或去除NEW标志页面,Action:修改动作 WebPage:目标页面 From：当前页面 ALType：当前页面是否分类过滤
function SignUdates(WebPage,Action,From,ALType){
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		if (Action!=""){
			Action="Id="+Action;}
		document.form1.action="../admin/"+WebPage+"_Updatad.php?"+Action+From+ALType;
		document.form1.submit();
	}

//转到更新记录页面:原多选项禁用状态

function UpdataOneNoChoose(WebPage,From,ALType){
	//多选项可用
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			e.disabled=false;
			} 
		}
	// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>1){
				UpdataIdX=form1.elements.length;
				break;
				}

			}
			
	if (UpdataIdX!=1){
		alert("多选或未选记录,本操作只针对一条记录!");
		//多选项禁用
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=true;
				} 
			}
		return (false);
		}
	else{
		if (From!=""){
			From="&From="+From;}
		if (ALType!=""){
			ALType="&ALType="+ALType;}
		location.href="../admin/"+WebPage+"_updata.php?Id="+Id+From+ALType;
		}
	}

function Analyzesorder(){//拆分订单
	// 检查是否多选或没有选记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					} 
				}
			if (UpdataIdX>1){
				UpdataIdX=form1.elements.length;
				break;
				}

			}
			
	if (UpdataIdX!=1){
		alert("多选或未选记录,本操作只针对一条记录!");
		return (false);
		}
	else{
		alert(Id);
		location.href="../admin/clientorder_analyzes.php?Id="+Id;
		}
	}
//全选记录行
var checkflag="false";
function checkall(form){
	if(checkflag=="false"){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.checked=true;	} 
			}
		checkflag="true";
		}
	else{
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.checked=false;} 
			}
		checkflag="false";
		}				
	}
//反向选择记录
function ReChoose(form){
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if (e.checked){
				e.checked=false;}
			else{
				e.checked=true;}
			}
		}
	}
//全选记录行并作加或减运算
function ChooseAdd(Num){
	//if(ChooseTemp=="false"){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked==false){
					e.checked=true;
					//改变底色
					var row=parseInt(i/Num)+1;
					alert(row);
					chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFE9D2","#FFCC99","");
					}
				} 
			}
		ChooseTemp="true";
		ChooseAlladd();
	}
//全选记录行并作加或减运算:其它将改用此函数;Start前面非多选框控件个数；Num一行中控件个数
function ChooseAddRow(Start,Num){
	//if(ChooseTemp=="false"){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				if(e.checked==false){
					e.checked=true;
					
					//改变底色
					var row=parseInt((i+Num-Start)/Num);
					chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFE9D2","#FFCC99","");
					}
				} 
			}
		ChooseTemp="true";
		ChooseAlladd();
	}
	
function ChooseMinusRow(Start,Num){
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			var row=parseInt((i+Num-Start)/Num);
			if (e.checked){
				e.checked=false;
				chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFFFFF","#FFCC99","");
				}
			else{
				e.checked=true;
				chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFE9D2","#FFCC99","");
				}
			}
		}
		ChooseAllMinus();
	}
//反向选择记录并置换数运算
function ChooseMinus(Num){
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			var row=parseInt(i/Num)+1;
			if (e.checked){
				e.checked=false;
				chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFFFFF","#FFCC99","");
				}
			else{
				e.checked=true;
				chooseRow(ListTable.rows[row],row,"click","#FFFFFF","#FFE9D2","#FFCC99","");
				}
			}
		}
		ChooseAllMinus();
	}
function payeeview(From,Record){
	win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}
function View(From,Record){
	switch(From){
		case "openphoto":
			win=window.open("openphoto.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "clientspec":
			win=window.open("admin/openphoto.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "OpenDocFile":
			win=window.open("OpenDocFile.php?Filename="+From+"&From="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "billdir":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "ViewBulletin":
			win=window.open("../ViewBulletin.php?Id="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "casepicture":
			win=window.open("casepicturemove.php?photoid="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			//win=window.open("casepicturemove.php?photoid="+Record+"","new"  ,"width=300, height=300,top=300,left=300,toolbar=no, menubar=no, scrollbars=no,resizable=no,location=no, status=no");
			break;
		case "shiplable":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "stufffile":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "clientdata":
			win=window.open("clientdata_view.php?CompanyId="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "ProviderData":
			win=window.open("ProviderData_view.php?CompanyId="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;		
		case "shippingdata":
			win=window.open("shippingdata_view.php?Id="+Record+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "cwdocument":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;			
		case "cwfkout":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "cwdfy":
			win=window.open("OpenDocFile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "teststandard":
			win=window.open("../admin/opendocfile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;			
		case "forwarddata":
			win=window.open("forwarddata_view.php?CompanyId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "freightdata":
			win=window.open("freightdata_view.php?CompanyId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "staff":
			win=window.open("staff_view.php?RecordId="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;
		case "errorcase":
			win=window.open("../admin/opendocfile.php?Filename="+Record+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
			break;			

	}
}
//用新窗口打开记录内容
function ShowRecord(RecordId){
	win=window.open("ShowRecord.php?RecordId="+RecordId+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}

//用新窗口打开图片
function OpenPhoto(photofilename){
	win=window.open("OpenPhoto.php?photoid="+photofilename+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}
	
function OpenDocFile(Filename,From){
	//var s=Filename;
	//Filename=s.replace('&','~');	
	win=window.open("OpenDocFile.php?Filename="+Filename+"&From="+From+"","new"  ,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}



//改变排序方式
function GoOrderKey(OrderKey){
	document.form1.OrderKey.value=OrderKey;
	document.form1.submit();
	}
					
//打开分页
function GoPage(PageNo){
	document.form1.page.value=PageNo;
	document.form1.submit();
	}

//更新时即时图片显示
var addimg=1;
function imgshow(add) 
{ 
	var imgpath=form1.Fname.value;
	if (add==1){
		if  (addimg==1)
		{
			Add.insertAdjacentHTML('AfterEnd','<div align="center"> <img  src="" width="115" height="125" name="showphoto" border="0" ></div>');
			addfimg=2;
			}
		}
	document.showphoto.src =imgpath ; 
	} 
function ShowFileName(filename)
{
	var str= filename;
    strs=str.toLowerCase();
    lens=strs.length;
    extname=strs.substring(lens-4,lens);
	indx=strs.lastIndexOf("\\")+1;
	filename=strs.substring(indx,lens-4);
	form1.Caption.value=filename;
	}

//检测上传Invoice的文件是否合要求
var UpIvoiceFile=1;
function AddInvoiceFile(thisname,thisvalue,Add)
{
	var Xname="Fname"+form1.hfield.value ;
  	var str= thisvalue;
    strs=str.toLowerCase();
    lens=strs.length;
    extname=strs.substring(lens-4,lens);
	indx=strs.lastIndexOf("\\")+1;
	filename=strs.substring(indx,lens-4);
    if(extname!=".pdf")
    {
		alert("请选择PDF类型的Invoice文件!");
		form1.add.disabled=true;	
		return (false);
		}
	else
    {	
		if(thisname==Xname){
			UpIvoiceFile++;
			form1.hfield.value=UpIvoiceFile;
			i=(UpIvoiceFile-1)*3-1;
			if (document.forms[0].elements[i].value==""){
				document.forms[0].elements[i].value=filename;}
			form1.add.disabled=false;	
			Add.insertAdjacentHTML('AfterEnd','<p id="Add'+UpIvoiceFile+'"> NO'+UpIvoiceFile+'：Invoice名称&nbsp;<input type="text" name="Cname'+UpIvoiceFile+'" size="12"> &nbsp;&nbsp;&nbsp; Invoice源文件 <input type="file" name="Fname'+UpIvoiceFile+'"  onchange="AddInvoiceFile(this.name,this.value,Add'+UpIvoiceFile+')" size="30">*&nbsp; &nbsp;新文档&nbsp;<input name="Newbz'+UpIvoiceFile+'" type="checkbox"  value="New"><p></P>');
			}
		else{
			form1.add.disabled=false;	
			alert("你已经改变Invoice文件，请相应修改Invoice名称");}		
    	}
	}

//添加新的报帐项目
var UpOthersFile=1;
function AddItem1(thisname,thisvalue,Add)
{
	var Xname="Fname"+form1.hfield.value ;
  	var str= thisvalue;
    strs=str.toLowerCase();
    lens=strs.length;
    extname=strs.substring(lens-4,lens);
	indx=strs.lastIndexOf("\\")+1;
	filename=strs.substring(indx,lens-4);
    if(extname==".php")
    {
		alert("不能上传PHP文件!");
		form1.add.disabled=true;	
		return (false);
		}
	else
    {	
		if(thisname==Xname){
			UpOthersFile++;
			form1.hfield.value=UpOthersFile;
			i=(UpOthersFile-1)*3-1;
			if (document.forms[0].elements[i].value==""){
				document.forms[0].elements[i].value=filename;}
			form1.add.disabled=false;	
			Add.insertAdjacentHTML('AfterEnd','<p id="Add'+UpOthersFile+'"> NO'+UpOthersFile+'：文档名称&nbsp;<input type="text" name="Cname'+UpOthersFile+'" size="12">&nbsp;源文件 <input type="file" name="Fname'+UpOthersFile+'"  onchange="AddOthersFile(this.name,this.value,Add'+UpOthersFile+')" size="30">&nbsp;来源&nbsp;<input type="text" name="Client'+UpOthersFile+'" size="12">&nbsp; &nbsp;新文档&nbsp;<input name="Newbz'+UpOthersFile+'" type="checkbox"  value="New"><p></P>');
			}
		else{
			form1.add.disabled=false;	
			alert("你已经改变文档源文件，请相应修改文档名称");}		
    	}
	}



//添加新的网页元素
var UpSpecFile=1;
function AddSpecFile(thisname,Add)
{
	var Xname="Fname"+form1.hfield.value ;
	if  (thisname==Xname){
		UpSpecFile++;
		form1.hfield.value=UpSpecFile;		
		Add.insertAdjacentHTML('AfterEnd','<p id="Add'+UpSpecFile+'"> NO'+UpSpecFile+'：中文名&nbsp;<input type="text" name="Cname'+UpSpecFile+'" size="12">* &nbsp;&nbsp;英文名&nbsp;<input type="text" name="Ename'+UpSpecFile+'" size="12"> &nbsp; 位置 <input type="file" name="Fname'+UpSpecFile+'"  onchange="AddSpecFile(this.name,Add'+UpSpecFile+')" size="16">*&nbsp; &nbsp;新文档&nbsp;<input name="Newbz'+UpSpecFile+'" type="checkbox"  value="New"><p></P>');
		}
}

//添加新的订单项目
var AddOrderItem=0;
var Ordered="";//记录已在列表中的产品号码
function AddItem(Action)
{	
	var ThisAmount=0;
	var ThisId=0;
	if(Action=="Product"){
	var PName=form1.Product.value;}
	else{
	var PName=form1.Stuff.value;}
	var Quantity=cTrim(form1.Quantity.value,0);
	form1.Quantity.value=Quantity;
	//去空格后检查数量是否数字,CheckNum=1是数字;CheckNum=0不是数字
	var CheckNum=fucCheckNUM(Quantity,"");
	if (CheckNum!=0){
	//当数量为合法的数字
		if(PName!=""){
		//当选取了产品
			var CL=PName.split("|");//分割产品信息字符串"产品编号CL[0]|产品名称CL[1]|售价CL[2]"
			
			if (AddOrderItem==0){
				//如果是首次添加产品,则先加上列标题
				if(Action=="Product"){
				Add1.insertAdjacentHTML('beforeBegin',' <br><div align="center">订 购 清 单</div><hr size="1"><div align="center">序号/NO. &nbsp;&nbsp;&nbsp;产品编号/PRODUCT NO.  &nbsp;&nbsp;&nbsp;产品名称/PRODUCT NAME &nbsp;&nbsp;&nbsp;订购数量/QUANTITY&nbsp;&nbsp;&nbsp;售价/PRICE&nbsp;&nbsp;&nbsp;小计/AMOUNT</div><hr size="1">');
				}else{
				Add1.insertAdjacentHTML('beforeBegin',' <br><div align="center">采 购 清 单</div><hr size="1"><div align="center">序号/NO. &nbsp;&nbsp;&nbsp;配件编号/PRODUCT NO.  &nbsp;&nbsp;&nbsp;配件名称/PRODUCT NAME &nbsp;&nbsp;&nbsp;采购数量/QUANTITY&nbsp;&nbsp;&nbsp;价格/PRICE&nbsp;&nbsp;&nbsp;小计/AMOUNT</div><hr size="1">');
					}
				}
			if (Ordered.indexOf(CL[0])==-1){
			//当该产品没被选中
				Ordered=Ordered+CL[0];
				AddOrderItem++;
				//小计=售价*数量;并格式化结果,保留两位小数
				ThisAmount=FormatNumber(CL[2]*Quantity,2);
				//1-9单元为其它操作单元;10-15为第一个选中产品的行资料单元
				QuantityId=(AddOrderItem-1)*6+13;//6每行的表单单元素总数,13为价格初始单元序号
				PriceId=(AddOrderItem-1)*6+14;
				//加入订购的产品资料行
				Add1.insertAdjacentHTML('beforeBegin',' <div align="center"><input type="text" name="NO['+AddOrderItem+']" value="'+AddOrderItem+'" class="OrderItemTextLeft" size="6" readonly><input type="text" name="FPNumber['+AddOrderItem+']" value="'+CL[0]+'" class="OrderItemTextCenter" size="26" readonly><input type="text" name="FPName['+AddOrderItem+']" value="'+CL[1]+'" class="OrderItemTextLeft" size="22" readonly><input type="text" name="Quantity['+AddOrderItem+']" value="'+Quantity+'" class="OrderItemQuantity" size="12" onchange="UpdataNum('+QuantityId+')"><input type="text" name="Price['+AddOrderItem+']" value="'+CL[2]+'" class="OrderItemQuantity" size="12" onchange="UpdataNum('+PriceId+')"><input type="text" name="Amount['+AddOrderItem+']" value="'+ThisAmount+'" class="OrderItemTextRight" size="14" readonly></div>');
				div1.style.display="";
				//总数量=原有总数量+这次产品添加的数量
				form1.QuantityTotal.value=Number(form1.QuantityTotal.value)+Number(Quantity);
				//总价格=原有总价格+这次订购的产品总额
				form1.Total.value=FormatNumber((Number(form1.Total.value)+Number(Quantity*CL[2])),2);
				//将价格从小数转英文大写
				form1.SayTotal.value=NumberToEnglish(form1.Total.value);
				}
			else{
			//该产品已经选中过.
				alert("该产品已经打购，你可以在下面的清单更改订购的数量");
				}
			}
		else{
		//当没有选取产品
			alert("没有选择要订购的产品！");	
		 	document.form1.Product.focus();
			}
		}
	else{
	//当数量不合法时
		alert("输入的订购数量非整数，请重新输入！");
		 document.form1.Quantity.focus();
		 document.form1.Quantity.select();
		}
	}

//产品数量改变时的操作
function UpdataNum(QuantityID){
	var Objects="";
	//默认情况下：QuantityID是数量,j是价格,i是小计
	//数量ID+1是该行产品的价格;+2是小计
	var i=QuantityID+2;
	var j=QuantityID+1;
	//判断QuantityID是来自于数量变化还是价格变化
	//非默认下,QuantityID是价格,j是数量,i是小计
	var ttty=QuantityID%10;
	if (((QuantityID-10)%6)!=3){
		Objects="Price";
		var i=QuantityID+1;
		var j=QuantityID-1;//是数量
		}
	//去空格后检查是否数字或价格
	var CheckNum=fucCheckNUM(cTrim(document.forms[0].elements[QuantityID].value,0),Objects);
	//如果是价格检查小数点后的数是否超出范围,此功能未使用，而用四舍五入法替代
	
	if (CheckNum!=0){
		if (Objects!="Price"){
			//数量回显
			document.forms[0].elements[QuantityID].value=cTrim(document.forms[0].elements[QuantityID].value,0);
			}
		else{
			//价格回显
			document.forms[0].elements[QuantityID].value=FormatNumber(cTrim(document.forms[0].elements[QuantityID].value,0),4);
			}
		//小计重新赋值		
		document.forms[0].elements[i].value=FormatNumber(document.forms[0].elements[QuantityID].value*document.forms[0].elements[j].value,2);
		
		var k=1;
		var QuantityTemp=0;
		var TotalTemp=0;
		while (k<=AddOrderItem){
			var x1=(k-1)*6+13;//每行的数量单元号码
			var x2=(k-1)*6+15;//
		//重求总数
		if (Objects!="Price"){
			QuantityTemp=QuantityTemp+Number(document.forms[0].elements[x1].value);}
		//重求总价
		TotalTemp=TotalTemp+Number(document.forms[0].elements[x2].value);
		k++;
		}
		//总数量重新赋值
		if (Objects!="Price"){
			form1.QuantityTotal.value=QuantityTemp;}
		//总价重新赋值
		form1.Total.value=FormatNumber(TotalTemp,2);
		//重新转换英文大写
		form1.SayTotal.value=NumberToEnglish(form1.Total.value);
	}
	else{
		if (Objects!="Price"){
		alert("更改的订购数量非整数，请重新输入！");
		//恢复原数
		document.forms[0].elements[QuantityID].value= Math.round(document.forms[0].elements[i].value/document.forms[0].elements[j].value);
		}
		else{
		alert("更改的价格不合要求，请重新输入！");
		//恢复原数
		document.forms[0].elements[QuantityID].value= FormatNumber(document.forms[0].elements[i].value/document.forms[0].elements[j].value,2);
			}
		
		}
}

/*  
*    ForDight(Dight,How):数值格式化函数，Dight要  
*    格式化的  数字，How要保留的小数位数。  
*/  
function  ForDight(srcStr,nAfterDot)  
{  
           srcStr  =  Math.round  (srcStr*Math.pow(10,nAfterDot))/Math.pow(10,nAfterDot);  
           return  srcStr;  
}  

//格式化数字
function FormatNumber(srcStr,nAfterDot){
　　var srcStr,nAfterDot;
　　var resultStr,nTen;
　　srcStr = ""+srcStr+"";
　　strLen = srcStr.length;
	//小数点位置
　　dotPos = srcStr.indexOf(".",0);
　　//-1为无小数点
	if (dotPos == -1){
		//整数则在后加.00
　　　　resultStr = srcStr+".";
　　　　for (i=0;i<nAfterDot;i++){
　　　　　　resultStr = resultStr+"0";
　　　　}
　　　　return resultStr;
　　}
　　else{
		// 如果小数点后的数字多于要保留的的位数
　　　　if ((strLen - dotPos - 1) >= nAfterDot){
　　　　　　nAfter = dotPos + nAfterDot + 1;
　　　　　　nTen =1;
　　　　　　for(j=0;j<nAfterDot;j++){
　　　　　　　　nTen = nTen*10;
　　　　　　}
　　　　　　resultStr = Math.round(parseFloat(srcStr)*nTen)/nTen;
			//三种结果：123.45   123.4   123
			//处理尾数是0的情况，要补0
		　　resultStr = ""+resultStr+"";
		　　strLen = resultStr.length;
			dotPos = resultStr.indexOf(".",0);
			//带一位小数123.4的补"0";带两位小数的略过
			if ((strLen - dotPos - 1)< nAfterDot){
				resultStr=resultStr+"0";
				}
			//不带小数123的加".00"
			if (dotPos=="-1"){
					resultStr=resultStr+".00";
					}
　　　　　　return resultStr;
　　　　}
　　　　else{
			// 如果小数点后的数字少于要保留的的位数
　　　　　　resultStr = srcStr;
　　　　　　for (i=0;i<(nAfterDot - strLen + dotPos + 1);i++){
　　　　　　　　resultStr = resultStr+"0";
　　　　　　}
　　　　　　return resultStr;
　　　　}
　　}
} 

//小数转英文大写
function NumberToEnglish(ThisOrderTotal){
	//待转换的数字
	var str=""+ThisOrderTotal+"";
	//小数点位置，从0算起
	var point=str.lastIndexOf(".");
	//字串总长度
	var StrLength=str.length;
	//小数点前的数字长度
	var FrontPoint=point;
	//个位十位数字英文对照
	var numberstr1="1~ONE~2~TWO~3~THREE~4~FOUR~5~FIVE~6~SIX~7~SEVEN~8~EIGHT~9~NINE~10~TEN~11~ELEVEN~12~TWELVE~13~THIRTEEN~14~FOURTEEN~15~FIFTEEN~16~SIXTEEN~17~SEVENTEEN~18~EIGHTEEN~19~MINETEEN";
	var NUM1=numberstr1.split("~");
	//十位数对照
	var numberstr2="2~TWENTY~3~THIRTY~4~FORTY~5~FIFTY~6~SIXTY~7~SEVENTY~8~EIGHTY~9~NINETY";
	var NUM2=numberstr2.split("~");
	//千进位
	var KStr="~THOUSAND~MILLION~BILLION~TRILLION";
	var KTemp=KStr.split("~");
	var NUM1length=NUM1.length;
	var NUM2length=NUM2.length;
	var i=FrontPoint;
	var OutStr="";
	var KN=0;
	
	while (i>0){
		//个位
		NowNumber1=str.substring(i,i-1);
		//十位
		var OutStr1="";//个位的值
		var OutStr10="";//十位的值
		var OutStr101="";//个位+十位的总值
		var OutStr100="";//百位的值
		//处理个位和十位数
		if (i-1>0)
		{
			//有十位
			NowNumber10=str.substring(i-1,i-2);
			NowNumber101=str.substring(i,i-2)		
			}
		else{
			//无十位
			NowNumber10=0;
			NowNumber101=NowNumber1;
			}
		//转十位内
		
		if (NowNumber10<2){
			//如果十位小于2,即是20以下的数，则直接对照英文
			var j=NUM1length-1;
			while(j>=0)
			{
			if (NUM1[j]==NowNumber101){
				OutStr10=NUM1[j+1];
				break;}
			if (OutStr10=="") {
				if (NUM1[j]==NowNumber1){
					OutStr1=NUM1[j+1];
					break;}
				}
			j--;}
			
			}//结束小于2的情况
			else{
				//大于2
				var j=0;
				while(j<NUM2length){
					if (NUM2[j]==NowNumber10){
						var OutStr10=NUM2[j+1];
						break;
						}
						j++;
					}
				var k=0;
				while(k<NUM1length){
					if (NowNumber1==NUM1[k]){
						OutStr1=" "+NUM1[k+1];
						break;
						}
					k++;
					}
					
			}//结束转十位				
		OutStr101=OutStr10+OutStr1;//个位和十位相加的值		
//百位
		if (i-2>0){
		NowNumber100=str.substring(i-2,i-3);
		//如果有百位数
		if (NowNumber100>0){
			var j=0;
			while(j<NUM1length){
				if (NowNumber100==NUM1[j]){
					if (OutStr101!=""){
					OutStr100=NUM1[j+1]+" HUNDRED AND ";}
					else{
						OutStr100=NUM1[j+1]+" HUNDRED";
						}
					}
					j++;
				}
			}			
			}
			
			//将这次的Str值+到输出的值中
			OutStr=OutStr100+OutStr101+" "+KTemp[KN]+" "+OutStr;
		i=i-3;
		KN++;
	}//end while (i>0)
	
	//转换小数点后的数字
	var PointStr10="";
	var PointStr1="";
	var PointStr="";
	var PointStr101="";
	//也分两种情况，个位
	Point1=Number(str.substring(StrLength-1,StrLength));
	Point10=Number(str.substring(StrLength-2,StrLength-1));
	Point101=Number(str.substring(StrLength-2,StrLength));
	//如果十位小于2,即是20以下的数，则直接对照英文
	if (Point10<2){
			var j=NUM1length-1;
			while(j>=0)
			{
			if (NUM1[j]==Point101){
				PointStr10=NUM1[j+1];
				break;}
			if (PointStr10=="") {
				if (NUM1[j]==Point1){
					PointStr1=NUM1[j+1];
					break;}
				}
			j--;}
			}//结束小于2的情况
	else{

		//如果十位大于2,即是20以上的数，则先对照十位然后对照个位
		var j=0;
				while(j<NUM2length){
					if (NUM2[j]==Point10){
						PointStr10=NUM2[j+1];
						break;
						}
						j++;
					}
				var k=0;
				while(k<NUM1length){
					if (NUM1[k]==Point1){
						PointStr1=" "+NUM1[k+1];
						break;
						}
					k++;
					}
		}
		PointStr101=PointStr10+PointStr1;//个位和十位相加的值
		if (PointStr101!=""){
			
			if(cTrim(OutStr,0)!=""){
			PointStr101="AND "+PointStr101+" CENTS";}
			else{
			//如果小于1
			PointStr101=PointStr101+" CENTS";
				}
			}
		else{
			PointStr101=PointStr101+"ONLY";
			}
	OutStr="SAY TOTAL: US DOLLAR "+cTrim(OutStr,0)+" "+PointStr101;
return OutStr;
}
//函数名：fucCheckNUM
//功能介绍：检查是否为数字
//参数说明：要检查的数字
//返回值：1为是数字，0为不是数字
//Objects:检查整数还是价格
function fucCheckNUM(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789"; 
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i)); 
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}

//刷新窗口
function RefreshWin(WebPage){
	location.href=WebPage+".php";}

// Description: sInputString 为输入字符串，iType为类型，分别为
// 0 - 去除前后空格; 1 - 去前导空格; 2 - 去尾部空格
//****************************************************************
function cTrim(sInputString,iType)
{
var sTmpStr = ' '
var i = -1

if(iType == 0 || iType == 1)
{
while(sTmpStr == ' ')
{
++i
sTmpStr = sInputString.substr(i,1)
}
sInputString = sInputString.substring(i)
}

if(iType == 0 || iType == 2)
{
sTmpStr = ' '
i = sInputString.length
while(sTmpStr == ' ')
{
--i
sTmpStr = sInputString.substr(i,1)
}
sInputString = sInputString.substring(0,i+1)
}
return sInputString
}

//焦点自动下移
function init() 
{ 
document.onkeydown=keyDown ; 
} 

function keyDown(e) {  
	if(event.keyCode==13) 
	{ 
		event.keyCode=9 ;
		} 
	}

//隐藏/显示表格
function showsubmenu(sid,idcount)
{
	whichEl = eval("submenu" + sid);
	if (whichEl.style.display == "none"){
		eval("submenu" + sid + ".style.display=\"\";");
		for(var i=1;i<=idcount;i++){
		if (i!=sid){
		eval("submenu" + i + ".style.display=\"none\";");}}
			}
	else{
		eval("submenu" + sid + ".style.display=\"none\";");	
				
			}
	}
//带动态的显示菜单
  function menuShow(obj,maxh,obj2)
  {
    if(obj.style.pixelHeight<maxh)
    {
      obj.style.pixelHeight+=maxh/20;
      obj.filters.alpha.opacity+=5;
      obj2.background="images/title_bg_hide.gif";
      if(obj.style.pixelHeight==maxh/10)
        obj.style.display='block';
      myObj=obj;
      myMaxh=maxh;
      myObj2=obj2;
      setTimeout('menuShow(myObj,myMaxh,myObj2)','5');
    }
  }
//带动态的隐藏菜单
  function menuHide(obj,maxh,obj2)
  {
    if(obj.style.pixelHeight>0)
    {
      if(obj.style.pixelHeight==maxh/20)
        obj.style.display='none';
      obj.style.pixelHeight-=maxh/20;
      obj.filters.alpha.opacity-=5;
      obj2.background="images/title_bg_show.gif";
      myObj=obj;
      myMaxh=maxh
      myObj2=obj2;
      setTimeout('menuHide(myObj,myMaxh,myObj2)','5');
    }
    else
      if(whichContinue)
        whichContinue.click();
  }

  function menuChange(obj,maxh,obj2)
  {
    if(obj.style.pixelHeight)
    {
      menuHide(obj,maxh,obj2);
      whichOpen='';
      whichcontinue='';
    }
    else
      if(whichOpen)
      {
        whichContinue=obj2;
        whichOpen.click();
      }
      else
      {
        menuShow(obj,maxh,obj2);
        whichOpen=obj2;
        whichContinue='';
      }
  }
	
//打开或关闭右框架
var KeyWord="open;"
function openOrclose(){
	if (KeyWord=="open"){ 
        KeyWord="close";
        top.frmMain.cols='*,8';
		arrowhead.src="images/arrowhead1.gif";
    	} 
    else{ 
        KeyWord="open";
        top.frmMain.cols='*,138';
		arrowhead.src="images/arrowhead2.gif";
    	} 
	}	
//yyyy-mm-dd格式检查
function yyyymmddCheck(str){
    var re = new RegExp("^([0-9]{4})[.-]{1}([0-9]{1,2})[.-]{1}([0-9]{1,2})$");
    var ar;
    var res = true;
    if ((ar = re.exec(str)) != null){
        var i;
        i = parseFloat(ar[3]);//01-12-2006:1
		
        // verify dd
        if (i <= 0 || i > 31){
            res = false;
        }
        i = parseFloat(ar[2]);
        // verify mm
        if (i <= 0 || i > 12){
            res = false;
        }
    }else{
        res = false;
    }
	return res;
	}
//去除空格
String.prototype.Trim = function() 
{ 
return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 

String.prototype.LTrim = function() 
{ 
return this.replace(/(^\s*)/g, ""); 
} 

String.prototype.RTrim = function() 
{ 
return this.replace(/(\s*$)/g, ""); 
} 
//去除全部空格
function allTrim(TempStr){
	while (TempStr.indexOf(" ",0) != -1){
		TempStr=TempStr.replace(" ","")}
	return TempStr;
}