<?php 
//电信-EWEN
//个税计算
		$TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz-$Kqkk-$Sb-$Gjj-$Otherkk-$dkfl;//+$Holidayjb+假日加班费
				//底新+工龄津贴+岗位津贴+奖金+生活补助+住宿补助+加班费+夜宵补助+交通补助-考勤扣款-个人社保-有薪工时扣福利费
		//echo "$Number:  $TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz-$Kqkk-$Sb-$Otherkk <br>";	
		$RandP=0;
		/*
		if($TaxAmount>=2000){
			if($TaxAmount>2500){
				if($TaxAmount>4000){
					$RandP=175;
					}
				else{
					$RandP=($TaxAmount-2000)*0.1-25;
					//echo "$RandP=($TaxAmount-2000)*0.1-25 <br>";
					}
				}
			else{//2000-2500
				$RandP=($TaxAmount-2000)*0.05;
				//echo "$RandP=($TaxAmount-2000)*0.05 <br>";
				}
			}
		*/	
		if($TaxAmount>3500){
			if($TaxAmount>4000){
				$RandP=15;
				}
			else{//2000-2500
				$RandP=($TaxAmount-3500)*0.03;
				//echo "$RandP=($TaxAmount-2000)*0.05 <br>";
				}
			}		
		
		
		if(($Number==10383) || ($Number==10138)){
			if(strtoupper($DataIn)!="PT"){
				 $RandP=15;   //175;
				}
				else{$RandP=0;}//徐莉在中云扣，在龙宝不扣
		}
		
		if(($Number==10001) || ($Number==10822) || ($Number==10943) || ($Number==10855) || ($Number==11136) ){$RandP=0;}   //老板不扣,大卫不扣
		$RandP=round($RandP);
		/*
		if($RandP>=175){
			$taxbz=100;  //个税补  从201109 后不再有个税补助
		}
		*/
		$taxbz=0; // add by zx 从201109 后不再有个税补助
		$Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz+$taxbz-$Jz-$Sb-$Gjj-$Kqkk-$dkfl-$RandP;
		//echo "$Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz+$taxbz-$Jz-$Sb-$Kqkk-$RandP <br>";

?>