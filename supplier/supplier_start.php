<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php 
include "../model/characterset.php";
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo "<link rel='stylesheet' href='../model/css/sharing.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
ChangeWtitle("$SubCompany 首页");
?>
<title>首页</title>
</head>
<style type="text/css">
/* Stats Module */
.stats_graph {
width: 64%;
float: left;
}

.stats_overview {
background: #F6F6F6;
border: 1px solid #ccc;
float: left;
width: 98%;
margin: 5px 10px 10px 10px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
}


.stats_overview p {
margin: 0; padding: 0;
text-align: center;
text-transform: uppercase;
text-shadow: 0 1px 0 #fff;
}

.stats_overview p.overview_day {
font-size: 12px;
font-weight: bold;
margin: 6px 0;
}

.stats_overview p.overview_count {
font-size: 26px;
font-weight: bold;
color: #333333;
}

.stats_overview b{
font-size: 13px;
font-weight: bold;
height: 25px;
line-height: 25px;
margin-left:5px;
color: #333333;
text-shadow: 1px 1px #fff;
}

.stats_overview span{
font-size: 13px;
color: #333333;
height: 25px;
line-height: 25px;
}

.stats_overview a{
 color:#f00;
}
.stats_overview p.overview_type {
font-size: 10px;
color: #999999;
margin-bottom: 8px}

</style>
</body>
<p></p>
<?php
 //今日新单
 $toDay=date("Y-m-d");
  $numSql="SELECT count(*) AS nums,SUM(S.FactualQty+S.AddQty) AS Qty
			FROM $DataIn.cg1_stocksheet S
			LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
			WHERE  S.Mid>0 AND M.CompanyId='$myCompanyId' AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id ) ";//AND M.Date='$toDay'  
    $numResult = mysql_fetch_array(mysql_query($numSql,$link_id));
    $nums=$numResult["nums"];
    $Qty=number_format($numResult["Qty"]);
    echo "<div class='stats_overview'><table width='100%'><tr><td width='100px' height='35'><b>新单</b></td>
			<td><a href='supplier_today_read.php' target='mainFrame' class='yellowN'>$Qty pcs</a></td></tr></table></div>"; 
?>
<div class="stats_overview">
    <table width='100%'><tr>
  <td width='100px' height='35'><b>提示信息</b></td> 
  <td >
  <!--
          <b><span style='color:#f00;border-bottom-color:#FF0000; border-bottom-style:dashed; border-bottom-width:1px;'> 1、本系统已于2013-12-12更新，如遇有操作问题或建议，请及时与我司联系。</span></b><br>
          <span style='color:#f00;'> 2、送货请直接送至采购单指定楼层！ (周六送货时间: 8:00~17:00，周日不收货)</span><br>
          <span style='color:#f00;'> 3、请在同一张送货单上只能开同一楼层的产品！</span><br>
    -->
 <?php 
          $toDay=date("Y-m-d");
		  $checkSql="SELECT N.Remark,N.Date,N.Operator,S.Name,L.CompanyId,DATEDIFF('$toDay',N.Date) AS Days  
		             FROM $DataIn.info4_cgmsg N
		             LEFT JOIN  $DataPublic.staffmain S ON S.Number=N.Operator
					 LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=N.CompanyId
					 WHERE N.Estate=1 AND (L.Id='$Login_P_Number' OR N.CompanyId=1) ORDER BY N.Date DESC";
		  $checkMsg=mysql_query($checkSql,$link_id);
		  if($MsgRow=mysql_fetch_array($checkMsg)){
		  	$i=1;
			do{
				$Remark=$MsgRow["Remark"];
				$Date=$MsgRow["Date"];
				$Operator=$MsgRow["Operator"];
				$Name=$MsgRow["Name"];
				$ColorSTR=$MsgRow["Days"]>7?"#0000FF":"#FF0000";
			    echo "<span style='color:$ColorSTR;font-size:13px;height:25px;line-height:25px;'>$i.&nbsp;" .$Remark."&nbsp;</span><br>";
				$i++;
				}while($MsgRow=mysql_fetch_array($checkMsg));
			}
			
			$checkSql="SELECT N.Remark,N.Date,N.Operator,S.Name,L.CompanyId,DATEDIFF('$toDay',N.Date) AS Days  
		             FROM $DataIn.info4_cgmsg N
		             LEFT JOIN  $DataPublic.staffmain S ON S.Number=N.Operator
					 LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=N.CompanyId
					 WHERE L.Id='$Login_P_Number' OR N.CompanyId='$myCompanyId' ORDER BY N.Date DESC";
		  $checkMsg=mysql_query($checkSql,$link_id);
		  if($MsgRow=mysql_fetch_array($checkMsg)){
			do{
				$Remark=$MsgRow["Remark"];
				$Date=$MsgRow["Date"];
				$Operator=$MsgRow["Operator"];
				$Name=$MsgRow["Name"];
				$ColorSTR=$MsgRow["Days"]>7?"#0000FF":"#FF0000";
			    echo "<span style='color:$ColorSTR;font-size:13px;height:25px;line-height:25px;'>$i.&nbsp;" .$Remark."&nbsp;</span><br>";
				$i++;
				}while($MsgRow=mysql_fetch_array($checkMsg));
			}

?>
</td></tr></table></div>

<?php
  echo "<div class='stats_overview'><b>郑重声明</b><br>
<p align='center'><img src='../download/providerfile/Gys_Tip.png' width='100%'/></p>
</div>"; 

  echo "<div class='stats_overview'><b>送货装框说明</b><br>
