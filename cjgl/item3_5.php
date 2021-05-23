<?php
$ClientList="";
$nowInfo="当前:生产记录查询";
$updateWebPage = "item3_5_updateqty.php";
$SearchRows ="";
$dataResult= mysql_query("SELECT DATE_FORMAT(SC.scDate,'%Y-%m-%d') AS scDate 
FROM  $DataIn.sc1_cjtj S 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId group by scDate ORDER BY scDate DESC",$link_id);
if ($dataRow = mysql_fetch_array($dataResult)){
    $dataList="<select name='scDate' id='scDate'  onChange='ResetPage(1,3)'>";
    do{
        $theScDate=$dataRow["scDate"];
        $scDate=$scDate==""?$theScDate:$scDate;
        if($scDate==$theScDate){
            $dataList.="<option value='$theScDate' selected>$theScDate</option>";
            $SearchRows.=" AND DATE_FORMAT(SC.scDate,'%Y-%m-%d')='$theScDate'";
            $nowInfo.= " - ".$theForshort;
        }
        else{
            $dataList.="<option value='$theScDate'>$theScDate</option>";
        }
        $i++;
    }while($dataRow = mysql_fetch_array($dataResult));
    $dataList.="</select>";
}



$WorkShopResult= mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.sc1_cjtj S 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
WHERE 1 $SearchRows GROUP BY SC.WorkShopId  order by SC.WorkShopId",$link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)){
	$WorkShopList="<select name='WorkShopId' id='WorkShopId' style = 'width:100px' onChange='ResetPage(1,3)'>";
	$i=1;
	do{
		$theWorkShopId=$WorkShopRow["WorkShopId"];
		$theWorkShopName=$WorkShopRow["WorkShopName"];
		$WorkShopId=$WorkShopId==""?$theWorkShopId:$WorkShopId;
		if($WorkShopId==$theWorkShopId){
			$WorkShopList.="<option value='$theWorkShopId' selected>$theWorkShopName</option>";
			$SearchRows.=" AND SC.WorkShopId='$theWorkShopId'";
			}
		else{
			$WorkShopList.="<option value='$theWorkShopId'>$theWorkShopName</option>";
			}
		$i++;
	}while($WorkShopRow = mysql_fetch_array($WorkShopResult));
	$WorkShopList.="</select>";
}


$Th_Col="配件|35|ID|30|客户|80|PO|70|工单流水号|100|中文名|320|工单数量|70|生产数量|70|工单备注|100|生产备注|100|生产车间|100|操作|50";
$Cols=12;
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols=$Cols-4;
//步骤5：

	echo"<table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
	<tr>
	<td colspan='$Cols' height='40px' class=''>$dataList &nbsp;&nbsp;$WorkShopList </td><td colspan='4' align='center' class=''><input name='SearchOrder' id='SearchOrder' type='text' size='20'><input class='ButtonH_25' type='button'  id='Querybtn' name='Querybtn' onclick='SearchScOrder()' value='查 询'><input tpye='text' value=$nowInfo readonly></td></tr>";
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
$today=date("Y-m-d");

