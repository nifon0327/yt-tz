<?php
$POrderId = substr($mStockId, 0, 12);
/*$searchTimes = 1;
$tempMStockId = $mStockId;
$tempArray = array();

while($searchTimes){
	
	$checkResult = mysql_fetch_array(mysql_query("SELECT mStockId FROM $DataIn.cg1_semifinished WHERE StockId = $tempMStockId",$link_id));
	$thisMStockId = $checkResult["mStockId"];
	if($thisMStockId>0){
	    $tempMStockId = $thisMStockId;
		$searchTimes = 1 ;
		$tempArray[] = $tempMStockId;
	}else{
		$searchTimes = 0 ;
	}
}

if(count($tempArray)>0){
	$mStockId = implode(",", $tempArray);
}*/


$mListSql = "SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,SUM(A.OrderQty) AS OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,
M.Date,D.StuffCname,
D.Picture,D.Gfile,D.Gstate,D.TypeId,D.DevelopState,B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,MT.TypeColor,MT.TitleName,MT.blSign,U.Name AS UnitName,K.tStockQty,D.DevelopState
FROM  $DataIn.cg1_semifinished   A 
LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StockId   = A.StockId
LEFT JOIN $DataIn.cg1_stockmain  M  ON M.Id        = S.Mid 
LEFT JOIN $DataIn.stuffdata      D  ON D.StuffId   = S.StuffId 
LEFT JOIN $DataIn.stufftype      ST ON ST.TypeId   = D.TypeId
LEFT JOIN $DataIn.stuffunit      U  ON U.Id        = D.Unit 
LEFT JOIN $DataIn.stuffmaintype  MT ON MT.Id       = ST.mainType
LEFT JOIN $DataIn.base_mposition MP ON MP.Id       = ST.Position 
LEFT JOIN $DataIn.staffmain      B  ON B.Number    = S.BuyerId 
LEFT JOIN $DataIn.trade_object   C  ON C.CompanyId = S.CompanyId 
LEFT JOIN $DataIn.ck9_stocksheet K  ON K.StuffId   = D.StuffId 
WHERE A.POrderId='$POrderId' AND  A.mStockId NOT IN ($mStockId) AND getStockIdContainSign('$mStockId',A.StockId)>0 AND ST.mainType!='" .$APP_CONFIG['WORKORDER_ACTION_MAINTYPE'] . "' AND MT.blSign=1 GROUP BY A.StockId  ORDER BY MT.SortId DESC,S.StockId ";
//echo $mListSql;
$mListResult = mysql_query($mListSql,$link_id);
    $i=1;
	$tId=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if ($mStockRows = mysql_fetch_array($mListResult)) {
	    $TableId="ListSubTB".$RowId;
        echo"<table id='ListSubTBm' width='$subTableWidth' cellspacing='1' border='1' align='left' style='margin-left:60px;margin-top:20px;margin-bottom:20px;'><tr bgcolor='#CCCCCC'>
			<td colspan='3'  height='20'></td>
			<td width='80' align='center'>????????????</td>
			<td width='90' align='center'>???????????????</td>
			<td width='330' align='center'>????????????</td>				
			<td width='40' align='center'>??????</td>
			<td width='40' align='center'>??????</td>
            <td width='40' align='center'>??????</td>		
			<td width='55' align='center'>????????????</td>
			<td width='55' align='center'>????????????</td>
			<td width='40' align='center'>??????</td>
			<td width='55' align='center'>????????????</td>
			<td width='55' align='center'>????????????</td>
			<td width='55' align='center'>????????????</td>
			<td width='55' align='center'>????????????</td>
            <td width='55' align='center'>??????</td>
            <td width='55' align='center'>??????</td>
			<td width='125' align='center'>?????????</td>
			<td width='55' align='center'>????????????</td>
			<td width='55' align='center'>??????</td>
			<td width='60' align='center'>????????????</td>
			<td width='55' align='center'>????????????</td>
			<td width='90' align='center'>?????????</td>
			<td width='70' align='center'>??????</td>
			<td width='55' align='center'>??????(<span class='redB'>???</span>)</td>
			<td width='55' align='center'>??????</td>
			<td width='55' align='center'>??????</td>
			<td width='55' align='center'>??????</td></tr>";
			/*
			<td width='40' align='center'>QC???</td>
			<td width='40' align='center'>??????</td>
			*/
		do{
			//??????	0??????	1??????	2??????	3??????
			//?????????	
			$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;$scQty="-";
			$OnclickStr="";
			$Mid=$mStockRows["Mid"];
			$thisId=$mStockRows["Id"];
			$StockId=$mStockRows["StockId"];
            $ProductId=$mStockRows["ProductId"];
			$Date=$mStockRows["Date"];
            $OrderDate=$mStockRows["OrderDate"];
			$StuffCname=$mStockRows["StuffCname"];
			$Position=$mStockRows["Position"]==""?"?????????":$mStockRows["Position"];
			$Price=$mStockRows["Price"];
			$Forshort=$mStockRows["Forshort"];
			$Buyer=$mStockRows["Name"];
			$UnitName=$mStockRows["UnitName"]==""?"&nbsp;":$mStockRows["UnitName"];
			$BuyerId=$mStockRows["BuyerId"];
			$OrderQty=$mStockRows["OrderQty"];
			$StockQty=$mStockRows["StockQty"];
			$FactualQty=$mStockRows["FactualQty"];
			$AddQty=$mStockRows["AddQty"];
			$DeliveryDate=$mStockRows["DeliveryDate"];	
			$DeliveryWeek=$mStockRows["DeliveryWeek"];		
			$StuffId=$mStockRows["StuffId"];
			$Picture=$mStockRows["Picture"];
			$TypeId=$mStockRows["TypeId"];
			$mainType=$mStockRows["mainType"];
			$TypeColor=$mStockRows["TypeColor"];
			$Currency=$mStockRows["Currency"];
			$Gfile=$mStockRows["Gfile"];
			$Gstate=$mStockRows["Gstate"];  //??????
            $tStockQty=$mStockRows["tStockQty"];  
	     	$Operator=$mStockRows["Operator"];
	     	$OrderEstate=$mStockRows["Estate"];
            //???????????????????????????????????????????????????
            include "../model/subprogram/stuff_date.php";	
			include "../model/subprogram/stuffimg_Gfile.php";	//????????????	
			//?????????????????????
			include "../model/subprogram/stuffimg_model.php";
            include"../model/subprogram/stuff_Property.php";//????????????   
            //??????QC???????????????
            include "../model/subprogram/stuffimg_qcfile.php";
                         
            //??????????????????qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH ?????????
		    include "../model/subprogram/stuffreach_file.php";
        
		    $blSign=$mStockRows["blSign"];
            $TitleName=$mStockRows["TitleName"];
            if($blSign==1){
	           if($FactualQty==0 && $AddQty==0){
		          $TempColor=3;			//??????
				  $Date="????????????";
				  $FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate=$DeliveryWeek="-";  
				  
	           }
	           else{
		           if ($Mid==0){ //???????????????
			           $Date="???????????????";
			           $rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";
		           }
		           else{
			          $TempColor=3;		//??????
					  $ReceiveDate=$mStockRows["ReceiveDate"];
					  //????????????				
					  $rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					  $rkQty=mysql_result($rkTemp,0,"Qty");
					  $Mantissa=$FactualQty+$AddQty-$rkQty;
		           }
		           if($DeliveryWeek>0){
						  include "../model/subprogram/deliveryweek_toweek.php";
				   } 
	           }
            }
            else{
	           $Date=$TitleName;
	           switch($mainType){
		           case 3:
		              //????????????
						$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
							FROM $DataIn.sc1_cjtj S
							LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
							LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId 
							WHERE 1 AND G.StockId='$StockId' AND D.TypeId=S.TypeId",$link_id); 
							$scQty=mysql_result($scSql,0,"scQty");												
						    $TempColor=$OrderQty==$scQty?3:2;
		                break;
		           case 5:
		                $OrderQty="-"; $StockQty="-"; $FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";
		                $DeliveryDate=$DeliveryWeek="-";$Position="-";$Forshort="-";$Buyer="-";$tStockQty="-";
		                $TempColor=3;		//??????
		                break;
		          default:
		                 $TempColor=3;		//??????
		                break;
	           }
	           $Position="-";
			   $Forshort="-";$Buyer="-";
            }
			//??????????????????
			switch($TempColor){
				case 1://??????
					$Sbgcolor="#FFFFFF";
					$ordercolor="#0099FF";
					break;
				case 2://??????
					$Sbgcolor="#FFCC00";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
				case 3://??????
					$Sbgcolor="#339900";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
					}
			//??????????????????
			$theDefaultColor=$TypeColor;
            if ($TypeId=='9104') $theDefaultColor="#FFFF00";
            if ($ClientProSign==1) $theDefaultColor="#FFBBC9";
			//??????????????????
			include "../model/subprogram/cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
			
			//???????????????
			$llQty="-";$llBgColor="";$llEstate="";    $blorder="";
		    if($blSign==1) {	
               $blDateResult=mysql_fetch_array(mysql_query("SELECT S.Date,M.Name FROM $DataIn.ck5_llsheet S 
               LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Operator
               WHERE S.StockId='$StockId' ORDER BY S.Date Limit 1",$link_id));
               $blDate=substr($blDateResult["Date"],0,16);
               $blName=$blDateResult["Name"];
			   $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id));
		       $llQty=$checkllQty["llQty"];
			   $llQty=$llQty==""?0:$llQty;
			   if($llQty>$OrderQty){//???????????????????????????,????????????
				    $llBgColor=" style='color:#FF0000;font-weight: bold;' title='????????????:$blDate,?????????:$blName'";
				}
				else{
					if($llQty==$OrderQty){//?????????????????????
						$llBgColor=" style='color:#009900;font-weight: bold;'  title='????????????:$blDate,?????????:$blName'";
					}
					else{				//??????????????????
						$llBgColor=" style='color:#FF6633;font-weight: bold;'";
					}
				}

				$llEstate=$checkllQty["llEstate"];
				$llEstate=$llEstate>0?"???":"";
	            for($k=0;$k<count($ValueArray);$k++){//??????????????????
	                   if($ValueArray[$k][0]==$StockId && $ValueArray[$k][3]!=""){
	                            $blorder="(". $ValueArray[$k][3].")"; break;
	                    }
	            }
		    }
			//?????????????????? 
			$lockcolor=''; $lockState=1;
			$lock="<div title='???????????????' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			   $lockRemark=$CheckSignRow["Remark"];
				$lock="<div style='background-color:#FF0000' title='??????:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
				$lockState=0;
			  }
            $OnclickStr="onclick='updateLock(\"$TableId\",$i,$StockId,$lockState)' style='CURSOR: pointer;'";           
     
            //??????BOM
            $showMaterialStr="&nbsp;"; $showMaterialTable="";
            $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.cg1_semifinished A  WHERE A.mStockId='$StockId' LIMIT 1",$link_id);
           if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
                $showMaterialStr="<img onClick='ShowDropTable(ShowMaterialTable$RowId$i,ShowMaterialTable$tId,ShowMaterialDiv$RowId$i,\"semifinished_order_ajax\",\"$StockId|$RowId$i\",\"admin\");'  src='../images/showtable.gif' 
			title='?????????????????????????????????.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='ShowMaterialTable$tId'>";
			    $showMaterialTable="<tr id='ShowMaterialTable$RowId$i' style='display:none'><td colspan='31'><div id='ShowMaterialDiv$RowId$i' width='$subTableWidth'></div></td></tr>";   
			    $tId++;
            }



			
			$DevelopState=$mStockRows["DevelopState"];  
            $DevelopStateStr="-";
	     	include "../model/subprogram/stuff_developstate.php";
		    $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>??????</a>";


			echo"<tr bgcolor='$theDefaultColor'><td  bgcolor='$lockcolor' align='center' height='20' width='20' $OnclickStr >$lock</td>
                 <td  align='center' width='20'>$showMaterialStr</td><td bgcolor='$Sbgcolor' align='center' width='15'>$i</td>";//???????????? 
            if ($DeliveryWeek=="0") $DeliveryWeek="-";
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$StockId</td>";//?????????????????????
			echo"<td  width='330'>$StuffCname</td>";//????????????
			echo"<td  align='center'>$Gfile</td>";//????????????
           // echo"<td  align='center'>$QCImage</td>";//QC??????
			//echo"<td  align='center'>$ReachImage</td>";//REACH
			echo"<td  align='center'>$DevelopState</td>";//??????
            echo"<td  align='center'>$qualityReport</td>";//????????????
			echo"<td  align='center'>$OrderQtyInfo</td>";//??????????????????
			echo"<td align='right'>$Price</td>";//????????????
			echo"<td  align='center'>$UnitName</td>";//??????
			echo"<td align='right'>$OrderQty</td>";//??????????????????
			echo"<td align='right'>$StockQty</td>";//???????????????
			echo"<td align='right'>$FactualQty</td>";//????????????
			echo"<td align='right'>$AddQty</td>";//????????????
            echo"<td align='right'>$tStockQty</td>";//??????
            echo"<td  align='center'>$Buyer</td>";//?????????
		    echo"<td >$Forshort</td>";//?????????
			echo"<td align='right'>$rkQty</td>";//????????????
			echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//??????
			echo"<td align='right'  $llBgColor> $llEstate $llQty $blorder</td>";//?????????
			echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$scQty</div></td>";
			echo"<td align='center'>$DeliveryWeek</td>";//??????????????????$OnclickStr
			echo"<td align='center'>$DevelopStateStr</td>";
            echo"<td align='right' $XDRemark>$XDDate</td>";//??????
            echo"<td align='right' $CGRemark>$CGDate</td>";//??????
            echo"<td align='right' $CKRemark>$CKDate</td>";//??????
            echo"<td align='right' $PJRemark>$PJDate</td>";//??????
			echo"</tr>";
            echo $showMaterialTable;
			$i++;
			}while ($mStockRows = mysql_fetch_array($mListResult));
			 echo"</table>";
		}
             
?>