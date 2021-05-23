function addQtyFun(e,Tid,t,POrderId,cutId,Relation,DengQty){

	var tableId='ListTable'+Tid;
	var nocutQtyId='nocutQty'+t;
	var cutedQtyId='cutedQty'+t;
    var ListTable=document.getElementById(tableId);
	if (e.value=="") return;
	var CS =/^[1-9]+[0-9]*]*$/;   //判断字符串是否为数字 
	if (CS.test(e.value)){
		var addQty=parseInt(e.value);
		var nocutQty=parseInt(document.getElementById(nocutQtyId).innerHTML);
		if(DengQty==0){alert("未领料,不能登记产量");e.value="";e.focus();return false;}
		if(addQty>DengQty){alert("超过允许登记的数量,不能超过:"+DengQty);e.value="";e.focus();return false;}
		if (!CS.test(nocutQty)) nocutQty=0;
		if (addQty>nocutQty){
		   alert ('错误！输入的生产数量大于未生产数量！');
		    e.value="";e.focus();   
		    return;	
		}
		var tmpdata=addQty % Relation;
		if (tmpdata>0){
		    alert ('错误！输入的生产数量应为切割关系（'+Relation+'）的倍数！');
		    e.value="";e.focus();   
		    return;	
		}
	
		msgStr="添加生产数量确认？";
		if (confirm(msgStr)){
                  var Qty=addQty/Relation;
		  var url="../../admin/cutting_sctj_ajax.php?POrderId="+POrderId+"&cutId="+cutId+"&Qty="+Qty+"&ActionId=1"; 
              var ajax=InitAjax(); 
	      ajax.open("GET",url,true);
	      ajax.onreadystatechange =function(){
		   if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){//更新成功
			    var scQty=parseInt(document.getElementById(cutedQtyId).innerHTML);
				 if (!CS.test(scQty)) scQty=0;
			     document.getElementById(cutedQtyId).innerHTML=scQty+addQty;
			     document.getElementById(nocutQtyId).innerHTML=nocutQty-addQty;
				 e.disabled=true;
			  }
			  else{alert ("生产登记失败！"+ajax.responseText); }
			}
		  }
	     ajax.send(null); 
		}
		else{
		  e.value="";	
		}
	}else{
		alert ('提示:请输入大于零的数字！');
		 e.value="";e.focus();   
		return;
	}
    
 }

function refresh()
{
	location.reload();
}

function restPage()
{
	var companyId = $("#CompanyId").val();
	var productType = $("#ProductTypeId").val();
	var typeId = $("#typeId").val();
	var url = "?CompanyId="+companyId+"&TypeId="+typeId+"&ProductTypeId="+productType;
	document.location = url;
}

function setProductQty(orderId,typeid)
{
	var d = new Date();
	var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
	var url="Item1_scdj.php?"+timTag+"#qty+"+orderId+"+"+typeid;
	document.location = url;
}
