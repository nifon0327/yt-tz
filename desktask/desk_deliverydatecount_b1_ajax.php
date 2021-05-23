<?php   
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//图档	QC图	REACH	品检报告	历史订单	配件价格	单位	订单数量	已用库存	需购数量	增购数量	采购	供应商	收货数量	欠数	已备料数	生产数量	交货期
$POrderId=$TempId;
echo"
<table width=\"99%\" cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr align=\"center\" bgcolor=\"#33CCCC\">
<td height=\"25\" width=\"30\">序号</td>
<td width=\"70\">采购日期</td>
<td width=\"100\">需求单流水号</td>
<td >配件名称</td>
<td width=\"30\">图档</td>
<td width=\"30\">历史<br>订单</td>
<td width=\"40\">配件<br>价格</td>
<td width=\"30\">单位</td>
<td width=\"40\">订单<br>数量</td>
<td width=\"40\">已用<br>库存</td>
<td width=\"40\">需购<br>数量</td>
<td width=\"50\">采购</td>
<td width=\"50\">供应商</td>
<td width=\"40\">收货<br>数量</td>
<td width=\"40\">欠数</td>
<td width=\"50\">已备<br>料数</td>
<td width=\"50\">生产<br>数量</td>
<td width=\"60\">交货期</td>
</tr>";

$sListResult = mysql_query("SELECT 
		S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
		M.Date,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,MP.Name AS Position,ST.mainType,MT.TypeColor,U.Name AS UnitName
		FROM $DataIn.cg1_stocksheet S
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
		LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
		LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
		LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
		LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
		$scTJ
		WHERE S.POrderId='$POrderId' $scSTR ORDER BY S.StockId",$link_id);
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
			$Date=$StockRows["Date"];
			$DeliveryDate=$StockRows["DeliveryDate"];		
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
			$Gfile=$StockRows["Gfile"];
			$Gstate=$StockRows["Gstate"];  //状态
			include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
                        
                         //配件QC检验标准图
                         include "../model/subprogram/stuffimg_qcfile.php";
                         
                         //配件品检报告qualityReport
                         include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH 法规图
		   include "../model/subprogram/stuffreach_file.php";
        
			if($FactualQty==0 && $AddQty==0 && $mainType!=3){
				$TempColor=3;			//绿色
				$Date="使用库存";
				$FactualQty="-";$AddQty="-";$Buyer="-";$Forshort="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
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
					else{		//统计项目:mainType=3黄色，2绿色
						if($mainType==3){
							$Date="生产项目";
							//生产数量
							$scSql=mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
								FROM $DataIn.sc1_cjtj S
								LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
								LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId 
								WHERE 1 AND G.StockId='$StockId' AND D.TypeId=S.TypeId",$link_id); 
								$scQty=mysql_result($scSql,0,"scQty");												
							$TempColor=$OrderQty==$scQty?3:2;
							}
						else{
							$Date="统计项目";
							$TempColor=3;		//绿色
							}
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
			///////////////////////////////////////////
			//加急订单标色
			include "../model/subprogram/cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				$Forshort="<div class='redB'>$Forshort</div>";
				}
			
			//备领料情况
			$llQty="-";$llBgColor="";$llEstate="";
		  if($mainType==1) {	
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
			//检查是否锁定 add by zx 20110109
			$lockcolor='';
			$OnclickStr="onclick='updateLock(\"$TableId\",$i,$StockId)' style='CURSOR: pointer;'";
			$lock="<div title='采购未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
				$lock="<div style='background-color:#FF0000' title='采购已锁定'> <img src='../images/lock.png' width='15' height='15'></div>";
				//$lockcolor='#FF0000';
				}				
				
			echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='center' height='20'>$i</td>";//序号 
			echo"<td  align='center'>$Date</td>";								//采购日期
			echo"<td  align='center'>$StockId</td>";							//配件采购流水号
			echo"<td $ChangeStuff>$StuffCname</td>";						//配件名称
			echo"<td  align='center'>$Gfile</td>";								//配件图档
			$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
			echo"<td  align='center'>$OrderQtyInfo</td>";//历史订单分析
			echo"<td align='right'>$Price</td>";//配件价格
			echo"<td  align='center'>$UnitName</td>";//单位
			echo"<td align='right'>$OrderQty</td>";//订单需求数量
			echo"<td align='right'>$StockQty</td>";//使用库存数
			echo"<td align='right'>$FactualQty</td>";//采购数量
			//echo"<td align='right'>$AddQty</td>";//增购数量
				echo"<td  align='center'>$Buyer</td>";//采购员
				echo"<td >$Forshort</td>";//供应商
				echo"<td >$rkQty</td>";//收货进度
				echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";//欠数
				echo"<td align='right'  $llBgColor> $llEstate $llQty</td>";//领料数
				echo"<td><div align='right' style='color:#339900;font-weight: bold;'>$scQty</div></td>";
				echo"<td align='center' $OnclickStr>$DeliveryDate</td>";//供应商交货期$
			echo"</tr>";
			$i++;
			}while ($StockRows = mysql_fetch_array($sListResult));	
	}
echo"</table>";
?>