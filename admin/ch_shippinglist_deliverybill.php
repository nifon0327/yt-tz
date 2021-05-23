<?php   
//电信-zxq 2012-08-01
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><link href='../cjgl/css/keyboard.css' rel='stylesheet' type='text/css' /></head>";
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$ColsNumber=14;
$tableMenuS=650;
ChangeWtitle("$SubCompany 未发货订单生产提货单");
$funFrom="ch_shippinglist";
$From=$From==""?"read":$From;
$sumCols="12,13";			//求和列,需处理
$MergeRows=4;
$Th_Col="选项|40|出货流水号|80|客户|90|Invoice名称|110|选项|40|序号|40|PO|80|订单流水号|100|中文名称名称|250|订单数量|80|已提货数量|80|本次提货数量|80";
$nowWebPage=$funFrom."_deliverybill";
include "../model/subprogram/read_model_3.php";
$otherAction="<span onClick='showMaskDiv(\"$funFrom\")' $onClickCSS>生成提货单</span>&nbsp;&nbsp;&nbsp;&nbsp;
<span onClick='Reopen()' $onClickCSS>返回</span>";
include "../model/subprogram/read_model_5.php";

	
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$CompanyNumber=0;
$CompanySql=mysql_query("SELECT M.CompanyId 
        FROM $DataIn.ch1_shipmain M WHERE M.Id IN ($Ids) Group by M.CompanyId",$link_id);
if($CompanyRow=mysql_fetch_array($CompanySql)){
  do{
     $CompanyNumber++;
    }while($CompanyRow=mysql_fetch_array($CompanySql));
}
if($CompanyNumber==1){
$CompanyId=mysql_result($CompanySql,0,"CompanyId");
echo "<input name='CompanyId' type='hidden' id='CompanyId' value='$CompanyId'>";

$mySql="SELECT  M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,C.Forshort  
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        WHERE M.Id IN ($Ids)";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	do{
	   $m=1;
	   $Id=$mainRows["Id"];
	   $CompanyId=$mainRows["CompanyId"];
	   $Number=$mainRows["Number"];
	   $InvoiceNO=$mainRows["InvoiceNO"]; 
	   $InvoiceFile=$mainRows["InvoiceFile"]; 
	   $Forshort=$mainRows["Forshort"];  
	   $checkAllclick="onclick=\"checkAll(this,$i);\" ";
	   $checkDisabled="";
	   
	      $checkOrderSql=mysql_query("SELECT
		  S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,M.Date,P.TestStandard
	      FROM $DataIn.ch1_shipsheet S 
          LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	      LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	      LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE M.Id='$Id' AND S.Type='1'
		  UNION ALL
	      SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,
		  O.Description AS eCode,S.Qty,S.Price,M.Date,'' AS TestStandard
	      FROM $DataIn.ch1_shipsheet S 
          LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	      LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	      WHERE M.Id='$Id' AND S.Type='2'
          UNION ALL
	      SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,
          S.Qty,S.Price,M.Date,'' AS TestStandard
	      FROM $DataIn.ch1_shipsheet S 
          LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	      LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	      WHERE M.Id='$Id' AND S.Type='3'",$link_id);
		  if(mysql_num_rows($checkOrderSql)>0){
	      echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
		  echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'><input name='checkAll$i' type='checkbox' id='checkAll$i' $checkAllclick></td>";//备料编号blId
		  $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		  echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Number</td>";				
		  $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		  echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";				
		  $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		  echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceNO</td>";				
		  $unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
		  echo"<td width='' class='A0101'>";
		      if($checkOrderRow=mysql_fetch_array($checkOrderSql)){
	          echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			  $j=1;
			   do{
			      $checkDisabled="";
			      $OrderPO=$checkOrderRow["OrderPO"]==""?"&nbsp;":$checkOrderRow["OrderPO"];
				  $POrderId=$checkOrderRow["POrderId"];
				  $cName=$checkOrderRow["cName"]==""?"&nbsp;":$checkOrderRow["cName"];
				  $OrderQty=$checkOrderRow["Qty"];
				  $TestStandard=$checkOrderRow["TestStandard"];
				  include "../admin/Productimage/getPOrderImage.php";
				  //======订单已提货数量
				  $DeliverySql=mysql_query("SELECT SUM(DeliveryQty) AS DeliveryQty FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId'",$link_id);
				 $DeliveryQty=mysql_result($DeliverySql,0,"DeliveryQty");
				 $DeliveryQty=$DeliveryQty==""?0:$DeliveryQty;
				 $TempQty=$OrderQty-$DeliveryQty;
				 if($TempQty<=0){
				   $DeliveryIMG="";
				   $DeliveryonClick="bgcolor='#96FF2D'";
				   $checkDisabled="disabled";
				    }
				else{
				    $DeliveryIMG="<img src='../images/register.png' width='30' height='30'>";
					$DeliveryonClick="onclick='showKeyboard(this,$i,$j,$OrderQty,$TempQty,$POrderId)'";
				    }
				  
				  $checkIdclick="onclick=\"checkId(this,$i,$j);\" ";
				      echo "<tr height='30'>";
					  $unitFirst=$Field[$m]-1;
					  echo "<td class='A0101' width='$unitFirst' align='center'>
					  <input name='checkId$i' type='checkbox' id='checkId$i' value='$POrderId' $checkIdclick $checkDisabled></td>";
					  $m=$m+2;
					  echo"<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
					  $m=$m+2;
					  echo"<td class='A0101' width='$Field[$m]' align='center'>$OrderPO</td>";
					  $m=$m+2;
					  echo"<td class='A0101' width='$Field[$m]' align='center'>$POrderId</td>";
					  $m=$m+2;
					  echo"<td class='A0101' width='$Field[$m]' align='center'>$TestStandard</td>";
					  $m=$m+2;
					  echo"<td class='A0101' width='$Field[$m]' align='center'>$OrderQty</td>";
					  $m=$m+2;
					  echo"<td class='A0101' width='$Field[$m]' align='center'>$DeliveryQty</td>";
					  $m=$m+2;
					  echo"<td class='A0100' style='color:#FF0000;' align='center' $DeliveryonClick>$DeliveryIMG</td>";		  
			     $j++;
			    }while($checkOrderRow=mysql_fetch_array($checkOrderSql));
				echo"</table>";
	          }
	      }
		  echo"</td></tr></table>";
		  $i++;
	  }while($mainRows = mysql_fetch_array($mainResult));
	    $i=$i-1;
		echo " <input name='cIdNumber' type='hidden' id='cIdNumber' value='$i'/>";
     }	
}
else{
	 echo "请选取同一家公司的订单";
	}
