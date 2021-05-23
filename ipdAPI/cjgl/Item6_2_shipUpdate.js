function viewOrderdata() 
	{
		var diag = new Dialog("live");
		var ClientTemp=document.saveForm.CompanyId.value;
		var ShipSign=document.saveForm.ShipSign.value;
		diag.Width = 840;
		diag.Height = 600;
		diag.Title = "订单资料";
		diag.URL = "Ordership_s1.php?CompanyId="+ClientTemp+"&ShipSign="+ShipSign;
		diag.ShowMessageRow = false;
		diag.MessageTitle ="";
		diag.Message = "";
		diag.ShowButtonRow = true;
		diag.selModel=2; //1只选一条；2多选；
		diag.OKEvent=function()
								{
									var backData=diag.backValue();
									if (backData)
									{
										editTabRecord(backData);
										diag.close();
									}
								};
	diag.show();
}

function editTabRecord(BackOrder){
  		var Rowstemp=BackOrder.split(",");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");//$StockId^^$StuffId^^$StuffCname^^$FactualQty^^$AddQty^^$CountQty^^$Unreceive
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var POrderIdtemp=ListTable.rows[j].cells[0].data;//隐藏ID号存于操作列	
				if(FieldArray[1]==POrderIdtemp){//如果流水号存在
					Message="订单流水号: "+FieldArray[1]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
				oTD.data=""+FieldArray[1]+"";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.data=""+FieldArray[0]+"";
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				
				//三: po
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="120";
				
				//四：中文名
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.width="250";
				
				//五:Product Code
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.width="210";

				//六：信价
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="80";

				//七：数量
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="79";
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
}

function deleteRow(RowTemp,TableTemp,OrderIdTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	if(TableTemp==ListTable){
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{
		//处理删除，删除成功后再删除行
		var LengthTemp=TableTemp.rows.length;
		if (LengthTemp==1){
			alert("本出货单最后一个订单，不能删除，请使用取消出货的功能！");return false;
			}
		else{
			var message=confirm("确定要删除此出货订单吗？如果删除，则需重新设置装箱并再次生成Invoice!");
			if (message==true){
				var Id=document.saveForm.ShipId.value;
				var ReBackId=OrderIdTemp;
				myurl="Item6_2_ajax.php?POrderId="+ReBackId+"&ActionId=934&Id="+Id;
				var ajax=InitAjax(); 
	            ajax.open("GET",myurl,true);
				  ajax.onreadystatechange =function(){
		　         if(ajax.readyState==4){// && ajax.status ==200
		             var BackData=ajax.responseText;
			         if(BackData=="Y"){
					     TableTemp.deleteRow(rowIndex);
					     ShowSequence(TableTemp);
				          }
			          else alert("删除失败！");return false;			
				     }
			      }
	             ajax.send(null); 	
				}
			else{
				return false;
				}
			}
		}	
}

function saveOrderList()
{
    var OrderIdsTemp="";
    
    var Id = document.getElementById("ShipId").value;
    var Notes = document.getElementById("Notes").value;
    var Terms = document.getElementById("Terms").value;
    var PaymentTerm = document.getElementById("PaymentTerm").value;
    var ModelId = document.getElementById("ModelId").value;
    var ShipType = document.getElementById("ShipType").value;
    var ShipEstate = document.getElementById("ShipEstate").value;
	
	var ListTable = document.getElementById("ListTable");
	for(var j=0;j<ListTable.rows.length;j++)
	{		
		if(OrderIdsTemp=="")
		{
		   OrderIdsTemp=ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data;
		}
		else
		{
			OrderIdsTemp=OrderIdsTemp+"|"+ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data;
		}
	}
		
	   var d = new Date();
	   var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
       var url="?"+timTag+"#OrderIds="+OrderIdsTemp+"&Notes="+Notes+"&Terms="+Terms+"&PaymentTerm="+PaymentTerm+"&ModelId="+ModelId+"&Id="+Id+"&ShipEstate="+ShipEstate+"&ShipType="+ShipType+"&ipadTag=yes"; 
       document.location = url;
		  
}

function CheckForm()
{
	var OrderIdsTemp="";
	for(var j=0;j<ListTable.rows.length;j++)
	{		
		if(OrderIdsTemp=="")
		{
			OrderIdsTemp=ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data;
		}
		else
		{
			OrderIdsTemp=OrderIdsTemp+"|"+ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data;
		}
	}
}
