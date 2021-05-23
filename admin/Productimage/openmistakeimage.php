<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../Productimage/style.css" rel="stylesheet" type="text/css" />
<title>研砼-Mistake案例图片</title>
<?php
  //电信-ZX  2012-08-01
   include "../../basic/chksession.php";
   include "../../basic/parameter.inc";

   include "js/popmenu.inc";
 ?>
</head>
<?php

$RType=$_GET["RT"];
$SType=$_GET["ST"];
$RType=$RType==""?"1":$RType;
$SType=$SType==""?"1":$SType;
$MisId=$_GET["ID"];
//$dateList=newGetDateSTR();
$MisSql="SELECT * FROM $DataIn.errorcasedata WHERE  Id='$MisId'";
$MisResult = mysql_query($MisSql,$link_id);
if($MisRow = mysql_fetch_array($MisResult)){
   $Picture=$MisRow["Picture"];
   $Date=$MisRow["Date"];
   $Type=$MisRow["Type"];
   $Owner=$MisRow["Owner"];
   $Field=explode("/",$Owner);
   $Count=count($Field);
   $Name="";
   for($i=0;$i<$Count;$i++){

     if($Field[$i]!="陈冠义"){
	   $Name=$Name."&nbsp;".$Field[$i];}
   }
   $Operator=$MisRow["Operator"];
   include "../subprogram/staffname.php";
   $width=1220;
	$img="../../download/errorcase/".$Picture;
	if (file_exists($img)){
        $img_info = getimagesize($img);
	    $imgWidth=$img_info[0];
	    if ($imgWidth>1220) $width=$imgWidth+10;
	}else{$imgWidth=1220;}
	$footWth=$width;
	$width="style='width:$width" . "px;'";
	if($Date>"2011-08-24"){
	$examimg="<img src='images/examine.gif' height='50'/>";}
	else {$examimg="&nbsp;";}
}
?>
<body  oncontextmenu='return false' onhelp='return false;'>
<div id="head" <?php    echo $width?>>
   <div id="head_left">&nbsp;&nbsp;&nbsp;
       <img id='sel3'  style="" src="../Productimage/images/Mistake.gif"/>
   </div>

   <div id="head_right">
    <div id="cName">
        <span style="color:#999999">Produced by:<?php    echo $Operator?></span><BR/>
        <span style="color:#999999">&nbsp;Date:<?php    echo $Date?>&nbsp;</span>
     </div>
	 <div id="examine"><?php    echo $examimg?></div>
   </div>
</div>
  <div id="bombar" <?php    echo $width?>>
     <div class="bar_left">
       <div id="BomList" class="bar_left">
          <div style="float:left;width:150px;"><b>案例编号:<?php    echo $MisId?></b>
          </div>
      <div id="IdNumber" style="float:left;" class="bom_smallfont"></div>
      </div>

 </div>

 <div  id="bombar_right">
        <div class="print-set">
           <span id="Review"></span>
           <span><a href="javascript:window.print();"><img src="../Productimage/images/Print.gif"/></a></span>
        </div>
        <div style="float:right;"><b>检讨人:<?php    echo $Name?>&nbsp;&nbsp;</b></div>
   </div>
</div>
<br />
<br calss="clears"/>
    <div id="mainShow" class="show_img"  style="width:<?php    echo $imgWidth?>;">
	<?php
	   if (file_exists($img)){
	   echo "<img id='Img' src='$img'/><br/>";
	   }
	?>
       <div id="listImage1" class='imglist'></div>
    </div>

<br calss="clears" />
    <div id="footbar" <?php    echo $width?>>
      <div id="foot_right">
          <TABLE border=0 cellpadding=0 cellspacing=0 width="<?php    echo $footWth?>">
          <TR>
              <TD valign='top'><img src="../Productimage/images/foot_left.gif"/></TD>
              <TD><span>Ash Cloud Co.,Ltd. Shenzhen</span>&nbsp;</TD>
              <TD><img src="../Productimage/images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>TEL:+86-755-61139580</span>&nbsp;</TD>
              <TD><img src="../Productimage/images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>FAX:+86-755-61139585</span>&nbsp;</TD>
              <TD><img src="../Productimage/images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>ADD:5F,Chen Tian Weixinda Dasha,Bao-Ming 2Rd,XiXiang,Baoan,Shenzhen,China</span>&nbsp;</TD>
              <TD><img src="../Productimage/images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>518102</span></TD>
              <TD width='8' valign='top'><img src="../Productimage/images/foot_right.gif"/></TD>
         </TR>
         </TABLE>
    </div>
  </div>
</body>
</html>
