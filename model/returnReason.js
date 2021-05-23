	
	function ShowReturnDialog(actionId, chooseNum)
	{
		if(chooseNum>0){//检查已选取的项目
		var choosedRow=0;
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				if(e.checked){
					choosedRow=choosedRow+1;
					if(chooseNum==1)
					{
						upId= e.value;
					}
					if(choosedRow>1)
						break;
					} 
				}
			}
		if(choosedRow==0){
			Message="该操作要求选定记录！";
			}
		else{
			if(chooseNum==1 && choosedRow>chooseNum)
				Message="该操作只能选取定一条记录！";
			}		
		}
		
		alert(choosedRow);
		
		
		
	}