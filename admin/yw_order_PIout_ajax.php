<?php   
//来自于生产登记页面
$subTableWidth=1815;
$Colsnum=18;
$tableStr="<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
			<td colspan='3'  height='20'></td>
			<td width='80' align='center'>采购日期</td>
			<td width='90' align='center'>待购流水号</td>
			<td width='330' align='center'>配件名称</td>				
			<td width='40' align='center'>图档</td>
            <td width='40' align='center'>QC图</td>
			<td width='40' align='center'>认证</td>
           <td width='40' align='center'>品检<br>报告</td>		
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
			<td width='55' align='center'>下单(<span class='redB'>锁</span>)</td>
			<td width='55' align='center'>采购</td>
			<td width='55' align='center'>仓库</td>
			<td width='55' align='center'>品检</td></tr>";
$mysql="SELECT * FROM (
         SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
		M.Date,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,MT.TypeColor,
        U.Name AS UnitName,K.tStockQty,(IFNULL(L.llQty,0)+K.tStockQty) AS TotaltStockQty
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
		LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
		LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
        LEFT JOIN (
                           SELECT StockId,SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet  WHERE 1 AND LEFT(StockId,12)='$POrderId' GROUP BY StockId
                             ) L  ON L.StockId=S.StockId
		WHERE S.POrderId='$POrderId'   AND ST.mainType<2 ORDER BY S.StockId ) A WHERE  A.TotaltStockQty-A.OrderQty<0";
//echo $mysql;
	$sListResult = mysql_query($mysql,$link_id);
	$k=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if ($StockRows = mysql_fetch_array($sListResult)) {
	echo $tableStr;
		do{
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化
			
			$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;$scQty="-";
			$OnclickStr="";
			$Mid=$StockRows["Mid"];
			$thisId=$StockRows["Id"];
			$StockId=$StockRows["StockId"];
            $ProductId=$StockRows["ProductId"];
			$Date=$StockRows["Date"];
			$StuffCname=$StockRows["StuffCname"];
			$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
			$Price=$StockRows["Price"];
			$Forshort=$StockRows["Forshort"];
			$Buyer=$StockRows["Name"];
			$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
			$BuyerId=$StockRows["BuyerId"];
			$OrderQty=$StockRows["OrderQty"];
			$StockQty=$StockRows["StockQty"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$DeliveryDate=$StockRows["DeliveryDate"];		
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
			$Gfile=$StockRows["Gfile"];
			$Gstate=$StockRows["Gstate"];  //状态
            $tStockQty=$StockRows["tStockQty"];  //状态
           //统计时间（下单，采购，品检，仓库）
           include "../model/subprogram/stuff_date.php";	
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
                        
            //配件QC检验标准图
            include "../model/subprogram/stuffimg_qcfile.php";
                         
            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH 法规图
		 //  include "../model/subprogram/stuffreach_file.php";
        
			if($FactualQty==0 && $AddQty==0 && $mainType!=3){
				$TempColor=3;			//绿色
				$Date="使用库存";
				$FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";//$Buyer="-";$Forshort="-";
				}
			else{
				if($Date==""){//未下采购单
					if($mainType<2){
						$TempColor=1;		//白色
						if($mainType==1){
							$Date="未下采购单";
							}
						else{
							$Date="客供产品";
							}
						}
					$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
					}
				else{//已下采购单
					$TempColor=3;		//绿色
					$ReceiveDate=$StockRows["ReceiveDate"];
					//收货情况				
					$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$Mantissa=$FactualQty+$AddQty-$rkQty;
						
						//可更新交期,如果当前浏览者的ID与采购的ID一致，则可以更新交期
					//if($BuyerId==){}
					if($DeliveryDate=="0000-00-00"){$DeliveryDate="-";}
					}
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
			///////////////////////////////////////////
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
			
			//备领料情况
			$llQty="-";$llBgColor="";$llEstate="";    $blorder="";
			$lockcolor='';
			$lockState=1;
			$lock="<div title='采购未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			   $lockRemark=$CheckSignRow["Remark"];
				$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
				$lockState=0;
				}    
			
			echo"<tr bgcolor='$theDefaultColor'>
			<td  bgcolor='$lockcolor' align='center' height='20' width='20' $OnclickStr >$lock</td>
            <td  align='center' width='20'>$showProcess</td>
			<td bgcolor='$Sbgcolor' align='center' width='15'>$k</td>";//配件状态 
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff>$StuffCname</td>";//配件名称
			echo"<td  align='center'>$Gfile</td>";//配件图档
            echo"<td  align='center'>$QCImage</td>";//QC图档
			echo"<td  align='center'>$ReachImage</td>";//REACH
            echo"<td  align='center'>$qualityReport</td>";//品检报告
			$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
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
		    echo"<td align='center' $OnclickStr>$DeliveryDate</td>";//供应商交货期$
		    echo"<td align='right' $XDRemark>$XDDate</td>";//下单
		    echo"<td align='right' $CGRemark>$CGDate</td>";//采购
		    echo"<td align='right' $CKRemark>$CKDate</td>";//仓库
		    echo"<td align='right' $PJRemark>$PJDate</td>";//品检
			echo"</tr>";
			$k++;
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
	echo"</table>";
?>