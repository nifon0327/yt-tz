<?php   
//**********电信---yang 20120801
for($i=0;$i<=($CheckMonths+1);$i++){
	if($i%2!=0 && $i<$CheckMonths){//双数画灰色区域
		imagefilledrectangle($image,($imL+$i*$Month_W),$imB-1,($imL+$i*$Month_W)+100,$imT+1,$Gridbgcolor); //画填充矩形
		}
	if($i>0 && $i<($CheckMonths+1)){//画月份间隔线
		//imageline($image,$imL+$i*$Month_W,$imB,$imL+$i*$Month_W,$imT-15,$Gridcolor);
		imageline($image,$imL+$i*$Month_W,$imB,$imL+$i*$Month_W,$imB,$Gridcolor);
		imageline($image,$imL+$i*$Month_W,$imB,$imL+$i*$Month_W,$imB+20,$Gridcolor);
		}
	//输出月份说明
	$j=$i+1;
	if($i<($CheckMonths+1)){
		$TempMonthSTR=date("m",strtotime("$StratDate +$i month"));//计算的起始日期
		if($TempMonthSTR==1){
			$TempMonthSTR=date("Y年m月",strtotime("$StratDate +$i month"));//计算的起始日期
			imagettftext($image,10,0,($imL+$j*$Month_W)-$Month_W+15,$imB+20,$TextRed,$UseFont,$TempMonthSTR);
			}
		else{
			imagettftext($image,10,0,($imL+$j*$Month_W)-$Month_W/2-10,$imB+20,$TextBlack,$UseFont,$TempMonthSTR."月");
			}
		}
	}

//金额间隔线$unitHeight
$countY=($imH-150)/25;
$AmountStep=25;
//  $countY=($imH-150)/$unitHeight;
//  $AmountStep=$unitHeight;     
  
for($i=0;$i<=$countY;$i++){
	$TempAmount=$AmountStep*$i;
	if($i==0 || $i==$countY){
		imageline($image,$imL,$imB-$i*$AmountStep,$imL,$imB-$i*$AmountStep,$TextBlack);		//斜线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-5,$imB-$i*$AmountStep,$TextBlack);//短线
		}
	else{
		imageline($image,$imL,$imB-$i*$AmountStep,$imR,$imB-$i*$AmountStep,$Gridcolor);			//间隔线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL,$imB-$i*$AmountStep,$Gridcolor);		//斜线
		imageline($image,$imL,$imB-$i*$AmountStep,$imL-5,$imB-$i*$AmountStep,$Gridcolor);//短线
		//输出金额
                $TempAmount=$TempAmount*($unitHeight/25);
		$TempAmountX=$TempAmount<100?30:($TempAmount<1000?24:18);
 		imagestring($image,3,$TempAmountX,($imB-$AmountStep*$i)-7,$TempAmount,$TextBlack);
		}
	}
//输出分类图块
imagesetthickness ($image,3);
$RemarkSql=mysql_query("SELECT * FROM $DataIn.productmaintype ORDER BY Id DESC",$link_id);
if($RemarkRow= mysql_fetch_array($RemarkSql)){
	$i=0;
	$TypeNum=0;
	do{
		$Id=$RemarkRow["Id"];
		$Name=$RemarkRow["Name"];
		$R=$RemarkRow["rColor"];
		$G=$RemarkRow["gColor"];
		$B=$RemarkRow["bColor"];
		$mtColor="mtColor".strval($Id);
		$$mtColor=imagecolorallocate($image,$R,$G,$B);
		imagefilledrectangle($image,$imR+10,$imB-$i*25,$imR+25,$imB-15-$i*25,$$mtColor);
		imagettftext($image,9,0,$imR+35,$imB-2-$i*25,$TextBlack,$UseFont,$Name);
		$i++;$TypeNum++;
		}while($RemarkRow= mysql_fetch_array($RemarkSql));
	}
imagesetthickness ($image,1);
?>