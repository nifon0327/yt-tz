function P_ShowOrHide(e,f,Order_Rows,URL,theParam,RowId,FromT,FromDir){  //公共的显示，灵活的
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(theParam!=""){
			var url="../"+FromDir+"/"+URL+"?"+theParam+"&RowId="+RowId+"&FromT="+FromT;
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					}
				}
			ajax.send(null); 
			}
		}
	}

