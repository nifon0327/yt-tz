<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>黑云-产品标准图例</title>
<?php   
include "../../basic/chksession.php";
include "../../basic/parameter.inc";
//$isImage_File="1";
include "../../basic/downloadFileIP.php";  //取得下载文档的IP
include "../../model/R_Function.php";  //取得下载文档的IP
include "js/popmenu.inc";
 ?>
</head>
<?php   
//电信-ZX  2012-08-01
$RType=$_GET["RT"];
$SType=$_GET["ST"];
$RType=$RType==""?"1":$RType;
$SType=$SType==""?"1":$SType;

switch($RType){
  case 1:
    $ProductId=$_GET["ID"];
    $BoxPcs="";
	break;
  case 2:
    $POrderId=$_GET["ID"];
	$orderSql="SELECT C.Forshort,S.ProductId,S.OrderPO,L.BoxPcs,S.Qty 
	     FROM $DataIn.yw1_ordersheet S
         LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		 LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
		 LEFT JOIN $DataIn.ch2_packinglist L ON L.POrderId=S.POrderId 
		 WHERE 1 AND S.POrderId='$POrderId'";
	$orderResult = mysql_query($orderSql,$link_id);
	if($orderRow = mysql_fetch_array($orderResult)){
	  $ProductId=$orderRow["ProductId"];
	  $Forshort="-". $orderRow["Forshort"];
	  $OrderPO=$orderRow["OrderPO"];
	  $BoxPcs=$orderRow["BoxPcs"];
	  $Qty=$orderRow["Qty"];
	}
	$StuffResult=mysql_query("SELECT G.StuffId,S.StuffCname From $DataIn.cg1_stocksheet G
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
	LEFT JOIN $DataIn.productdata D ON D.ProductId=Y.ProductId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
	WHERE G.POrderId='$POrderId' AND S.StuffCname LIKE '%日期%' AND C.CompanyId IN('1003','1057')");
	if($StuffRow=mysql_fetch_array($StuffResult)){
	  $TempStuffCname=$StuffRow["StuffCname"];
	  $TempStuffId=$StuffRow["StuffId"];
	  $Stuffimg="../../download/stufffile/".$TempStuffId."_s".".jpg";
	 }
	
    break;  
}

$ProdSql="SELECT P.cName,P.eCode,P.Code,P.TestStandard,P.Weight,U.Name AS Unit,TY.mainType,TY.TypeName,S.Date 
	     FROM $DataIn.productdata P 
		 LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
		 LEFT JOIN  $DataIn.productstandimg S ON S.ProductId=P.ProductId 
		 LEFT JOIN $DataIn.producttype TY ON TY.TypeId=P.TypeId 
		 WHERE 1 AND P.ProductId='$ProductId'";
$ProdResult = mysql_query($ProdSql,$link_id);
if($ProdRow = mysql_fetch_array($ProdResult)){
	$cName=$ProdRow["cName"];
	$eCode=$ProdRow["eCode"];
	$Unit=$ProdRow["Unit"];
	$passDate=$ProdRow["Date"];
	$TestStandard=$ProdRow["TestStandard"];
	if($TestStandard==1&& $passDate>="2011-08-03"){$examimg="<img src='images/examine.png' height='40'/>";}
	else{$examimg="";}
	$Weight=$ProdRow["Weight"];	
    $Weight=$Weight<=0?"—":$Weight;
	$mainType=$ProdRow["mainType"];
	$TypeName=$ProdRow["TypeName"];
	$BoxCode=$ProdRow["Code"];
	$width=1220;	
	$img="../../download/teststandard/" . "T".$ProductId.".jpg";
	if (file_exists($img)){
        $imgDownload="downloadfile.php?file_name=T" . $ProductId .".jpg&Type=1";
        $img_info = getimagesize($img); 
	    $imgWidth=$img_info[0];
	    if ($imgWidth>1220) $width=$imgWidth+10;
	}else{$imgWidth=1220;}
	$footWth=$width-130;
	$width="style='width:$width" . "px;'";
}

 $productId=$ProductId;
  include "../../model/subprogram/weightCalculate.php";
  if ($Weight>0){
       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
  }
$extraWeight=sprintf("%.1f", $extraWeight);
/*
$mistakeImg="";
$mistakeSql="select E.Id,E.Title,E.Picture,E.Owner,E.Operator,E.Date   
					FROM $DataIn.casetoproduct C 
					LEFT JOIN $DataIn.errorcasedata E ON E.Id=C.cId 
					WHERE C.ProductId='$ProductId' AND E.Estate=1";
$mistakeResult=mysql_query($mistakeSql,$link_id);
if($mistakeRow=mysql_fetch_array($mistakeResult)){
     // $Picture=$mistakeRow["Picture"];
	  $mistakeImg="<img src='images/examine.gif' height='52'/>";
     }
*/
?>
<body  oncontextmenu='return false' onhelp='return false;'>
<!--
<div id="head" <?php    echo $width?>>
   <div id="head_left">
       <a  href="#" onclick='selIndex(1)'><img id='sel1'  src="images/Specification.gif"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       
       <a  href="#" onclick='selIndex(2)'><img id='sel2' class='bom_img_nosel' src="images/Qcstandard.gif"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       <a  href="#" onclick='selIndex(3)'><img id='sel3' class='bom_img_nosel' src="images/Mistake.gif"/></a>
     
	</div>

   <div id="head_right">
    
    <div id="cName">
        <span><?php    echo $eCode?></span><BR/>
        <span style="background:#CCC">&nbsp;<?php    echo $cName?>&nbsp;</span>
     </div>
     <span id="Produced"></span>
	  <div id="examine"><?php    echo $examimg?></div>
	  <div id="mistakeImg"><?php    echo $mistakeImg?></div>
   </div>
  
</div> 
  -->
  <div id="bombar" <?php    echo $width?>>
     <div class="bar_left">
       <div id="BomList" class="bar_left">
          <div style="float:left;width:100px;">
			  <ul id="qm0" class="qmstyle qmmc">
          <li><a class="qmparent" href="javascript:void(0)"><span class='bom_img'><img src="images/Bom.gif"/></span></a>
          <ul>
    <?php   
  	//从配件表和配件关系表中提取配件数据	  
	$StuffResult = mysql_query("SELECT D.StuffId,D.StuffCname,D.Picture,D.TypeId,D.Spec,A.Relation,D.BoxPcs   
				FROM $DataIn.pands A,$DataIn.stuffdata D 
				WHERE A.ProductId='$ProductId' and D.StuffId=A.StuffId ORDER BY A.Id",$link_id);
	$k=1;
    if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
	      do{
			$StuffId=$StuffMyrow["StuffId"]; 
			$StuffCname=$StuffMyrow["StuffCname"];
			$Picture=$StuffMyrow["Picture"];
			$TypeId=$StuffMyrow["TypeId"];
			if ($TypeId=='9040'){
				$BoxSpec=$StuffMyrow["Spec"];
				$Relation=$StuffMyrow["Relation"];
			}
			if($StuffMyrow["BoxPcs"]>0){
				$BoxPcs = $StuffMyrow["BoxPcs"];
			}
			
			$Fisexists=0;
			include "pdf2jpg.php";
			if ($donwloadFileIP!="" ) {  //走远程
				$ImageName="$donwloadFileIP/download/stufffile/" . $StuffId ."_s.jpg";
				
				if ($Fisexists!=1 && check_remote_file_exists($ImageName)) {   //判断远程文件是否存在
					$Fisexists=1;
				}
				
			}
			else 
			{
				$ImageName="../../download/stufffile/" . $StuffId ."_s.jpg";	
				if (file_exists($ImageName)){
					$Fisexists=1;
				}
			}
			
			if ($Fisexists==1){
			   echo "<li><a href='javascript:void(0)'>$k - $StuffCname  <img src='images/image_s.gif'/></a> <ul><li><span class='qmtitle'><img src='$ImageName'/></span></li></ul></li>";
			}else{
				echo "<li><a href='javascript:void(0)'>$k - $StuffCname</a></li>";
			}
			$k++;
		  } while ($StuffMyrow = mysql_fetch_array($StuffResult));
	}
	
	
	if ($BoxPcs==""){
	    $arr_temp=explode("/",$Relation);
		$BoxPcs=$arr_temp[1];
	}
	
   ?>
      <li class="qmclear"></li></ul> </li>
      </ul></div>
      <div id="IdNumber" style="float:left;" class="bom_smallfont">
	  <b>ID:</b><?php    echo $ProductId?><?php    echo $Forshort?><input name="TempStuffId" id="TempStuffId" type="hidden" value="<?php    echo $TempStuffId?>"><input name="TempStuffCname" id="TempStuffCname" type="hidden" value="<?php    echo $TempStuffCname?>">
	  </div>
	  	<div  style="float:left;margin:0px 0px 0px 20px;width:350px;height:40px;text-align:center;font-size:15px;font-family:Arial, Helvetica, sans-serif;" >
        <div style='height:20px;line-height:20px;'><?php    echo $eCode?></div><div style='height:20px;line-height:20px;'><?php    echo $cName?></div>
     </div>

      </div>
     <div id="round">
         <div class="box_img"><img src="images/box_left.gif"/></div>
         <div class="box_img box_bg"><font id="roundNo"></font></div>
         <div class="box_img"><img src="images/box_right.gif"/></div>
         <input id='typeName' name='typeName' type='hidden' value="<?php    echo $TypeName?>" />
         <div id="caseNo" class="mList"></div>  
         <div id="QctNo"  class="mList"></div> 
     </div>
 </div>

 <div  id="bombar_right">
        <div class="print-set">
           <span id="Review"></span>
           <span id='downBtn'><a href="#" onclick="uploadImg()" id="upImg">&nbsp;&nbsp;<img src="images/download_a.gif"/></a></span>
           <span id='PirntBtn'><a href="javascript:window.print();">&nbsp;&nbsp;<img src="images/Print_a.gif" /></a></span>
        </div>
        <div style="float:right;">
           <table id="bombar_tab"><tr>
            <td><div style='margin:-10px 30px 0px 0px;'><?php    echo $examimg?></div></td>
            <td><img src="images/weight_a.gif"/></td>
             <td> <span class="bombar_spanbox_a">&nbsp;<?php    echo $Weight?></span>&nbsp; g</td>
             <td width='5px'><img src="images/ver_line_a.gif" /></td>
             <td width='40px'><ul id="qm1" class="qmstyle qmmc">
                <li><a class="qmparent" href="javascript:void(0)"><img src="images/boxpcs_a.gif"/></a>
                   <ul style="margin:0px 0px 0px -400px;">
                      <li> <span class="qmtitle" ><img src="createLabel.php?ID=<?php    echo $POrderId?>&RT=<?php    echo $RType?>&BCODE=<?php    echo $BoxCode?>&CODE=<?php    echo $cName .'|'.$eCode.'|'.$OrderPO.'|'.$Unit.'|'.$BoxSpec.'|'.$BoxPcs.'|'.$Qty ?>"/></span></li>
                     <li> <span class="qmtitle" ><img src='createBox.php?BoxSpec=<?php    echo $BoxSpec?>'/></span></li>
                     <li class="qmclear"></li>
                   </ul>
              </li> </ul></td>
              <td> <span class="bombar_spanbox_a box_top">&nbsp;<?php    echo $BoxPcs?></span>&nbsp; <?php    echo strtolower($Unit)?> </td>
              <td width='5px'><img src="images/ver_line_a.gif"/></td>
               <td><img src="images/box_a.gif"/></td>
               <td><span class="bombar_spanbox_a box_top">&nbsp;<?php    echo $extraWeight?></span>&nbsp; g</td>
               <td width='5px'><img src="images/ver_line_a.gif"/></td>
              </tr>
               </table>
        </div>
   </div>
</div>
<br />
<br calss="clears"/>
    <div id="mainShow" class="show_img"  style="width:<?php    echo $imgWidth?>;">
       <div id="listImage1" class='imglist'></div>
       <div id="listImage2" class='imglist'></div>
       <div id="listImage3" class='imglist'></div>
    </div>

<br calss="clears" />
    <div id="footbar" <?php    echo $width?>>
        <div id="foot_left">
    <?php    
          $mainTypeFile="images/Type_" . $mainType . ".png";
          if (!file_exists($mainTypeFile)){
              echo "<img src='createtypeimg.php?mainType=$mainType' style='width:105px'/>";
          }else{
             echo "<img src='$mainTypeFile' style='width:105px'/>"; 
          }
     ?>
        </div>
      <div id="foot_right">
          <TABLE border=0 cellpadding=0 cellspacing=0 width="<?php    echo $footWth?>">
          <TR>
              <TD valign='top'><img src="images/foot_left.gif"/></TD>
              <TD><span>Ash Cloud Co.,Ltd. Shenzhen</span>&nbsp;</TD>
              <TD><img src="images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>TEL:+86-755-61139580</span></TD>
              <TD><img src="images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>FAX:+86-755-61139585</span>&nbsp;</TD>
              <TD><img src="images/foot_line.gif"/>&nbsp;</TD>
              <TD><span>ADD:Building 48,Bao-Tian Industrial Zone,Qian-Jin 2Rd,XiXiang,Baoan,Shenzhen,China</span>&nbsp;</TD>
              <TD><img src="images/foot_line.gif"/>&nbsp;</TD> 
              <TD><span>518102</span></TD> 
              <TD width='8' valign='top'><img src="images/foot_right.gif"/></TD>
         </TR>
         </TABLE>
    </div>
  </div>
</body>
</html>
<script type="text/javascript">



  var readOne=new Array(3,0,0,0);
  var caseImage=new Array();
  var qcImage=new Array();
  
     qm_create(0,false,0,500,false,false,false,false,false);
	 qm_create(1,false,0,500,false,false,false,false,false);
	 selIndex(<?php    echo $SType?>);

function uploadImg(){

   var upImg=document.getElementById('upImg');
   if(document.getElementById('listImage1').style.visibility=="visible"){
         upImg.href="<?php    echo $imgDownload?>";
		 }
	else{	     
		 if(document.getElementById('listImage2').style.visibility=="visible"){
		      var url2="Image_read.php?PId=<?php    echo $ProductId?>"+"&mType=2&do="+Math.random();
			  var qcdata=getData(url2);
			  var qc_data=qcdata.split("||");
			      qcImage[0]=qc_data[1]*1;
				for (m=1;m<=1;m++){//m<qcImage[0];
				      var qcImg=document.getElementById('qcImg'+m);
					  if(qcImg.src!=""){
					     downloadFile(qcImg.src,2);
					    } 
				    }
		      }
		 else{
		      var url3="Image_read.php?PId=<?php    echo $ProductId?>"+"&mType=3&do="+Math.random();
		      var casedata=getData(url3);
			  var case_data=casedata.split("||");
			      caseImage[0]=case_data[1]*1;
				for (m=1;m<=1;m++){
				      var caseImg=document.getElementById('caseImg'+m);
					  if(caseImg.src!=""){
					     downloadFile(caseImg.src,3);
					    } 
				    }
		     }
	 }
}

function downloadFile(url,type){ 
    var upImg=document.getElementById('upImg'); 
    var fileArray=new Array();
    fileArray=url.split("/");
	file_name=fileArray[fileArray.length-1];
    upImg.href="downloadfile.php?file_name="+file_name+"&Type="+type;
	//alert(upImg.href);
}

function selIndex(index){
  var selobj,listObj,readObj,readFlag,url;
  
  readFlag=readOne[index];
  if (readFlag==0) readOne[index]=1; //只加载一次图片
     
  url="Image_read.php?PId=<?php    echo $ProductId?>"+"&mType="+index+"&do="+Math.random();
  
  for (i=1;i<=readOne[0];i++){
     selobj="sel"+i; listObj="listImage"+i;
	if (i==index) {
	   if (readFlag==0) {
		  var strData=getData(url);
		  var GDnumber="①①②③④⑤⑥⑦⑧⑨⑩";
		  var nullImg="<br/><br/><br/><div class='ImgNull'><img src='images/nofile.gif'/>要浏览的图档不存在，请检查并重新上传！</div>";
		  switch(i){
		   case 1:  
		        if (strData=='0'){
				   document.getElementById(listObj).innerHTML=nullImg;
				}else{
				   var arr_Data=strData.split("||");
		           document.getElementById(listObj).innerHTML=arr_Data[0];
			       if (arr_Data.length>2){
					 var str_temp= document.getElementById("IdNumber").innerHTML;
					 document.getElementById("IdNumber").innerHTML=str_temp+"&nbsp;&nbsp;By: "+arr_Data[1]+"&nbsp;&nbsp;Date:" + arr_Data[2];;
					 
				  }
				}
				
				 var TempStuffId=document.getElementById("TempStuffId").value;
				 var TempStuffCname=document.getElementById("TempStuffCname").value;
				 //alert(TempStuffCname);
				 if(TempStuffId!=""){
				  str_temp= document.getElementById("IdNumber").innerHTML+"&nbsp;&nbsp;<b>"+TempStuffCname;
				   var cutStr="";
				  //var cutStr="<a href='#' onclick='loadDataB()'><b>"+GDnumber.split('')[i]+ "</b></a>";
				  document.getElementById("IdNumber").innerHTML =str_temp+cutStr;
				     }
			 break;
			 
		  case 2:
			 if (strData=='0'){
				   document.getElementById(listObj).innerHTML=nullImg;
				    qcImage[0]=0;
			 }else{
			    var arr_Data=strData.split("||");
			  	var qctStr="&nbsp;&nbsp;";
				 qcImage[0]=arr_Data[1]*1;
				for (m=1;m<=qcImage[0];m++){
				   qcImage[m]=arr_Data[m+1];
				   qctStr=qctStr+"<a href='#' onclick='selQct(" + m + ")'><b>检验图"+GDnumber.split('')[m]+ "</b></a>&nbsp;&nbsp;";
				}
		       document.getElementById(listObj).innerHTML=arr_Data[0];
			   document.getElementById("QctNo").innerHTML=qctStr; 
			 }
			break;
			
		   case 3:
			   if (strData=='0'){
				   document.getElementById(listObj).innerHTML=nullImg;
				   caseImage[0]=0;
			   }
			   else{
			    var arr_Data=strData.split("||");
				var caseStr="&nbsp;&nbsp;";
				caseImage[0]=arr_Data[1]*1;
				for (m=1;m<=caseImage[0];m++){
				   caseImage[m]=arr_Data[m+1];
				   caseStr=caseStr+"<a href='#' onclick='selCase(" + m + ")'><b>案例"+GDnumber.split('')[m]+ "</b></a>&nbsp;&nbsp;";
				}
			   document.getElementById("caseNo").innerHTML=caseStr; 
			   document.getElementById(listObj).innerHTML=arr_Data[0];
			   }
			  break;
		  }
	    }
	  // document.getElementById(selobj).className=''; 
	   document.getElementById(listObj).style.display="block";
	   document.getElementById(listObj).style.visibility="visible";
	   }
	  else{
	  // document.getElementById(selobj).className='bom_img_nosel';
	   document.getElementById(listObj).style.display="none";
	   document.getElementById(listObj).style.visibility="hidden";
	   }
   }
   if(index==1 && readFlag==1 ){//标准图第二次加载
         listObj="listImage"+index;
         var strData=getData(url);
		 var arr_Data=strData.split("||");
		 document.getElementById(listObj).innerHTML=arr_Data[0];
      }
   
   switch(index){
	  case 1:
	  	viewId("Review,round,QctNo",false);//Produced,mistakeImg
	    viewId("bombar_tab,BomList,downBtn",true);//,cName,examine
	    break;
	 case 2:
	   document.getElementById("roundNo").innerHTML=document.getElementById("typeName").value;
	   viewId("bombar_tab,BomList,Review,cName,caseNo,examine",false);//,mistakeImg
	   viewId("Produced,round,downBtn,QctNo",true);
	   if (qcImage[0]>0) selQct(1);
	    break;
     case 3:
	    viewId("bombar_tab,BomList,cName,QctNo,examine",false);
        viewId("Produced,round,caseNo,Review,downBtn",true);//,mistakeImg
		if (caseImage[0]>0) {
			selCase(1);
		}else{
			document.getElementById("roundNo").innerHTML="案例编号:&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	    break;
   }
}
function loadDataB(){
   var stuffimg="<img src='<?php    echo $Stuffimg?>'/>";
   document.getElementById("listImage1").innerHTML=stuffimg;

   }

function selQct(index){
	var imgObj;
	for (n=1;n<=qcImage[0];n++){
		imgObj="qcImg"+n;
		if (n==index){
			var arr_str=qcImage[n].split("#");
			document.getElementById("Produced").innerHTML="Produced:by "+arr_str[0]+"<br/>Date:" + arr_str[1];
		    viewId(imgObj,true);
		}else{
		    viewId(imgObj,false);
		}
	}
}

function selCase(index){
	var imgObj;
	for (n=1;n<=caseImage[0];n++){
		imgObj="caseImg"+n;
		if (n==index){
			var arr_str=caseImage[n].split("#");
			document.getElementById("Produced").innerHTML="Produced:by "+arr_str[2]+"<br/>Date:" + arr_str[3];
		    document.getElementById("roundNo").innerHTML="案例编号:" +arr_str[0];
			document.getElementById("Review").innerHTML="<b>检讨人:</b>" +arr_str[1]+"&nbsp;&nbsp;";
		    viewId(imgObj,true);
		}else{
		    viewId(imgObj,false);
		}
	}
}

function viewId(obj,Flag){
	var objArr=obj.split(",");
	var objLen=objArr.length;
	if (objLen>0){
	    if (Flag){
		  for (i=0;i<objLen;i++){
		     document.getElementById(objArr[i]).style.display="block";
		     document.getElementById(objArr[i]).style.visibility="visible";
		 }
	   }else{
		  for (i=0;i<objLen;i++){
		    document.getElementById(objArr[i]).style.display="none";
		    document.getElementById(objArr[i]).style.visibility="hidden";		
		  }
	   }
	}
}

function getData(php_url) {
	var request=false;
	var requestText="";
	var browsetype=getOs();
   try {
     request = new XMLHttpRequest();
   } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }  
     }
   }

   if (!request){
     alert("Error initializing AJAX!");
    }
   else
   {
      request.open("POST",php_url,false);
      request.setRequestHeader("cache-control","no-cache");
      request.setRequestHeader('Content-type','application/x-www-form-urlencoded');  
	  if(browsetype!="Firefox")
      {
        request.onreadystatechange=function(){
		   if(request.readyState == 4 ) {
			   if(request.status == 200) requestText=request.responseText;}
		  }
	  }
      request.send(null);
	  if(browsetype=="Firefox") requestText=request.responseText; 
    }
     return (requestText);
  }
 
 function getOs() 
{ 
   var OsObject = ""; 
   if(navigator.userAgent.indexOf("MSIE")>0) { 
        return "MSIE";       //IE浏览器
   }
   if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){ 
        return "Firefox";     //Firefox浏览器
   }
   if(isSafari=navigator.userAgent.indexOf("Safari")>0) { 
        return "Safari";      //Safan浏览器
   }
   if(isCamino=navigator.userAgent.indexOf("Camino")>0){ 
        return "Camino";   //Camino浏览器
   }
   if(isMozilla=navigator.userAgent.indexOf("Gecko/")>0){ 
        return "Gecko";    //Gecko浏览器
   } 
} 
 </script>                                                                              