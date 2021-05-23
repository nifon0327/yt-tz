<?php 
//EWEN 2013-08-04 加入餐费、加班奖金
//个税计算
$TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Jbjj+$Yxbz+$Jtbz-$Kqkk-$Sb-$Gjj-$Otherkk-$dkfl;
//底新+工龄津贴+岗位津贴+奖金+生活补助+住宿补助+加班费+加班奖金+夜宵补助+交通补助-考勤扣款-个人社保-有薪工时扣福利费-公积金－餐费扣款
$RandP=0;
if($TaxAmount>3500){
	if($TaxAmount>4000){
		$RandP=15;
		}
	else{//2000-2500
		$RandP=($TaxAmount-3500)*0.03;
		}
	}		
			
if(($Number==10383) || ($Number==10138)){
	if(strtoupper($DataIn)!="PT"){
		$RandP=15;   //175;
		}
	else{
		$RandP=0;
		}//徐莉在中云扣，在龙宝不扣
	}
if(($Number==10001) || ($Number==10822) || ($Number==10943) || ($Number==10855) || ($Number==11136) || ($Number==11880) || $Number==11903){  
	$RandP=0;
	}   //老板不扣,大卫不扣,//陈信荣
	
if($Number=='11880'){  //陈信荣
	$Ct=0;	
}	
$RandP=round($RandP);
$taxbz=0; // add by zx 从201109 后不再有个税补助
$Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Jbjj+$Yxbz+$Jtbz+$taxbz-$Jz-$Sb-$Gjj-$Ct-$Kqkk-$dkfl-$RandP-$Otherkk;
?>