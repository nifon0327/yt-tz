<link href="../cjgl/css/keyboard.css" rel="stylesheet" type="text/css" />
<?php   
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='skech_mask.css'>";
$From=$From==""?"skech":$From;
$tableMenuS=950;
ChangeWtitle("$SubCompany Stock Delivery Record");
$funFrom="orderstock_skech";
$nowWebPage=$funFrom;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$Th_Col="Choose|50|NO|40|ID|60|Product Code|280|Price|60|Qty|70|DeliveredQty|70|ReserveQty|70|BalanceQty|70|Reserve|100|Purchase|100";
$Th_Col="Choose|50|NO|60|ID|80|Product Code|280|Price|60|Qty|70|DeliveredQty|70|BalanceQty|70";
$sumCols="6,7";			//求和列,需处理
$ColsNumber=9;	
//步骤3：
$CompanySTR=" and SM.CompanyId='$myCompanyId' ";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	
	if($myCompanyId==1091 ) {

		echo "<select name='zipstr' id='zipstr' onchange='ResetPage(this.name)'>";
		$result = mysql_query("select * from (
				SELECT substr(eCode,1,position('-' in eCode)-1) as zipstr FROM $DataIn.`productdata` WHERE `CompanyId`='1091' 
				group by substr(eCode,1,position('-' in eCode)-1)) A order by zipstr",$link_id);
		
		if($myrow = mysql_fetch_array($result)){
			do{
				$thezipstr=$myrow["zipstr"];
				//$zipstr=$zipstr==""?$thezipstr:$zipstr;
				if($zipstr==$thezipstr){
					if($zipstr!=""){
						echo"<option value='$thezipstr' selected>$thezipstr</option>";
					   	$SearchRows=" AND P.eCode like '$zipstr%'";
					}
					else{
						echo"<option value='$thezipstr' selected> Choose All </option>";	
					}
				 }
				 else{
					echo"<option value='$thezipstr'>$thezipstr</option>";
					}
				}while($myrow = mysql_fetch_array($result));
			}
		echo "</select> &nbsp;";	
		}

    //已提和未提分类
    if($deliveryState == '1'){
      $availableSelect = '';
      $deliveredSelect = 'selected';
      $stateSearch = ' and A.ShipQty=A.DeliveryQty';
    }else{
      $availableSelect = 'selected';
      $deliveredSelect = '';
      $stateSearch = ' and A.ShipQty>A.DeliveryQty';
    }

    echo "<select name='deliveryState' id='deliveryState' onchange='ResetPage(this.name)'>";
    echo "<option value='0' $availableSelect>Available Stock</option>";
    echo "<option value='1' $deliveredSelect>Stock Delivered</option>";
    echo "</select> &nbsp;";

}

echo $CencalSstr;
$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";
include "../admin/subprogram/read_model_5.php";
$subTableWidth=$tableWidth-30;

//步骤6：需处理数据记录处理
$Keys = '31';
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM (
SELECT  P.Price, P.cName,P.eCode,P.TestStandard,P.ProductId,C.Forshort,SUM(S.Qty) AS ShipQty,IFNULL(SUM(D.DeliveryQty),0) AS DeliveryQty
FROM  $DataIn.ch1_shipsheet S 
LEFT JOIN ( 
           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
        ) D ON D.POrderId=S.POrderId
LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=SM.CompanyId 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=SM.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
WHERE 1 AND O.Id IS NOT NULL  AND  P.ProductId!='' $CompanySTR   $SearchRows GROUP BY P.ProductId )  A  WHERE  1 $stateSearch";



