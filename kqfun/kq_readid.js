function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}
//签卡状态设定
var NowTime=new Date().toLocaleString().slice(-8,-6);
if(NowTime>12){
	document.getElementById("CheckType").value="O";
	document.getElementById("bgcolor_O").style.backgroundColor="#33CC66";
	}
else{
	document.getElementById("CheckType").value="I";
	document.getElementById("bgcolor_I").style.backgroundColor="#33CC66";
	}

function ReBackDefault(){
	document.getElementById("ReBack").style.display="none";
	//状态判断：如果是12点前，为签到状态，否则为签退状态
	}

function ReBackType(){//返回默认签卡状态
	var NowTime=new Date().toLocaleString().slice(-8,-6);
		if(NowTime>12){
		document.getElementById("CheckType").value="O";
		document.getElementById("bgcolor_O").style.backgroundColor="#33CC66";
		document.getElementById("bgcolor_I").style.backgroundColor="#FFFFFF";
		document.getElementById("bgcolor_S").style.backgroundColor="#FFFFFF";
		document.getElementById("bgcolor_Q").style.backgroundColor="#FFFFFF";
		}
	else{
		document.getElementById("CheckType").value="I";
		document.getElementById("bgcolor_I").style.backgroundColor="#33CC66";
		document.getElementById("bgcolor_O").style.backgroundColor="#FFFFFF";
		document.getElementById("bgcolor_S").style.backgroundColor="#FFFFFF";
		document.getElementById("bgcolor_Q").style.backgroundColor="#FFFFFF";
		}
	}
function ToActionS(ActionTemp){
		playSound("../media/Windows Default.wav");
		document.getElementById("ReBack").innerHTML="";
		document.getElementById("ReBack").style.display="none";
		switch(ActionTemp){
		case 0://签退
			document.getElementById("CheckType").value="O";
			document.getElementById("bgcolor_O").style.backgroundColor="#33CC66";
			document.getElementById("bgcolor_I").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_S").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_Q").style.backgroundColor="#FFFFFF";
			//状态判断：如果是12点前，为签到状态，否则为签退状态
			if(iType)clearTimeout(iType);
				iType=setTimeout("ReBackType()",10000);//5秒后自动恢复
		break;
		case 1://签到
			document.getElementById("CheckType").value="I";
			document.getElementById("bgcolor_O").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_I").style.backgroundColor="#33CC66";
			document.getElementById("bgcolor_S").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_Q").style.backgroundColor="#FFFFFF";
			//状态判断：如果是12点前，为签到状态，否则为签退状态
			if(iType)clearTimeout(iType);
				iType=setTimeout("ReBackType()",10000);//5秒后自动恢复
		break;
		case 2://查询,要终止恢复计算
			if(iType)clearTimeout(iType);
			document.getElementById("ReBack").innerHTML="<table width='100%' height='100%'><tr><td align='center' valign='middle' class='OInfo'>请刷卡</td></tr></table>";
			document.getElementById("ReBack").style.display="";
			document.getElementById("CheckType").value="S";
			document.getElementById("bgcolor_O").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_I").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_S").style.backgroundColor="#33CC66";
			document.getElementById("bgcolor_Q").style.backgroundColor="#FFFFFF";
		break;
		case 3://请假
			if(iType)clearTimeout(iType);
			document.getElementById("ReBack").innerHTML="<table width='100%' height='100%'><tr><td align='center' valign='middle' class='OInfo'>请刷卡</td></tr></table>";
			document.getElementById("ReBack").style.display="";
			document.getElementById("CheckType").value="Q";
			document.getElementById("bgcolor_O").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_I").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_S").style.backgroundColor="#FFFFFF";
			document.getElementById("bgcolor_Q").style.backgroundColor="#33CC66";
		break;
			}
	}

marqueesHeight=50; //内容区高度
stopscroll=false; //这个变量控制是否停止滚动
with(jbr){
	noWrap=true; //这句表内容区不自动换行
	style.width=0; //于是我们可以将它的宽度设为0，因为它会被撑大
	style.height=marqueesHeight;
	style.overflowY="hidden"; //滚动条不可见
	}

//这时候，内容区的高度是无法读取了。下面输出一个不可见的层"templayer"，稍后将内容复制到里面：
document.write('<div id="templayer" style="position:absolute;z-index:100;visibility:hidden"></div>');
function init(){ //初始化滚动内容
	init2();
	//多次复制原内容到"templayer"，直到"templayer"的高度大于内容区高度：
	templayer.innerHTML+=jbr.innerHTML;
	if(templayer.offsetHeight>marqueesHeight){
		while(templayer.offsetHeight<marqueesHeight){
			templayer.innerHTML+=jbr.innerHTML;
			}
		jbr.innerHTML=templayer.innerHTML+templayer.innerHTML;
		setInterval("scrollUp()",100);
		}
	}
document.body.onload=init;
preTop=0;
function scrollUp(){
	if(stopscroll==true) return;
	preTop=jbr.scrollTop;
	jbr.scrollTop+=1;
	if(preTop==jbr.scrollTop){
		jbr.scrollTop=templayer.offsetHeight-marqueesHeight+1;
	}
}

stopscroll2=false; //这个变量控制是否停止滚动
with(rsr){
	noWrap=true; //这句表内容区不自动换行
	style.width=0; //于是我们可以将它的宽度设为0，因为它会被撑大
	style.height=marqueesHeight;
	style.overflowY="hidden"; //滚动条不可见
	}

