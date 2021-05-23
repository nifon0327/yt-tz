<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv=refresh content="1800">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<TITLE>公司摄像头监控</TITLE>
</head>
<link rel='stylesheet' href='../model/tl/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>

<style type="text/css">
<!--
 @font-face { 
        font-family:AshCloud8; src:url('font/AshCloud8.ttf');
  }

.style1 {
	font-size: 120px;
	color: #0000FF;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
.style2 {
	font-size: 50px;
	color: #0000FF;
	font-family: "黑体";
}

.week div{
   display:inline-block;
   margin-left:2px; 
   width: 25px;height: 40px;
   line-height: 40px;
   vertical-align: middle;
   -webkit-transform: skew(-3deg);  
   -moz-transform: skew(-3deg); 
   -o-transform: skew(-3deg);
   
	font-family: "AshCloud8";
	font-size: 26pt;
	text-align: center;
	color: #000000;
	background-color: #FFFFFF;
}

.week{
	font-size: 22pt;
	color: #FFFFFF;
	line-height: 32px; 
}

.week span{
	font-size: 26pt;
	margin:22px 0 0 16px;
	width:200px;
	height: 30px;
	line-height: 30px; 
	text-align: center;
	vertical-align: bottom;
	color: #FFFFFF;
}

-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<BODY style="background:#000000;scroll:no; overflow-y: hidden;overflow-x: hidden;">
<?php   
    include "../basic/parameter.inc";			//数据库连接
    $f=48;$Cles=1;$IP="127.0.0.1";$Width=1080;
    
    $tops =$NewSign ==1?0:20; 
    switch($Floor){
	   case "4A": $Id="41,50,63,62";  break;
	   case "4B": $Id="42,49,51,61";  break;
	   case "3A": $Id="50,63,59,62";  break;
	   case "3B": $Id="51,49,60,61";  break;
	   case "1A": $Id="63,41,59,62";  break;
	   case "1B": $Id="49,42,60,61";  break;
	   case "2A": $Id="41,50,59,62";  break;
	   case "2B": $Id="42,51,60,61";  break;
	   //default: $Id="41,50";  break;
   }
   
   /*
     $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS curWeek",$link_id));
      $curWeek=$dateResult["curWeek"];
      $Week1=substr($curWeek, 4,1);
	  $Week2=substr($curWeek,5,1);
   
   $weekarray=array("日","一","二","三","四","五","六");
   $weekName="星期".$weekarray[date("w")];
   */

?>

<table border="0" align="center"  cellspacing="5" style='margin-top:<?php echo $tops; ?>px;'>
<?php
  $ids=explode(",", $Id);
  if (count($ids)==4){
	  $i=0;
	  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Id IN ($Id) ORDER BY C.Order,C.Id",$link_id);
	   if($checkCamRow=mysql_fetch_array($checkCamSql)){
	      //echo"<tr>";
	      //echo"<tr ><td  class='week'><div>$Week1</div><div>$Week2</div>&nbsp;<span id='clock'></span></td></tr>";
	      //echo"<tr ><td  class='week'><span id='clock'></span>&nbsp;&nbsp;&nbsp;&nbsp;$weekName</td></tr>";
	      echo"<tr ><td  class='week'><span id='clock'></span></td></tr>";
		do{ 
	          $Port=$checkCamRow["Port"];
	          $IP=$checkCamRow["IP"];
	          echo"<tr>";
	          /*
			  if($i%2==0 && $i>0){
					echo"</tr>";
					echo"<tr ><td  class='week'><div>$Week1</div><div>$Week2</div>&nbsp;<span id='clock'></span></td></tr>";
					echo"<tr>";
				}
			   */
				
				$Params=$checkCamRow["Params"];
				if (stripos($Params, "subcam/")===false){
					$src="http://$IP:$Port/$Params";
				}
				else{
					$src=$Params;
				}
				
				//$class=$Floor=="2B"?" style='margin-left:-320px;width:640px;' ":"";
				//echo"<td class='A1111' style='width:320px;height:470px; overflow:hidden;'><iframe frameborder=0 width=320 height=470 marginheight=0 marginwidth=0 scrolling=no src='$src' $class></iframe></td>"; 
				switch($checkCamRow["Id"]){
					case 61:
					  $class=" style='margin-top:-50px;height:340px;'";
					  break;
					case 50:
					  $class=" style='margin-top:-110px;height:400px;'";
					  break;
					default:
					  $class=" style='margin-top:-80px;height:370px;'";
					  break;
				}
				
				echo"<td class='A1111' style='width:640px;height:290px;overflow:hidden;'><iframe frameborder=0 width=640 height=290 marginheight=0 marginwidth=0 scrolling=no src='$src' $class></iframe></td>"; 
				echo "</tr>";
			$i++;
			
			}while($checkCamRow=mysql_fetch_array($checkCamSql));
			//echo "</tr>";
		}

  }
  else{
      //两行显示
	  $i=1;
	  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Id IN ($Id) ORDER BY C.Order,C.Id",$link_id);
	   if($checkCamRow=mysql_fetch_array($checkCamSql)){
	
		do{ 
	          $Port=$checkCamRow["Port"];
	          $IP=$checkCamRow["IP"];
	          
			  if($i>1){
			      echo"<tr ><td  class='week'><span id='clock'></span></td></tr>";
			    // echo"<tr ><td  class='week'><span id='clock'></span>&nbsp;&nbsp;&nbsp;&nbsp;$weekName</td></tr>";
				//echo"<tr ><td  class='week'><div>$Week1</div><div>$Week2</div>&nbsp;<span id='clock'></span></td></tr>";
				}
				echo"<tr>";
				$Params=$checkCamRow["Params"];
				if (stripos($Params, "subcam/")===false){
					$src="http://$IP:$Port/$Params";
				}
				else{
					$src=$Params;
				}
				
				 echo"<td class='A1111' style='width:640px;height:470px'><iframe frameborder=0 width=640 height=470 marginheight=0 marginwidth=0 scrolling=no src='$src'></iframe></td>"; 
			$i++;
			echo "</tr>";
			}while($checkCamRow=mysql_fetch_array($checkCamSql));
		}
  }
?>
</table>
</BODY>
</HTML>

<script>
    //window.setTimeout("tick()", 1000);
    window.onload=function(){
	     setInterval("tick()", 1000); 
    }
    
    function tick(){
	    document.getElementById("clock").innerHTML=getCurentTime();
    }
    
	function getCurentTime(){ 
        var now = new Date();
       
        var year = now.getFullYear();       //年
        var month = now.getMonth() + 1;     //月
        var day = now.getDate();            //日
       
        var hh = now.getHours();            //时
        var mm = now.getMinutes();          //分
        var ss = now.getSeconds();          //分
        
        var weeks= new Array("日","一","二","三","四","五","六"); 
        var weekName="星期"+weeks[now.getDay()];
        //var clock = year + "年";
       var clock="";
        if(month < 10) clock += "0";
        
        clock+=month + "/";
        if(day < 10)  clock += "0";
           
        clock += day+ " "+weekName+" ";
       
        if(hh < 10)
            clock += "0";
           
        clock += hh + ":";
        if (mm < 10) clock += '0'; 
        clock += mm;

        return(clock); 
    } 
</script>
