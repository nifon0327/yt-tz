<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
include "../model/stuffcombox_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=2150;
//来自于生产登记页面
$FromT=$FromT==0?"":$FromT;
if($FromT!=""){$subTableWidth=1360;}
             $Colsnum=25;

$CheckStockRow = mysql_fetch_array(mysql_query("SELECT level,POrderId,cgSign FROM $DataIn.cg1_stocksheet WHERE StockId ='$StockId'",$link_id));   
$POrderId =  $CheckStockRow["POrderId"];         
$level=  $CheckStockRow["level"]; 
$cgSign =  $CheckStockRow["cgSign"];            
$ordercolor=3;
//对备料时间进行排序
$blDateResult=mysql_query("SELECT DISTINCT S.StockId,S.Date,M.Name 
FROM $DataIn.ck5_llsheet S  
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Operator
WHERE S.POrderId='$POrderId' ORDER BY S.Date",$link_id);
if($blRow=mysql_fetch_array($blDateResult)){
$j=0;$k=1;
 do{
      $blStockId=$blRow["StockId"];
      $blName=$blRow["Name"];
      $blDate=$blRow["Date"];
      if($j==0){
            $ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);
      }
      else {
            if($tempDate==$blDate)$ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);
            else{$k++; $ValueArray[$j]=array(0=>$blStockId,1=> $blName,2=>$blDate,3=>$k);}
              }
     $tempDate=$blDate;
     $j++;
   }while($blRow=mysql_fetch_array($blDateResult));
}
 
	if($level == 1 && $cgSign==0){
	
            $MaxWeek  = date("Y")."99";
	        $CheckOrderRow = mysql_fetch_array(mysql_query("SELECT Y.OrderPO,T.Forshort,Y.PackRemark,
	        Y.sgRemark,Y.Qty,P.ProductId,P.cName,P.TestStandard,PI.Leadtime,
		    IFNULL(IFNULL(PI.Leadweek,PL.Leadweek),$MaxWeek) AS Leadweek
	        FROM $DataIn.yw1_ordersheet Y 
	        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber 
	        LEFT JOIN $DataIn.productdata P ON P.ProductId = Y.ProductId
	        LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId 
	        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
		    LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId = Y.POrderId
	        WHERE Y.POrderId ='$POrderId'",$link_id)); 
	        
	        $OrderPO=$CheckOrderRow["OrderPO"];
	        $Client=$CheckOrderRow["Forshort"];
	        $PackRemark=$CheckOrderRow["PackRemark"];
	        $sgRemark=$CheckOrderRow["sgRemark"];
	        $PQty=$CheckOrderRow["Qty"];
	        $ProductId=$CheckOrderRow["ProductId"];
	        $cName=$CheckOrderRow["cName"];
	        $TestStandard=$CheckOrderRow["TestStandard"];
		    include "../admin/Productimage/getPOrderImage.php";
		    $Leadtime=$CheckOrderRow["Leadtime"];
            $Leadweek=$CheckOrderRow["Leadweek"];
	        include "../model/subprogram/PI_Leadweek.php";
	        
      		echo "<table width='$subTableWidth' border='0' cellspacing='0' style='margin-left:30px;'>
			<tr bgcolor='#B7B7B7'>
			<td ><br> &nbsp;&nbsp;产品名称：<span>$TestStandard</span>&nbsp;&nbsp;订单PO：<span class='redB'>$OrderPO</span>&nbsp;&nbsp;订单流水号：<span class='redB'>$POrderId</span>&nbsp;&nbsp;客户：<span class='redB'>$Client</span>&nbsp;&nbsp;数量：<span class='redB'>$PQty </span>&nbsp;&nbsp;订单备注：<span class='redB'>$PackRemark</span>&nbsp;&nbsp;生管备注：<span class='redB'>$sgRemark</span>&nbsp;&nbsp;PI交期：$Leadweek_Span</td>
			</tr></table>";
		   $sListSql = "SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,
		    (S.OrderQty-IFNULL(SM.OrderQty,0)) AS OrderQty,S.StockQty,S.AddQty,S.FactualQty,
		    S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,M.Date,A.StuffCname,A.Picture,A.Gfile,
	        A.Gstate,A.TypeId,A.DevelopState,B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,
	        MT.TypeColor,MT.TitleName,MT.blSign,U.Name AS UnitName,U.Decimals ,K.tStockQty,A.DevelopState
			FROM $DataIn.cg1_stocksheet S
			LEFT JOIN $DataIn.cg1_stockmain  M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.stuffdata      A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype      ST ON ST.TypeId=A.TypeId
			LEFT JOIN $DataIn.stuffunit      U ON U.Id=A.Unit 
			LEFT JOIN $DataIn.stuffmaintype  MT ON MT.Id=ST.mainType
			LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
			LEFT JOIN $DataIn.staffmain      B ON B.Number=S.BuyerId
			LEFT JOIN $DataIn.trade_object   C ON C.CompanyId=S.CompanyId 
	        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
	        LEFT JOIN (SELECT StockId,IFNULL(SUM(OrderQty),0) AS OrderQty FROM cg1_semifinished 
	                    WHERE POrderId = $POrderId GROUP BY StockId
	                    ) SM ON SM.StockId = S.StockId
			WHERE S.POrderId='$POrderId'  AND S.Level=1 ORDER BY MT.SortId,S.StockId ";
		
	}else{
	
	   if ($cgSign==1){
		     	$CheckSemiRow = mysql_fetch_array(mysql_query("SELECT G.mStockId,D.Picture,D.StuffCname,(S.AddQty+ S.FactualQty) AS OrderQty , M.PurchaseID,S.DeliveryDate,S.DeliveryWeek,T.Forshort
		FROM $DataIn.cg1_semifinished G 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId = G.mStockId
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = S.Mid
		LEFT JOIN $DataIn.trade_object  T ON T.CompanyId = S.CompanyId  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId 
		WHERE G.mStockId ='$StockId'",$link_id));  
	   }else{
	     	$CheckSemiRow = mysql_fetch_array(mysql_query("SELECT G.mStockId,D.Picture,D.StuffCname,(S.AddQty+ S.FactualQty) AS OrderQty , M.PurchaseID,S.DeliveryDate,S.DeliveryWeek,T.Forshort
	FROM $DataIn.cg1_semifinished G 
	LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId = G.mStockId
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = S.Mid
	LEFT JOIN $DataIn.trade_object  T ON T.CompanyId = S.CompanyId  
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId 
	WHERE G.StockId ='$StockId'",$link_id));  
	   }
	
		$mStockId = $CheckSemiRow["mStockId"];
		$Picture = $CheckSemiRow["Picture"];
		$StuffCname = $CheckSemiRow["StuffCname"];
		include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性
		$PQty = $CheckSemiRow["OrderQty"];
		$PurchaseID = $CheckSemiRow["PurchaseID"];
		$DeliveryDate = $CheckSemiRow["DeliveryDate"];
		$DeliveryWeek = $CheckSemiRow["DeliveryWeek"];
		$Client =$CheckSemiRow["Forshort"];
		include "../model/subprogram/deliveryweek_toweek.php";
		
		echo "<table width='$subTableWidth' border='0' cellspacing='0' style='margin-left:30px;'>
			<tr bgcolor='#B7B7B7'>
			<td ><br> &nbsp;半成品名称:<span>$StuffCname</span> &nbsp;&nbsp;采购单号:<span class='redB'>$PurchaseID</span>&nbsp;&nbsp;采购流水号:<span class='redB'>$mStockId </span>&nbsp;&nbsp;加工单位:<span class='redB'>$Client</span>&nbsp;&nbsp;数量<span class='redB'>$PQty </span>&nbsp;&nbsp;交期:<span>$DeliveryWeek_span</span></td>
			</tr></table>";
		

		$sListSql="SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,A.OrderQty,S.StockQty,S.Price,
                    S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,
                    M.Date,D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.TypeId,D.DevelopState,
                    B.Name,C.Forshort,C.Currency,MP.Name AS Position,
                    ST.mainType,MT.TypeColor,MT.TitleName,MT.blSign,U.Name AS UnitName,U.Decimals,
                    K.tStockQty,D.DevelopState
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
		WHERE  A.mStockId='$mStockId'  ORDER BY MT.SortId,S.StockId ";
		
	}

      echo"<table id='$TableId' width='$subTableWidth' cellspacing='1' border='1' align='left' style='margin-left:30px;margin-top:20px;margin-bottom:30px;'><tr bgcolor='#CCCCCC'>
			<td colspan='3'  height='20'></td>
			<td width='80' align='center'>采购日期</td>
			<td width='50' align='center'>配件ID</td>
			<td width='100' align='center'>采购流水号</td>
			<td width='330' align='center'>配件名称</td>				
			<td width='40' align='center'>图档</td>
            <td width='40' align='center'>QC图</td>
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
			<td width='70' align='center'>已备料数</td>
			<td width='55' align='center'>生产数量</td>
			<td width='90' align='center'>交货期</td>
			<td width='70' align='center'>开发</td></tr>";	           
    $sListResult = mysql_query($sListSql,$link_id);		
	$i=1;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	if ($StockRows = mysql_fetch_array($sListResult)) {
	
		do{
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化	
			$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;$scQty="-";
			$OnclickStr="";
			$Mid=$StockRows["Mid"];
			$thisId=$StockRows["Id"];
			$StockId=$StockRows["StockId"];
			$Date=$StockRows["Date"];
			$StuffCname=$StockRows["StuffCname"];
			$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
			$Price=$StockRows["Price"];
			$CompanyId=$StockRows["CompanyId"];
			$Forshort=$StockRows["Forshort"];
			$Buyer=$StockRows["Name"];
			$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
			$BuyerId=$StockRows["BuyerId"];
			$OrderQty=$StockRows["OrderQty"];
			$StockQty=$StockRows["StockQty"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$DeliveryDate=$StockRows["DeliveryDate"];	
			$DeliveryWeek=$StockRows["DeliveryWeek"];
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
			$Gfile=$StockRows["Gfile"];
			$Gstate=$StockRows["Gstate"];  //状态
            $tStockQty=$StockRows["tStockQty"];  
	     	$Operator=$StockRows["Operator"];
	     	$OrderEstate=$StockRows["Estate"];
	     	$Decimals=$StockRows["Decimals"];
	     	
            //统计时间（下单，采购，品检，仓库）
          // include "../model/subprogram/stuff_date.php";	
		   include "../model/subprogram/stuffimg_Gfile.php"; //图档显示	
			//检查是否有图片
		   include "../model/subprogram/stuffimg_model.php";
           include"../model/subprogram/stuff_Property.php";//配件属性   
            //配件QC检验标准图
           include "../model/subprogram/stuffimg_qcfile.php";
                         
            //配件品检报告qualityReport
           include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH 法规图
		   include "../model/subprogram/stuffreach_file.php";
        
           $blSign=$StockRows["blSign"];
           $TitleName=$StockRows["TitleName"];
           if ($blSign==1){
	           if($FactualQty==0 && $AddQty==0){
		          $TempColor=3;			//绿色
				  $Date="使用库存";
				  $FactualQty="-";$AddQty="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";  $DeliveryWeek = "-";
	           }
	           else{
		           if ($Mid==0){ //未下采购单
			           $Date="未下采购单";
			           $rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";
		           }
		           else{
			          $TempColor=3;		//绿色
					  $ReceiveDate=$StockRows["ReceiveDate"];
					  //收货情况				
					  $rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					  $rkQty=mysql_result($rkTemp,0,"Qty");
					  $Mantissa=$FactualQty+$AddQty-$rkQty;
		           }
		           
		           if($DeliveryDate=="0000-00-00" || $DeliveryWeek=="0"){
					        $DeliveryDate="-";
					        $DeliveryWeek = "-";
					  }
					  else{
					     $DateShow_Style=1;
					     include "../model/subprogram/deliveryweek_toweek.php";
				  }
	           }
           }
           else{
	           $Date=$TitleName;
	           switch($mainType){
		           case 3:
		              //生产数量
						$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
							FROM $DataIn.sc1_cjtj S WHERE 1 AND S.StockId='$StockId'",$link_id); 
							$scQty=mysql_result($scSql,0,"scQty");												
						    $TempColor=$OrderQty==$scQty?3:2;
		                break;
		           case 5:
		                $OrderQty="-"; $StockQty="-"; $FactualQty="-";$AddQty="-";
		                $rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";
		                $Position="-";$Forshort="-";$Buyer="-";$tStockQty="-";
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
	
            if ($TypeId==$APP_CONFIG['REFUND_TYPE']) $theDefaultColor="#FFFF00";
            if ($ClientProSign==1) $theDefaultColor="#FFBBC9";
			///////////////////////////////////////////
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
			
			//备领料情况
			$llQty="-";$llBgColor="";$llEstate="";    $blorder="";
		  if($blSign==1) {	
             $blDateResult=mysql_fetch_array(mysql_query("SELECT S.Date,M.Name FROM $DataIn.ck5_llsheet S 
             LEFT JOIN $DataIn.staffmain M ON M.Number=S.Operator
             WHERE S.StockId='$StockId' ORDER BY S.Date Limit 1",$link_id));
            $blDate=substr($blDateResult["Date"],0,16);
            $blName=$blDateResult["Name"];
			 $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS llQty,
			 sum(case  when L.Estate=1 then L.Estate  else 0 end) as llEstate  
			 FROM $DataIn.ck5_llsheet L 
			 LEFT JOIN $DataIn.yw1_scsheet S ON S.sPOrderId = L.sPOrderId 
			 WHERE L.StockId='$StockId' AND S.Level = 1",$link_id));
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
             $showStr="&nbsp;"; $showTable="";
             
             //母配件显示子配件
             if($ComboxMainSign==1){
                   $ListId=getRandIndex();      
                   $showStr="<img onClick='ShowDropTable(ShowTable$ListId,ShowGif$ListId,ShowDiv$ListId,\"stuffcombox_pand_ajax\",\"$StockId|$StuffId\",\"admin\");' name='ShowGif$ListId' src='../images/showtable.gif' 
			title='显示或隐藏子配件资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' >";
			       $showTable="<tr id='ShowTable$ListId' style='display:none'><td colspan='$Colsnum'><div id='ShowDiv$ListId' width='720'></div></td></tr>";
             }
             else{
                 //半成品配件
                  $CheckSemiSql=mysql_query("SELECT * FROM $DataIn.cg1_semifinished G  WHERE G.mStockId ='$StockId' AND G.POrderId='$POrderId' LIMIT 1",$link_id);
                  if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
                      $ListId=getRandIndex();
                    
		              $showStr="<img onClick='ShowOrHideSemi(ShowTable_$ListId,ShowGif_$ListId,showStuffTB$ListId,\"$StockId\",$ListId,\"$NewSign\");' name='ShowGif_$ListId' src='../images/showtable.gif' 
				title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";
			         $showTable="<tr id='ShowTable_$ListId' style='display:none'><td colspan='$Colsnum'><div id='showStuffTB$ListId' width='$subTableWidth'>&nbsp;</div><br></td></tr>";
                  }
             } 
			$DevelopState=$StockRows["DevelopState"];  
            $DevelopStateStr="-";
	     	include "../model/subprogram/stuff_developstate.php";
		    
		    $OrderQty=round($OrderQty,$Decimals);
		    $StockQty=round($StockQty,$Decimals);
		    $FactualQty=round($FactualQty,$Decimals);
		    $AddQty=round($AddQty,$Decimals);
		    $tStockQty=round($tStockQty,$Decimals);
		    $llQty=round($llQty,$Decimals);
			echo"<tr bgcolor='$theDefaultColor'>
			<td  bgcolor='$lockcolor' align='center' height='20' width='20' $OnclickStr >$lock</td>
                        <td  align='center' width='20'>$showStr</td>
			<td bgcolor='$Sbgcolor' align='center' width='15'>$i</td>";//配件状态 
			echo"<td  align='center'>$Date</td>";
			echo"<td  align='center'>$StuffId</td>";//StuffId
			echo"<td  align='center'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff>$StuffCname</td>";//配件名称
			echo"<td  align='center'>$Gfile</td>";//配件图档
            echo"<td  align='center'>$QCImage</td>";//QC图档
			
			echo"<td  align='center'>$DevelopState</td>";//开发
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
			if($FromT==""){
				echo"<td align='right'>$rkQty</td>";//收货进度
				echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
				echo"<td align='right'  $llBgColor> $llEstate $llQty $blorder</td>";//领料数
				echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$scQty</div></td>";
				echo"<td align='center'>$DeliveryWeek</td>";//供应商交货期$OnclickStr
				echo"<td align='center'>$DevelopStateStr</td>";
               // echo"<td align='right' $XDRemark>$XDDate</td>";//下单
               // echo"<td align='right' $CGRemark>$CGDate</td>";//采购
               // echo"<td align='right' $CKRemark>$CKDate</td>";//仓库
              //  echo"<td align='right' $PJRemark>$PJDate</td>";//品检
			  }
			echo"</tr>";
            echo $showTable;
			$i++;
			}while ($StockRows = mysql_fetch_array($sListResult));
			
		}
	else{
		  echo"<tr><td height='30' cols='19'>记录异常，此订单没有发现需求记录. $Tid</td></tr>";
		}
?>
