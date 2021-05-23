<?php   
/*
独立已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$ToDay=date("Y-m-d");
//未收货款总额
$GatheringSUM=0;
$ShipResult = mysql_query("
SELECT SUM(GatheringSUM) AS GatheringSUM FROM(
	SELECT SUM( S.Price * S.Qty * D.Rate * M.Sign) AS GatheringSUM
		FROM $DataIn.ch1_shipmain M
		LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		WHERE M.Estate =0 AND cwSign IN (1,2)
	UNION ALL
		SELECT SUM(-P.Amount*D.Rate* M.Sign) AS GatheringSUM 
		FROM $DataIn.cw6_orderinsheet P
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=P.chId 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
		WHERE M.cwSign='2' 
	)A
	",$link_id);
if($ShipRow = mysql_fetch_array($ShipResult)) {
	$GatheringSUM=sprintf("%.2f",$ShipRow["GatheringSUM"]); 
	}
//预收客户货款
$CheckPreSql=mysql_query("SELECT SUM(M.Amount*D.Rate) AS PreAmount FROM $DataIn.cw6_advancesreceived M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
WHERE M.Mid='0'",$link_id);
if($CheckPreRow=mysql_fetch_array($CheckPreSql)){
	$PreAmount=$CheckPreRow["PreAmount"];
	$GatheringSUM=$GatheringSUM-$PreAmount;
	}

//读取未付款总额：货款、订金、杂费、运费、快递费、寄样费
$noPaySql = mysql_query("
	SELECT SUM(Amount) AS sumAmount FROM (
			SELECT SUM((F.AddQty+F.FactualQty)*F.Price*C.Rate) AS Amount,concat('供应商货款') AS Types FROM $DataIn.cw1_fkoutsheet F  LEFT JOIN $DataIn.trade_object P ON P.CompanyId=F.CompanyId LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  WHERE F.Estate=3
		UNION ALL
			SELECT SUM(D.Amount*C.Rate) AS Amount,concat('未结付订金') AS Types 
			FROM $DataIn.cw2_fkdjsheet D 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=D.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  WHERE D.Estate=3
		UNION ALL
			SELECT SUM(Amount) AS Amount,concat('开发费用') AS Types FROM $DataIn.cwdyfsheet WHERE Estate=3
		UNION ALL
			SELECT SUM(H.Amount*C.Rate) AS Amount,concat('行政费用') AS Types FROM $DataIn.hzqksheet H LEFT JOIN $DataPublic.currencydata C ON C.Id=H.Currency  WHERE H.Estate=3
		UNION ALL
			SELECT SUM(Amount) AS Amount,concat('员工薪资') AS Types FROM $DataIn.cwxzsheet WHERE Estate=3 
		UNION ALL
			SELECT SUM(Amount) AS Amount,concat('临时工薪资') AS Types FROM $DataIn.cwxztempsheet WHERE Estate=3 
		UNION ALL
			SELECT SUM(D.Amount*C.Rate) AS Amount,concat('Forward费用') AS Types FROM $DataIn.ch3_forward D,$DataPublic.currencydata C WHERE D.Estate=3 AND C.Id=3
		UNION ALL
			SELECT SUM(D.mcWG*D.Price+D.depotCharge*C.Rate) AS Amount,concat('中港运费') AS Types FROM $DataIn.ch4_freight D,$DataPublic.currencydata C WHERE D.Estate=3 AND C.Id=3
		UNION ALL
			SELECT SUM(D.Amount) AS Amount,concat('快递费用') AS Types FROM $DataIn.ch9_expsheet D WHERE D.Estate=3
		UNION ALL
			SELECT SUM(D.Amount) AS Amount,concat('寄样费用') AS Types FROM $DataIn.ch10_samplemail D WHERE D.Estate=3
		UNION ALL
			SELECT SUM(mAmount+cAmount) AS Amount,concat('社保费用') AS Types FROM $DataIn.sbpaysheet WHERE Estate=3
		UNION ALL
			SELECT SUM(Amount) AS Amount,concat('假日加班费') AS Types FROM $DataIn.hdjbsheet WHERE Estate=3 
		UNION ALL
			SELECT SUM(Amount) AS Amount,concat('节日奖金') AS Types FROM $DataIn.cw11_jjsheet WHERE Estate=3 
		UNION ALL
			SELECT SUM(D.Qty*D.Price) AS Amount,concat('总务采购费用') AS Types FROM $DataIn.zw3_purchases D WHERE D.Estate=3
	) A
	",$link_id);
if($noPayRow = mysql_fetch_array($noPaySql)) {
	$noPayAmount=sprintf("%.2f",$noPayRow["sumAmount"]);
	}
	
//现金结余
$SystemDataS="2008-07-01";
//取已结付财务数据条件
$Terms="WHERE 1 AND M.PayDate>='$SystemDataS'"; 
//RMB专有项目
$Result1="  UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cwxztempmain 	M $Terms";//临时工薪资
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cwxzmain     	M $Terms";//正式工薪资
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw11_jjmain  	M $Terms";//节日奖金
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cwdyfmain 		M $Terms";//开发费用
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.sbpaymain 		M $Terms";//社保
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.hdjbmain  		M $Terms";//假日加班费
$Result1.=" UNION ALL SELECT SUM(M.Amount)    *-1 AS Amount FROM $DataIn.cwygjz 			M $Terms";//员工借支
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw4_freight 		M $Terms";//中港运费
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw9_expsheet 	M $Terms";//快递费
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw10_samplemail 	M $Terms";//寄样费
$Result1.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.zw3_purchasem 	M $Terms";//总务采购费用
$Result1.=" UNION ALL SELECT SUM(M.PayAmount+M.checkCharge)*-1  AS Amount  FROM $DataIn.cw12_declaration M  $Terms";  ////报关费用  add by zx 20101208

//HKD专有项目
$Result3=" UNION ALL SELECT SUM(M.depotCharge) *-1 AS Amount FROM $DataIn.cw4_freight M $Terms";//入仓费
$Result3.=" UNION ALL SELECT SUM(M.PayAmount) *-1 AS Amount FROM $DataIn.cw3_forward M $Terms";//Forward杂费


//共有项目
for($j=1;$j<4;$j++){//1RMB 2USD 3HKD
	$Currency=$j;
	$ResultSTR="Result".strval($Currency); 
	$Result="SELECT SUM(M.Amount) 	   AS Amount FROM $DataIn.cw4_otherin M $Terms AND M.Currency='$Currency'";//其它收入
	$Result.=" UNION ALL SELECT SUM(M.InAmount)     AS Amount FROM $DataIn.cw5_fbdh M $Terms AND M.InCurrency='$Currency'";//汇兑转入
	$Result.=" UNION ALL SELECT SUM(-M.OutAmount) AS Amount FROM $DataIn.cw5_fbdh M $Terms AND M.OutCurrency='$Currency'";//汇兑转出
	$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.hzqkmain M,$DataIn.hzqksheet S $Terms AND S.Mid=M.Id AND S.Currency='$Currency' ";//行政费用
	$Result.=" UNION ALL SELECT SUM(M.PayAmount-M.Handingfee) AS Amount FROM $DataIn.cw6_orderinmain M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId $Terms AND C.Currency=$Currency";//客户货款收入
	$Result.=" UNION ALL SELECT SUM(M.Amount) AS Amount FROM $DataIn.cw6_advancesreceived M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId $Terms AND C.Currency=$Currency";//预收客户货款
	$Result.=" UNION ALL SELECT SUM(-PayAmount) AS Amount FROM $DataIn.cw1_fkoutmain M,$DataIn.trade_object C $Terms AND C.CompanyId=M.CompanyId AND C.Currency=$Currency";//货款支出
	$Result.=" UNION ALL SELECT SUM(-S.Amount) AS Amount FROM $DataIn.cw2_fkdjmain M,$DataIn.cw2_fkdjsheet S,$DataIn.trade_object P $Terms AND S.Mid=M.Id AND P.CompanyId=S.CompanyId AND P.Currency=$Currency".$$ResultSTR;//预付订金
	$MyResult = mysql_query("SELECT SUM(Amount) AS SumAmount FROM ($Result) A",$link_id);
	if($MyRow = mysql_fetch_array($MyResult)) {
		$ResultSTR="SumAmount".strval($Currency); 
		$$ResultSTR=sprintf("%.2f",$MyRow["SumAmount"]);
		}
	}
//汇率
$checkCurrency=mysql_query("SELECT Symbol,Rate FROM $DataPublic.currencydata WHERE Estate=1 AND Id>1 ORDER BY Id",$link_id);
if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
	do{
		$TempRate=strval($checkCurrencyRow["Symbol"])."Rate"; 
		$$TempRate=$checkCurrencyRow["Rate"];	
		}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
	}
//现金结余金额合计
$caseSum=$SumAmount1+$SumAmount2*$USDRate+$SumAmount3*$HKDRate;
$caseSum=round($caseSum,2);

$GaplineW=90;								//显示日期天数																			
//历史记录最大值
$curveMAX=mysql_fetch_array(mysql_query("SELECT MAX(wsk) AS MAXwsk,MAX(wjf) AS MAXwjf FROM $DataIn.curvechart WHERE  Date> DATE_SUB('$ToDay',INTERVAL $GaplineW DAY) ORDER BY Date",$link_id));
$MAXwsk=$curveMAX["MAXwsk"];
$MAXwjf=$curveMAX["MAXwjf"];
$MAXhistory=$MAXwsk>$MAXwjf?$MAXwsk:$MAXwjf;
$TempAmount=$GatheringSUM>$noPayAmount?$GatheringSUM:$noPayAmount;
$TempAmount=$TempAmount>$MAXhistory?$TempAmount:$MAXhistory;
$imH=intval($TempAmount/200000)*20+260;
$imW=2000;														//图像宽度
$imH=$imH<500?500:$imH;														//图像高度
$Diameter=4;													//圆点直径
$UseFont = "c:/windows/fonts/simhei.ttf"; 						//使用的中文字体

$image = imagecreate ($imW,$imH);								//输出空白图像
imagecolorallocate($image,255,255,255);							//图像背景色

$BlackColor = 	imagecolorallocate($image,0,0,0);				//黑色
$GridColor	=	imagecolorallocate($image,153,153,153);			//网格颜色，灰色
$redline 	= 	imagecolorallocate($image,255,0,0);				//红色
$greenline 	= 	imagecolorallocate($image,40,170,40);			//绿色
$blueline 	= 	imagecolorallocate($image,0,25,168);	        //蓝色
$goldline 	= 	imagecolorallocate($image,184,134,11);	        //黄色

$Tile="金额变化趋势图";											//图像标题
$titleX=$imW/2-80;												//标题输出的起始X位置
$jgSetp=20;														//网格宽
$imT=50;														//顶部Y
$imL=50;														//顶部X
$imR=$imW-10;													//底部X
$imB=$imH-50;													//底部Y

imagettftext($image,15,0,$titleX,20,$BlackColor,$UseFont,$Tile);//输出标题
imagerectangle($image,$imL,$imT,$imR,$imB,$BlackColor); 		//画矩形：曲线图范围

//输出日期线
$dayToline = array();
$StartDay=date("Y-m-d",strtotime("- $GaplineW days"));//日期起始日
for($i=1;$i<=$GaplineW;$i++){
	$xJGvalue=date("d",strtotime("+ $i days",strtotime($StartDay)));
	imageline($image,($imL+$jgSetp*$i),$imT+1,($imL+$jgSetp*$i),$imB,$GridColor);						//输出纵向网格线:
	imageline($image,($imL+$jgSetp*$i),$imB-10,($imL+$jgSetp*$i),$imB,$BlackColor);						//输出纵向分隔线:
	$dayToline[$i]=$i;
	if($xJGvalue==1){
		$Month=date("Y年m月",strtotime("+ $i days",strtotime($StartDay)));
		imagestring($image,$jgSetp,($imL-5+$jgSetp*$i),$imT-17,$xJGvalue,$BlackColor);	//输出1日
		imagettftext($image,10,0,($imL+$jgSetp*$i)-30,$imT-18,$BlackColor,$UseFont,$Month);				//输出月份
		imagestring($image,$jgSetp,($imL-5+$jgSetp*$i),$imH-48,$xJGvalue,$BlackColor);	//输出1日
		imagettftext($image,10,0,($imL+$jgSetp*$i)-30,$imH-20,$BlackColor,$UseFont,$Month);				//输出月份
		}
	else{
		imagestring($image,3,($imL-5+$jgSetp*$i),$imT-15,$xJGvalue,$BlackColor);					//输出其它日期
		imagestring($image,3,($imL-5+$jgSetp*$i),$imH-48,$xJGvalue,$BlackColor);					//输出其它日期
		}
  	}
imagettftext($image,10,0,(70+$jgSetp*$i),$imH-35,$BlackColor,$UseFont,"(日期)");

//输出横向金额线
$GaplineH=intval($imH/$jgSetp)-5;									//金额间隔线数量
$AmountStart=-140;														//金额起始标额
$AmountLastX=$GaplineW*20+$imL;
for($i=1;$i<$GaplineH;$i++){
	$OutAmount =$i*$jgSetp+$AmountStart;							//间隔线对应金额
	if($OutAmount==0){													//间隔线对应金额为0时，输出黑色线，其它为灰色间隔线
		imageline($image,$imL,($imB-$jgSetp*$i),$AmountLastX,($imB-$jgSetp*$i),$BlackColor);
		}
	else{
		imageline($image,$imL,($imB-$jgSetp*$i),$AmountLastX,($imB-$jgSetp*$i),$GridColor);
		}
	imageline($image,$imL,($imB-$jgSetp*$i),60,($imB-$jgSetp*$i),$BlackColor);			//输出黑色短线
	$setp=$OutAmount<=-100?24:($OutAmount<0?25:($OutAmount==0?39:($OutAmount<100?32:26)));					//金额输出的起始位参数
 	imagestring($image,3,$setp,($imB-$jgSetp*$i-7),$OutAmount,$BlackColor);				//输出金额
	imagestring($image,3,1858,($imB-$jgSetp*$i-7),$OutAmount,$BlackColor);				//输出金额
  }
imagettftext($image,10,90,$setp-7,($imH/2),$BlackColor,$UseFont,"金额(单位:万元)");		//输出金额线说明

/*
1、默认当前日期至前30天
2、指定的起始日期
3、指定月份
*/
imagesetthickness ($image,2);
$PrePointX="";
$PrePointY1="";
$PrePointY2="";
$PrePointY3="";
$PrePointY4="";

