function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function ChangeThis(ID,textID,Itemp,Mid){
	var Field=Itemp;
	var oldValue=document.form1.TempValue.value;//改变前的值
	var Clienttemp=	document.getElementById('CompanyId').value;
	switch(Field){
		case "Unit":
			var Unittemp=eval("document.form1.Unit"+textID+".value");//改变后的值
			var Result=fucCheckNUM(Unittemp,'Price');
			if(Result==0){
				alert("输入不正确的售价:"+Unittemp+",重新输入!");
				eval("document.form1.Unit"+textID).value=oldValue;
				}
			else{
				document.form1.action='shipmentbill_updated.php?Action='+Field+'&POrderId='+ID+'&Value='+Unittemp+'&CompanyId='+Clienttemp+'&Mid='+Mid;
				document.form1.submit();
				}
			break;
		}
	}
function ChenckFrom(Action,ALType){
	//解锁，检查是否单选记录
	var UpdataIdX=0;
	var j=1;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					Id=e.value;
					InvoiceSTR=eval("document.form1.Invoice"+j+".value");
					} 
				j++;
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
		switch(Action){
			case "Update":
				document.form1.action='shipmentbill_update.php?Id='+Id+ALType;document.form1.submit();			
			break;
			case "ProductInfo":
				document.form1.action='shipmentbill_ProductInfo.php';document.form1.submit();
				break;
			case "CreditNote":
				document.form1.action='creditnote_add.php';document.form1.submit();
				break;
			case "Set"://装箱设置
				document.form1.action='shipmentbill_tolable.php?Id='+Id+ALType;document.form1.submit();
				break;
			case "Del":
				DelIds("shipmentbill","","");
				break;
			case "toInvoice":
				document.form1.action='shipmentbill_toinvoice.php?Mid='+Id+ALType;document.form1.submit();
				break;
			case "AddInfo":
				document.form1.action='shipmentbill_addinfo.php?Action=AddInfo&Id='+Id+ALType;document.form1.submit();
				break;
			case "Cancel":
				//提醒
				var message=confirm("确定要取消此出货单吗？");
				if (message==true){
					document.form1.action='shipmentbill_cancel.php?Id='+Id+ALType;document.form1.submit();
					}
				else{
					return false;
					}				
				break;
			case "NoBox":
				document.form1.action='shipmentbill_addinfo.php?Action=NoBox&Id='+Id+ALType;document.form1.submit();
				break;
			default:
				if(InvoiceSTR==""){
					alert("该出货单没有进行装箱设置！不能出货！");}
				else{					
					document.form1.action='shipmentbill_updated.php?Action=shipment&Mid='+Id;document.form1.submit();
					}
				break;
			}
		}
	}