SetMaskDiv();//遮罩初始化
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script src='../cjgl/showkeyboard.js' type=text/javascript></script>
<script src='../cjgl/taskstyle.js' type=text/javascript></script>
<script>
var keyboard=new KeyBoard();
var tasksboard=new TasksBoard();
var QtyArray=new Array();
var IdArray=new Array();
var eArray=new Array();
var eImg="<img src='../images/register.png' width='30' height='30'>";

function showKeyboard(e,index,j,OrderQty,TempQty,POrderId){
    var addQtyFun=function(){
		var checkId="checkId"+index;
		var ListCheck=document.getElementsByName(checkId);
		var eStr=parseInt(e.innerHTML);
	    if (eStr>=0){
	       ListCheck[j-1].checked=true;
		}else{
		   ListCheck[j-1].checked=false;
		}
		checkFlag(index);
		addQty(e,POrderId);
		};
    keyboard.show(e,TempQty,'<=',TempQty,addQtyFun);
}
//全选
function checkAll(e,index){
	var tempval5,tempval6,tempval7;
	var tableId="ListTable"+index;
	var checkId="checkId"+index;
	var ListTable=document.getElementById(tableId);
	var ListCheck=document.getElementsByName(checkId);
    if (e.checked){
	 for(var i=0;i<ListTable.rows.length;i++){ 
	   if (ListCheck[i].disabled==false){
		   ListCheck[i].checked=true;
		   tempval5=parseInt(ListTable.rows[i].cells[5].innerHTML);
		   tempval6=parseInt(ListTable.rows[i].cells[6].innerHTML);
		   tempval7=tempval5-tempval6;
		   ListTable.rows[i].cells[7].innerHTML=tempval7; 
		   addQty(ListTable.rows[i].cells[7],ListCheck[i].value);
	    } 
		}
	}
	else{
	 for(var i=0;i<ListTable.rows.length;i++){ 
	   if (ListCheck[i].disabled==false){
		 ListCheck[i].checked=false;
		 ListTable.rows[i].cells[7].innerHTML=eImg; 
		 addQty(ListTable.rows[i].cells[7],ListCheck[i].value);
		}
	  }
	}
}
// 多选
function checkId(e,index,i){
	var tempval5,tempval6,tempval7;
    var checkId="checkId"+index;
    var tableId="ListTable"+index;
	var ListTable=document.getElementById(tableId);
	var ListCheck=document.getElementsByName(checkId);
	i=i-1;
	if (ListCheck[i].checked){
		tempval5=parseInt(ListTable.rows[i].cells[5].innerHTML);
		tempval6=parseInt(ListTable.rows[i].cells[6].innerHTML);
		tempval7=tempval5-tempval6;
	    ListTable.rows[i].cells[7].innerHTML=tempval7;
		addQty(ListTable.rows[i].cells[7],ListCheck[i].value);
	  }
	else{
	    ListTable.rows[i].cells[7].innerHTML=eImg; 	
		addQty(ListTable.rows[i].cells[7],ListCheck[i].value);
	}
	checkFlag(index);
}
//检查全选状态
function checkFlag(index){
   var checkId="checkId"+index;	
   var checkAllId="checkAll"+index;
   var Flag=true;
   var ListCheck=document.getElementsByName(checkId);
   var checkAll=document.getElementById(checkAllId);
   for(var i = 0; i<ListCheck.length; i++) {
	  if (ListCheck[i].checked==false &&  ListCheck[i].disabled==false){
		  Flag=false;break;
	  }
   }
   checkAll.checked=Flag;
}

