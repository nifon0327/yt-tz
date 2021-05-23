<?php
$mListResult = mysql_query("SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,SUM(A.OrderQty) AS OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,M.Date,D.StuffCname,
D.Picture,D.Gfile,D.Gstate,D.TypeId,D.DevelopState,B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,MT.TypeColor,MT.TitleName,MT.blSign,U.Name AS UnitName,K.tStockQty,D.DevelopState,U.Decimals, 
GROUP_CONCAT(A.ParentNode) AS  ParentNode 
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
WHERE   A.POrderId='$POrderId' AND A.mStockId!='$mStockId' AND  FIND_IN_SET('$mStockId',A.ParentNode)>0  AND ST.mainType NOT IN (2,3) GROUP BY A.StockId ORDER BY MT.SortId,S.StockId ",$link_id);//getStockIdContainSign('$mStockId',A.StockId)>0
    $i=1;
	$tId=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if ($mStockRows = mysql_fetch_array($mListResult)) {
	    $TableId="ListSubTB".$RowId;
        echo"<table id='ListSubTBm' width='$subTableWidth' cellspacing='1' border='1' align='left' style='margin-left:60px;margin-top:20px;margin-bottom:20px;'><tr bgcolor='#CCCCCC'>
			<td colspan='3'  height='20'></td>
			<td width='80' align='center'>采购日期</td>
			<td width='90' align='center'>采购流水号</td>
			<td width='330' align='center'>配件名称</td>				
			<td width='40' align='center'>图档</td>
			<td width='40' align='center'>开发</td>
            <td width='40' align='center'>品检</td>		
			<td width='55' align='center'>历史订单</td>
			<td width='55' align='center'>配件价格</td>
			<td width='40' align='center'>单位</td>
			<td width='55' align='center'>订单数量</td>
			<td width='55' align='center'>已用库存</td>
			<td width='55' align='center'>需购数量</td>
			<td width='55' align='center'>增购数量</td>
            <td width='55' align='center'>在库</td>
            <td width='55' align='center'>采购</td>
			<td width='125' align='center'>供应商</td>
			<td width='55' align='center'>收货数量</td>
			<td width='55' align='center'>欠数</td>
			<td width='60' align='center'>已备料数</td>
			<td width='55' align='center'>生产数量</td>
			<td width='90' align='center'>交货期</td>
			<td width='70' align='center'>开发</td>
			<td width='55' align='center'>下单(<span class='redB'>锁</span>)</td>
			<td width='55' align='center'>采购</td>
			<td width='55' align='center'>仓库</td>
			<td width='55' align='center'>品检</td></tr>";
		do{
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化	
			$rkQty=0;		$thQty=0;		$bcQty=$Mantissa=0;		$llQty=0;$scQty="-";
			$OnclickStr="";
			$Mid=$mStockRows["Mid"];
			$thisId=$mStockRows["Id"];
			$StockId=$mStockRows["StockId"];
            $ProductId=$mStockRows["ProductId"];
			$Date=$mStockRows["Date"];
            $OrderDate=$mStockRows["OrderDate"];
			$StuffCname=$mStockRows["StuffCname"];
			$Position=$mStockRows["Position"]==""?"未设置":$mStockRows["Position"];
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
			$StuffId=$mStockRows["StuffId"];
			$Picture=$mStockRows["Picture"];
			$TypeId=$mStockRows["TypeId"];
			$mainType=$mStockRows["mainType"];
			$TypeColor=$mStockRows["TypeColor"];
			$Currency=$mStockRows["Currency"];
			$Gfile=$mStockRows["Gfile"];
			$Gstate=$mStockRows["Gstate"];  //状态
            $tStockQty=$mStockRows["tStockQty"];  
	     	$Operator=$mStockRows["Operator"];
	     	$OrderEstate=$mStockRows["Estate"];
	     	$Decimals=$mStockRows["Decimals"];
	     	$ParentNode =$mStockRows["ParentNode"]; 
            //统计时间（下单，采购，品检，仓库）
            include "../model/subprogram/stuff_date.php";	
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
            include"../model/subprogram/stuff_Property.php";//配件属性   
            //配件QC检验标准图
            include "../model/subprogram/stuffimg_qcfile.php";
                         
            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH 法规图
		    include "../model/subprogram/stuffreach_file.php";
        
		    $blSign=$mStockRows["blSign"];
            $TitleName=$mStockRows["TitleName"];
            if($blSign==1){
	           if($FactualQty==0 && $AddQty==0){
		          $TempColor=3;			//绿色
				  $Date="使用库存";
				  $FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";  
	           }
	           else{
		           if ($Mid==0){ //未下采购单
			           $Date="未下采购单";
			           $rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
		           }
		           else{
			          $TempColor=3;		//绿色
					  $ReceiveDate=$mStockRows["ReceiveDate"];
					  //收货情况				
					  $rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					  $rkQty=mysql_result($rkTemp,0,"Qty");
					  $Mantissa=round($FactualQty+$AddQty-$rkQty,$Decimals);
						
					 if($DeliveryDate=="0000-00-00"){
					        $DeliveryDate="-";
					  }
					  else{
					        $DateShow_Style=1;
					        include "../model/subprogram/CG_DeliveryDate.php";
					  }

		           }
	           }
            }
            else{
	           $Date=$TitleName;
	           switch($mainType){
		           case 3:
		              //生产数量
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
		                $DeliveryDate="-";$Position="-";$Forshort="-";$Buyer="-";$tStockQty="-";
		                $TempColor=3;		//绿色
		                break;
		          default:
		                 $TempColor=3;		//绿色
		                break;
	           }
	           $Position="-";
			   $Forshort="-";$Buyer="-";$DeliveryDate="-";
            }
			//采单颜色标记
			switch($TempColor){
				case 1://白色
					$Sbgcolor="#FFFFFF";
					$ordercolor="#0099FF";
					break;
				case 2://黄色
					$Sbgcolor="#FFCC00";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
				case 3://绿色
					$Sbgcolor="#339900";
					$ordercolor=$TempColor<$ordercolor?$TempColor:$ordercolor;
					break;
					}
			//配件分类颜色
			$theDefaultColor=$TypeColor;
            if ($TypeId=='9104') $theDefaultColor="#FFFF00";
            if ($ClientProSign==1) $theDefaultColor="#FFBBC9";
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
			
			//备领料情况
			$llQty="-";$llBgColor="";$llEstate="";    $blorder="";
		    if($blSign==1) {	
               $blDateResult=mysql_fetch_array(mysql_query("SELECT S.Date,M.Name 
		       FROM $DataIn.ck5_llsheet S 
		       LEFT JOIN $DataIn.yw1_scsheet C ON C.sPOrderId = S.sPOrderId 
		       LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Operator
		       WHERE S.StockId='$StockId' AND C.mStockId='$mStockId' ORDER BY S.Date Limit 1",$link_id));
		       $blDate=substr($blDateResult["Date"],0,16);
		       $blName=$blDateResult["Name"];
		       
		       //if ($Login_P_Number==10868 && $StockId=='201605280045102') echo $ParentNode;
			   $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS llQty,sum(case  when S.Estate=1 then S.Estate  else 0 end) as llEstate  
			   FROM $DataIn.ck5_llsheet S 
			   LEFT JOIN $DataIn.yw1_scsheet C ON C.sPOrderId = S.sPOrderId 
			   WHERE S.StockId='$StockId'  AND  FIND_IN_SET(C.mStockId,'$ParentNode')>0  ",$link_id));
			   
		       $llQty=$checkllQty["llQty"];
			   $llQty=$llQty==""?0:$llQty;
			   if($llQty>$OrderQty){//领料总数大于订单数,提示出错
				    $llBgColor=" style='color:#FF0000;font-weight: bold;' title='备料时间:$blDate,备料人:$blName'";
				}
				else{
					if($llQty==$OrderQty){//刚好全领，绿色
						$llBgColor=" style='color:#009900;font-weight: bold;'  title='备料时间:$blDate,备料人:$blName'";
					}
					else{				//未领完，黄色
						$llBgColor=" style='color:#FF6633;font-weight: bold;'";
					}
				}

				$llEstate=$checkllQty["llEstate"];
				$llEstate=$llEstate>0?"★":"";
	            for($k=0;$k<count($ValueArray);$k++){//备料时间排序
	                   if($ValueArray[$k][0]==$StockId && $ValueArray[$k][3]!=""){
	                            $blorder="(". $ValueArray[$k][3].")"; break;
	                    }
	            }
		    }
			//检查是否锁定 
			$lockcolor=''; $lockState=1;
			$lock="<div title='采购未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			   $lockRemark=$CheckSignRow["Remark"];
				$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
				$lockState=0;
			  }
            $OnclickStr="onclick='updateLock(\"$TableId\",$i,$StockId,$lockState)' style='CURSOR: pointer;'";           
     
            //二级BOM
            $showMaterialStr="&nbsp;"; $showMaterialTable="";
		    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.cg1_semifinished A  WHERE A.mStockId='$StockId' LIMIT 1",$link_id);  
		    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
		        //显示或隐藏bom
		        $ajaxFile="semifinishedbom_ajax";
		        $ajaxDir="admin";
		        
		        if (in_array($APP_CONFIG['PT_CUT_PROPERTY'],$StuffPropertys)){
			        $ajaxFile="slicebom_ajax";
			        $ajaxDir="pt";
		        }
		        
		        $ShowId= $StockId . getRandIndex();
		        $ShowBomImageId= "Material_StuffImage_" . $ShowId;
		        $ShowBomTableId= "Material_StuffTable_" . $ShowId;
		        $ShowBomDivId  = "Material_StuffDiv_" . $ShowId;
		        
		        $showMaterialStr = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$StockId|$ShowId\",\"$ajaxDir\");'  src='../images/showtable.gif' 
			title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";
			    $showMaterialTable = "<tr id='$ShowBomTableId' style='display:none'><td colspan='31'><div id='$ShowBomDivId' width='$subTableWidth'></div></td></tr>"; 
			    $tId++;  
		    }

			
			$DevelopState=$mStockRows["DevelopState"];  
            $DevelopStateStr="-";
	     	include "../model/subprogram/stuff_developstate.php";
		    $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";


			echo"<tr bgcolor='$theDefaultColor'><td  bgcolor='$lockcolor' align='center' height='20' width='20' $OnclickStr >$lock</td>
                 <td  align='center' width='20'>$showMaterialStr</td><td bgcolor='$Sbgcolor' align='center' width='15'>$i</td>";//配件状态 
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$StockId</td>";//配件采购流水号
			echo"<td  width='330'>$StuffCname</td>";//配件名称
			echo"<td  align='center'>$Gfile</td>";//配件图档
           // echo"<td  align='center'>$QCImage</td>";//QC图档
			//echo"<td  align='center'>$ReachImage</td>";//REACH
			echo"<td  align='center'>$DevelopState</td>";//开发
            echo"<td  align='center'>$qualityReport</td>";//品检报告
			echo"<td  align='center'>$OrderQtyInfo</td>";//历史订单分析
			echo"<td align='right'>$Price</td>";//配件价格
			echo"<td  align='center'>$UnitName</td>";//单位
			echo"<td align='right'>$OrderQty</td>";//订单需求数量
			echo"<td align='right'>$StockQty</td>";//使用库存数
			echo"<td align='right'>$FactualQty</td>";//采购数量
			echo"<td align='right'>$AddQty</td>";//增购数量
            echo"<td align='right'>$tStockQty</td>";//在库
            echo"<td  align='center'>$Buyer</td>";//采购员
		    echo"<td >$Forshort</td>";//供应商
			echo"<td align='right'>$rkQty</td>";//收货进度
			echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
			echo"<td align='right'  $llBgColor> $llEstate $llQty $blorder</td>";//领料数
			echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$scQty</div></td>";
			echo"<td align='center'>$DeliveryDate</td>";//供应商交货期$OnclickStr
			echo"<td align='center'>$DevelopStateStr</td>";
            echo"<td align='right' $XDRemark>$XDDate</td>";//下单
            echo"<td align='right' $CGRemark>$CGDate</td>";//采购
            echo"<td align='right' $CKRemark>$CKDate</td>";//仓库
            echo"<td align='right' $PJRemark>$PJDate</td>";//品检
			echo"</tr>";
            echo $showMaterialTable;
			$i++;
			}while ($mStockRows = mysql_fetch_array($mListResult));
			 echo"</table>";
		}
             
?>