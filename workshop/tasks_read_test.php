<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='font/AshCloud8.ttf' rel='stylesheet'>
<title>tasks</title>
-->
<?php
      $Line=$Line==""?"C":$Line;
      include "../basic/parameter.inc";
?>
<div id='headdiv'>
	 <div id='linediv'><?php echo $Line; ?></div>
	 <img id='leader'  src='http://10.0.10.1/download/staffPhoto/P11204.png'/>  
	 <div  id='leader_name'>胡荣飞</div>
	 <ul id='group'>
	      <li><img src='image/group_staff.png'/>133人</li>
	      <li><img src='image/working_staff.png'/>17人</li>
	      <li><img src='image/leave_staff.png'/>1人</li>
	 </ul>
	 <ul id='quantity'>
	      <li><span>16,000 </span><div></div></li>
	      <li>50,000</li>
	 </ul>
	 <ul id='count'>
	      <li>欠尾数 <div></div><span><br>334(2) </span></li>
	      <li>生产中 <div></div><span><br><span id='register'>2,000</span>/10,000</span></li>
	      <li>已分配 <span><br>12,180(11)</span></li>
	 </ul>
</div>
<div>
	<table>
		<tr>
		    <td rowspan='2' width='80' class='week bgcolor_black'><div>4</div><div>5</div></td>
		    <td colspan='3' width='750' class='title'><span>CL-B-</span>iP6超薄PP(透明)</td>
		    <td width='250' class='td_line'><div class='line'>A</div></td>
	   </tr>
	   <tr>
		    <td>18704</td>
		    <td class='qty'><img src='image/order.png'/>10,000</td>
		    <td class='qty'><img src='image/register.png'/>9,840</td>
		    <td class='time'>1小时前</td>
	   </tr>
	   <tr>
		    <td  class='remark_icon'><img src='image/remark.png'/></td>
		    <td colspan='4' class='remark'>欠60个主产品</td>
	   </tr>
	</table>
	<table>
		<tr>
		    <td rowspan='2' width='80' class='week bgcolor_red'><div>4</div><div>2</div></td>
		    <td colspan='3' width='750' class='title'><span>VOG-</span>iP6侧翻A(白)</td>
		    <td width='250' class='td_line'><div class='line'>D</div></td>
	   </tr>
	   <tr>
		    <td>2484</td>
		    <td class='qty'><img src='image/order.png'/>&nbsp;500</td>
		    <td class='qty'><img src='image/register.png'/>&nbsp;450</td>
		    <td class='time'>1小时前<div>备</div></td>
	   </tr>
	   <tr>
		    <td  class='remark_icon'><img src='image/remark.png'/></td>
		    <td colspan='4' class='remark'>欠50个主产品</td>
	   </tr>
	</table>
</div>

<!--
</body>
</html>
-->