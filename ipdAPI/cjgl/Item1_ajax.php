<?php 
/*
OK
*/
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1350;
//来自于生产登记页面
echo"<table id='$TableId'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'><tr>
	<td width='15' height='20' class='A1111'></td>
	<td width='80' align='center' class='A1101'>采购日期</td>
	<td width='90' align='center' class='A1101'>待购流水号</td>
	<td width='330' align='center' class='A1101'>配件名称</td>
    <td width='30' align='center' class='A1101'>单位</td>
	<td width='65' align='center' class='A1101'>订单数量</td>
	<td width='65' align='center' class='A1101'>已备料数</td>
	<td width='65' align='center' class='A1101'>已生产数</td>
	<td width='55' align='center' class='A1101'>采购</td>
	<td width='60' align='center' class='A1101'>存储位置</td>
	</tr>";
$ordercolor=3;
$sListResult = mysql_query("SELECT 
	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
	M.Date,A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,MP.Remark AS Position,ST.mainType,MT.TypeColor,U.Name AS Unit 
	FROM $DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId
	LEFT JOIN $DataPublic.stuffmaintype MT ON MT.Id=ST.mainType
	LEFT JOIN $DataIn.base_mposition MP ON MP.Id=ST.Position 
	LEFT JOIN $DataPublic.staffmain B ON B.Number=S.BuyerId 
    LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
	WHERE S.POrderId='$POrderId' ORDER BY S.StockId",$link_id);
$i=1;
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
			$DeliveryDate=$StockRows["DeliveryDate"];		
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
            $Unit=$StockRows["Unit"]==""?"&nbsp;":$StockRows["Unit"];
			$Currency=$StockRows["Currency"];
				//检查是否有图片
				include "../model/subprogram/stuffimg_model.php";
			if($FactualQty==0 && $AddQty==0){
				$TempColor=3;			//绿色
				$Date="使用库存";
				$FactualQty="-";$AddQty="-";$Buyer="-";$Forshort="-";$rkQty="-";$thQty="-";$bcQty="-";$Mantissa="-";$DeliveryDate="-";
				}
			else{
				if($Date==""){//未下采购单
					if($mainType==1){
						$TempColor=1;		//白色
						$Date="未下采购单";
						}
					else{		//统计项目:8000以下黄色，	8000-9000绿色
						if($mainType==3){
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
							$TempColor=3;		//绿色
							}
						$Date="统计项目";
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
			///////////////////////////////////////////
			//加急订单标色
			include "../admin/subprogram//cg_cgd_jj.php";
			if($Currency==2){
				$Price="<div class='redB'>$Price</div>";
				}
			echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='right' height='20' class='A0111'>$i</td>";//配件状态 
			echo"<td  align='center' class='A0101'>$Date</td>";
			echo"<td  align='center' class='A0101'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff class='A0101'>$StuffCname</td>";//配件名称
                        echo"<td align='center' class='A0101'>$Unit</td>";//配件单位
			echo"<td align='right' class='A0101'>$OrderQty</td>";//订单需求数量
			echo"<td align='right' class='A0101' $llBgColor> $llEstate $llQty</td>";//领料数
			echo"<td align='right' class='A0101'>$scQty</td>";//生产数量
			echo"<td  align='center' class='A0101'>$Buyer</td>";//采购员
			echo"<td align='center' class='A0101'>$Position</td>";//仓库位置
			echo"</tr>";
			$i++;
/////////////////////////////////////
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
echo"</table>";
?>