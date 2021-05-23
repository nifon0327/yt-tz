<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
	echo"<table   cellspacing='1' border='1' align='center'  width='700px'>
	<tr bgcolor='#CCCCCC'>
	<td width='30' height='20'></td>
	<td width='120' align='center'>申领日期</td>
	<td width='70' align='center'>使用地点</td>
	<td width='50' align='center'>申领数量</td>
	<td width='250' align='center'>申领备注</td>
	<td width='70' align='center'>发放日期</td>
	<td width='60' align='center'>发放人</td>
	<td width='50' align='center'>状态</td></tr>";
	$sListResult = mysql_query("SELECT A.Id,A.GoodsId,A.WorkAdd,A.Qty,A.Remark,A.ReturnReasons,A.OutDate,A.Estate,A.Locks,A.Date,A.Operator,
B.GoodsName,B.BarCode,B.Attached,B.Unit,C.TypeName,D.wStockQty,D.oStockQty,D.mStockQty,E.Name AS WorkAdd,F.Name AS OutOperator,G.Name AS GetName
FROM $DataIn.nonbom8_outsheet A
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom0_ck  E ON E.Id=A.WorkAdd AND E.TypeId IN (0,2)
LEFT JOIN $DataPublic.staffmain F ON F.Number=A.OutOperator
LEFT JOIN $DataPublic.staffmain G ON G.Number=A.GetNumber
WHERE A.GoodsId=$GoodsId  AND A.GetNumber='$Login_P_Number' ORDER BY A.Date DESC",$link_id);
	$i=1;
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	if($ListRows = mysql_fetch_array($sListResult)) {
		do{
  		$Date=$ListRows["Date"];
		$OutOperator="<span class='redB'>未发放</span>";

       $OutDate=$ListRows["OutDate"]=="0000-00-00 00:00:00"?"&nbsp;":$ListRows["OutDate"];
		$TypeName=$ListRows["TypeName"];
		$GoodsId=$ListRows["GoodsId"];
		$GoodsName=$ListRows["GoodsName"];
		$Unit=$ListRows["Unit"];
		
		$Qty=del0($ListRows["Qty"]);
		$QtySum+=$Qty;
		$Remark=$ListRows["Remark"]==""?"&nbsp;":$ListRows["Remark"];
		$Operator=$ListRows["Operator"];
		include "../model/subprogram/staffname.php";
           switch($ListRows["Estate"]){
			case 1:
				$EstateStr="<span class='yellowB'>已审核</span>";
				break;
			case 2:
				$EstateStr="<span class='redB'>待审核</span>";
				break;
			break;
			case 3:
				$ReturnReasons=$myRow["ReturnReasons"]==""?"未填写退回原因":$myRow["ReturnReasons"];
			    $EstateStr="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
				break;
			case 0:
				 $EstateStr="<span class='greenB'>已发放</span>";
				 $OutOperator=$ListRows["OutOperator"];
				 $OutDate=substr($ListRows["OutDate"],0,10);
				break;
			}
        $GetName=$ListRows["GetName"];
		$Locks=$ListRows["Locks"];
		$WorkAdd=$ListRows["WorkAdd"];

			echo"<tr bgcolor='$theDefaultColor'><td  align='center' height='20'>$i</td>";
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$WorkAdd</td>";
			echo"<td align='center'>$Qty</td>";
			echo"<td >$Remark</td>";
			echo"<td  align='center'>$OutDate</td>";
			echo"<td align='center'>$OutOperator</td>";//仓库位置
			echo"<td  align='center'>$EstateStr</td>";
			echo"</tr>";
			$i++;
			}while ($ListRows = mysql_fetch_array($sListResult));	
		}
 echo "</table>";

$sListResult = mysql_query("SELECT C.BarCode,C.GoodsNum,K.Name AS rkName,C.Picture,C.Date,C.Estate
                           	FROM  $DataIn.nonbom7_code C 
                          	LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
                       	  WHERE  C.GoodsId=$GoodsId  AND C.Number='$Login_P_Number'  ",$link_id);
                        $i=1;
                if($ListRows= mysql_fetch_array($sListResult)){
                       echo "<br><br>";
	            	     echo"<table   cellspacing='1' border='1' align='center' width='700px'>
              	   	         <tr bgcolor='#CCCCCC'>
	           	              <td width='30' height='20'></td>
	            	          <td width='120' align='center'>固定条码</td>
	            	          <td width='150' align='center'>资产编号</td>
	           	              <td width='50' align='center'>状态</td></tr>";
                        do{
                                               $BarCode=$ListRows["BarCode"];
                                               $GoodsNum=$ListRows["GoodsNum"];
                                               $rkName=$ListRows["rkName"];
                                               $Picture=$ListRows["Picture"];
                                               $Date=$ListRows["Date"];
                                               $Estate=$ListRows["Estate"];
                                               $Remark=$ListRows["Remark"];
                                               $Picture=$ListRows["Picture"];
            $PictureStr="";
            if($Picture!="") {
               $DirCode=anmaIn("download/nonbomCode/",$SinkOrder,$motherSTR);
			      $Picture=anmaIn($Picture,$SinkOrder,$motherSTR);
                $BarCodeStr="<span onClick='OpenOrLoad(\"$DirCode\",\"$Picture\")'  style='CURSOR: pointer;color:#FF6633'>$BarCode</span>";
              }
           else  $BarCodeStr=$BarCode;
                                            switch($Estate){
                                                       case "0":  $EstateStr="<span class='redB'>报废</span>";break;
                                                       case "1":  $EstateStr="<span class='greenB'>在库</span>";   break;
                                                       case "2":  $EstateStr="<span class='blueB'>领用</span>";  break;
                                                }
                                               echo"<tr bgcolor='$theDefaultColor'>";
                                               echo"<td height='20' align='center' >$i</td>";
                                               echo"<td  align='center' >$BarCodeStr</td>";
                                               echo"<td  align='center' >$GoodsNum</td>";
                                              // echo"<td   >$Remark</td>";
                                               ///echo"<td  align='center' >$Date</td>";
                                               echo"<td align='center' >$EstateStr</td>";
                                               echo"</tr>";
                                         $i++;
                       }  while($ListRows= mysql_fetch_array($sListResult));
    }
echo "</table>";
?>