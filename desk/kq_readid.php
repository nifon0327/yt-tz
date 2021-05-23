<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../kqfun/kq_readid_tq.php";
include "../kqfun/kq_readid_info.php";
include "../model/kq_YearHolday.php";  //add by zx 2010-12-27 年假时间，已休天数的函数  kq_sorq_ajax1.php
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="0">
<title>研砼考勤窗口</title>
<link rel="stylesheet" href="kqfun/kq_readid.css">
</head>
<script src='model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='DatePicker/WdatePicker.js'></script>
<script src='model/pagefun.js' type=text/javascript></script>
<script src='webcam/webcam.js' type=text/javascript></script>
<script type="text/javascript" language="javascript">
<!--
var iTimer;
var iType;
Date.prototype.toCommonCase=function(){
    var xYear=this.getYear();
    var xMonth=this.getMonth()+1;
    if(xMonth<10){
        xMonth="0"+xMonth;
		}
    var xDay=this.getDate();
    if(xDay<10){
        xDay="0"+xDay;
		}
    var xHours=this.getHours();
    if(xHours<10){
        xHours="0"+xHours;
		}
    var xMinutes=this.getMinutes();
    if(xMinutes<10){
        xMinutes="0"+xMinutes;
		}
    var xSeconds=this.getSeconds();
    if(xSeconds<10){
        xSeconds="0"+xSeconds;
		}
    return xYear+"-"+xMonth+"-"+xDay+" "+xHours+":"+xMinutes+":"+xSeconds;
	}
//日期时间
//var DateStr=setInterval("rq.innerHTML=new Date().toLocaleString()+'  &nbsp;星期'+'日一二三四五六'.charAt(new Date().getDay());mainInfo.innerHTML=new Date().toLocaleString().split(' ')[1];",1000);
-->
</script>
<body style="overflow-x:hidden;overflow-y:hidden" onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;" oncut = "return false" oncopy = "return false" onpaste = "return false" onselectstart="return false">
<bgsound id="snd" loop="0" src="">
<div id="ReBack" style="position:absolute; left:37px; top:139px; width:1073px; height:485px; z-index:1; background-color: #FFFFFF; layer-background-color: #FFFFFF; border: 1px none #000000;display:none;"></div>
<script language="JavaScript">
function playSound(src){
	var _s = document.getElementById('snd');
	if(src!='' && typeof src!=undefined){
		_s.src = src;
		}
	}
</script>
<input name="TempId" type="hidden" id="TempId"><input name="CheckType" type="hidden" id="CheckType" />
<div id="container0">
	<div id="container1">
		<div id="content">

			<div id="jb">
				<div id="jbl" onDblClick="javascript:window.close();">加班通知</div>
				<div id="jbr">
					<table width="1023"><tr><td class="tdInfo"><?php    echo $JbMsg?></td></tr></table>
				</div>
			</div>
			<!--
			<div id="rq"></div>

			<div id="main">
				<div id="mainInfo"></div>
			</div>
            -->
            <div id="wnmain" style='background:#FFFFFF;border: 1px solid #666666;'>
                <iframe src="../wnlDate/wn.php" id="wnDateFrm" name="wnDateFrm"  frameborder="0" width="100%" height="552" margwidth="0" margheight="0" scrolling="no" allowTransparency="true" style="margin-left:32px;"></iframe>
              </div>
			<div id="tq">
				<table width="100%">
					<tr>
					  <td width="8%" onClick="ToActionS(1)" id="bgcolor_I"  align="center" class="funSize">签到</td>
						<td width="8%" onClick="ToActionS(0)" id="bgcolor_O"  align="center" class="funSize">签退</td>
						<td width="8%" onClick="ToActionS(2)" id="bgcolor_S"  align="center" class="funSize">查询</td>
						<td width="8%" onClick="ToActionS(3)" id="bgcolor_Q"  align="center" class="funSize">请假</td>
						<td width="8%" onClick="javascript:window.location.reload();"  align="center" class="funSize">刷新</td>
						<td width="20%" align="right"><?php    echo $tqImg;?></td>
						<td align="left" class="tqSize"><?php    echo $tqTable;?></td>
					</tr>
				</table>
			</div>

			<div id="rs">
				<div id="rsl">人事通知</div>
				<div id="rsr">
				<table width="1023">
					<tr><td class="tdInfo"><?php    echo $RsMsg?></td></tr>
				</table>
				</div>
			</div>
			<?php
			/*
			<div id="Sign">
				<div id="SignA">请假</div>
				<div id="SignC">迟到</div>
				<div id="SignB">未签卡</div>
			</div>
			*/
			?>
		</div>
		<div id="TodayLog">
		<div class="content" style="width:200;height:560;overflow-x:hidden;overflow-y:scroll;float:right;">
			<table width="100%" id="ListTable" bgcolor="#FFFFFF"  style="font-size:12px;">
				<tr><td>序号</td><td>姓名</td><td>签卡时间</td></tr>
				<?php    echo $ListTable;?>
			</table>
		</div>
          <div class="content" style="width:200;height:155;overflow:hidden;;float:right;margin-top:5px;">
          <!--	<script language="JavaScript">
	 	         webcam.set_api_url('webcam/savecam.php' );
				 webcam.set_swf_url('webcam/webcam.swf' );
		         webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		         webcam.set_shutter_sound(true,'webcam/shutter.mp3');// play shutter click sound

		         document.write( webcam.get_html(180,135,640,480) );
				 webcam.set_hook( 'onComplete', 'cam_reset' );
                 function cam_reset(msg) {webcam.reset();}
	        </script>-->
         <!--   <iframe src="../webcam/webcam.html" id="webcamFrm" name="webcamFrm"  frameborder="0" width="100%" height="100%" margwidth="0" margheight="0" scrolling="no" allowTransparency="true"></iframe>-->
		  </div>
	  </div>
	</div>
