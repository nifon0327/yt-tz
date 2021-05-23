var TYPE_ID=new Array(4);
var TYPE_NAME=new Array(4);
var oldHTML;
TYPE_ID[0]="I";
TYPE_NAME[0]="上班签到";
TYPE_ID[1]="O";
TYPE_NAME[1]="下班签退";
TYPE_ID[2]="K";
TYPE_NAME[2]="跨日签退";
var TYPE_Counts=3;

function updateTHIS(theID,Fields,ID,CHECKTYPE){
	oldHTML=eval(theID).innerHTML;		//原单元格内容
	eval(theID).onclick="";
	var optionText;
	var thisFields=Fields;//toString()
	for(var i=0; i<TYPE_Counts; i++){
		CHECKTYPE+="";
		var nowCHECKTYPE=TYPE_ID[i]+"";
		if(CHECKTYPE==nowCHECKTYPE){
				optionText += "<option value='"+TYPE_ID[i]+"' selected>"+TYPE_NAME[i]+"</option>";				
				}
			else{
				optionText += "<option value='"+TYPE_ID[i]+"'>"+TYPE_NAME[i]+"</option>";
				}
				}
    eval(theID).innerHTML="<select id='TableSel' style='width: 70px;' OnBlur='backTHIS("+theID+",\""+thisFields+"\","+ID+",\""+CHECKTYPE+"\")' onchange='selectChange(this.options[this.selectedIndex].value,this.options[this.selectedIndex].text,"+theID+",\""+thisFields+"\","+ID+",\""+CHECKTYPE+"\")'>"+optionText+"</select>";
	document.form1.TableSel.focus();
	//清空缓冲区
    optionText = "";
	}

function selectChange(selectVALUE,selectTEXT,theID,Fields,ID,CHECKTYPE)//(选框ID值,选框显示名,,操作字段)
{
	eval(theID).innerHTML="<div align='center'  style='color:#009900'>"+selectTEXT+"</div>";
	eval(theID).onclick=function(){updateTHIS(this.id,Fields,ID,CHECKTYPE)};
	myurl="kq_checkio_updated.php?ActionId=902&Id="+ID+"&CheckType="+selectVALUE+"&Field="+Fields;
	retCode=openUrl(myurl); 
	if (retCode==-2){
		alert("更新失败,此问题请及时反映给系统管理员!");
		}
	else{
		document.form1.submit();
		}
	}
function backTHIS(theID,Fields,ID,CHECKTYPE){
	if(oldHTML!=""){
		eval(theID).innerHTML="<div align='center'  style='color:#009900'>"+oldHTML+"</div>";
		eval(theID).onclick=function(){updateTHIS(this.id,Fields,ID,CHECKTYPE)};
		oldHTML="";
		}
	}
