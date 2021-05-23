<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
$From=$From==""?"read":$From;
//需处理参数			
//$tableMenuS=550;
$tableMenuS=750;
ChangeWtitle("$SubCompany 客户库存");
$funFrom="ch_shipoutclient";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 500;
$Th_Col="选项|60|序号|40|产品ID|60|产品中文名|320|Product Code|180|Price|50|数量|70|金额|70|已提|70|未提|70|未提箱数|70|提货数量|60|最后提货时间|80|存储位置|100";
$sumCols="6,7,8,9,10,11";			//求和列,需处理
$ColsNumber=12;	
$ActioToS="1,112,38"; 
//步骤3：
$todays=date("Y-m-d") ;
$curDate=date("Y-m-d",strtotime("$todays  -365   day"));
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//月份
	$SearchRows="";		
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId AND M.Id IN (SELECT ShipId FROM $DataIn.ch1_shipout)
	$SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>--全部客户--</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and  SM.CompanyId='$thisCompanyId' ";
				$ModelCompanyId=$thisCompanyId;
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		  echo"</select>&nbsp;";
		}	
//***************提货状态
   $lastDeliverySign = 0;
   $DeliverySign=$DeliverySign==""?1:$DeliverySign;
   $DeliveryStr="DeliverySign".$DeliverySign;
   $$DeliveryStr="selected";
   echo"<select name='DeliverySign' id='DeliverySign' onchange='RefreshPage(\"$nowWebPage\")'>";
   echo "<option value='0' $DeliverySign0>已提</option>";
   echo "<option value='1' $DeliverySign1>未(部分)提</option>";
   echo "<option value='2' $DeliverySign2>一年以上(未提)</option></select>";
//   if($DeliverySign!="")$SearchRows.=" and S.DeliverySign='$DeliverySign' ";
      switch($DeliverySign){
              case "0":
                       $SearchRowsA=" and A.ShipQty=A.DeliveryQty ";
                  break;
              case "1":
                       $SearchRowsA=" and A.ShipQty>A.DeliveryQty ";
                  break;
              case "2":
                       $SearchRowsA=" and A.ShipQty>A.DeliveryQty ";
                        $lastDeliverySign = 1;
                        //$SearchRowsB="AND SM.Date <='$curDate'";
                  break;
             }
	}

 
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
$otherAction="<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\")' $onClickCSS>生成提货单</span>&nbsp;";

include "../model/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//步骤6：需处理数据记录处理
$ShiptotalAmout=0;
$ShiptotalQty=0;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql=" SELECT * FROM (
SELECT  P.Price, P.cName,P.eCode,P.TestStandard,P.ProductId,C.Forshort,SUM(S.Qty) AS ShipQty,IFNULL(SUM(D.DeliveryQty),0) AS DeliveryQty,SM.Date AS ShipDate
FROM  $DataIn.ch1_shipsheet S 
LEFT JOIN ( 
           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
        ) D ON D.POrderId=S.POrderId
LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=SM.CompanyId 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=SM.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
WHERE 1 AND O.Id IS NOT NULL  AND  P.ProductId!='' $SearchRows $SearchRowsB GROUP BY P.ProductId )  A  WHERE  1 $SearchRowsA Order by eCode";
//if ($Login_P_Number==10868)echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$SumQty1=0;$SumQty2=0;
    
	do{
		$m=1;
		$ProductId=$myRow["ProductId"];	
		$Forshort=$myRow["Forshort"];
		$Price=$myRow["Price"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getProductImage.php";

		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $ShipQty=$myRow["ShipQty"];
        $ShipDate=$myRow["ShipDate"];
        $Amount=sprintf("%.2f",$Price*$ShipQty);
		 $DeliveryQty=$myRow["DeliveryQty"];
         $SumQty1+=$DeliveryQty;
        /* $DeliveryAmount=sprintf("%.2f",$Price*$DeliveryQty);
         $unAmount=$Amount-$DeliveryAmount;*/
		$unDeQty=$ShipQty-$DeliveryQty;
       $overTime=0;
		      //最后提货时间
        if($DeliveryQty>0){
		       $lastDeliveryResult = mysql_fetch_array(mysql_query("SELECT  MAX(M.DeliveryDate) AS LastDeliveryDate  FROM $DataIn.ch1_deliverymain M 
		       LEFT JOIN $DataIn.ch1_deliverysheet  S ON S.Mid = M.Id
		       LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		       LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId  
		       WHERE  P.ProductId =$ProductId",$link_id));
		       $lastDeliveryDate =$lastDeliveryResult["LastDeliveryDate"];
              $overTime = (strtotime($todays)-strtotime($lastDeliveryDate))/60/60/24;
           }else{
                 $lastDeliveryDate = "&nbsp;";
                 $overTime = (strtotime($todays)-strtotime($ShipDate))/60/60/24;
            }
          if($overTime>=365)$lastDeliveryDate="<span class='redB'>$lastDeliveryDate</span>";
         if($lastDeliverySign==1){
               if($overTime<365){
                     continue;
                 }
             }
   
		//9040 外箱，未设外箱检查配件是否设置箱/pcs
		$Relation="";
		$PcsPerBox=0;
		$HaveBox=0;
		$RelationResult=mysql_fetch_array(mysql_query("SELECT A.Relation  FROM $DataIn.pands A
												   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
												   WHERE A.ProductId='$ProductId' AND D.TypeId=9040
												   ",$link_id));
		if($RelationResult){
			  $Relation=$RelationResult["Relation"];
		}	

		//echo "$Relation";

		 if($Relation!=""){
			
			$ARelation=explode("/",$Relation);
			if(count($ARelation)==1){
				$PcsPerBox=$ARelation[0];
			}
			else{
				$PcsPerBox=$ARelation[1];
			}
			
			$HaveBox=ceil($unDeQty/$PcsPerBox);
			$MainBoxs="$HaveBox";	
			
		}	
		else{	
        	$BoxPcsResult=mysql_fetch_array(mysql_query("SELECT D.BoxPcs  FROM $DataIn.pands A
												   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
												   WHERE A.ProductId='$ProductId' AND D.BoxPcs>0 Limit 1",$link_id));
           	$BoxPcs=$BoxPcsResult["BoxPcs"];
            if($BoxPcs>0){
            	$MainBoxs=ceil($unDeQty/$BoxPcs);
            }
			else    $MainBoxs="&nbsp;";	
	    }
		 
		 $theunDeQty=$unDeQty;
         $SumQty2+=$unDeQty;
         $unDeQty=$unDeQty>0?"<a href='ch_shipoutclient_show.php?ProductId=$ProductId&DeliverySign=1' target='_blank' style='color: #FF0000;font-weight: bold;'>$unDeQty</a>":"&nbsp;";
		 
		 if($CompanyId!="" && $theunDeQty>0){
		 	$BS_Qty="<input name='BSQty[]' type='text' id='BSQty$i' style='width:50px' value=''  class='I0000C'>";  //add by zx 2013-10-15
		        }
		 else{
		 	$BS_Qty="&nbsp;";  //add by zx 2013-10-15			 
		 }
        $CheckAdressResult=mysql_fetch_array(mysql_query("SELECT   * FROM $DataIn.product_ckadress WHERE ProductId=$ProductId",$link_id));
        $CKAdress=$CheckAdressResult["Adress"]==""?"&nbsp;":$CheckAdressResult["Adress"];
		 
         $DeliveryQty=$DeliveryQty>0?"<a href='ch_shipoutclient_show.php?ProductId=$ProductId&DeliverySign=0' target='_blank' style='color: #009900;font-weight: bold;'>$DeliveryQty</a>":"&nbsp;";
		 $OrderSignColor=""; 
		 $showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$ProductId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: hand'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		     $ValueArray=array(
			   array(0=>$ProductId,	1=>"align='center'"),
			   array(0=>$TestStandard),
			   array(0=>$eCode),
			   array(0=>$Price,1=>"align='right'"),
			   array(0=>$ShipQty,	1=>"align='right'"),
			   array(0=>$Amount,	1=>"align='right'"),
			   array(0=>$DeliveryQty,	1=>"align='right'"),
			   array(0=>$unDeQty,	1=>"align='right'"),
			   array(0=>$MainBoxs,	1=>"align='right'"),
			   array(0=>$BS_Qty,	1=>"align='center'"),
			   array(0=>$lastDeliveryDate,	1=>"align='center'"),
			   array(0=>$CKAdress,	1=>"align='left'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,$ProductId,1,\"$cName\",\"$CKAdress\")' style='cursor:hand'")
			  );
		  
		$checkidValue=$ProductId;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
		echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
	        $m=1;
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumQty1,		1=>"align='right'"),
				array(0=>$SumQty2,		1=>"align='right'"),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				);
			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";		
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：//
List_Title($Th_Col,"0",0);
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<SCRIPT  src='../model/IE_FOX_MASK.js' type=text/javascript></SCRIPT>   
<script language="javascript">
/////////遮罩层函数/////////////
function showMaskDiv(WebPage,CompanyId,FileDir){	//显示遮罩对话框
	//检查是否有选取记录
	UpdataIdX=0;
	 var upIds,eValue,eArray;
	 upIds="";
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					eValue=e.value;
					eArray=eValue.split("^^");
					upIds+=upIds==""?eArray[0]:","+eArray[0];
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0 || CompanyId==""){
		alert("没有选取记录或公司名称!");return false;
		}
	else{
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		sOrhDiv(""+WebPage+"",CompanyId,FileDir,upIds);
		}
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage,DeliveryValue,FileDir,upIds){
	if(DeliveryValue!=""){	
       if(FileDir=="public"){
		   var url="../"+FileDir+"/"+WebPage+"_mask.php?DeliveryValue="+DeliveryValue+"&upIds="+upIds; 
		   }	
		else{
			//var url="../admin/"+WebPage+"_mask.php?CompanyId="+DeliveryValue+"&DeliveryValue="+DeliveryValue+"&upIds="+upIds;
			var url="../admin/"+WebPage+"_mask.php?CompanyId="+DeliveryValue+"&upIds="+upIds;
			}
	　	//var show=eval("divInfo");
	    //alert(url);
	　	var ajax=InitAjax(); 
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;					
				divInfo.innerHTML=BackData;
				}
			}
		ajax.send(null); 
		}
	}


function saveQty(){
    var DeliveryNumber=document.getElementById('DeliveryNumber').value;
    var ForwaderId=document.getElementById('ForwaderId').value;
	var ModelId=document.getElementById('ModelId').value;
	var CompanyId=document.getElementById('CompanyId').value;
	if(ModelId=="" || ForwaderId==""){
	alert("请选择提货模板和Forwader");return false;}

   var checkId,Checked,CompanyId,QtyId,Qty,Result,ArrProductId,ArrQty;
   ArrProductId="";
   ArrQty="";
   
   var cIdNumber=parseInt(document.getElementById("cIdNumber").value); 
  
   for (var i=1;i<cIdNumber;i++){
       checkId="checkid"+i;
	   Checked=document.getElementById(checkId).checked;
	   if(Checked){
		  ProductId= document.getElementById(checkId).value;
		  //alert(CompanyId);
		  QtyId="BSQty"+i; 
		  Qty=document.getElementById(QtyId).value;
		  Result=fucCheckNUM(Qty,'');
		  if(Result==0 || Qty==0){
			   alert("输入了不正确的数量:"+Qty+",重新输入!");
			   return false;
		  }
		  else{  //把它保存值
		    ArrProductId=ArrProductId+ProductId+"|";
			ArrQty=ArrQty+Qty+"|";
		  }
		 
	   }
		 
    }
	
	//alert (ArrProductId); return;
	//alert (ArrQty);
	//document.getElementById('AllProductId').value=ArrProductId;
	//document.getElementById('AllQty').value=ArrQty;
	
	document.form1.action="Ch_shipoutclient_updated.php?CompanyId="+CompanyId+"&AllProductId="+ArrProductId+"&AllQty="+ArrQty+"&ActionId=112";
	document.form1.submit();

}

function sOrhOrder(e,f,Order_Rows,ProductId,RowId){
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(ProductId!=""){			
			var url="../admin/ch_shipoutclient_ajax.php?ProductId="+ProductId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
	
	

function updateJq(TableId,RowId,runningNum,toObj,ProdcutName,CKAdress){//行即表格序号;列，流水号，更新源
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
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//
           //    if(eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML!="")CKAdress=eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML;
				InfoSTR="<input name='runningNum' type='hidden' id='runningNum' value='"+runningNum+"'  class='TM0000' readonly>产品名为:"+ProdcutName+"的存储位置:<textarea  name='CkAdress' id='CkAdress'  cols='52' rows='3'>"+CKAdress+"</textarea><br>";
				break;			
			}
		if(toObj>0){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
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
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
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
            var tempCkAdress=encodeURIComponent(document.form1.CkAdress.value);
			myurl="ch_shipoutclient_ajax.php?ProductId="+temprunningNum+"&CkAdress="+tempCkAdress+"&ActionId=99";
			var ajax=InitAjax(); 
	　		   ajax.open("GET",myurl,true);
			       ajax.onreadystatechange =function(){
	　		  if(ajax.readyState==4){
                        if(ajax.responseText=="Y"){
				              eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML=document.form1.CkAdress.value;
				             CloseDiv();
                           }
				      }
			    }
		    ajax.send(null); 
			break;
		}
	}

</script>