<p align='center'><img src='../download/providerfile/Pack_All2.png' width='100%'/></p>
</div>"; 

echo "<div class='stats_overview' ><b>送货提示</b><br>
<p align='center' style='background: #FFFFFF;'><img src='../download/providerfile/sendflow_all.png' width='100%'/></p>
</div>"; 

echo "<div class='stats_overview'><b>自动请款流程图</b><br>
<p align='center'><img src='../download/providerfile/qkflow_all.png' width='100%'/></p>
</div>"; 
//是否包含送货装框图
  $picResult = mysql_fetch_array(mysql_query("SELECT PackFile,TipsFile FROM $DataIn.trade_object WHERE CompanyId='$myCompanyId' LIMIT 1",$link_id));
  $TipsFile=$picResult["TipsFile"];
  $PackFile=$picResult["PackFile"];
  if ($TipsFile==1){
	   $TipsFileName="Tips_$myCompanyId.png";
         // $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
	      //echo "<div class='stats_overview'><b>送货胶框图</b> <span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color: 
#FF6633;'>查看</span>";
echo "<div class='stats_overview'><b>商标去除提示图例</b><br>
<p align='center'><img src='../download/providerfile/$TipsFileName' width='100%'/></p>
</div>"; 

  }
  
  
  if ($PackFile==1)
  {
        // $d=anmaIn("download/providerfile/",$SinkOrder,$motherSTR);
         $PackFileName="Pack_$myCompanyId.png";
         // $f=anmaIn($PackFileName,$SinkOrder,$motherSTR);	
	      //echo "<div class='stats_overview'><b>送货胶框图</b> <span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color: 
#FF6633;'>查看</span>";
echo "<div class='stats_overview'><b>送货胶框图</b><br>
<p align='center'><img src='../download/providerfile/Pack_$myCompanyId.png' width='100%'/></p>
</div>"; 
  }
  
 
 echo "<div class='stats_overview'><b>小台车堆放说明图</b><br>
<p align='center'><img src='../download/providerfile/Pack_All.png' width='100%'/></p>
</div>"; 
/*			
//存在不可显示金额，则不显示全部分类查询统计
  $isPriceResult = mysql_query("SELECT P.IsPrice
	    FROM $DataIn.sys4_gysfunpower P
	    WHERE 1 AND P.UserId='$Login_Id' AND P.IsPrice=0 ",$link_id);  	    
if (!$risPriceRow = mysql_fetch_array($isPriceResult)){
	      $FK_Sql=mysql_fetch_array(mysql_query("SELECT sum(Amount) as sumje  FROM  $DataIn.cw1_fkoutsheet WHERE CompanyId='$myCompanyId' AND Estate =3",$link_id));
			$sumje=$FK_Sql["sumje"];
			$sumje=number_format(sprintf("%.2f",$sumje),2);	
			echo "<div class='stats_overview'><b>未结付货款</b>
			<span><a href='supplier_fkcount_read.php' target='mainFrame' class='yellowN'>￥$sumje</a></span></div>";        
 }
 */
?>
<div id="popWin" style="position:absolute;top:0;left:0;"></div>
</body>
</html>
<script>
		function popWindow(f,Id,img){
			var d = document.getElementById('popWin'),wh = getWH(f);
			d.style.cssText +=";width:"+wh.w+'px;height:'+wh.h+'px';
			d.innerHTML="<image src='../download/errorcase/"+img+"' width='100%'/>";
			d.innerHTML+="<div width='100%' style='background:#bbb;text-align:center;'><input type='button' value='已  阅' style='margin:10px 10px;width:75px;' onclick='closeDiv()'/><input type='hidden' id='upId' name='upId' value='"+Id+"'/> </div>";
			
		}
		var getWH = function (){
			var d = document,doc = d[d.compatMode == "CSS1Compat"?'documentElement':'body'];
			return function(f){
				return {
					w:doc[(f?'client':'scroll')+'Width'],
							h:f?doc.clientHeight:Math.max(doc.clientHeight,doc.scrollHeight)
				}
			}
		}()
		
		function closeDiv(){
			var d = document.getElementById('popWin');
			var Id=document.getElementById('upId').value;
			var url="supplier_start_ajax.php?Action=1&Id="+Id;
	        var ajax=InitAjax();
	        
　	        ajax.open("GET",url,true);
	        ajax.onreadystatechange =function(){
	　　     if(ajax.readyState==4 && ajax.status ==200){
	　　　        d.style.display="none";
			      }
		     }
　      	ajax.send(null);
	}
	</script>
<?
$eResult = mysql_query("SELECT  E.Id,E.Title,E.Picture,E.Date FROM $DataIn.errorcasedata E
                        LEFT JOIN $DataIn.casetostuff C ON C.cId=E.Id
						LEFT JOIN $DataIn.stuffprovider S ON S.StuffId=C.StuffId
						LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=S.CompanyId
                        WHERE E.Estate=1 AND L.Id=$Login_P_Number  AND TIMESTAMPDIFF(hour,E.Date,Now())<168
						GROUP BY E.Id ORDER BY E.Date DESC LIMIT 1",$link_id); //AND E.ReadState=0  
if($eRow = mysql_fetch_array($eResult)){
   $Picture=$eRow["Picture"];
   $Id=$eRow["Id"];
   echo "<script>popWindow('','$Id','$Picture');</script>";
}
						
?>