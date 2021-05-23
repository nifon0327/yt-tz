<?php   
//$ashcloudStartX=$CurMargin; //起点X
//$ashcloudStartY=$tmpTableY; //起点Y
//$ashcloudWidth=184; //长度
//$ashcloudHeight=$MaxContainY-$tmpTableY; //高度
//X=每个ashcloud长度约12,每个增长约1.5,空格长约2, 共14个，节点1-3(42)下降 4是平的(14)，5-9上升(70),10是平的(14),11~14下降(60)
//Y=每一行高度10,每个增长,10/24=0.41 
$ashcloudArray="研 | | 砼 | - | 治 | | 筑 | | ";
$ashcloudAr=explode('|',$ashcloudArray);
$ashcloudCount=count($ashcloudAr);


for($YY=-5;$YY<=$ashcloudHeight;$YY=$YY+5){
	$tempAX=$ashcloudStartX;
	$tempAY=$ashcloudStartY+$YY;
	$tempDX1=0.5; //X跳距
	$tempDX2=1.3; //X跳距
	$tempDY1=0.3; //Y跳距
	$tempDY2=0.2; //Y跳距
	for($A3=0;$A3<3;$A3=$A3+1){  //第一波下降	
		for($AI=0;$AI<$ashcloudCount;$AI=$AI+1){
			if($ashcloudAr[$AI]=="-"){ //只跳不输出
				$tempAX=$tempAX+$tempDX1;
				$tempAY=$tempAY+$tempDY2;
			}else{
				if($ashcloudAr[$AI-1]=="l"){
					$tempAX=$tempAX+$tempDX2-0.6;
					$tempAY=$tempAY+$tempDY1;
				}
				else{
					if($AI==1 || $AI==2){
						if($AI==2){ //H
							$tempAX=$tempAX+$tempDX2-0.3;
						}
						else{  //S
							$tempAX=$tempAX+$tempDX2-0.2;
						}
					}
					else{
						$tempAX=$tempAX+$tempDX2;
					}
					$tempAY=$tempAY+$tempDY1;					
				}
				if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
					$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
				}
			}
			
		}
	}
	//平的，Y不增
	for($AI=0;$AI<$ashcloudCount;$AI=$AI+1){
		if($ashcloudAr[$AI-1]=="l"){
			$tempAX=$tempAX+$tempDX2-0.6;
			$tempAY=$tempAY;
		}
		else{
			//$tempAX=$tempAX+$tempDX2;
			if($AI==1 || $AI==2){
				if($AI==2){ //H
					$tempAX=$tempAX+$tempDX2-0.3;
				}
				else{  //S
					$tempAX=$tempAX+$tempDX2-0.2;
				}
			}
			else{
				$tempAX=$tempAX+$tempDX2;
			}			
			$tempAY=$tempAY;			
		}
		if($ashcloudAr[$AI]!="-"){ 
				if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
					$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
				}
		}
	}
	
	for($A3=0;$A3<5;$A3=$A3+1){  //第三波上升	
		for($AI=0;$AI<$ashcloudCount;$AI=$AI+1){
			if($ashcloudAr[$AI]=="-"){ //只跳不输出
				$tempAX=$tempAX+$tempDX1;
				$tempAY=$tempAY-$tempDY2;
			}else{
				if($ashcloudAr[$AI-1]=="l"){
					$tempAX=$tempAX+$tempDX2-0.6;
					$tempAY=$tempAY-$tempDY2;
				}
				else {
					if($AI==1 || $AI==2){
						if($AI==2){ //H
							$tempAX=$tempAX+$tempDX2-0.3;
						}
						else{  //S
							$tempAX=$tempAX+$tempDX2-0.2;
						}
					}
					else{
						$tempAX=$tempAX+$tempDX2;
					}
					$tempAY=$tempAY-$tempDY2;					
				}
				if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
					$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
				}
			}
			
		}
	}
	
	//平的，Y不增
	for($AI=0;$AI<$ashcloudCount;$AI=$AI+1){
		if($ashcloudAr[$AI-1]=="l"){
			$tempAX=$tempAX+$tempDX2-0.6;
			$tempAY=$tempAY;
		}
		else{
			if($AI==1 || $AI==2){
				if($AI==2){ //H
					$tempAX=$tempAX+$tempDX2-0.3;
				}
				else{  //S
					$tempAX=$tempAX+$tempDX2-0.2;
				}
			}
			else{
				$tempAX=$tempAX+$tempDX2;
			}
			$tempAY=$tempAY;			
		}
		if($ashcloudAr[$AI]!="-"){ 
			if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
					$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
			}
		}
	}
	
	for($A3=0;$A3<6;$A3=$A3+1){  //第五波下降	
		for($AI=0;$AI<$ashcloudCount;$AI=$AI+1){
			if($ashcloudAr[$AI]=="-"){ //只跳不输出
				$tempAX=$tempAX+$tempDX1;
				$tempAY=$tempAY+$tempDY2;
			}else{
				if($ashcloudAr[$AI-1]=="l"){
					$tempAX=$tempAX+$tempDX2-0.6;
					$tempAY=$tempAY+$tempDY2;
				}
				else{
					if($AI==1 || $AI==2){
						if($AI==2){ //H
							$tempAX=$tempAX+$tempDX2-0.3;
						}
						else{  //S
							$tempAX=$tempAX+$tempDX2-0.2;
						}
					}
					else{
						$tempAX=$tempAX+$tempDX2;
					}
					$tempAY=$tempAY+$tempDY2;					
				}
				if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
					$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
				}
			}
			
		}
	}
	
	//平的，Y不增
	for($AI=0;$AI<3;$AI=$AI+1){
		if($ashcloudAr[$AI-1]=="l"){
			$tempAX=$tempAX+$tempDX2-0.6;
			$tempAY=$tempAY;
		}
		else{
			if($AI==1 || $AI==2){
				if($AI==2){ //H
					$tempAX=$tempAX+$tempDX2-0.3;
				}
				else{  //S
					$tempAX=$tempAX+$tempDX2-0.2;
				}
			}
			else{
				$tempAX=$tempAX+$tempDX2;
			}
			$tempAY=$tempAY;			
		}
		if($ashcloudAr[$AI]!="-"){ 
			if($tempAY>=$ashcloudStartY && $tempAY<=$MaxContainY-2){
				$pdf->Text($tempAX,$tempAY,$ashcloudAr[$AI]);  //输出I
			}
		}
	}

} //end for (YY)

?>