$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$SumQty=0;$SumQty1=0;$SumQty2=0; $SumWaitingQty=0;$SumPreserveQty=0;
	do{
    $m=1;
		$ProductId=$myRow["ProductId"];
		$Forshort=$myRow["Forshort"];
		$Price=$myRow["Price"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		$ProductId=$myRow["ProductId"];
		$TestStandard=$myRow["TestStandard"];
        $eCode=$myRow["eCode"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
  		 $eCode=$eCode==""?"&nbsp;":$eCode;
		 $Operator=$myRow["Operator"];
		 include "../model/subprogram/staffname.php";
         $ShipQty=$myRow["ShipQty"];
         $Amount=sprintf("%.2f",$Price*$ShipQty);    
		 $DeliveryQty=$myRow["DeliveryQty"];
         $SumQty1+=$DeliveryQty;
       //*********************************等到发货和预留数量
         $PreserveReslut=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty),0) AS PreserveQty FROM $DataIn.skech_deliverysheet   S
         LEFT JOIN $DataIn.skech_deliverymain M ON M.Id=S.Mid
         LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
        WHERE Y.ProductId=$ProductId AND S.Type=2  AND M.Estate>0 AND M.CompanyId=$myCompanyId",$link_id));
         $PreserveQty=$PreserveReslut["PreserveQty"];
         $SumPreserveQty+=$PreserveQty;

		 $unDeQty=$ShipQty-$DeliveryQty-$PreserveQty;
         $SumQty2+=$unDeQty;
         $SumQty=$SumQty1+$SumQty2;
         $thisProductDeliverStr="<input type='hidden' id='ProductId$i' name='ProductId$i' value='$ProductId'>";

        if($unDeQty>0){
				   $DeliveryIMG="<img src='../images/register.png' width='30' height='30'>";
			       $DeliveryonClick="onclick='showKeyboard(this,$i,$unDeQty,$unDeQty,$ProductId)'";
         	         $PreserveonClick="onclick='showKeyboard(this,$i,$unDeQty,$unDeQty,$ProductId)'";
              }
        else{
                $DeliveryIMG="";
                $DeliveryonClick="";
                $PreserveonClick="";
                 }
         $unDeQty=$unDeQty>0?"<span class='redB'>$unDeQty</span>":"&nbsp;";
         $DeliveryQty=$DeliveryQty>0?"<a href='ch_shipoutclient_show.php?ProductId=$ProductId' target='_blank' style='color: #009900;font-weight: bold;'>$DeliveryQty</a>":0;
		 $OrderSignColor=""; 
		 $showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$ProductId\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		     $ValueArray=array(
			   array(0=>$ProductId.$thisProductDeliverStr,	1=>"align='center'"),
			   array(0=>$eCode),
			   array(0=>$Price,	1=>"align='center'"),
			   array(0=>$ShipQty,	1=>"align='right'"),
			   array(0=>$DeliveryQty,	1=>"align='right'"),
			 //  array(0=>$PreserveQty,	1=>"align='right'"),
			  array(0=>$unDeQty=="&nbsp;"?"0":$unDeQty,	1=>"align='right'"),
			  // array(0=>$DeliveryIMG,	1=>"align='center'$PreserveonClick"),
			//   array(0=>$DeliveryIMG,	1=>"align='center' $DeliveryonClick")
			  );
		$checkidValue=$ProductId;
    $LockRemark = '';
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	        $m=1;
           $sendSub="<input type='button' id='submit1' name='submit1' onclick='sendToDelivery(\"orderstock_skech\",\"$myCompanyId\",1)' value='Purchase'>";
           $PreserveSub="<input type='button' id='submit2' name='submit2' onclick='sendToDelivery(\"orderstock_skech\",\"$myCompanyId\",2)'value='Reserve'>";

			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
		    	array(0=>"&nbsp;"	),
				array(0=>$SumQty,		1=>"align='right'"),
				array(0=>$SumQty1,		1=>"align='right'"),
				//array(0=>$SumPreserveQty,		1=>"align='right'"),
				array(0=>$SumQty2,		1=>"align='right'"),
		    //	array(0=>$PreserveSub	,		1=>"align='center'"),
				//array(0=>$sendSub,		1=>"align='center'")
				);
			$ShowtotalRemark="Total";
			$isTotal=1;
            $HightStr="height=30";
			include "../model/subprogram/read_model_total.php";		
	}
else{
	noRowInfo($tableWidth);
  	}
echo "<input type='hidden' id='tempCount' name='tempCount' value='$i'>";
echo "<input type='hidden' id='TmyCompanyId' name='TmyCompanyId' value='$myCompanyId'>";
//步骤7：
echo '</div>';//
List_Title($Th_Col,"0",0);
SetMaskDiv();//遮罩初始化
$Keys=31;
$ActioToS="144,145,38";
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "subprogram/client_menu.php";
?>

<script src='../cjgl/showkeyboard.js' type=text/javascript></script>
<script language="javascript">
//var  arrayObj = new Array();
//arrayObj=<?php echo json_encode($modelArray);?>;
var keyboard=new KeyBoard();
var eImg="<img src='../images/register.png' width='30' height='30'>";
function showKeyboard(e,index,OrderQty,TempQty,ProductId){
    var addQtyFun=function(){
		};
    keyboard.show(e,OrderQty,'<=',TempQty,addQtyFun);
}

function My_sendToDelivery(ActionId){
	var myCompanyId=document.getElementById("TmyCompanyId").value;
	var WebPage='orderstock_skech';
	sendToDelivery(WebPage,myCompanyId,ActionId);
}

