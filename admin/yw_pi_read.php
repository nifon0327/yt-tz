<?php   
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/shadow.css'>";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany PI列表");
$funFrom="yw_pi";
$nowWebPage=$funFrom."_read";
$Th_Col="Option|50|NO.|50|Client|80|PI|150|PI回传单|180|OrderPO|100|Leadtime|120|PaymentTerm|200|Remark|200|Date|100|Operator|60";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分
$Page_Size = 100;
$ActioToS="1,2,3,4,38,44,99,100,153,166";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	$clientResult = mysql_query("SELECT P.CompanyId,C.Forshort 
	FROM $DataIn.yw3_pisheet P,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=P.CompanyId  GROUP BY P.CompanyId ORDER BY C.OrderBy DESC",$link_id);
	
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and P.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT P.Id,P.PI,P.Leadtime,P.Paymentterm,P.Date,P.Operator,C.Forshort ,P.Remark,M.ClientOrder
FROM $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.Id = P.oId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 $SearchRows GROUP BY PI
ORDER BY P.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){

	do{
	   $OrderPO="&nbsp;";
		$m=1;
		$PI=$myRow["PI"];
		$Id=$PI;
		//加密参数
		$f=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
		$d=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);		
        $PI=$PI==""?"&nbsp;":"<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$PI</a>";
		$Leadtime=$myRow["Leadtime"];
		$PI_NoColor=1;
		include "../model/subprogram/PI_Leadtime.php";

        $PIRebackFilePath="../download/pipdfreback/PIback" .$Id.".pdf";
		if(file_exists($PIRebackFilePath)){
            $f2=anmaIn("PIback".$Id.".pdf",$SinkOrder,$motherSTR);
            $d2=anmaIn("download/pipdfreback/",$SinkOrder,$motherSTR);	
            $PIReback="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>PIback" .$Id.".pdf</a>";
		}
        else{
            $PIReback="&nbsp;";
            
            $ClientOrder=$myRow["ClientOrder"];
			if($ClientOrder!=""){//原单在序号列显示
				$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
				$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
				$PIReback="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$ClientOrder</a>";
				}
        }


		$Paymentterm=$myRow["Paymentterm"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Forshort=$myRow["Forshort"];
		$Locks=1;
		
		$PINums=0;$OdNums=0;
		$OrderResult=mysql_query("SELECT Count(*) AS PINums,S.OrderPO FROM $DataIn.yw3_pisheet P
		                          LEFT JOIN $DataIn.yw1_ordersheet S ON S.Id=P.oId
								  WHERE P.PI='$Id' Group by S.OrderPO",$link_id);
		if($OrderRow=mysql_fetch_array($OrderResult)){
		  do{
		     $OrderPO.=$OrderRow["OrderPO"]." ";
		     $PINums+=$OrderRow["PINums"];
		     $checkOrderPO=mysql_fetch_array(mysql_query("SELECT Count(*) AS OdNums FROM  $DataIn.yw1_ordersheet S 
								  WHERE S.OrderPO='" . $OrderRow["OrderPO"] . "'",$link_id));
			$OdNums+=$checkOrderPO["OdNums"]==""?0:$checkOrderPO["OdNums"];	  
		    }while($OrderRow=mysql_fetch_array($OrderResult));
		  }
		else{
		     $OrderPO="&nbsp;";
		    }
		  
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$ValueArray=array(
			array(0=>$Forshort, 1=>"align='center'"),
			array(0=>$PI, 		1=>"align='center'"),
			array(0=>$PIReback, 1=>"align='center'"),
			array(0=>$OrderPO, 	1=>"align='center'"),
			array(0=>$Leadtime,	1=>"align='center'"),
			array(0=>$Paymentterm),
			array(0=>$Remark,	1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,8,$Id,1)' style='CURSOR: pointer'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'"),		
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type="text/javascript"></script>
<script language="JavaScript" type="text/JavaScript">
function Quotation(e,Mid){
   
  if(e.innerHTML=="&nbsp;"){
      var msg="PI中是否加入报价规则";
      if(confirm(msg)){
	     myurl="yw_pi_updated.php?Mid="+Mid+"&ActionId=903";
	     var ajax=InitAjax(); 
	         ajax.open("GET",myurl,true);
	         ajax.onreadystatechange =function(){
		     if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
		      // alert(ajax.responseText);
		      e.innerHTML="<div class='greenB'>√</div>";
			 }
		   }
	      ajax.send(null);
        }
    } 
  else{
    var delmsg="取消加入报价规则";
	if(confirm(delmsg)){
       myurl="yw_pi_updated.php?Mid="+Mid+"&ActionId=904";
	     var ajax=InitAjax(); 
	         ajax.open("GET",myurl,true);
	         ajax.onreadystatechange =function(){
		     if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
		      // alert(ajax.responseText);
		      e.innerHTML="&nbsp;";
			 }
		   }
	      ajax.send(null);
        }
    }
 }
 
 
 
 function updateJq(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//备注
				InfoSTR="更新PI为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='TM0000' readonly>的备注<input name='Remark' type='text' id='Remark' size='50' class='INPUT0100'><br>";
				break;									
			}
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}
	
function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) { 
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();  
	}
	
	
function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
	
		case "1":		
			var tempRemark0=document.form1.Remark.value;
			var tempRemark1=encodeURIComponent(tempRemark0);
			myurl="yw_pi_updated.php?PI="+temprunningNum+"&tempRemark="+tempRemark1+"&ActionId=PIRemark";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempRemark0+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
					
		}
	}
</script>