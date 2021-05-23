<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php

   $outType = $outType==""?2:$outType;
   $outTypeStr = "outType".$outType;
   $$outTypeStr = "selected";

   $OutTypeList="<select name='outType' id='outType' onChange='ResetPage(14,5)'>";
   $OutTypeList.="<option value='1' $outType1>外发备料①</option>";
   $OutTypeList.="<option value='2' $outType2>外发备料②</option>";
   $OutTypeList.="</select>&nbsp;";

   if($outType==1){
	   include "item5_14_1.php";
   }else if($outType ==2){
	   include "item5_14_2.php";
   }

?>
<script src='showkeyboard.js' type=text/javascript></script>
<script src='taskstyle.js' type=text/javascript></script>
<script>
var keyboard=new KeyBoard();
var tasksboard=new TasksBoard();
var QtyArray=new Array();
var IdArray=new Array();
var eArray=new Array();
var eImg="<img src='../images/register.png' width='30' height='30'>";

function showKeyboard(e,index,j,OrderQty,llQty,StockId){
    var addQtyFun=function(){
		var checkId="checkId"+index;
		var ListCheck=document.getElementsByName(checkId);
		var eStr=parseFloat(e.innerHTML);
	    if (eStr>=0){
	       ListCheck[j-1].checked=true;
		}else{
		   ListCheck[j-1].checked=false;
		}
		checkFlag(index);
		addQty(e,StockId);
		};
    keyboard.show(e,OrderQty,'<=',llQty,addQtyFun);
}
//全选
function checkAll(e,index){
	var tempval7,tempval8,tempval9,tempval10;
	var tableId="ListTable"+index;
	var checkId="checkId"+index;
	var ListTable=document.getElementById(tableId);
	var ListCheck=document.getElementsByName(checkId);
    if (e.checked){
	   for(var i=0;i<ListTable.rows.length;i++){
		   if (ListCheck[i].disabled==false){
			   ListCheck[i].checked=true;
			   tempval7=parseFloat(ListTable.rows[i].cells[7].innerHTML);
			   tempval8=parseFloat(ListTable.rows[i].cells[8].innerHTML);
			   tempval9=parseFloat(ListTable.rows[i].cells[9].innerHTML);
			   tempval10=tempval8-tempval9;
			   if (tempval10>tempval7) tempval10=tempval7;
			   ListTable.rows[i].cells[10].innerHTML=tempval10.toFixed(1);
			   addQty(ListTable.rows[i].cells[10],ListCheck[i].value);
		    }
		}
	}
	else{
	 for(var i=0;i<ListTable.rows.length;i++){
	   if (ListCheck[i].disabled==false){
		 ListCheck[i].checked=false;
		 ListTable.rows[i].cells[10].innerHTML=eImg;
		 addQty(ListTable.rows[i].cells[10],ListCheck[i].value);
		}
	  }
	}
}
// 多选
function checkId(e,index,i){
	var tempval7,tempval8,tempval9,tempval10;
    var checkId="checkId"+index;
    var tableId="ListTable"+index;
	var ListTable=document.getElementById(tableId);
	var ListCheck=document.getElementsByName(checkId);
	i=i-1;
	if (ListCheck[i].checked){
		tempval7=parseFloat(ListTable.rows[i].cells[7].innerHTML);
		tempval8=parseFloat(ListTable.rows[i].cells[8].innerHTML);
		tempval9=parseFloat(ListTable.rows[i].cells[9].innerHTML);
		tempval10=tempval8-tempval9;
		if (tempval10>tempval7) tempval10=tempval7;
	    ListTable.rows[i].cells[10].innerHTML=tempval10;
		addQty(ListTable.rows[i].cells[10],ListCheck[i].value);
	  }
	else{
	    ListTable.rows[i].cells[10].innerHTML=eImg;
		addQty(ListTable.rows[i].cells[10],ListCheck[i].value);
	}
	checkFlag(index);
}
//检查全选状态
function checkFlag(index){
   var checkId="checkId"+index;
   var checkAllId="checkAll"+index;
   var Flag=true;
   var ListCheck=document.getElementsByName(checkId);
   var checkAll=document.getElementById(checkAllId);
   for(var i = 0; i<ListCheck.length; i++) {
	  if (ListCheck[i].checked==false &&  ListCheck[i].disabled==false){
		  Flag=false;break;
	  }
   }
   checkAll.checked=Flag;
}

function addQty(e,StockId){
	var eStr=parseFloat(e.innerHTML);
	if (eStr>=0){
	    m= ArrayPostion(IdArray,StockId);
		if (m>=0){
			QtyArray[m]=eStr;
		    }
		else{
		   IdArray.unshift(StockId);
		   eArray.unshift(e);
		   QtyArray.unshift(eStr);
		   BtnDisabled(false);
	       e.style.color='#F00';
		    }
	   }
	else{
		m= ArrayPostion(IdArray,StockId);
		if (m>=0){
			IdArray.splice(m,1);
			QtyArray.splice(m,1);
			eArray.splice(m,1);
		}
		e.innerHTML=eImg;
	}
}

function ArrayPostion(Arr,Str){
   var backValue=-1;
   var sLen=Arr.length;
   if (sLen>0){
	 for (i=0;i<sLen;i++){
		 if (Arr[i]==Str){backValue=i;break;}
	 }
    BtnDisabled(false);
   }
   return backValue;
}

function ArrayClear(){
   IdArray=[];
   QtyArray=[];
   var sLen=eArray.length;
   if (sLen>0){
	  for (i=0;i<sLen;i++){eval(eArray[i]).innerHTML=eImg;}
   }
   eArray=[];
   var checkId,ListCheck,checkAllId;
   var cIdNumber=parseInt(document.getElementById("cIdNumber").value);

   for (var i=1;i<=cIdNumber;i++){
       checkId="checkId"+i;
	   checkAllId="checkAll"+i;
	   document.getElementById(checkAllId).checked=false;
	   ListCheck=document.getElementsByName(checkId);
	   for(var j = 0; j<ListCheck.length; j++) {
		  if (ListCheck[j].disabled==false) ListCheck[j].checked=false;
	   }
    }
	BtnDisabled(true);
}

function BtnDisabled(Flag){
   document.getElementById("saveBtn").disabled=Flag;
   document.getElementById("cancelBtn").disabled=Flag;
}




function saveQty(fromAction){
	if (IdArray.length<=0){
		alert ("请先添加领料数量！");
		return false;
	}
	BtnDisabled(true);
	var Ids=IdArray.join("|");
	var Qty=QtyArray.join("|");
	var fromPage = document.getElementById("fromPage").value;
	if(fromAction==2){
	    var url="item5_3_ajax.php?Id="+Ids+"&Qty="+Qty+"&ActionId=31&fromPage="+fromPage;
	 }else{
		 var url="item5_14_ajax.php?Id="+Ids+"&Qty="+Qty+"&ActionId=31&fromPage="+fromPage;

	 }
    var ajax=InitAjax();
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
		        alert(ajax.responseText);
			   // alert ("数据保存成功！");
				document.form1.submit();
			}
		}
	   ajax.send(null);
}


function CnameChanged(e){
	var StuffCname=e.value;
	if (StuffCname.length>=1){
	   e.style.color='#000';
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  e.style.color='#DDD';
	  document.getElementById("stuffQuery").disabled=true;
	}
}

</script>