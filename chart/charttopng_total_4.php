<?php   
	////////////////A 当月出货总量有超过 20万的/////////////////**********电信---yang 20120801
	$clientSql=mysql_query("
	SELECT Amount,CompanyId,Forshort FROM (
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,M.CompanyId,D.Forshort 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency
		WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.CompanyId 
		) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC",$link_id);
	if($clientRow=mysql_fetch_array($clientSql)){
	//坐标计算：X轴坐标每月只变化一次
		$YB=$imB;
		$YT=$imB;
		$XL=$imL+$Month_W*($i)+$jCube*1+$wCube*2;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
		$XR=$XL+$wCube;
		$ComPanyIdS="";
		$SumAmount=0;
		do{
			//颜色
			$CompanyId=$clientRow["CompanyId"];
			$ComPanyIdS=$ComPanyIdS==""?$CompanyId:($ComPanyIdS.",".$CompanyId);
			$Forshort=$clientRow["Forshort"];
			$Amount=$clientRow["Amount"];
			$SumAmount=$SumAmount+$Amount;
			$shipAmount=intval(sprintf("%.0f",$Amount/10000))*(25/$unitHeight);											//月出货总额
			$YT =$YB-$shipAmount;
			$Tcolor="T".$CompanyId."out";
			//画柱形图正面
			imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$$Tcolor); //画填充矩形
			$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
			imagettftext($image,9,1,$XL,$YB,$TextBlack,$UseFont,$Forshort);
			//Y坐标重设
			$YB=$YT;
			}while($clientRow=mysql_fetch_array($clientSql));
		//其它没到20万的客户的金额总和
		$otherSql=mysql_query("
		SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND M.CompanyId NOT IN ($ComPanyIdS)
			",$link_id);
		if($otherRow=mysql_fetch_array($otherSql)){
			$Amount=$otherRow["Amount"];
			$shipAmount=sprintf("%.2f",$Amount);											//月出货总额
			$SumAmount=$SumAmount+$Amount;
			$TempHight=intval($shipAmount/10000)*(25/$unitHeight);
			$YT =$YT-$TempHight;
			//画柱形图正面
			imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$Totherout); //画填充矩形
			//Y坐标重设
			$YB=$YT;
			}
		//顶部
		$TempPoints[6]=$XL;$TempPoints[7]=$YT;
		//总金额
		$SumAmount=sprintf("%.0f",$SumAmount/10000);
		imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
		////////////////20万以上的用户/////////////////////
		}
	else{//没有达到20万的客户，则只计算该月总数
		/////////////////////////
		$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount 
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth'",$link_id);
		if($allRow=mysql_fetch_array($allSql)){
			$YB=$imB;
			$YT=$imB;
			$XL=$imL+$Month_W*$i+$jCube*2+$wCube;
			$XR=$XL+$wCube;
			$SumAmount=0;
			$Fcolor="Totherin";$Rcolor="Totherin";$Tcolor="Totherin";
			$Amount=$allRow["Amount"];
			$SumAmount=$SumAmount+$Amount;
			$shipAmount=intval(sprintf("%.0f",$Amount/10000))*(25/$unitHeight);											//月出货总额
			$YT =$YB-$shipAmount;
			//总金额
			$SumAmount=sprintf("%.0f",$SumAmount/10000);
			if($SumAmount>0){
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$$Tcolor); //画填充矩形
				//顶部
				$TempPoints[6]=$XL;$TempPoints[7]=$YT;
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
				}
			}
		}
		//各产品主分类出货金额
		imagesetthickness ($image,3);
		$mainTypeSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,T.mainType
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$YB=$imB-1;	//条形图起始Bottom座标
			$YT=$imB;	//条形图起始Top座标
			$XL=$imL+$Month_W*($i+1)-$jCube-$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$XR=$XL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=intval($typeAmount/10000)*(25/$unitHeight);
					$YT =$YT-$TempHight;//条形图新的Top座标
					if($YB-$YT<5){
						imagerectangle($image,$XL,$YT,$XR,$YB,$mainTypeC); //画矩形
						}
					else{
						imagefilledrectangle($image,$XL,$YT+2,$XR,$YB-2,$TextWhite); //画矩形
						imagerectangle($image,$XL,$YT+3,$XR,$YB,$mainTypeC); //画矩形
						}
					$YB=$YT;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}
		$mainTypeSql=mysql_query("
			SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,T.mainType
			FROM $DataIn.yw1_ordersheet S  
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE 1 AND DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' AND T.mainType IS NOT NULL GROUP BY T.mainType ORDER BY Amount DESC",$link_id);
		if($mainTypeRow=mysql_fetch_array($mainTypeSql)){
			$YB=$imB-1;	//条形图起始Bottom座标
			$YT=$imB;	//条形图起始Top座标
			$XL=$imL+$Month_W*$i+$jCube+$wCube+2;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
			$XR=$XL+4;
			do{
				$mainType=$mainTypeRow["mainType"];
				$typeAmount=$mainTypeRow["Amount"];
				//画图
				if($typeAmount>0){
					//条形图颜色
					$Id=$mainTypeRow["mainType"];
					$mainTypeC="mtColor".strval($Id);
					$mainTypeC=$$mainTypeC;
					$TempHight=intval($typeAmount/10000)*(25/$unitHeight);
					$YT =$YT-$TempHight;//条形图新的Top座标
					if($YB-$YT<5){
						imagerectangle($image,$XL,$YT,$XR,$YB,$mainTypeC); //画矩形
						}
					else{
						imagefilledrectangle($image,$XL,$YT+2,$XR,$YB-2,$TextWhite); //画矩形
						imagerectangle($image,$XL,$YT+3,$XR,$YB,$mainTypeC); //画矩形
						}
					$YB=$YT;
					}
				}while($mainTypeRow=mysql_fetch_array($mainTypeSql));
			}			
		imagesetthickness ($image,1);
		//当月车间薪资
		$hzSql=mysql_query("SELECT SUM(Amount) AS Amount FROM(

			SELECT SUM(S.Amount+S.Sb+S.Jz+S.RandP+S.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.cAmount) AS Amount 
			FROM $DataIn.sbpaysheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			UNION ALL
			SELECT SUM(S.Amount) AS Amount 
			FROM $DataIn.hdjbsheet S,$DataPublic.staffmain M WHERE S.Month='$theMonth' AND M.Number=S.Number AND M.BranchId=5
			)E
			",$link_id);
		if($hzRow=mysql_fetch_array($hzSql)){
			$hzAmount=$hzRow["Amount"];
			$SumAmount=sprintf("%.0f",$hzAmount/10000);
			if($SumAmount>0){
				$YB=$imB;
				$YT=$imB;
				$XL=$imL+$Month_W*($i+1)-$wCube;//起始左座标+X间隔*(i-1)+柱图宽+间隔内起始宽
				$XR=$XL+$wCube/2;
				$TempHight=intval($hzAmount/10000)*(25/$unitHeight);
				$YT =$YT-$TempHight;
				imagefilledrectangle($image,$XL,$YB-1,$XR,$YT,$TextGreen); //画填充矩形
				//顶部
				$TempPoints[6]=$XL+2;$TempPoints[7]=$YT;
				//总金额
				
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
				}
			}
		//***********当月下单输出
		$clientSql=mysql_query("SELECT Amount,CompanyId,Forshort FROM (
		SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount,M.CompanyId,D.Forshort 
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
		LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
		WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' GROUP BY M.CompanyId 
		) A WHERE Amount>=150000 GROUP BY CompanyId ORDER BY Amount DESC",$link_id);
		if($clientRow=mysql_fetch_array($clientSql)){//坐标计算：X轴坐标每月只变化一次
			$YB=$imB-1;
			$YT=$imB;
			$XL=$imL+$Month_W*($i)+$jCube;
			$XR=$XL+$wCube;
			$ComPanyIdS="";
			$SumAmount=0;
			do{
				//颜色
				$CompanyId=$clientRow["CompanyId"];
				$ComPanyIdS=$ComPanyIdS==""?$CompanyId:($ComPanyIdS.",".$CompanyId);
				$Forshort=$clientRow["Forshort"];
				$Amount=$clientRow["Amount"];
				$SumAmount=$SumAmount+$Amount;
				$shipAmount=intval(sprintf("%.0f",$Amount/10000))*(25/$unitHeight);											//月出货总额
				$YT =$YB-$shipAmount;
				$Tcolor="T".$CompanyId."out";
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Tcolor); //画填充矩形
				//imagefilledrectangle($image,$XL,$YB,$XR,$YT,$alpha_white); //画透明
				$Forshort=$Forshort=="MCA包装"?"包装":$Forshort;
				imagettftext($image,9,1,$XL,$YB,$TextBlack,$UseFont,$Forshort);
				//Y坐标重设
				$YB=$YT;
				}while($clientRow=mysql_fetch_array($clientSql));
			//其它没到20万的客户的金额总和
			$otherSql=mysql_query("
				SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
				LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
				WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth' AND M.CompanyId NOT IN ($ComPanyIdS)",$link_id);
			if($otherRow=mysql_fetch_array($otherSql)){
				$Amount=$otherRow["Amount"];
				$shipAmount=sprintf("%.2f",$Amount);											//月出货总额
				$SumAmount=$SumAmount+$Amount;
				$TempHight=intval($shipAmount/10000)*(25/$unitHeight);
				$YT =$YT-$TempHight;
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$Totherout); //画填充矩形
				
				$YB=$YT;//Y坐标重设
				}
			//顶部
			$TempPoints[6]=$XL;$TempPoints[7]=$YT;
			$alpha_T=$YT;//透明矩形顶部坐标
			//总金额
			$SumAmount=sprintf("%.0f",$SumAmount/10000);
			imagettftext($image,12,0,$TempPoints[6]-3,$TempPoints[7]-2,$TextBlack,$UseFont,$SumAmount);
			}
		//////////////////////////////////////////////////
		else{
			$allSql=mysql_query("SELECT SUM(S.Qty*S.Price*C.Rate) AS Amount 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
			LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
			WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$theMonth'",$link_id);
			if($allRow=mysql_fetch_array($allSql)){
				$YB=$imB;
				$YT=$imB;
				$XL=$imL+$Month_W*($i-1)+$jCube;
				$XR=$XL+$wCube;
				$SumAmount=0;
				$Fcolor="Totherout";$Rcolor="Totherout";$Tcolor="Totherout";
				$Amount=$allRow["Amount"];
				$SumAmount=$SumAmount+$Amount;
				$shipAmount=intval(sprintf("%.0f",$Amount/10000))*(25/$unitHeight);//月出货总额
				$YT =$YB-$shipAmount;
				//画柱形图正面
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$$Fcolor); //画填充矩形
				imagefilledrectangle($image,$XL,$YB,$XR,$YT,$alpha_white); //画填充矩形
				$TempPoints[6]=$XL;$TempPoints[7]=$YT;//顶部
				$SumAmount=sprintf("%.0f",$SumAmount/10000);//总金额
				imagettftext($image,12,0,$TempPoints[6],$TempPoints[7],$TextBlack,$UseFont,$SumAmount);
				}
			}
?>