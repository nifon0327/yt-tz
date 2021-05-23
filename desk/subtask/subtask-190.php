<?php   
//电信-zxq 2012-08-01
//负利产品资料统计
$myResult=mysql_query("SELECT 
C.Forshort,(P.Price*D.Rate) AS Amount,P.ProductId
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId  
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
WHERE 1 AND T.Estate=1 AND P.Estate>0 AND C.Estate=1 AND C.cSign=$Login_cSign ORDER BY P.CompanyId
",$link_id);
$lljlFProduct=0;
$lljlZProduct=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Forshort=$myRow["Forshort"];					//客户
		$ProductId=$myRow["ProductId"];					//产品ID
		$SaleRMB=sprintf("%.3f",$myRow["Amount"]); //产品售价
		//配件成本计算
		$BuyRmbSum=0;		//全部成本
		$BuyHzSum=0;	//自购成本
		$CostResult=mysql_query("SELECT (D.Price*C.Rate) AS Price,A.Relation,P.Currency
			FROM $DataIn.pands A
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
			LEFT JOIN $DataIn.bps B ON B.StuffID=D.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
			WHERE A.ProductId='$ProductId'
			",$link_id);//按供应商类型分
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$thisBuyRMB=0;
				$Price=$CostRow["Price"];
				$CurrencyTemp=$CostRow["Currency"];
				$Relation=$CostRow["Relation"];
				$OppositeQTY=explode("/",$Relation);//拆分对应数量
				if (count($OppositeQTY)>1){	//非整数对应关系
					$thisBuyRMB=$OppositeQTY[1]!=0?$Price*$OppositeQTY[0]/$OppositeQTY[1]:0;
					}
				else{						//整数对应关系
					$thisBuyRMB=$Price*$OppositeQTY[0];
					}
				$BuyRmbSum=$BuyRmbSum+$thisBuyRMB;	//总成本
				if($CurrencyTemp==1){	//货币ID为1时RMB自购
					$BuyHzSum+=$thisBuyRMB;
					}
				}while ($CostRow= mysql_fetch_array($CostResult));
			}
		
		//理论净利
		$profitRMB=sprintf("%.3f",$SaleRMB-$BuyRmbSum-$BuyHzSum*$HzRate);//echo $BuyHzSum;
		$profitRMB=$profitRMB==-0?0:$profitRMB;
		if($SaleRMB!=0)$profitRMBPC=sprintf("%.0f",($profitRMB*100)/$SaleRMB);
		if($profitRMBPC<7){//理论净利百分比负值
			if($profitRMBPC<0){	//负净利的
				$lljlFProduct++;
				}
			else{
				if($profitRMB>0){
					$lljlZProduct++;
					}
				}
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
$contentSTR="<li class=TitleA>产品的理论净利分类统计</li>";
$contentSTR.="<li class=DataBL>负净利</li><li class=DataBR><span class='purpleB'>$lljlFProduct</span></li>";
$contentSTR.="<li class=DataBL>0-7%净利</li><li class=DataBR><span class='redN'>$lljlZProduct</span></li>";

//$contentSTR="、产品的理论净利分类统计(<span class='purpleB'>负净利".$lljlFProduct."个</span>&nbsp;/&nbsp;<span class='redN'>0-7%净利".$lljlZProduct."个</span>)<br>";
?> 