$curveSql=mysql_query("SELECT * FROM $DataIn.curvechart WHERE  Date> DATE_SUB('$ToDay',INTERVAL $GaplineW DAY) ORDER BY Date",$link_id);
if($curveRow=mysql_fetch_array($curveSql)){
	$i=1;
	do{
		//计算X轴坐标
		$thisDay=intval(date("d",strtotime($curveRow["Date"])));
		
		$XPoint=$dayToline[$i];
		$PointX=($imL+20*$XPoint);
		
		//计算Y轴坐标
		$PointY1 =$imB+$AmountStart-intval($curveRow["wsk"]/10000);
		$PointY2 =$imB+$AmountStart-intval($curveRow["wjf"]/10000);
		$PointY4 =$imB+$AmountStart-intval($curveRow["xjjy"]/10000);
		$PointY3=$imB+$AmountStart+($PointY1-$PointY2);
		
		if($PrePointX!="" && $PrePointY1!=""){
			//画线
			imageline($image,$PrePointX,$PrePointY1,$PointX,$PointY1,$greenline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
			imageline($image,$PrePointX,$PrePointY2,$PointX,$PointY2,$redline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
			imageline($image,$PrePointX,$PrePointY3,$PointX,$PointY3,$blueline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
			imageline($image,$PrePointX,$PrePointY4,$PointX,$PointY4,$goldline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
			//画圆点
			imagefilledarc($image,$PointX,$PointY1,$Diameter,$Diameter,0,360,$greenline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY2,$Diameter,$Diameter,0,360,$redline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY3,$Diameter,$Diameter,0,360,$blueline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY4,$Diameter,$Diameter,0,360,$goldline,IMG_ARC_PIE);
			}
		else{
			//画圆点
			imagefilledarc($image,$PointX,$PointY1,$Diameter,$Diameter,0,360,$greenline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY2,$Diameter,$Diameter,0,360,$redline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY3,$Diameter,$Diameter,0,360,$blueline,IMG_ARC_PIE);
			imagefilledarc($image,$PointX,$PointY4,$Diameter,$Diameter,0,360,$goldline,IMG_ARC_PIE);
			}
		//前置点重新设值
		$PrePointX=$PointX;
		$PrePointY1=$PointY1;
		$PrePointY2=$PointY2;
		$PrePointY3=$PointY3;
		$PrePointY4=$PointY4;
		$i++;
		}while ($curveRow=mysql_fetch_array($curveSql));
	}
//读取今天的数据

$todayPointX=$imL+20*$dayToline[$i];

$todayPointY2=$imB+$AmountStart-intval($noPayAmount/10000);
$todayPointY1=$imB+$AmountStart-intval($GatheringSUM/10000);
$todayPointY4=$imB+$AmountStart-intval($caseSum/10000);
$todayPointY3=$imB+$AmountStart+($todayPointY1-$todayPointY2);

if($PrePointX!="" && $PrePointY1!=""){
	//画线
	imageline($image,$PrePointX,$PrePointY1,$todayPointX,$todayPointY1,$greenline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
	imageline($image,$PrePointX,$PrePointY2,$todayPointX,$todayPointY2,$redline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
	imageline($image,$PrePointX,$PrePointY3,$todayPointX,$todayPointY3,$blueline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
	imageline($image,$PrePointX,$PrePointY4,$todayPointX,$todayPointY4,$goldline);//画线:画板，起点X，起点Y，终点X，终点Y，颜色
	//画圆点
	imagefilledarc($image,$todayPointX,$todayPointY1,$Diameter,$Diameter,0,360,$greenline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY2,$Diameter,$Diameter,0,360,$redline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY3,$Diameter,$Diameter,0,360,$blueline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY4,$Diameter,$Diameter,0,360,$goldline,IMG_ARC_PIE);
	}
else{
	//画圆点
	imagefilledarc($image,$todayPointX,$todayPointY1,$Diameter,$Diameter,0,360,$greenline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY2,$Diameter,$Diameter,0,360,$redline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY3,$Diameter,$Diameter,0,360,$blueline,IMG_ARC_PIE);
	imagefilledarc($image,$todayPointX,$todayPointY4,$Diameter,$Diameter,0,360,$goldline,IMG_ARC_PIE);
	}

//图例说明参数
$RemarkLineH=1;
$RemarkLineW=80;
$RemarkX1=$imW-110;
$RemarkX2=$RemarkX1+$RemarkLineW;
$RemarkY1=intval($imH/5);
$RemarkStep=100;
$RemarkNumber=5;
$RemarkStr=20;
$Value=number_format($GatheringSUM-$noPayAmount,2);
$GatheringSUM=number_format($GatheringSUM,2);
$noPayAmount=number_format($noPayAmount,2);
$caseSum=number_format($caseSum,2);

imagerectangle($image,$RemarkX1,$RemarkY1,$RemarkX2,$RemarkY1+$RemarkLine,$greenline);
imagettftext($image,10,0,$RemarkX1,$RemarkY1-$RemarkNumber,$greenline,$UseFont,$GatheringSUM);
imagettftext($image,10,0,$RemarkX1,$RemarkY1+$RemarkStr,$greenline,$UseFont,"未收货款金额");
$RemarkY1=$RemarkY1+$RemarkStep;
imagerectangle($image,$RemarkX1,$RemarkY1,$RemarkX2,$RemarkY1+$RemarkLine,$redline);
imagettftext($image,10,0,$RemarkX1,$RemarkY1-$RemarkNumber,$redline,$UseFont,$noPayAmount);
imagettftext($image,10,0,$RemarkX1,$RemarkY1+$RemarkStr,$redline,$UseFont,"未结付金额");

$RemarkY1=$RemarkY1+$RemarkStep;
imagerectangle($image,$RemarkX1,$RemarkY1,$RemarkX2,$RemarkY1+$RemarkLine,$blueline);
imagettftext($image,10,0,$RemarkX1,$RemarkY1-$RemarkNumber,$blueline,$UseFont,$Value);
imagettftext($image,10,0,$RemarkX1,$RemarkY1+$RemarkStr,$blueline,$UseFont,"金额差");

$RemarkY1=$RemarkY1+$RemarkStep;
imagerectangle($image,$RemarkX1,$RemarkY1,$RemarkX2,$RemarkY1+$RemarkLine,$goldline);
imagettftext($image,10,0,$RemarkX1,$RemarkY1-$RemarkNumber,$goldline,$UseFont,$caseSum);
imagettftext($image,10,0,$RemarkX1,$RemarkY1+$RemarkStr,$goldline,$UseFont,"现金结余金额");
//输出图像
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);               //释放资源
?>