</div>
<div class="hiddenDiv"></div>
</body>
</html>
<script src='kqfun/kq_readid.js' type=text/javascript></script>
<!-- 以下是javascript代码 -->
<script   language="javascript"   event="onkeydown"   for="document">
	if(event.keyCode==13){
		var TempIN=document.all('TempId').value;
		var CheckType=document.all('CheckType').value;
		//读取数据
		if(CheckType=="I" || CheckType=="O"){
		var InTime=encodeURIComponent(new Date().toCommonCase());
		var url_1="../kqfun/kq_sign_ajax.php?IdNum="+TempIN+"&CheckType="+CheckType+"&InTime="+InTime;
		var ajax1=InitAjax();
		ajax1.open("POST",url_1,true);
		ajax1.onreadystatechange =function(){
			if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){//&& ajax1.responseText!=oldMsg1
				var RebackInfo=ajax1.responseText;
				var msg3Array=RebackInfo.split("|");
				document.getElementById("ReBack").innerHTML=msg3Array[0];
				document.getElementById("ReBack").style.display="";
				var Rowslength=msg3Array.length;
				playSound("../media/Speech On.wav");
				if(Rowslength>1){//输出最后签卡记录
				    var FieldArray=msg3Array[1].split("~");
					//var apiurl='webcam/savecam.php?Id='+FieldArray[2];
				    //webcam.set_api_url(apiurl);
					//webcam.snap();//拍照
					//输出至表格
					oTR=ListTable.insertRow(1);
					tmpNum=ListTable.rows.length-1;
					//第一列:序号
					oTD=oTR.insertCell(0);
					oTD.innerHTML=""+tmpNum+"";
					oTD.align="center";

					//二、姓名
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+FieldArray[0]+"";

					//二、姓名
					oTD=oTR.insertCell(2);
					oTD.innerHTML=""+FieldArray[1]+"";

					}
				if(iTimer)clearTimeout(iTimer);
					iTimer=setTimeout("ReBackDefault()",5000);//5秒后自动恢复
				if(iType)clearTimeout(iType);
					iType=setTimeout("ReBackType()",5000);//5秒后自动恢复
				}
			}
		ajax1.send(null);
		document.all('TempId').value="";
		}
		else{//查询或请假
			///////////////////////////////////////
			var url_1="../kqfun/kq_sorq_ajax.php?IdNum="+TempIN+"&CheckType="+CheckType;
			var ajax1=InitAjax();
			ajax1.open("POST",url_1,true);
			ajax1.onreadystatechange =function(){
				if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){//&& ajax1.responseText!=oldMsg1
					var RebackInfo=ajax1.responseText;
					var msg3Array=RebackInfo.split("|");
					document.getElementById("ReBack").innerHTML=msg3Array[0];
					document.getElementById("ReBack").style.display="";
					var Rowslength=msg3Array.length;
					playSound("../media/Speech On.wav");
					}
				}
			ajax1.send(null);
			document.all('TempId').value="";
			///////////////////////////////////////
			}
		}
	else{//非回车则字符连接 13回车，16Shift_L,48-57为数字0-9
		if(event.keyCode>=48 && event.keyCode<=57){
			var TempIN=document.all('TempId').value;
			document.all('TempId').value=TempIN+Number(event.keyCode-48);
			}
		}

</script>