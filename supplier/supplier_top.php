<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php 
include "../model/characterset.php";
include "../basic/chksession.php";
include "../basic/parameter.inc";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<style type="text/css">
* {
	padding: 0;
	margin: 0;
}
a{
text-decoration:none;
color: #000;
font-size: 13px;
}
a hover{
	color: #000;
}
#header {
	position: absolute;
	top: 0;
	left: 0;
	display: block;
	width: 100%;
	height: 55px;
	z-index: 21;
}

#innerHeader {
margin: 0;
height: 54px;
/*background-color: #BBBBBB;*/
background-image: url(images/headerbg.png);
background-repeat: repeat-x; 
border-bottom: 1px solid #B3B3B3;
box-shadow: rgb(195, 195, 195) 0px 0px 5px 0px;
}

.book-top .left{
	width: 780px;
	height: 54px;
}
.cf{ 
	zoom : 1;
}

.fl{
	float: left;
	background-attachment: scroll;
	background-clip: border-box;
	background-color: rgba(0, 0, 0, 0);
	background-image: none;
	background-origin: padding-box;
	/*
	color: rgb(51, 51, 51);
	font-family: 微软雅黑;
	*/
	cursor: auto;
	display: inline;
    color: rgb(113, 120, 128);
    font-family: helvetica;
	font-size: 24px;
	font-weight: bold;
	height: auto;
	line-height: 54px;
	text-decoration: none;
	text-shadow: rgb(255, 255, 255) 0px 1px 0px;
	width: auto;
	margin-left:8px;
}

.fl2{
	float: left;
	background-attachment: scroll;
	background-clip: border-box;
	background-color: rgba(0, 0, 0, 0);
	background-image: none;
	background-origin: padding-box;
	cursor: auto;
	display: inline;
    font-family: helvetica;
	font-size: 16px;
	height: auto;
	line-height: 54px;
	text-decoration: none;
	width: auto;
	margin-top:4px;
	margin-left:75px;
}

.fr{
	float: right;
	display: inline;
	line-height: 54px;
	margin-right: 8px;
	text-align: right;
}
.uimg{
	line-height: 54px;
	height: 28px;
	width:28px;
   vertical-align: middle;
   margin-bottom: 10px;
}
.user{
	margin-right: 200px;
	font-size:13px;
}

#canvaslist{ 
    list-style:none; 
    overflow: auto; 
    margin:0; 
    padding:0; 
    zoom:1; 
    font-size:13px;
    white-space: nowrap;      
    width: 100%; 
    vertical-align: middle;
} 
#canvaslist li{ 
    line-height:54px; 
    margin:5px 4px; 
    text-align:center;
    display: inline; 
} 

.linespace{
	 border-left: 1px solid #B3B3B3;
}

