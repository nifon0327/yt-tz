function refresh()
{
	 location.reload();
}

function ShowOrHide(e,f,RowId,ShipId){//行，列，行号
	e.style.display=(e.style.display=="none")?"":"none";
	f.innerHTML=f.innerText=="[ + ]"?"[ - ]":"[ + ]";
	var yy=f.src;
	if (f.innerText=="[ - ]"){//展开
		//动态加入采购明细
		if(ShipId!=""){			
			var url="../../admin/ch_shiporder_ajax.php?ShipId="+ShipId+"&RowId="+RowId+"&ipadTag=yes"; 
			//alert(url);
		　	var show=eval("ShowDiv"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
		           // alert(ajax.responseText);
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
}

function changeInfo()
{
	var listType = document.getElementById("ShipEstate").value;
	try
	{
		var dateType = document.getElementById("chooseDate").value;
	}
	catch(err)
	{
		var dateType = "";
	}
	var url = "?ShipEstate="+listType+"&chooseDate="+dateType;
	document.location = url;
}

function viewOrderdata() 
{
	//var diag = new Dialog("live");
	var ClientTemp=document.saveForm.CompanyId.value;
	var ShipSign=document.saveForm.ShipSign.value;
	
	diag.URL = "Ordership_s1.php?CompanyId="+ClientTemp+"&ShipSign="+ShipSign;
}

function updateOrder()
{	
	var choosedRow=0;
	var listType = document.getElementById("ShipEstate").value;
	
	var n = $("input:checked").length;
	if(n === 1)
	{
		var  checkNum = $("input:checked").val();
		var d = new Date();
		var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
		var url = "?"+timTag+"#function+updateList+"+checkNum+"+"+listType;
		document.location = url;
	}
	else
	{
		var alerMessage = (n === 0)?"该操作要求选定记录!":"该操作只能选取定一条记录!";
		alert(alerMessage);
	}
	
}

function actionToShipping(type)
{
	var choosedRow=0;
	var n = $("input:checked").length;
	if(n === 1)
	{
		var  checkNum = $("input:checked").val();
		var d = new Date();
		var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
		if(type == 59)
		{
			var url = "?"+timTag+"#function+credit+"+checkNum;
		}
		else
		{
			var url = "?"+timTag+"#function+actionShip+"+checkNum+"+"+type;
		}
		document.location = url;
	}
	else
	{
		var alerMessage = (n === 0)?"该操作要求选定记录!":"该操作只能选取定一条记录!";
		alert(alerMessage);
	}
}
