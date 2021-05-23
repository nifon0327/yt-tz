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
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$tableMenuS=750;
ChangeWtitle("$SubCompany Stock Delivery Record");
$funFrom="PreserverQty";
$nowWebPage=$funFrom;
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$Th_Col="Choose|50|NO|30|DeliveryNO.|100|DeliveryDate|100|Consignee|120|Destination|120|ShipType|60|Remark|40|NO.|30|PO|80|Product Code|120|Qty|60|Updated|80";
$sumCols="6,7";			//求和列,需处理
$ColsNumber=9;
//步骤3：
$CompanySTR=" and M.CompanyId='$myCompanyId' ";
$SearchRows=" AND M.Type=2";
include "../model/subprogram/read_model_3.php";
echo $CencalSstr;
/*$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";*/
include "../admin/subprogram/read_model_5.php";
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
$subTableWidth=$tableWidth-30;
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataIn.skech_deliverymain M  WHERE  1 $CompanySTR  $SearchRows  AND Estate>0 ORDER BY Type   ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
          $m=1;
          $Id=$myRow["Id"];
          $ModelId=$myRow["ModelId"];
          $ForwaderId=$myRow["ForwaderId"];
          $DeliveryNumber=$myRow["DeliveryNumber"];
          $DeliveryDate=$myRow["DeliveryDate"];
         if($ModelId>0){
		         $checkModel=mysql_fetch_array(mysql_query("SELECT Id,EndPlace,Address FROM $DataIn.ch8_shipmodel WHERE 1 AND Id=$ModelId  ORDER BY Id",$link_id));
                 $EndPlace=$checkModel["EndPlace"];
                  $Adress=$checkModel["Adress"];
                 }
         else{
                    $EndPlace=$myRow["EndPlace"];
                    $Adress=$myRow["Adress"];
            }
          $ShipType=$myRow["ShipType"];
          $Type=$myRow["Type"];
         // if($Type==2)$bgColor="bgcolor='#9ACD32'";
        $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id'><img src='../images/lock.png' width='15' height='15'>";
          $Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
          $Date=$myRow["Date"];
		       echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' ><tr $bgColor>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Choose</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$i</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$DeliveryNumber</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$DeliveryDate</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$EndPlace</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' >$Adress</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' >$ShipType</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' >$Remark</td>";
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
		        $checkStockSql=mysql_query("SELECT  S.Id,S.Qty,P.eCode,P.TestStandard,Y.OrderPO
                    FROM $DataIn.skech_deliverysheet S 
                  LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
                 LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
                 WHERE S.Mid=$Id",$link_id);
                if($checkStockRow=mysql_fetch_array($checkStockSql)){
			            echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $bgColor>";
			            $j=1;
			          do{
                              $Sid=$checkStockRow["Id"];
                              $eCode=$checkStockRow["eCode"];
                              $OrderPO=$checkStockRow["OrderPO"]==""?"&nbsp;":$checkStockRow["OrderPO"];
                              $TestStandard=$checkStockRow["TestStandard"];
		                      $OperationIMG="<img src='../images/register.png' width='30' height='30'>";
                              $Operation="onclick='updateJq($i,$j,$Sid,2,\"$eCode\")'";
                              if($TestStandard==1){
                                      $FileName="T".$ProductId.".jpg";
                                      $f=anmaIn($FileName,$SinkOrder,$motherSTR);
                                      $d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);
                                      $eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
                                }
                              $Qty=$checkStockRow["Qty"];
                               echo "<tr height='25'>";
				         	  $unitFirst=$Field[$m]-1;
				         	  echo "<td class='A0001' width='$unitFirst' align='center'>$j</td>";
				         	  $m=$m+2;
					         	echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderPO</td>";
					         	$m=$m+2;
					         	echo"<td class='A0001' width='$Field[$m]' align='center'>$eCode</td>";
					         	$m=$m+2;
					         	echo"<td class='A0001' width='$Field[$m]'  align='center'>$Qty</td>";
					         	$m=$m+2;
					         	echo"<td class='A0000' align='center' style='color:#FF0000;' $Operation>$OperationIMG</td>";	//领料
					         	echo"</tr>";
						$j++;

        			    	}while($checkStockRow=mysql_fetch_array($checkStockSql));
				     echo"</table>";
                   }
		   echo"</td></tr></table>";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
$Keys=31;
//$ActioToS="142,143,140";
include "subprogram/client_menu.php";
List_Title($Th_Col,"0",0);
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script>
function updateJq(TableId,RowId,runningNum,toObj,productCode){//行即表格序号;列，流水号，更新源
	//showMaskBack();
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 2:
				InfoSTR="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Updated&nbsp;&nbsp;<input name='runningNum' type='hidden' id='runningNum' value='"+runningNum+"'>"+productCode+"'s PreserverQty: &nbsp;&nbsp;<input name='Qty' type='text' id='Qty' size='18' class='INPUT0100' onblur='CheckQty(this)'><br>";
				break;
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='Update' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='Cancel' onclick='CloseDiv()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {
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
//	closeMaskBack();
	}
function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
		case "2":
			var tempQty=document.form1.Qty.value;
            if(tempQty>=0 && tempQty!=""){
					myurl="PreserverQty_ajax.php?Sid="+temprunningNum+"&Qty="+tempQty+"&ActionId=2";
					var ajax=InitAjax();
					ajax.open("GET",myurl,true);
					ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200
                 		   document.form1.submit();
							CloseDiv();
							}
						}
					ajax.send(null);
               }
		break;
		}
}



function CheckQty(e){
  var Num=fucCheckNUM(e.value);
        if(Num!=1){
               e.value="";
              alert("Not a Number!");
          }
}

function fucCheckNUM(NUM){
 var i,j,strTemp;
 strTemp="0123456789";
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i));
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}
</script>