<?php 

//计税工资   =  底新+工龄津贴+岗位津贴+奖金+生活补助+住宿补助+加班费+加班奖金+夜宵补助+交通补助-考勤扣款-有薪工时扣福利费－餐费扣款
$TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Jbjj+$Yxbz+$Jtbz+$Studybz+$Ywjj-$Kqkk-$Otherkk-$dkfl- $Sb-$Gjj ;

$RandP=0;
$baseTax = 4800;

$TaxAmount-=$baseTax;

if ($TaxAmount>1500){
    //暂时更改为工资超过10000按10000元计算个人所得税
     $TaxAmount=$TaxAmount>5200?5200:$TaxAmount;

	if($TaxAmount >1500 && $TaxAmount<= 4500){
	      $RandP =  $TaxAmount*0.1-105;
	}else if($TaxAmount > 4500 && $TaxAmount <= 9000){
	      $RandP =  $TaxAmount*0.2-555;
	}else if($TaxAmount > 8000 && $TaxAmount <= 12500){
	      $RandP =  $TaxAmount*0.2-555;
	}else if($TaxAmount > 9000 && $TaxAmount <= 35000){
	      $RandP =  $TaxAmount*0.3-2755;
	}else if($TaxAmount >55500 && $TaxAmount <= 80000){
	      $RandP =  $TaxAmount*0.35-5505;
	}else if($TaxAmount >80000 ){
	      $RandP =  $TaxAmount*0.45-13505;
	}
}

$RandP=round($RandP);

$Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz+$taxbz+$Studybz+$Ywjj-$Jz-$Sb-$Gjj-$Ct-$Kqkk-$dkfl-$RandP-$Otherkk;//+$Jbjj
?>