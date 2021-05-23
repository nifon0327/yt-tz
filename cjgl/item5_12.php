<?php
//电信-zxq 2012-08-01
 $checkResult = mysql_query("SELECT JobId FROM $DataPublic.staffmain WHERE Number=$Login_P_Number ORDER BY Number LIMIT 1",$link_id);
$JobId=mysql_result($checkResult,0,"JobId");

$Th_Col="序号|40|配件Id|80|配件名称|300|历史订单|50|供应商|50|单位|30|可用库存|70|在库|70|禁用原因|150|报废按钮|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}

$SearchRows="";
$GysList="";
$GysList1="";
$nowInfo="当前: 禁用类库存配件";
$funFrom="item5_12";
$addWebPage=$funFrom . "_delete.php";
	$SearchRows="";
        /*$result = mysql_query("SELECT  DISTINCT T.TypeId,T.Letter,T.TypeName FROM $DataIn.stuffdata S
			  LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId
			  LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	          WHERE T.Estate=1 order by T.Letter",$link_id);
        */

	$result = mysql_query("SELECT  T.TypeId,T.Letter,T.TypeName,SUM(C.oStockQty) AS oStockQty 
                       FROM $DataIn.ck9_stocksheet C
		       LEFT JOIN $DataIn.stuffdata S ON C.StuffId=S.StuffId 
		       LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	          WHERE S.Estate='0' AND oStockQty>0 GROUP BY T.TypeId order by T.Letter",$link_id);
	/*echo "SELECT  DISTINCT T.TypeId,T.Letter,T.TypeName FROM $DataIn.stuffdata S
			  LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId
			  LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	          WHERE T.Estate=1 order by T.Letter";*/
	if($myrow = mysql_fetch_array($result)){
	$GysList.="<select name='StuffType' id='StuffType' onchange='ResetPage(0,5)'>";//<option value='' selected>--配件类型--</option>
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
                        $StuffType=$StuffType==""?$theTypeId:$StuffType;
			if ($StuffType==$theTypeId){
				$GysList.="<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" AND S.TypeId='$theTypeId'";
				}
			else{
				$GysList.="<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			$GysList.="</select>&nbsp;";
		}
	//供应商
 /* $ForshortResult = mysql_query("SELECT DISTINCT P.Forshort,P.CompanyId,P.Letter
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	WHERE 1 AND S.Estate='0' AND C.oStockQty>0 order by P.Letter",$link_id);*/
   $ForshortResult = mysql_query("SELECT  P.Forshort,P.CompanyId,P.Letter,SUM(C.oStockQty) AS oStockQty 
                       FROM $DataIn.ck9_stocksheet C
		       LEFT JOIN $DataIn.stuffdata S ON C.StuffId=S.StuffId 
		       LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId 
                       LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
	               WHERE S.Estate='0' $SearchRows  AND oStockQty>0 GROUP BY P.CompanyId order by P.Letter",$link_id);

	if($ForshortRow = mysql_fetch_array($ForshortResult)){
	$GysList.="<select name='CompanyId' id='CompanyId' onchange='ResetPage(1,5)'>";
	$GysList.="<option value='' selected>全部</option>";
		do{
			$ForshortName=$ForshortRow["Forshort"];
			$theCompanyId=$ForshortRow["CompanyId"];
			if ($CompanyId==$theCompanyId){
				$GysList.="<option value='$theCompanyId' selected>$ForshortName</option>";
				$SearchRows.=" AND P.CompanyId='$theCompanyId'";
				}
			else{
				$GysList.="<option value='$theCompanyId'>$ForshortName</option>";
				}
			}while ($ForshortRow = mysql_fetch_array($ForshortResult));
			$GysList.="</select>&nbsp;";
		}

//步骤5：
echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList</td>
	<td colspan='5' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){

		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";

$DefaultBgColor=$theDefaultColor;

$i=1;
$mySql="SELECT S.Id,S.StuffId,S.StuffCname,S.StuffEname,S.Gfile,S.Gstate,S.Picture,S.Gremark,S.Estate,
	P.Forshort,S.GfileDate,C.oStockQty,C.tStockQty ,U.Name AS UnitName,D.Reason
	FROM $DataIn.stuffdata S 
	LEFT JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN  $DataPublic.stuffunit U ON U.Id=S.Unit
	LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
	LEFT JOIN  $DataIn.stuffdisable  D ON D.StuffId=S.StuffId
	WHERE 1 $SearchRows  AND S.Estate='0' AND C.oStockQty>0 order by S.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);

if($myRow = mysql_fetch_array($myResult)){
	do{

		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Picture=$myRow["Picture"];
		$UnitName=$myRow["UnitName"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];

		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		include "../model/subprogram/stuffimg_model.php";
		$Forshort=$myRow["Forshort"];
		$oStockQty=$myRow["oStockQty"];
        $tStockQty=$myRow["tStockQty"];
        $Qty=$oStockQty>$tStockQty?$tStockQty:$oStockQty;
		$Reason=$myRow["Reason"];
		$checkNum=mysql_query("SELECT S.Price,D.StuffCname,M.Date FROM $DataIn.cg1_stocksheet S
	                          LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	                          LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
	                          WHERE S.StuffId=$StuffId and S.Mid!=0",$link_id);
		if($checkRow=mysql_fetch_array($checkNum))
		{
		  $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
		}
		else{
		 $OrderQtyInfo="&nbsp;";}
		$UpdateIMG="&nbsp;";$UpdateClick="";
		//$UpdateIMG="<input class='ButtonH_25' type='button'  name='button$i' value='报废'/>";
	    //$UpdateClick=" onclick='BfStuff(this,$i,$Id,$StuffId)'";
	    if($SubAction==31 && $Qty>0){//&& $JobId==34
	             $UpdateIMG="<input class='ButtonH_25' type='button'  name='button$i' value='报废' onclick=\"openWinDialog(this,'$StuffId',320,160,'left','$Qty','$Reason')\"/>";
			      }
		 else{//有权限报废
			 $UpdateIMG="";
		     }

			echo"<tr>";
			echo"<td class='A0111' align='center' height='25'>$i</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101' >$StuffCname</td>";
			echo"<td class='A0101' align='center'>$OrderQtyInfo</td>";
			echo"<td class='A0101' align='center'>$Forshort</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101' align='center'>$oStockQty</td>";
			echo"<td class='A0101' align='center'>$tStockQty</td>";
			echo"<td class='A0101' >$Reason</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='10' align='center' height='30' class='A0111'><div class='redB'>没有相应的配件记录.</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>

<script language = "JavaScript">

//打开DIV弹出窗口
function openWinDialog(e,StuffId,w,h,Pos,Qty,Reason){
    bfButton=e;
	var showDialog=document.getElementById("winDialog");
	showDialog.innerHTML="";
	var url="item5_12_delete.php?StuffId="+StuffId+"&Qty="+Qty+"&dReason="+Reason;
	//alert(url);
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	showDialog.innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
    showDialog.style.width=w+"px";
    //showDialog.style.height=h+"px";
	//定位对话框
	//var Pos="center";
	var offsetPos=getAbsolutePos(e);
	switch(Pos){
		case "top":
		   var calTop=offsetPos.y-h.height-5;
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "left":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x-w -5;
		   break;
		case "right":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x+e.offsetWidth+5;
		   break;
		case "bottom":
		   var calTop=offsetPos.y+e.offsetHeight+5;
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "center":
		   if(!-[1,]){  //判断是否为IE
		     calTop=document.documentElement.scrollTop +(document.documentElement.clientHeight -h)/2;
             calLeft =document.documentElement.scrollLeft +(document.documentElement.clientWidth-w)/2;
               }
           else{
	           calLeft = window.pageXOffset+(window.innerWidth-w)/2;
	           calTop= window.pageYOffset+(window.innerHeight-h)/2;
            }
		  break;
	}
	  if (calTop<=0) calTop=5;
	  if (calLeft<=0) calLeft=5;
	 showDialog.style.left =calLeft+"px";
	 showDialog.style.top =calTop+"px";
	 showDialog.style.display='block';
}

function closeWinDialog(){
	document.getElementById('winDialog').style.display='none';
}

function confirmSave(StuffId,Qty){
 var Reason=document.getElementById('Reason').value;
 //alert(Reason);
 var otherCause=document.getElementById("otherCause").value;
 var msg="配件ID为"+StuffId+"报废数量:"+Qty;
 if(Reason!=""){
   if(confirm(msg)){
        var url="item5_12_ajax.php?StuffId="+StuffId+"&Qty="+Qty+"&Reason="+Reason+"&otherCause="+otherCause;
		//alert(url);
        var ajax=InitAjax();
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
			 if(ajax.responseText=="Y"){

			    document.getElementById('winDialog').style.display='none';
				// bfButton.innerHTML="已报废";
			     bfButton.style.color="#FF0000";
			     bfButton.onclick="";
				}
			}
		 }
	    ajax.send(null);
	   }
	 }
 else{
     alert("请选择报废原因");
     }
}
function otherCauseClick(ee){

    var oCause=document.getElementById("otherCause");
    if (ee.value=="0"){
       var inputStr=prompt("请输入其它原因",oCause.value);
       if (inputStr==null || inputStr==""){
            ee.options[0].selected = true;
            return false;
           }
       else{
           inputStr=inputStr.replace(/(^\s*)|(\s*$)/g,"");
           if (inputStr==""){
               oCause.value="";
               ee.options[0].selected = true;
               }
           else{
               var n = ee.selectedIndex;
               ee.options[n].text ="其它原因:"+ inputStr;
               oCause.value=inputStr;
           }

         }
    }
}
</script>