<?php
$subTitle="";
$NumsResult=mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(IF(YEARWEEK(M.rkDate,1)>YEARWEEK(G.Deliverydate,1),S.Qty,0)) AS OverQty   
							FROM $DataIn.ck1_rksheet S
							LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
							LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
							LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
							LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
							WHERE  M.CompanyId='$myCompanyId' AND  DATE_FORMAT(M.rkDate,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')",$link_id);
								if($NumsRow = mysql_fetch_array($NumsResult)){
									  $P_Qty=$NumsRow["Qty"];
									  if ($P_Qty>0){
											  $P_OverQty=$NumsRow["OverQty"];
											  $Punc_Value=($P_Qty-$P_OverQty)/$P_Qty*100;
											  $Punc_Color=$Punc_Value<80?"#66B3FF":"#0050FF";
											  $Punc_Value=round($Punc_Value);
											  $subTitle=$Punc_Value>=0?"<span style='Color:$Punc_Color'>" . $Punc_Value ."%</span>":" "; 
									  }
								}
?>

</style>
</head>
<body>
<div id="header">
      <div id="innerHeader">
           <div class="book-top cf">
                <div class="fl">研砼供应商采购管理系统</div>
                <div class="fl2"> 本月送货准时率:<?php echo $subTitle ?></div>
                <div class="fr">
                          <?php 
               $pResult =  mysql_fetch_array(mysql_query("SELECT L.Name,P.Forshort,L.Email FROM $DataIn.linkmandata L 
                 LEFT JOIN $DataIn.trade_object P ON P.CompanyId=L.CompanyId  WHERE L.Id='$Login_P_Number' ORDER BY L.Id LIMIT 1",$link_id));
				$Name=$pResult["Name"];
				$Forshort=$pResult["Forshort"];
				$Email=$pResult["Email"];
				/*echo "<img src='images/user.png' class='uimg'/>
				<span class='user'> $Name-$Forshort   &nbsp;&nbsp;&nbsp;&nbsp;
				<img src='images/mail.png' class='uimg'/> $Email</span>";
				*/
				$listStr="<ul id='canvaslist'><li style='color:#FFFFFF'> $myCompanyId </li><li> $Name-$Forshort </li><li class='linespace'>&nbsp;</li>";
				if ($Email!="") {
					$listStr.="<li> $Email</li><li class='linespace'>&nbsp;</li>";
				}
				$listStr.="<li><a href='../exit.php' target='_parent'>退出系统</a></li></ul>";
				echo $listStr;
				/*
				echo "
				<ul id='canvaslist'>
				   <li> $Name-$Forshort </li>
				    <li class='linespace'>&nbsp;</li>
				   <li> $Email</li>
				   <li class='linespace'>&nbsp;</li>
				   <li><a href='../exit.php' target='_parent'>退出系统</a></li>
				</ul>";
			   */
			  ?>
			  
                   <!--<span><a href="../exit.php" target="_parent"><img src='images/sign-out.png' class='uimg'/>退出系统</a></span>-->
                </div>
           </div>
  </div>
</div>
</body>
</html>

<script language="javascript" type="text/javascript">
//如果已提示一次则不再提示
var exitInfo=0;
//toOnline1();
//function toOnline1(){ //如果已提示一次则不再提示 暂时禁用(20180518 by xfy)
//	var url="online_count1.php";
////　var show=document.getElementById("show_news");
//	//var WorkInfo=document.getElementById("myWorkInfo");
//　	var ajax=InitAjax();
//　	ajax.open("GET",url,true);
//	ajax.onreadystatechange =function(){
//	　　if(ajax.readyState==4 && ajax.status ==200){
//			var BackData=ajax.responseText;
//			var DataArray=BackData.split("`");
//	　　　	//show_news.innerHTML=DataArray[0];//在线人数
//			//show_sms.innerHTML=DataArray[1]; //短消息数
//			//WorkInfo.innerHTML=DataArray[3]; //维护信息
//			//alert(BackData);
//			if (DataArray[2]!=0){//踢出		//
//				if(DataArray[2]==1){//被踢出
//					alert("系统更新或网络掉线等原因!你的帐号将退出！如有问题请跟管理员反映!");
//					}
//				else{//重复登录
//					alert("你的帐号在 "+DataArray[2]+" 重新登录!当前窗口将退出！");
//					}
//				parent.location.href="../index.php";
//				exitInfo=1;
//				}
//			}
//		}
//　	//发送空
//　	ajax.send(null);
//	if(exitInfo==0){
//		setTimeout( "toOnline1() ",10000);
//		}
//	}
/*
if(navigator.userAgent.indexOf("MSIE")>-1)
{
window.attachEvent("onload", correctPNG);
}
function correctPNG()
{
  for(var i=0; i<document.images.length; i++)
  {
   var img = document.images[i]
   var imgName = img.src.toUpperCase()
   if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
   {
   var imgID = (img.id) ? "id='" + img.id + "' " : ""
   var imgClass = (img.className) ? "class='" + img.className + "' " : ""
   var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
   var imgStyle = "display:inline-block;" + img.style.cssText
   if (img.align == "left") imgStyle = "float:left;" + imgStyle
   if (img.align == "right") imgStyle = "float:right;" + imgStyle
   if (img.parentElement.href) imgStyle = "CURSOR: pointer;" + imgStyle 
   var strNewHTML = "<span " + imgID + imgClass + imgTitle
   + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
   + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
   + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
   img.outerHTML = strNewHTML
   i = i-1
   }
  }
}
*/
</script>