function addQty(e,POrderId){
	var eStr=parseInt(e.innerHTML);
	if (eStr>=0){
	    m= ArrayPostion(IdArray,POrderId);
		if (m>=0){
			QtyArray[m]=eStr;
		    }
		else{
		   IdArray.unshift(POrderId);
		   eArray.unshift(e);
		   QtyArray.unshift(eStr); 
	       e.style.color='#F00';
		    }
	   }
	else{
		m= ArrayPostion(IdArray,POrderId);
		if (m>=0){
			IdArray.splice(m,1);
			QtyArray.splice(m,1);
			eArray.splice(m,1);
		}
		e.innerHTML=eImg;
	}
}

function ArrayPostion(Arr,Str){
   var backValue=-1;
   var sLen=Arr.length;
   if (sLen>0){
	 for (i=0;i<sLen;i++){
		 if (Arr[i]==Str){backValue=i;break;}
	 }
   }
   return backValue;
}

function ArrayClear(){
   IdArray=[];
   QtyArray=[];
   var sLen=eArray.length;
   if (sLen>0){
	  for (i=0;i<sLen;i++){eval(eArray[i]).innerHTML=eImg;}
   }
   eArray=[];
   var checkId,ListCheck,checkAllId;
   var cIdNumber=parseInt(document.getElementById("cIdNumber").value);
   
   for (var i=1;i<=cIdNumber;i++){
       checkId="checkId"+i;
	   checkAllId="checkAll"+i;
	   document.getElementById(checkAllId).checked=false; 
	   ListCheck=document.getElementsByName(checkId);
	   for(var j = 0; j<ListCheck.length; j++) {
		  if (ListCheck[j].disabled==false) ListCheck[j].checked=false; 
	   }
    }
}
function saveQty(){
    var DeliveryNumber=document.getElementById('DeliveryNumber').value;
    var ForwaderId=document.getElementById('ForwaderId').value;
	var ModelId=document.getElementById('ModelId').value;
	if(ModelId=="" || ForwaderId==""){
	alert("请选择提货模板和Forwader");return false;}
	var POrderId=IdArray.join("|");
	var Qty=QtyArray.join("|");
	document.form1.action="ch_shippinglist_shipout_ajax.php?Id="+POrderId+"&Qty="+Qty+"&ActionId=31";
	document.form1.submit();
   }
   
   
function Reopen(){//返回的页面和参数
	document.form1.action="ch_shippinglist_read.php?DeliverySign=0";
	document.form1.submit();
	}

function showMaskDiv(WebPage){	//显示遮罩对话框
      if (IdArray.length<=0){
		alert ("请先添加提货数量！");
		return false;		
	     }
	//检查是否有选取记录
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		sOrhDiv(""+WebPage+"");
	}

function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage){	
        var CompanyId=document.getElementById('CompanyId').value;	
		var url="../admin/"+WebPage+"_shipout_mask.php?CompanyId="+CompanyId; 
	　	//var show=eval("divInfo");
	   // alert(url);
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

</script>