document.write('<div id="templayer2" style="position:absolute;z-index:100;visibility:hidden"></div>');
function init2(){ //初始化滚动内容
	//多次复制原内容到"templayer"，直到"templayer"的高度大于内容区高度：
	templayer2.innerHTML+=rsr.innerHTML;
	if(templayer2.offsetHeight>marqueesHeight){
		while(templayer2.offsetHeight<marqueesHeight){
			templayer2.innerHTML+=rsr.innerHTML;
			}
		rsr.innerHTML=templayer2.innerHTML+templayer2.innerHTML;
		setInterval("scrollUp2()",100);
		}
	}
//document.body.onload=init2;
preTop2=0;
function scrollUp2(){
	if(stopscroll2==true) return;
	preTop2=rsr.scrollTop;
	rsr.scrollTop+=1;
	if(preTop2==rsr.scrollTop){
		rsr.scrollTop=templayer2.offsetHeight-marqueesHeight+1;
	}
}

function qjAction(){
	var MsgInfo="";
	//检查请假原因是否选择
	if(document.getElementById("Type").value==""){
		MsgInfo="未选择请假原因";
		}
	//检查请起日期和时间是否选择
	if(document.getElementById("Sdate").value==""){
		MsgInfo="未选择起始日期";
		}
	if(document.getElementById("Stime").value==""){
		MsgInfo="未选择起始时间";
		}
	//检查结束日期和时间是否选择
	if(document.getElementById("Edate").value==""){
		MsgInfo="未选择结束日期";
		}
	if(document.getElementById("Etime").value==""){
		MsgInfo="未选择结束时间";
		}
	//检查结束日期时间是否大于起始日期时间
	if(document.getElementById("Sdate").value>document.getElementById("Edate").value){
		MsgInfo="不正确,起始日期大于结束日期";
		}
	if((document.getElementById("Sdate").value==document.getElementById("Edate").value) &&(document.getElementById("Stime").value>document.getElementById("Etime").value)){
		MsgInfo="不正确,起始时间大于结束时间";
		}
	
	if(MsgInfo==""){
		var NumberTemp=document.getElementById("Number").value;
		var TypeTemp=document.getElementById("Type").value;
		var SdateTemp=document.getElementById("Sdate").value;
		var StimeTemp=document.getElementById("Stime").value;
		var EdateTemp=document.getElementById("Edate").value;
		var EtimeTemp=document.getElementById("Etime").value;
		var qjTemp=NumberTemp+","+TypeTemp+","+SdateTemp+" "+StimeTemp+","+EdateTemp+" "+EtimeTemp;
		//收集数据并交由后台处理
		//如果处理成功，10秒后自动返回默认状态
		//如果处理失败，提示关返回输入界面
		var url_1="kqfun/kq_sorq_ajax1.php?qjStr="+qjTemp;
		var ajax1=InitAjax();
		ajax1.open("POST",url_1,true);
		ajax1.onreadystatechange =function(){
			if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){
				var RebackInfo=ajax1.responseText;
				document.getElementById("ReBack").innerHTML=RebackInfo;
					//document.getElementById("ReBack").style.display="";
				playSound("../media/Speech On.wav");
				if(iTimer)clearTimeout(iTimer);
					iTimer=setTimeout("ReBackDefault()",5000);//5秒后自动恢复
				if(iType)clearTimeout(iType);
					iType=setTimeout("ReBackType()",5000);//5秒后自动恢复
				}
			}
		ajax1.send(null);
		}
	else{
		alert(MsgInfo);
		}
	}

//查询动作
function ToSearch(Actions){//查询分类，员工工号，年份，月份
	var sTypeTemp=document.getElementById("sType").value;	//查询分类
	var NumberTemp=document.getElementById("Number").value;	//员工工号
	var sYearTemp="";
	if(document.all("sYear")!=null){
		sYearTemp=document.getElementById("sYear").value;//年份
		}
	var sMonthTemp="";
	if(document.all("sMonth")!=null){
		sMonthTemp=document.getElementById("sMonth").value;//年份
		}
	var searchTemp=Actions+","+sTypeTemp+","+NumberTemp+","+sYearTemp+","+sMonthTemp;
	var url_1="kqfun/kq_sorq_ajax_2.php?searchStr="+searchTemp;
		var ajax1=InitAjax();
		ajax1.open("POST",url_1,true);
		ajax1.onreadystatechange =function(){
			if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){
				var RebackInfo=ajax1.responseText;
				var RebackArray=RebackInfo.split("@");
				document.getElementById("ResultsTable").innerHTML=RebackArray[0];
				//如果是查询考勤,则返回月份
				switch(Actions){
					case 1://改变年份
						//如果是考勤，则需改变月份
						if(sTypeTemp==1){
							document.getElementById("ResultsMonth").innerHTML=RebackArray[2];
							}
					break;
					case 3://改变查询项目
						if(sTypeTemp<4){//非年假查询，返回年份选框
							document.getElementById("ResultsYear").innerHTML=RebackArray[1];
							if(sTypeTemp==1){
								document.getElementById("ResultsMonth").innerHTML=RebackArray[2];
								}
							else{
								document.getElementById("ResultsMonth").innerHTML="";
								}
							}
						else{	//年假查询，需清除年份和月份
							document.getElementById("ResultsYear").innerHTML="";
							document.getElementById("ResultsMonth").innerHTML="";
							}
					break;
					}
				}
			}
		ajax1.send(null);
	}
function QJSH(RowTemp,IdTemp){
	var url_1="kqfun/qjsh_ajax.php?Id="+IdTemp;
	var ajax1=InitAjax();
	ajax1.open("POST",url_1,true);
	ajax1.onreadystatechange =function(){
		if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){
			var RebackInfo=ajax1.responseText;
			if(RebackInfo!=""){
				eval("RecordList").rows[RowTemp].cells[9].innerHTML=RebackInfo;
				}
			}
		}
	ajax1.send(null);	
	}