if($WorkShopId == '101'){

  $mySql="SELECT S.Id,S.GroupId,S.StockId,S.Qty AS scQty,S.Remark AS scRemark,S.Estate,
		    SC.POrderId,SC.sPOrderId,SC.Qty,SC.Remark,
		    P.ProductId,P.cName,P.eCode,P.TestStandard,
		    Y.OrderPO,O.Forshort,
		    W.Name AS WorkShopName
			FROM $DataIn.sc1_cjtj S 
			INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
			INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
			INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
			INNER JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
			INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
			INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
			WHERE 1  $SearchRows ORDER BY S.Date";

}else{
     $mySql="SELECT S.Id,S.GroupId,S.StockId,S.Qty AS scQty,S.Remark AS scRemark,S.Estate,
            SC.POrderId,SC.sPOrderId,SC.Qty,SC.Remark,
			D.StuffId,D.StuffCname,D.Picture,
			SM.PurchaseID AS OrderPO,O.Forshort,
			W.Name AS WorkShopName
			FROM $DataIn.sc1_cjtj S 
			INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
			INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
			INNER JOIN $DataIn.yw1_ordermain N ON N.OrderNumber = Y.OrderNumber
			INNER JOIN $DataIn.trade_object O ON O.CompanyId = N.CompanyId
			INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
			INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
			INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
			LEFT  JOIN $DataIn.cg1_stockmain SM ON SM.Id = G.Mid
			INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
			WHERE 1  $SearchRows";
}
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$sPOrderId=$myRow["sPOrderId"];
		$OrderPO=toSpace($myRow["OrderPO"]);
        if($WorkShopId == 101){
			$cName=$myRow["cName"];
			$ProductId=$myRow["ProductId"];
			$eCode=toSpace($myRow["eCode"]);
			$TestStandard=$myRow["TestStandard"];
			include "../admin/Productimage/getProductImage.php";
			$chineseName = $TestStandard;
			$fromWorkShopPage = 1;

        }else{

			$StuffId=$myRow["StuffId"];
			$Picture=$myRow["Picture"];
			$StuffCname=$myRow["StuffCname"];
	        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    	include "../model/subprogram/stuffimg_model.php";
	        include"../model/subprogram/stuff_Property.php";//配件属性
	        $chineseName =  $StuffCname;
	        $fromWorkShopPage = 2 ;
        }

		$WorkShopName=$myRow["WorkShopName"];
        $Estate=$myRow["Estate"];
		$Qty=$myRow["Qty"];
		$scQty=$myRow["scQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$scRemark = $myRow["scRemark"]==""?"&nbsp;":$myRow["scRemark"];

        $UpdateIMG = "";
        $UpdateClick = "";
		if($SubAction==31  && $Estate ==1){
              $UpdateIMG="<img src='../images/register.png' width='30' height='30'";

              $UpdateClick="onclick=\"openWinDialog(this,'$updateWebPage?Id=$Id&sPOrderId=$sPOrderId&fromWorkShopPage=$fromWorkShopPage',400,300,'left')\" ";

          }
		//动态读取配件资料
		$showPurchaseorder="[ + ]";
		$ListRow="<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
		echo"<tr><td class='A0111' align='center' id='theCel$i' height='25' valign='middle'  onClick='ShowOrHide(ListRow$i,theCel$i,$i,$POrderId,$sPOrderId);' >$showPurchaseorder</td>";
		echo"<td class='A0101' align='center'>$i</td>";
		echo"<td class='A0101' align='center' >".$myRow['Forshort']."</td>";
		echo"<td class='A0101' align='center' >$OrderPO</td>";
		echo"<td class='A0101' align='center'>$sPOrderId</td>";
		echo"<td class='A0101'>$chineseName</td>";
		echo"<td class='A0101' align='right'>$Qty</td>";
		echo"<td class='A0101' align='right'>$scQty</td>";
		echo"<td class='A0101' >$Remark</td>";
		echo"<td class='A0101' >$scRemark</td>";
		echo"<td class='A0101' align='center'>$WorkShopName</td>";
		echo"<td class='A0101' align='center'  $UpdateClick>$UpdateIMG</td>";
		echo"</tr>";
		echo $ListRow;
		$j=$j+2;$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='$Count' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script language = "JavaScript">
function updateQty(){
	var ActionId = 2 ;
	var Id = document.getElementById("Id").value;
	var sPOrderId = document.getElementById("sPOrderId").value;
	var delQty = document.getElementById("delQty").value;
	var scQty = document.getElementById("scQty").value;
	if(delQty == "" || delQty ==0){
		alert("请填写减少的生产数量!");
		return false;
	}

	var message = "确定减少:"+delQty+"的生产数量?";
	if(confirm(message)){
	    var url="item3_5_ajax.php?Id="+Id+"&sPOrderId="+sPOrderId+"&delQty="+delQty+"&scQty="+scQty+"&ActionId="+ActionId;

		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				//更新该单元格内容
				if (ajax.responseText=="Y"){
			       closeWinDialog();
			       document.form1.submit();
				}else{
					alert ("生产记录更新失败！");
				}
			}
		}
	　	ajax.send(null);
	}
}


function deleteQty(){
	var ActionId = 1 ;
	var Id = document.getElementById("Id").value;
	var sPOrderId = document.getElementById("sPOrderId").value;
	var message = "确定删除此条生产记录?";
	if(confirm(message)){
	    var url="item3_5_ajax.php?Id="+Id+"&sPOrderId="+sPOrderId+"&ActionId="+ActionId;
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				//更新该单元格内容
				if (ajax.responseText=="Y"){
			       closeWinDialog();
			       document.form1.submit();
				}else{
					alert ("生产记录删除失败！");
				}
			}
		}
	　	ajax.send(null);
	}
}
</script>