function sendToDelivery(WebPage,myCompanyId,ActionId){
    var tempk=0;
    var tempCount=document.getElementById("tempCount").value;
   switch(ActionId){
        case 1://submit
                for(k=1;k<tempCount;k++){
                    	var tableId="ListTable"+k;
	                   var ListTable=document.getElementById(tableId);
                        var pQty=ListTable.rows[0].cells[10].innerHTML;
                          if(pQty>0) tempk++;
                    } 
           break;
        case 2://Preserve
                for(k=1;k<tempCount;k++){
                    	var tableId="ListTable"+k;
	                   var ListTable=document.getElementById(tableId);
                        var PreserveQty=ListTable.rows[0].cells[9].innerHTML;
                          if(PreserveQty>0) tempk++;
                    } 
           break;
          }
       if(tempk==0){
                   alert("Please Write down the Qty!");return false;
                 }
	  else{
		       document.getElementById('divShadow').style.display='block';
               document.getElementById('divShadow').style.top =document.body.scrollTop+200;
			  // document.getElementById('divShadow').style.top =document.body.scrollHeight-480;
		       //divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
				divPageMask.style.width = document.body.scrollWidth;
				divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;			   
	    	   document.getElementById('divPageMask').style.display='block';
		        sOrhDiv(""+WebPage+"",myCompanyId,ActionId);
		   }
}

function closeMaskDiv(){	//隐藏遮罩对话框
	      document.getElementById('divShadow').style.display='none';
	     document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage,myCompanyId,ActionId){
     var passData="";
    var tempCount=document.getElementById("tempCount").value;
    switch(ActionId){
       case 1:
          for(k=1;k<tempCount;k++){
             	var tableId="ListTable"+k;
	            var ListTable=document.getElementById(tableId);
                 var pQty=ListTable.rows[0].cells[10].innerHTML;
                 var ProductId=document.getElementById("ProductId"+k).value;
                 if(pQty>0){
                         if(passData=="")passData=ProductId+"^^"+pQty;
                        else{
                                  passData=passData+"|"+ProductId+"^^"+pQty;
                                 }
                      }
                } 
             break;
       case 2:
          for(k=1;k<tempCount;k++){
             	var tableId="ListTable"+k;
	            var ListTable=document.getElementById(tableId);
                 var PreserveQty=ListTable.rows[0].cells[9].innerHTML;
                 var ProductId=document.getElementById("ProductId"+k).value;
                 if(PreserveQty>0){
                         if(passData=="")passData=ProductId+"^^"+PreserveQty;
                        else{
                                  passData=passData+"|"+ProductId+"^^"+PreserveQty;
                                 }
                      }
                } 
             break;
         }
			var url=WebPage+"_mask.php?myCompanyId="+myCompanyId+"&passData="+passData+"&ActionId="+ActionId;
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


function saveQty(ActionId,myCompanyId){
     var passData="";
    var tempCount=document.getElementById("tempCount").value;
    switch(ActionId){
       case 1:
          for(k=1;k<tempCount;k++){
             	var tableId="ListTable"+k;
	            var ListTable=document.getElementById(tableId);
                 var pQty=ListTable.rows[0].cells[10].innerHTML;
                 var ProductId=document.getElementById("ProductId"+k).value;
                 if(pQty>0){
                         if(passData=="")passData=ProductId+"^^"+pQty;
                        else{
                                  passData=passData+"|"+ProductId+"^^"+pQty;
                                 }
                      }
                } 
             break;
       case 2:
          for(k=1;k<tempCount;k++){
             	var tableId="ListTable"+k;
	            var ListTable=document.getElementById(tableId);
                 var PreserveQty=ListTable.rows[0].cells[9].innerHTML;
                 var ProductId=document.getElementById("ProductId"+k).value;
                 if(PreserveQty>0){
                         if(passData=="")passData=ProductId+"^^"+PreserveQty;
                        else{
                                  passData=passData+"|"+ProductId+"^^"+PreserveQty;
                                 }
                      }
                } 
             break;
         }
       var msg="Please confirm to proceed.";
      if(passData!="" && confirm(msg)){
	          document.form1.action="orderstock_Skech_ajax.php?passData="+passData+"&ActionId="+ActionId+"&myCompanyId="+myCompanyId;
	          document.form1.submit();
            }
    else{
            alert("erro");
             }
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
			var url="ch_shipoutclient_ajax.php?ProductId="+ProductId+"&RowId="+RowId; 
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

function AdressNew(){
      var checkbox1=document.getElementById("checkbox1");
       var ModelId=document.getElementById("ModelId");
     if(checkbox1.checked){
            document.getElementById("newEndPlacetr").style.display="";
            document.getElementById("newAddresstr").style.display="";
            ModelId.value="";
         }
     else{
            document.getElementById("newEndPlacetr").style.display="none";
            document.getElementById("newAddresstr").style.display="none";
                }
}

function changeModel(){
      var checkbox1=document.getElementById("checkbox1");
       var ModelId=document.getElementById("ModelId");
        if(ModelId.value>0){
              checkbox1.disabled="disalbed";
              checkbox1.checked=false;
            document.getElementById("newEndPlacetr").style.display="none";
            document.getElementById("newAddresstr").style.display="none";
                }
         else {
                     checkbox1.disabled=""; 
                  }
}
</script>