//层数，记录行数，+号ID，参数，下层页面
function SandH(divNum,RowId,f,TempId,ToPage){
	var e=eval("HideTable_"+divNum+RowId);
	e.style.display=(e.style.display=="none")?"":"none";
	
		if(TempId!=""){			
			var url=ToPage+"_ajax.php?TempId="+TempId+"&RowId="+RowId;
		　	var show=eval("HideDiv_"+divNum+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
	}


function zhtj(){
	if(document.all("CompanyId")!=null){
		document.forms["form1"].elements["CompanyId"].value="";
		}
	document.form1.action="cg_stuffqty_read.php";
	document.form1.submit();
	}
	
function toPurchase(){
	var UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if(e.checked){
				UpdataIdX=UpdataIdX+1;
				break;
				} 
			}
		}
	if (UpdataIdX==0){
		alert("没有选定配件需求记录!");
		return;
		}
	else{
	//设定交货期限和采购备注
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				e.disabled=false;
				} 
			}
		document.form1.action="cg_cgdmain_save.php";
		document.form1.submit();
		}
	}
