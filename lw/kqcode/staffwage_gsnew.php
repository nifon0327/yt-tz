<?php 

//计税工资   =  底新+工龄津贴+岗位津贴+奖金+生活补助+住宿补助+加班费+加班奖金+夜宵补助+交通补助+助学补助+购房补助-考勤扣款-有薪工时扣福利费－餐费扣款
$TaxAmount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Jbjj+$Yxbz+$Jtbz+$Studybz+$Housebz+$Ywjj-$Kqkk-$Otherkk-$dkfl- $Sb-$Gjj ;

$RandP=0;
$oRandP=0;
$baseTax = 3500;

if($TaxAmount >3500 && $TaxAmount<= 5000){
      $RandP =  ($TaxAmount - $baseTax )*0.03;
}else if($TaxAmount > 5000 && $TaxAmount <= 8000){
      $RandP =  ($TaxAmount  - $baseTax )*0.1-105;
}else if($TaxAmount > 8000 && $TaxAmount <= 12500){
      $RandP =  ($TaxAmount - $baseTax )*0.2-555;
}else if($TaxAmount > 12500 && $TaxAmount <= 38500){
      $RandP =  ($TaxAmount  - $baseTax )*0.25-1005;
}else if($TaxAmount >38500 && $TaxAmount <= 58500){
      $RandP =  ($TaxAmount - $baseTax )*0.3-2755;
}else if($TaxAmount >58500 && $TaxAmount <= 83500){
      $RandP =  ($TaxAmount  - $baseTax )*0.35-5505;
}else if($TaxAmount >83500 ){
      $RandP =  ($TaxAmount  - $baseTax )*0.45-13505;
}
/*
//暂时更改为工资超过10000按10000元计算个人所得税
if($TaxAmount >3500 && $TaxAmount<= 5000){
      $RandP =  ($TaxAmount - $baseTax )*0.03;
}else if($TaxAmount > 5000 && $TaxAmount <= 8000){
      $RandP =  ($TaxAmount  - $baseTax )*0.1-105;
}else if($TaxAmount > 8000){
      $TaxAmount=$TaxAmount>12000?12000:$TaxAmount;
      $RandP =  ($TaxAmount - $baseTax )*0.2-555;
}
*/

$RandP=round($RandP);
if ($KqSign==1 && $chooseMonth>'2015-10'){
    $x_TaxAmount=$TaxAmount-$Jbjj;
    $x_RandP=0;
	if($x_TaxAmount >3500 && $x_TaxAmount<= 5000){
	      $x_RandP =  ($x_TaxAmount - $baseTax )*0.03;
	}else if($x_TaxAmount > 5000 && $x_TaxAmount <= 8000){
	      $x_RandP =  ($x_TaxAmount  - $baseTax )*0.1-105;
	}else if($x_TaxAmount > 8000){
	      $x_TaxAmount=$x_TaxAmount>12000?12000:$x_TaxAmount;
	      $x_RandP =  ($x_TaxAmount - $baseTax )*0.2-555;
	}
	
    $x_RandP=round($x_RandP);
    if ($RandP>$x_RandP){
         $oRandP=$RandP-$x_RandP;
	     $RandP=$x_RandP;
    }
}

$Amount=$Dx+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jbf+$Yxbz+$Jtbz+$taxbz+$Studybz+$Housebz+$Ywjj-$Jz-$Sb-$Gjj-$Ct-$Kqkk-$dkfl-$RandP-$Otherkk;//+$Jbjj
?>