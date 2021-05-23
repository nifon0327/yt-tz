$(document).ready(init());

function init()
{
}

function callImage(orderId,name)
{
	var d = new Date();
	var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
	var url =  "?"+timTag+"#function+showImage+"+orderId+"+"+name;
	document.location = url;
}

function creatShip()
{
	var companyId = 1001;
	var index=document.getElementById("AllId").value;
	var tempIdArray=0;
	
    for(var i=1;i<=index-1;i++)
	 {
	 	 var checkid="checkid"+i;
	 	 var e=document.getElementById(checkid);
	 	 if(e.checked)
	 	 {
	 	 	if(tempIdArray==0)
	 	 	{
	 	 		tempIdArray=e.value;
	 	 	}
	 	 	else 
	 	 	{
	 	 		tempIdArray=tempIdArray+"^^"+e.value;
	 	 	}
	    }
	 }
	 
	
	 var d = new Date();
	 var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
	 var url="?"+timTag+"#function+shipList+"+tempIdArray+"+"+companyId;
	 document.location = url;
	
}

function refresh()
{
	 location.reload();
}