<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "../model/stuffcombox_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1350;
//来自于生产登记页面

/*echo "SELECT  Id FROM $DataIn.cg1_semifinished WHERE mStockId = '$StockId' AND POrderId ='$POrderId' limit 1 ";*/

//查找他的半成品，没有半成品显示一级，有则显示当前半成品那一级
$checkSemiRow = mysql_fetch_array(mysql_query("SELECT  mStockId FROM $DataIn.cg1_semifinished WHERE StockId = '$StockId' AND POrderId ='$POrderId' limit 1 ",$link_id));

$mStockId= $checkSemiRow["mStockId"];
if($mStockId>0){
	
		$slistSql = "SELECT 	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,
S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,M.Date,
A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,
C.Forshort,C.Currency,MP.Remark AS Position,ST.mainType,MT.TypeColor ,U.Name AS UnitName,K.tStockQty 
	FROM $DataIn.cg1_semifinished  SM 
	LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StockId = SM.StockId
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
	LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
	LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
	LEFT JOIN $DataIn.base_mposition MP ON MP.Id=A.SendFloor 
	LEFT JOIN $DataIn.staffmain B ON B.Number=S.BuyerId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
	WHERE SM.mStockId='$mStockId'  ORDER BY S.StockId";	
	
	

$checkSmeiName=mysql_fetch_array(mysql_query("SELECT  (G.AddQty+G.FactualQty) AS cgQty,G.StuffId,D.StuffCname,D.Picture,G.DeliveryDate,G.DeliveryWeek  
        FROM $DataIn.cg1_stocksheet  G 
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
        WHERE G.StockId='$mStockId'",$link_id));
$mStuffId=$checkSmeiName["StuffId"];
$StuffCname=$checkSmeiName["StuffCname"];
$Picture=$checkSmeiName["Picture"];
include "../model/subprogram/stuffimg_model.php";
$mStuffCname =$StuffCname;
$DeliveryDate=$checkSmeiName["DeliveryDate"];
$DeliveryWeek=$checkSmeiName["DeliveryWeek"];
$cgQty=$checkSmeiName["cgQty"];
include "../model/subprogram/deliveryweek_toweek.php";

$POrderSTR="<span class='redB'>半成品名称: </span>$mStuffCname&nbsp;<span class='redB'>采购流水号: </span>$mStockId&nbsp;<span class='redB'>数量:</span>$cgQty&nbsp; <span class='redB'><span class='redB'>交期:</span>$DeliveryWeek&nbsp; <span class='redB'>";

	
}else{
	
	$slistSql = "SELECT 	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,
S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,M.Date,
A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,
C.Forshort,C.Currency,MP.Remark AS Position,ST.mainType,MT.TypeColor ,U.Name AS UnitName,K.tStockQty 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
	LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
	LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
	LEFT JOIN $DataIn.base_mposition MP ON MP.Id=A.SendFloor 
	LEFT JOIN $DataIn.staffmain B ON B.Number=S.BuyerId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
	WHERE S.POrderId='$POrderId' AND S.Level = 1 ORDER BY S.StockId";
	
	
	$checkProduct=mysql_fetch_array(mysql_query("SELECT Y.ProductId,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,PI.Leadtime,PI.Leadweek,C.Forshort AS Client,P.cName,P.TestStandard    
        FROM $DataIn.yw1_ordersheet Y
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
        LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
        WHERE Y.POrderId='$POrderId'",$link_id));
$ProductId=$checkProduct["ProductId"];
$OrderPO=$checkProduct["OrderPO"];
$PQty=$checkProduct["PQty"];
$Client=$checkProduct["Client"];
$cName=$checkProduct["cName"];
$TestStandard=$checkProduct["TestStandard"];
include "../admin/Productimage/getProductImage.php";

$PackRemark=$checkProduct["PackRemark"]==""?"--":$checkProduct["PackRemark"];
$sgRemark=$checkProduct["sgRemark"]==""?"--":$checkProduct["sgRemark"];
$ShipType=$checkProduct["ShipType"]==""?"--":$checkProduct["ShipType"];
$Leadtime=$checkProduct["Leadtime"]=="0000-00-00"?"--":$checkProduct["Leadtime"];
$Leadweek=$checkProduct["Leadweek"]=="0000-00-00"?"--":$checkProduct["Leadweek"];
include "../model/subprogram/PI_Leadweek.php";

$POrderSTR="<span class='redB'>PO:</span>$OrderPO&nbsp;<span class='redB'>业务单流水号: </span>$POrderId($Client : $TestStandard)&nbsp;<span class='redB'>数量:</span>$PQty&nbsp; <span class='redB'><span class='redB'>交期:</span>$Leadweek_Span&nbsp; <span class='redB'>";
}





echo"<table id='$TableId'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'>
    <tr  bgcolor='#D9D9D9' height='20'><td colspan='15'>$POrderSTR</td></tr>
    <tr>
	<td width='35' height='20' class='A1111'></td>
	<td width='70' align='center' class='A1101'>采购日期</td>
	<td width='90' align='center' class='A1101'>待购流水号</td>
	<td width='250' align='center' class='A1101'>配件名称</td>
    <td width='40' align='center' class='A1101'>QC图</td>
	<td width='50' align='center' class='A1101'>历史<br>订单</td>
	<td width='30' align='center' class='A1101'>单位</td>
	<td width='55' align='center' class='A1101'>订单<br>数量</td>
    <td width='55' align='center' class='A1101'>收货<br>数量</td>
    <td width='55' align='center' class='A1101'>在库</td>
	<td width='55' align='center' class='A1101'>已备<br>料数</td>
	<td width='55' align='center' class='A1101'>已生<br>产数</td>
	<td width='50' align='center' class='A1101'>采购</td>
	<td width='55' align='center' class='A1101'>存储<br>位置</td>
	</tr>";
$ordercolor=3;
//
	
$sListResult = mysql_query($slistSql,$link_id);
$i=1; //,K.tStockQty 
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
if($StockRows = mysql_fetch_array($sListResult)) {
	do{
/////////////////////////////
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化
			$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;	$scQty="-";
			$OnclickStr="";
			$Mid=$StockRows["Mid"];
			$thisId=$StockRows["Id"];
			$StockId=$StockRows["StockId"];
			$StuffCname=$StockRows["StuffCname"];
			$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
			$Price=$StockRows["Price"];
			$Forshort=$StockRows["Forshort"];
			$Buyer=$StockRows["Name"];
			$BuyerId=$StockRows["BuyerId"];
			$OrderQty=$StockRows["OrderQty"];
			$StockQty=$StockRows["StockQty"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$Date=$StockRows["Date"];
			$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
			$DeliveryDate=$StockRows["DeliveryDate"];		
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
            $tStockQty=$StockRows["tStockQty"];
                        
				//检查是否有图片
				include "../model/subprogram/stuffimg_model.php";
                include"../model/subprogram/stuff_Property.php";//配件属性
			if($FactualQty==0 && $AddQty==0){
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
					else{		//统计项目:8000以下黄色，	8000-9000绿色
						if($mainType==3){
							//生产数量
							$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
								FROM $DataIn.sc1_cjtj S
								WHERE 1 AND S.StockId='$StockId' ",$link_id); 
								$scQty=mysql_result($scSql,0,"scQty");				
							$TempColor=$OrderQty==$scQty?3:2;
							$Date="生产项目";
							}
						else{
						    $Date="统计项目";
							$TempColor=3;		//绿色
							}
					   //$Date="统计项目";
						$Position="-";
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
                                        
                    if ($Mantissa>0){
                        $rkQty="<div style='color:#FF6633;font-weight: bold;'>$rkQty</div>"; 
                    }else{
                        $rkQty="<div style='color:#009900;font-weight: bold;'>$rkQty</div>"; 
                    }
						
					if($DeliveryDate=="0000-00-00"){$DeliveryDate="-";}
					}
				}
			//采单颜色标记
			switch($TempColor){
				case 1://白色
					$Sbgcolor="#FFFFFF";
					$ordercolor=1;
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
			//配件分类色
			$theDefaultColor=$TypeColor;
                   
			///////////////////////////////////////////
			//备领料情况
			$llQty=0;$llBgColor="";$llEstate="";
		  if($mainType<=1) {	
			 $checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
		     $llQty=mysql_result($checkllQty,0,"llQty");
			 $llQty=$llQty==""?0:$llQty;
			 if($llQty>$OrderQty){//领料总数大于订单数,提示出错
				$llBgColor=" style='color:#FF0000;font-weight: bold;'";
				}
			else{
				if($llQty==$OrderQty){//刚好全领，绿色
					$llBgColor=" style='color:#009900;font-weight: bold;'";
					}
				else{				//未领完，黄色
					$llBgColor=" style='color:#FF6633;font-weight: bold;'";
					}
			}
			 $llEstate=mysql_result($checkllQty,0,"llEstate");
			 $llEstate=$llEstate>0?"★":"";
		    }
			///////////////////////////////////////////
            //库存数量    
            if($mainType<=1) {    
                if ($tStockQty>=$OrderQty-$llQty){
                    $tStockQty="<div style='color:#009900;font-weight: bold;'>$tStockQty</div>";
                }else{
                    $tStockQty="<div style='color:#FF6633;font-weight: bold;'>$tStockQty</div>";
                }
            }else{
                 $tStockQty="-"; 
            }  
            $llQty=$llQty==0?"-":$llQty;
                //配件QC检验标准图
            $QCImage="";
            include "../model/subprogram/stuffimg_qcfile.php";
            $QCImage=$QCImage==""?"&nbsp;":$QCImage;
             //历史订单
			 $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>view</a>";
			//REACH 法规图
		    include "../model/subprogram/stuffreach_file.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				}
		


		
		
			echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='right' height='25' class='A0111'>$showStr$i</td>";//态 
			echo"<td  align='center' class='A0101'>$Date</td>";
			echo"<td  align='center' class='A0101'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff class='A0101'>$StuffCname</td>";//配件名称
            echo"<td class='A0101' align='center'>$QCImage</td>";//QC图
			//echo"<td class='A0101' align='center'>$ReachImage</td>";//REACH
			echo"<td class='A0101' align='center'>$OrderQtyInfo</td>";//历史订单
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td align='right' class='A0101'>$OrderQty</td>";//订单需求数量
            echo"<td align='right' class='A0101'>$rkQty</td>";//收货数量
             echo"<td align='right' class='A0101'>$tStockQty</td>";//在库
			echo"<td align='right' class='A0101' $llBgColor> $llEstate $llQty</td>";//领料数
			echo"<td align='right' class='A0101'>$scQty</td>";//生产数量
			echo"<td  align='center' class='A0101'>$Buyer</td>";//采购员
			echo"<td align='center' class='A0101'>$Position</td>";//仓库位置
			echo"</tr>";
				
			$i++;
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
echo"</table>";

?>