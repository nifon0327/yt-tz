<?php 
//2011-08-12 ewen更新电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
$ToDate=date("Y-m-d");
//产品资料
$ProductInfo=mysql_fetch_array(mysql_query("SELECT P.cName,P.eCode,C.Forshort 
FROM $DataIn.productdata P 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId WHERE P.ProductId='$Pid' LIMIT 1",$link_id));
$cName=$ProductInfo["cName"];
$eCode=$ProductInfo["eCode"];
$Forshort=$ProductInfo["Forshort"];
$Info="客户：$Forshort     产品名称：$cName     Product Code：$eCode";
//提取12个月内最高数据
$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Qty) AS MaxQty FROM ( 
		SELECT SUM(S.Qty) AS Qty FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
	UNION ALL
		SELECT SUM(S.Qty) AS Qty FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
	)A",$link_id));
$MaxQty=ceil($MaxResult["MaxQty"]/1000)*1000;								//范围月份内最高月出货总额
//读取从当前月之前的12个月有数据的历史月份
$MonthResult =mysql_query("
	SELECT Month FROM(
	SELECT Month FROM ( 
		SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
	UNION ALL
		SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber WHERE S.ProductId='$Pid' GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')
	)A GROUP BY Month ORDER BY Month DESC LIMIT 0,12
	) B ORDER BY Month
",$link_id);
if($MonthRow=mysql_fetch_array($MonthResult)){
	$k=1;
	do{
		$mDate=$MonthRow["Month"]."-01";
		$MonthArray[$k]=$mDate;
		$k++;
		}while($MonthRow=mysql_fetch_array($MonthResult));
	}
$Info.=" / ".$k;
$TypeResult = mysql_query("
	SELECT SUM(Qty) AS Qty,'1' AS Estate FROM $DataIn.yw1_ordersheet WHERE Estate>0 AND ProductId='$Pid'
	UNION ALL
	SELECT SUM(Qty) AS Qty,'0' AS Estate FROM $DataIn.yw1_ordersheet WHERE Estate=0 AND ProductId='$Pid'
	",$link_id);
if($TypeRow=mysql_fetch_array($TypeResult)){
	$QtySum=0;
	do{
		$Qty=$TypeRow["Qty"]==""?0:$TypeRow["Qty"];
		$Estate=$TypeRow["Estate"];
		$QtyTemp="Qty".$Estate;
		$$QtyTemp=$Qty;
		$QtySum=$QtySum+$Qty;
		}while($TypeRow=mysql_fetch_array($TypeResult));
	//数量比例图
	$imW=900;														//图像宽度
	$imH=intval($MaxAmount/100000)*10+360;							//图像高度
	$imH=$imH<500?500:$imH;
	$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体
	$image = imagecreate ($imW,$imH);								//输出空白图像
	imagecolorallocate($image,235,235,235);							//图像背景色
	////设置字体颜色
	$Gridcolor=imagecolorallocate($image,153,153,153);
	$TextBlack = imagecolorallocate($image,0,0,0);
	$TextRed= imagecolorallocate($image,255,0,0);
	$TextGreen= imagecolorallocate($image,0,204,0);
	$TextWhite= imagecolorallocate($image,255,255,255);
	$TextBlue=imagecolorallocate($image,0,0,204);
	if($QtySum>0){
		$unShip=$TextRed;
		$Ship=$TextGreen;
		//输出说明
		$Tile="产品分析图例-$Pid";
		$titleX=$imW-140;												//标题输出的起始X位置
		imagettftext($image,11,0,$titleX,25,$TextBlack,$UseFont,$Tile);	//输出标题
		imagettftext($image,10,0,50,40,$TextBlack,$UseFont,$Info);	//输出标题
		//数量分析
		//条形图长度 840
		
		$QtyPc1=round(($Qty1/$QtySum)*100);
		$QtyPc2=100-$QtyPc1;
		$QtyPc1=$QtyPc1==0?"":$QtyPc1."%";
		$QtyPc2=$QtyPc2==0?"":$QtyPc2."%";
		$QtyX1=intval(($Qty1*840)/$QtySum);
		$QtyX2=840-$QtyX1;
		//imageline($image,20,50,40,50,$Gridcolor);
		//imageline($image,30,50,30,100,$Gridcolor);
		//imageline($image,20,100,40,100,$Gridcolor);
		//未出矩形:起始X：50，结束X：50+$QtyX1
		$XL1=50;		$YT1=65;		$XR1=$QtyX1+50;		$YB1=85;
		imagefilledrectangle($image,$XL1,$YT1,$XR1,$YB1,$unShip); //画填充矩形
	
		$XL2=$XR1;		$YT2=$YT1;		$XR2=890;		$YB2=$YB1;
		imagefilledrectangle($image,$XL2,$YT2,$XR2,$YB2,$Ship); //画填充矩形
		
		//上数量
		$Qty0=$Qty0==0?"":$Qty0;
		$Qty1=$Qty1==0?"":$Qty1;
		imageline($image,50,50,50,60,$Gridcolor);
		imageline($image,$XR1,50,$XR1,60,$Gridcolor);
		imageline($image,890,50,890,60,$Gridcolor);
		imageline($image,50,55,890,55,$Gridcolor);
		imagettftext($image,12,0,intval($XR1/2),60,$TextBlack,$UseFont,$Qty1);	//输出标题
		imagettftext($image,12,0,$QtyX1+50+intval($QtyX2/2),60,$TextBlack,$UseFont,$Qty0);	//输出标题
		//下百分比
		imageline($image,50,90,50,100,$Gridcolor);
		imageline($image,$XR1,90,$XR1,100,$Gridcolor);
		imageline($image,890,90,890,100,$Gridcolor);
		imageline($image,50,95,890,95,$Gridcolor);
		imagettftext($image,12,0,intval($XR1/2),100,$TextBlack,$UseFont,$QtyPc1);	//输出标题
		imagettftext($image,12,0,$QtyX1+50+intval($QtyX2/2),100,$TextBlack,$UseFont,$QtyPc2);	//输出标题
		
		//月订单、出货数量柱形图
		$imT=120;														//顶部Y
		$imL=50;														//顶部X
		$imR=$imW-10;													//底部X
		$imB=$imH-50;													//底部Y
		imagerectangle($image,$imL,$imT,$imR,$imB,$TextBlack); 			//画矩形：曲线图范围
		//金额间隔线
		$AmountStep=30;
		$QtyTemp=$MaxQty/10;
		for($i=1;$i<11;$i++){
			$TempAmount=$QtyTemp*$i;
			imageline($image,$imL,$imB-$i*$AmountStep,$imR,$imB-$i*$AmountStep,$Gridcolor);			//间隔线
			//输出金额
			$TempAmountX=$TempAmount<10000?20:($TempAmount<100000?13:6);
			imagestring($image,3,$TempAmountX,($imB-$AmountStep*$i)-5,$TempAmount,$TextBlack);
			}
		
		//月份间隔线
		$MonthStep=70;
		for($m=1;$m<$k;$m++){
			$MonthTemp=date("y年m月",strtotime($MonthArray[$m]));
			$TempMonth=date("Y-m",strtotime($MonthArray[$m]));
			
			//读取数据
			$readDataSql=mysql_query("
			SELECT SUM(S.Qty) AS Qty,'1' AS Estate FROM $DataIn.yw1_ordersheet S LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber WHERE S.ProductId='$Pid' AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$TempMonth'
			UNION ALL
			SELECT SUM(S.Qty) AS Qty,'0' AS Estate FROM $DataIn.ch1_shipsheet S LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid WHERE S.ProductId='$Pid' AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
			",$link_id);
			if($readDataRow=mysql_fetch_array($readDataSql)){
				do{
					$Qty=$readDataRow["Qty"];
					$YT=intval(($Qty*$AmountStep)/$QtyTemp);
					
					$XC=($m-1)*$MonthStep+85;
					if($YT>0){
						if($readDataRow["Estate"]==1){//蓝色
							$XL=($m-1)*$MonthStep+70;
							//画矩形图
							imagefilledrectangle($image,$XL,$imB,$XC,$imB-$YT,$TextBlue); //画填充矩形
							imagettftext($image,10,60,$XL+10,$imB-$YT,$TextBlue,$UseFont,$Qty);
							}
						else{							//绿色
							$XR=($m-1)*$MonthStep+100;
							//画矩形图
							imagefilledrectangle($image,$XC,$imB,$XR,$imB-$YT,$TextGreen); //画填充矩形
							imagettftext($image,10,60,$XC+10,$imB-$YT,$TextGreen,$UseFont,$Qty);
							}
						}
					}while ($readDataRow=mysql_fetch_array($readDataSql));
				}
			imageline($image,$imL+$m*$MonthStep,$imB,$imL+$m*$MonthStep,$imT+1,$Gridcolor);
			imagettftext($image,10,0,$imL+$m*$MonthStep-$MonthStep/2-25,$imB+20,$TextBlack,$UseFont,$MonthTemp);
			}//end for
		}
	else{
		$ErrorInfo="没有历史订单.";
		$Tile="产品分析图例-$Pid";
		$titleX=$imW-140;												//标题输出的起始X位置
		imagettftext($image,11,0,$titleX,25,$TextBlack,$UseFont,$Tile);	//输出标题
		imagettftext($image,10,0,50,40,$TextBlack,$UseFont,$Info);	//输出标题
		imagettftext($image,10,0,$imW/2-80,200,$TextRed,$UseFont,$ErrorInfo);	//输出标题
		}
	//输出图像
	header("Content-type: image/png");
	imagepng($image);
	imagedestroy($image);              //释放资源
	}
?>