<?php 
//电信-zxq 2012-08-01
//include "../basic/chksession.php";
include "../basic/parameter.inc";	  								
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; utf-8" />
<title>公告栏</title>
</head>
<script language='javascript' type='text/javascript' src='mupage.js'></script>
<style type="text/css">
p{
text-align:center;
}
#todayFont{
    fONT-FAMILY:'思源黑体';
	FONT-SIZE:20pt;
	font-weight:bold;
	COLOR: #ff000;
	border-bottom:1px dashed #999;
}
#otherdayFont{
    fONT-FAMILY: '思源黑体';
	FONT-SIZE:20pt;
	font-weight:bold;
	COLOR: #0000ff;
	border-bottom:1px dashed #999;
}
#msgFont{
    fONT-FAMILY:'思源黑体';
	FONT-SIZE:12pt;
}
#endFont{
    fONT-FAMILY:'思源黑体';
	font-weight:bold;
	FONT-SIZE:12pt;
}
#kongFont{
    fONT-FAMILY:'思源黑体';
	FONT-SIZE:1pt;
}
textarea{
FONT-FAMILY: '思源黑体';
FONT-SIZE: 12pt; 
TEXT-ALIGN: LEFT;
border:0;
scrollbar-base-color: #eee;
scrollbar-arrow-color:#00f;
background:#E6E6E6;
overflow:auto;
line-height:22px;
overflow-x:hidden;
} 

#menu{
width:500px;
height:400px;
overflow:hidden;
background:lightblue;
}
.page{
position:absolute;
width:480px;
height:380px;
left:20px;
top:20px;
background:#EEE;
border:1px solid #999;
overflow:hidden;
}
.tip{
display:block;
height:20px;
margin:0px 20px;
line-height:20px;
text-align:center;
font-size:12px;
background:#999;
}
</style>
</head>
<body>
<div id="menu">
<?php 
	$Today=date("Y-m-d");
	$LastMonth=substr(date("Y-m-d",strtotime("$Today  -6   month") ),0,7);
	$mySql = "select Id,Title,Content,Date,Operator FROM $DataPublic.msg1_bulletin WHERE Date>='$LastMonth' order by Date desc";
	$myResult = mysql_query($mySql,$link_id);
	while ($myRow = mysql_fetch_array($myResult)){
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		//$Content=nl2br($myRow["Content"]);
		$Content=$myRow["Content"];		
		$Date=$myRow["Date"];
		//$Operator=$myRow["Operator"];
		//include "../admin/subprogram/staffname.php";
		if($Date==$Today){
			$theDefaultColor="todayFont";
			}
		else {
			$theDefaultColor="otherdayFont";
		}
		  echo "<div id='div' class='page'>
		     <span><font id='kongFont'>&nbsp;</span>
			 <p> <font id='$theDefaultColor'>$Title</span></font></p>
			 <p> <font id='endFont'>发布日期：$Date</font></p>
			  <p><textarea name='textMsg' rows='18' cols='54'>$Content</textarea></p>
			   </div>
		        ";
		   // echo "<font id='$theDefaultColor' align='middle'>$Title</font></br></br>
			//      <font id='msgFont'>$Content</font></br>
			//      <font id='endFont'>发布日期：$Date</font></br></br>
			//";		
	 }	
?>	
</div>
</body>
</html>