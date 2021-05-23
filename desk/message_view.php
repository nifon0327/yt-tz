<?php
//电信-zxq 2012-08-01
$Model=$Model==""?"5":$Model;
//blue 1	green 2	orange 3	red 4	white	5	yellow6
$modelcolor="modelcolor".$Model.".css";
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src='../model/pagefun.js' type=text/javascript></script>
<title></title>
<link rel='stylesheet' href='../message/<?php    echo $modelcolor?>'>
<style type="text/css">
<!--
html { overflow-x: hidden; overflow-y: hidden; }
A:link {
	COLOR: #FFFF00; TEXT-DECORATION: none}
A:active {
	COLOR: #FFFF00; TEXT-DECORATION: none}
A:visited {
	COLOR: #FFFF00; TEXT-DECORATION: none}
A:hover {
	COLOR: #FFFF00}
.mytrans { filter:revealTrans(Duration=2,Transition=11)}

#msg3{
  position:absolute;
  filter:revealTrans(Duration=2,Transition=19);
  visibility:hide;
  }
.NowTime{
	color: #FFFF00;
	border: 2px solid #FFFF00;
	font-family: "黑体";
	font-weight: bold;
	font-size: 28px;
	}
.msg4{
	color: #FFFF00;
	border: 2px solid #FFFF00;
	font-family: "黑体";
	font-weight: bold;
	font-size: 28px;
	}
.TQ{color: #FFFF00;	font-family: ;font-weight: bold;font-size: 18px;}
-->
</style>
<script language="javascript" type="text/javascript">
var  clocktext;
function  scroll()  {
	today  =  new  Date();
	yy  =  today.getYear();
    mm  =  today.getMonth()+1;
	dd  =  today.getDate();
	sec  =  today.getSeconds();
	hr  =  today.getHours();
	min  =  today.getMinutes();
	if  (hr  <=  9)  hr  =  "0"  +  hr;
	if  (min  <=  9)  min  =  "0"  +  min;
	if  (sec  <=  9)  sec  =  "0"  +  sec;
	var  clocktext  =yy+"-"+mm+"-"+dd+"&nbsp;"+hr+":"+min+":"+sec;
	clocktimer  =  setTimeout("scroll()",  1000);
	NowTime.innerHTML=clocktext;
	}
var layerHeight = 440; // 定义滚动区域的高度.qqqqqqqqq
var iFrame = 1; // 定义每帧移动的象素.
var iFrequency = 50; // 定义帧频率.
var timer; // 定义时间句柄.

function toReadMsg(){
	var url_1="message/msg_ajax1.php";
	var show1=document.getElementById("msg1");
　	var ajax1=InitAjax();
　	ajax1.open("GET",url_1,true);
	var oldMsg1=show1.innerHTML;
	ajax1.onreadystatechange =function(){
	　　if(ajax1.readyState==4 && ajax1.status ==200 && ajax1.responseText!="" ){//&& ajax1.responseText!=oldMsg1
	　　　	show1.innerHTML=ajax1.responseText;
			moveDiv();
			}
		}
	ajax1.send(null);

	var url_2="message/msg_ajax2.php";
	var show2=document.getElementById("msg2");
	var ajax2=InitAjax();
　	ajax2.open("GET",url_2,true);
	var oldMsg2=show2.innerHTML;
	ajax2.onreadystatechange =function(){
	　　if(ajax2.readyState==4 && ajax2.status ==200 && ajax2.responseText!="" && ajax2.responseText!=oldMsg2){
	　　　	show2.innerHTML=ajax2.responseText;
			}
		}
　	ajax2.send(null);

	//人事公告
	var IdNums=document.form1.IdNums.value;
	var url_3="message/msg_ajax3.php?IdNums="+IdNums;
	var show3=document.getElementById("msg3");
	var ajax3=InitAjax();
　	ajax3.open("GET",url_3,true);
	var oldMsg3=show3.innerHTML;
	ajax3.onreadystatechange =function(){
	　　if(ajax3.readyState==4 && ajax3.status ==200 && ajax3.responseText!="" ){//&& ajax3.responseText!=oldMsg3
	　　　	var msg3Date=ajax3.responseText;
			var msg3Array=msg3Date.split("`");
			if(msg3Array[0]!=IdNums){
				document.form1.IdNums.value=msg3Array[0];
				show3.style.visibility="hidden";

				show3.innerHTML=msg3Array[1];
				show3.filters.revealTrans.apply();
				show3.style.visibility="visible";
				show3.filters.revealTrans.play();
				}
			}
		}
　	ajax3.send(null);

	//日期检查,如果日期有变，则刷新页面
	var DefaultDay=document.form1.DefaultDay.value;
	var today = new Date();
	var Day = today.getDate();
	var Hrs = today.getHours();
	if(DefaultDay!=Day && Hrs>7){//每天8点之后更新一次
		window.location.reload(true);
		}
	setTimeout( "toReadMsg()",30000);//10分钟重新读取数据
	}
function window_onDBclick(){
	window.close();
	}
/*
function window_onDBclick(){
	var NewModel=Number(document.form1.Model.value)+1;
	if(NewModel>6){
		NewModel=1;
		}
	document.form1.Model.value=NewModel;
	document.form1.submit();
	}*/
</script>
</head>
<body runat="server" language=javascript ondblclick="return window_onDBclick()">
<form name="form1" method="post" action="">
<input name="IdNums" type="hidden" id="IdNums" value="0">
<input name="Move" type="hidden" id="Move" value="0">
<input name="Model" type="hidden" id="Model" value="<?php    echo $Model?>">
<input name="DefaultDay" type="hidden" id="DefaultDay" value="<?php    echo date("d")?>">
<table width="100%" border="0" cellspacing="5" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr>
    <td width="70%" height="481" rowspan="3" valign="top" class="A1111"><div class="Title">公告</div>
	<div id="layer1" style="overflow-y:hidden;"><div id="msg1" height="440"></div><div id="layer3"></div></div>
	</td>
    <td width="30%" class="NowTime" align="center"><div id="NowTime"></div></td>
  </tr>
  <tr>
    <td height="340" valign="top"  class="A1111">
      <div class="Title" align="center">今日加班通知</div>
      <div id="msg2"></div></td>
  </tr>
  <tr>
    <td class="msg4"><div id="msg4"></div></div></td>
  </tr>
</table>
<table width="99%" border="0" cellspacing="0" align="center" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr>
    <td width="10%" height="180" class="A1110"><div class="Title">人事<br>通知</div></td>
	<td width="90%" valign="middle" class="A1101"><div id="msg3" style="position:absolute;top:510px;">&nbsp;</div>&nbsp;</td>
  </tr>
</table>
<div id="msg4">
<div id="tq">
<?php
$url="http://tianqi.2345.com/d/city/59493.htm";
$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
$content=iconv("GB2312","UTF-8",$str);								//将收到的内容再转回UTF-8
$start="相关地区：";
$end="尊敬的";
$content = strstr( $content, $start );
$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start ));
$content= str_replace("\"","'",$content);

$content=str_replace("&&","&",str_replace("' />","'&",str_replace("><img",">&", str_replace("<td width='14%'>","*",$content))));
$content=strip_tags($content);
//以*号分割

$tqTable="<table width='100%' border='0' cellspacing='0'><tr>";
$theContent= explode("*",$content);
$lenTemp=count($theContent);
for($i=1;$i<4;$i++){
	$tqTemp=explode("&",$theContent[$i]);
	$lendayTemp=count($tqTemp);
	if($lendayTemp<4){//长度为3
		$tqTable.="<td width='8%' align='center'><img $tqTemp[1]></td><td align='left' class='TQ'>$tqTemp[0]<br>$tqTemp[2]</td>";
		}
	else{//长度为4
		$tqTable.="<td width='8%' align='center'><img $tqTemp[1]><img $tqTemp[2]></td><td align='left' class='TQ'>$tqTemp[0]<br>$tqTemp[3]</td>";
		}
	}
$tqTable.="</tr></table>";
echo $tqTable;

/*
$url="http://www.koubei.com/city/weatherinfo.html?name=上海";
$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
$content=iconv("GB2312","UTF-8",$str);								//将收到的内容再转回UTF-8
$start="<ul id=\"ThreeDays\" class=\"FLli\">";
$end="</ul>";
$content = strstr( $str, $start );
$content = substr( $content, strlen( $start ), strpos( $content, $end ) - strlen( $start ));
$tq= explode("</li>",str_replace("<li>","",$content));
//echo $content;
$tqTable="<table width='100%' border='0' cellspacing='0'><tr>";
$ToDay=date("Y-m-d");
$WeekArray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
for($i=0;$i<3;$i++){
	$theContent=$tq[$i];
	$start="<img";
	$end="alt";
	$Img = strstr( $theContent, $start );
	$Img = substr( $Img, strlen( $start ), strpos( $Img, $end ) - strlen( $start ) );
	$theContent =iconv("GB2312","UTF-8",chop(strip_tags(str_replace("</td>","@</td>",$theContent)))); //防止乱码，再转UTF-8
	$theContent= explode("@",$theContent);
	$Week="星期".$WeekArray[date("w", strtotime("$i day",strtotime($ToDay)))];
	$tqTable.="<td width='8%' align='center'><img $Img></td><td align='left' class='TQ'>$theContent[0] $Week<br>$theContent[2] $theContent[3] $theContent[4]</td>";
	}
$tqTable.="</tr></table>";
echo $tqTable;
*/
?>
</div>
</div>
</form>
</body>
</html>
<script language="javascript" type="text/javascript">

toReadMsg();
if  (document.all) scroll();
function move(){
	if(document.getElementById("layer1").scrollTop >= document.getElementById("msg1").offsetHeight){
		document.getElementById("layer1").scrollTop -= (document.getElementById("msg1").offsetHeight - iFrame);
       	}
	else{
		document.getElementById("layer1").scrollTop += iFrame;
       }
    }

function moveDiv(){
	if(document.getElementById("msg1").offsetHeight >= layerHeight)
		document.getElementById("layer1").style.height = layerHeight;
	else
		document.getElementById("layer1").style.height = document.getElementById("msg1").offsetHeight;
		document.getElementById("layer3").innerHTML = document.getElementById("msg1").innerHTML;
	if(document.form1.Move.value==0){
		timer = setInterval("move()",iFrequency);
		document.form1.Move.value=1;
		}
	}
	var url_4="message/news_ajax4.php";
	var show4=document.getElementById("msg4");
　	var ajax4=InitAjax();
　	ajax4.open("GET",url_4,true);
	var oldMsg4=show4.innerHTML;
	ajax4.onreadystatechange =function(){
	　　if(ajax4.readyState==4 && ajax4.status ==200 && ajax4.responseText!="" && ajax4.responseText!=oldMsg4){//
	　　　	show4.innerHTML=ajax4.responseText;
			}
		}
	ajax4.send(null);
</script>
