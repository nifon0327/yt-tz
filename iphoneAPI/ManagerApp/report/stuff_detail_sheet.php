<?php 

//$StuffId;
//读取配件库存值
$POrderId = strlen($info[1])>12?substr($info[1],0,12):$info[1];

switch ($ModuleType) {
	case "2":
	{
//读取配件库存值
$CheckSql=mysql_query("SELECT S.dStockQty,S.tStockQty,S.oStockQty,S.mStockQty,D.StuffId,D.StuffCname,U.Decimals
FROM $DataIn.ck9_stocksheet S
INNER JOIN $DataIn.stuffdata D ON S.StuffId=D.stuffId 
INNER JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
WHERE D.StuffId='$StuffId' ",$link_id);; 
if($CheckRow = mysql_fetch_array($CheckSql))	{
	$dStockQty=$CheckRow["dStockQty"];
	$tStockQty=$CheckRow["tStockQty"];
	$oStockQty=$CheckRow["oStockQty"];
	$StuffId=$CheckRow["StuffId"];
	$mStockQty=$CheckRow["mStockQty"];
	$StuffCname=$CheckRow["StuffCname"];
	$Decimals=$CheckRow["Decimals"];
	$thisDate=date("Y-m-d");
	}

$UStuffCname=urlencode($StuffCname);


//检查是否为子配件
$subResult=mysql_query("SELECT mStuffId,Relation FROM $DataIn.stuffcombox_bom WHERE StuffId='$StuffId' LIMIT 1 ",$link_id);  
if($subRow = mysql_fetch_array($subResult))	{
     $mStuffId=$subRow["mStuffId"];
     $Relation=$subRow["Relation"];
     
    //订单数据
	$UnionSTR="
			SELECT S.Date,concat('1') AS Sign,IFNULL(SUM(G.OrderQty),0) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId  
			WHERE G.StuffId='$StuffId' AND S.cgSign=0 GROUP BY S.Date";
			
	 //半成品特采产生的订单需求量 
	$UnionSTR.="
	UNION ALL 
			SELECT S.Date,concat('2') AS Sign,IFNULL(SUM(G.OrderQty),0) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId 
			WHERE G.StuffId='$StuffId' AND S.cgSign=1 GROUP BY S.Date";
					
     //采购数据（包括已下采购单和没下采购单）
	$UnionSTR.="
	UNION ALL
	        SELECT M.Date AS Date,concat('3') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId 
			INNER JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
			WHERE G.StuffId='$StuffId' AND S.Mid>0 AND S.cgSign=0 GROUP BY M.Date
	UNION ALL
	        SELECT '$unDate' AS Date,concat('3') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId 
			WHERE G.StuffId='$StuffId' AND S.Mid=0 AND S.cgSign=0";
	
	//特采数据
	$UnionSTR.="
	UNION ALL
	        SELECT M.Date AS Date,concat('4') AS Sign,IFNULL(SUM(G.FactualQty+G.AddQty),0) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId 
			INNER JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
			WHERE G.StuffId='$StuffId'  AND S.Mid>0 AND S.cgSign=1  GROUP BY M.Date 
    UNION ALL
	        SELECT '$unDate' AS Date,concat('4') AS Sign,IFNULL(SUM(G.FactualQty+G.AddQty),0) AS Qty 
			FROM $DataIn.cg1_stuffcombox G
			INNER JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.mStockId 
			WHERE G.StuffId='$StuffId'  AND (G.FactualQty+G.AddQty)>0 AND S.Mid=0 AND S.cgSign=1";
     
}
else{
    //订单数据
    $UnionSTR="SELECT G.Date,concat('1') AS Sign,SUM(G.OrderQty) AS Qty 
			FROM $DataIn.cg1_stocksheet G
			WHERE G.StuffId='$StuffId' AND G.cgSign=0 GROUP BY G.Date";
   
     //半成品特采产生的订单需求量
     $UnionSTR.=" 
       UNION ALL 
            SELECT G.Date,concat('2') AS Sign,IFNULL(SUM(G.OrderQty),0)  AS Qty  
		    FROM $DataIn.cg1_stocksheet G
		    WHERE G.StuffId='$StuffId' AND G.cgSign=1 GROUP BY G.Date";
			

	//采购数据（包括已下采购单和没下采购单）
	$UnionSTR.="
      UNION ALL
		  SELECT A.Date,concat('3') AS Sign,SUM(A.Qty) AS Qty 
		  FROM(
		     SELECT M.Date,SUM(G.FactualQty+G.AddQty) AS Qty    
		     FROM $DataIn.cg1_stocksheet G
		     INNER JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
		     WHERE G.StuffId='$StuffId' AND G.Mid>0  AND G.OrderQty>0 GROUP BY M.Date
		  UNION ALL
		   SELECT '$unDate' AS Date,IFNULL(SUM(G.FactualQty+G.AddQty),0) AS Qty    
		   FROM $DataIn.cg1_stocksheet G
		   WHERE G.StuffId='$StuffId' AND  G.Mid=0 AND G.OrderQty>0 
		   )A GROUP BY A.Date"; 

		   
	//特采购数据（包括已下采购单和没下采购单）
	$UnionSTR.="
		UNION ALL
		   SELECT M.Date AS Date,concat('4') AS Sign,IFNULL(SUM(G.FactualQty+G.AddQty),0) AS Qty    
		   FROM $DataIn.cg1_stocksheet G
		   INNER JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid 
		   WHERE G.StuffId='$StuffId'AND  G.Mid>0 AND G.OrderQty=0 GROUP BY M.Date
		UNION ALL
		   SELECT '$unDate' AS Date,concat('4') AS Sign,IFNULL(SUM(G.FactualQty+G.AddQty),0) AS Qty    
		   FROM $DataIn.cg1_stocksheet G
		   WHERE G.StuffId='$StuffId'AND  G.Mid=0  AND G.OrderQty=0";
}
//入库数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('5') AS Sign,SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StuffId='$StuffId' AND R.Type=1 GROUP BY M.Date";


//检查是否为母配件
$mStuffSign=0;
$mStuffResult=mysql_query("SELECT StuffId,Relation FROM $DataIn.stuffcombox_bom WHERE mStuffId='$StuffId' LIMIT 1 ",$link_id);  
if($mStuffRow = mysql_fetch_array($mStuffResult))	{
   //备品转入数据
   $mStuffSign=1;
	$UnionSTR.="
	UNION ALL
	SELECT '子配件*',concat('6') AS Sign,ROUND(Min(A.Qty/A.Relation)) AS bpQty FROM(
										SELECT S.StuffId,S.Relation,SUM(IFNULL(B.Qty,0)) AS Qty 
										FROM $DataIn.stuffcombox_bom S 
										LEFT JOIN $DataIn.ck1_rksheet B ON S.StuffId=B.StuffId AND  B.Type=2  
										WHERE S.mStuffId='$StuffId'   GROUP BY S.StuffId
									)A 
	";
}
else{
		//备品转入数据
		$UnionSTR.="
		UNION ALL
		SELECT Date,concat('6') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND  Type=2   GROUP BY Date";
}
//领料数据
$UnionSTR.="
UNION ALL
SELECT A.Date,concat('7') AS Sign,SUM(A.Qty) AS Qty FROM (
SELECT S.Date,S.Qty 
FROM $DataIn.ck5_llsheet S  WHERE S.StuffId='$StuffId' AND S.Type IN (1,5)) A GROUP BY  A.Date";

//已出货数据
$UnionSTR.="
UNION ALL
SELECT B.Date,concat('8') AS Sign,SUM(B.Qty) AS Qty
FROM (
		SELECT M.Date,A.Qty 
		FROM (
			SELECT Y.POrderId,SUM(S.Qty) AS Qty 
			FROM $DataIn.ck5_llsheet S 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
			WHERE S.StuffId='$StuffId' AND Y.Estate=0 GROUP BY Y.POrderId
		)A 
			LEFT JOIN  $DataIn.ch1_shipsheet C ON C.POrderId=A.POrderId 
			LEFT JOIN  $DataIn.ch1_shipmain M ON M.Id=C.MId 
		GROUP BY A.POrderId
)B
GROUP BY B.Date";
//LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
//报废数据,只有审核通过的才算 modify by zx 2010-11-30
if ($mStuffSign==1){
    $UnionSTR.="
	UNION ALL
	     SELECT '子配件*',concat('9') AS Sign,ROUND(Min(A.Qty/A.Relation)) AS bfQty FROM(
										SELECT S.StuffId,S.Relation,SUM(IFNULL(B.Qty,0)) AS Qty 
										FROM $DataIn.stuffcombox_bom S 
										LEFT JOIN $DataIn.ck8_bfsheet B ON S.StuffId=B.StuffId AND  (B.Estate=0 OR B.Estate=3) 
										WHERE S.mStuffId='$StuffId'  GROUP BY S.StuffId
									)A";
									
}
else{							
   $UnionSTR.="
	UNION ALL
	SELECT Date,concat('9') AS Sign,SUM(Qty) AS Qty FROM $DataIn.ck8_bfsheet WHERE (Estate=0 OR Estate=3)  AND StuffId='$StuffId' GROUP BY Date";
}	

//退换数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('10') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' AND S.Estate=0 GROUP BY M.Date";

//补仓数据
$UnionSTR.="
UNION ALL
SELECT M.Date,concat('11') AS Sign,SUM(S.Qty) AS Qty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id WHERE S.StuffId='$StuffId' GROUP BY M.Date";
//echo $UnionSTR;
$result = mysql_query($UnionSTR,$link_id);

$DateTemp=array();
$QtyTemp=array();
$SignTemp=array();
$sum1=0;	$sum2=0;	$sum3=0;	$sum4=0;	$sum5=0;
$sum6=0;	$sum7=0;	$sum8=0;	$sum9=0;  $sum10=0; $sum11=0;
if($myrow = mysql_fetch_array($result)){
	//$i=1;
	do{
		$Qty= $myrow["Qty"];
		$Sign= $myrow["Sign"];
		if($myrow["Date"]==""){
			  $Date="0000-00-00";
			}
		else{
			$Date=substr($myrow["Date"],0,10);
			}
		
		if($Qty>0 or $Qty<0){
			$DateTemp[]=$Date;
			$QtyTemp[]=$Qty;
			$SignTemp[]=$Sign;
			//echo $i." - ".$Sign."/".$Date."/".$Qty."<br>";$i++;			
			}
		
		}while ($myrow = mysql_fetch_array($result));		
	}

$grade = array("Date"=>$DateTemp,"Qty"=>$QtyTemp,"Sign"=>$SignTemp);
$tt=array_multisort($grade["Date"], SORT_STRING, SORT_ASC,$grade["Sign"], SORT_NUMERIC, SORT_ASC,$grade["Qty"], SORT_NUMERIC, SORT_ASC);
$count=count($DateTemp);

$colTitle=array("采购日期",
				  "订单数量①","订单数量②","采购数量","特采数量","入库数量",$uType==2?"车间退料":"备品转入",
				  "领料数量","出货数量","报废数量","退换数量","补仓数量",
				  "在库","可用");


$NumOfCol=11;
$ColTemp=$NumOfCol;//当前列
$DateTemp="";
$c2TEMP=$dStockQty;//当天在库,初始值为初始库存
$c3TEMP=$dStockQty;//当天可用库存,初始值为初始库存
$Rowtemp=0;
$colData = array();
$temDate = "";
for($i=0;$i<$count;$i++){
	$Date=$grade[Date][$i];	
	$Qty=$grade[Qty][$i];
	$Sign=$grade[Sign][$i];//有数据的列
	
	if($DateTemp!=$grade[Date][$i]){//新行,如果日期与参照日期不一致，表示新行开始
		$DateTemp=$grade[Date][$i];//重新设置参照日期
		if($ColTemp!=$NumOfCol){//如果当前列数不是9，表示上一行未结束,先补足上一行
			for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
				$colData[$temDate][]="";
			}
			$c3Row=$c3TEMP>=0?$c3TEMP:0;
			$colData[$temDate][]="$c2TEMP";
			$colData[$temDate][]="$c3Row";
		}
		//新行正式开始
		$ColTemp=0;
		$Rowtemp++;
		//新行前两列：序号列和日期列
		if($Date=="0000-00-00"){
			//$Date="<span title='全部还没有下采购单的特采单数量' style='CURSOR: pointer'>◆</span>";
		}
	}

	for($ColTemp=$ColTemp+1;$ColTemp<$Sign*1;$ColTemp++){
		$colData[$DateTemp][]="";
	}
	$colData[$DateTemp][]="$Qty";
	
	switch($ColTemp){
		case 1://订单数量
		   $c3TEMP=round($c3TEMP-$Qty,$Decimals); $sum1=$sum1+$Qty;
		   break;
	    case 2://半成品订单数量
		   $c3TEMP=round($c3TEMP-$Qty,$Decimals); $sum2=$sum2+$Qty;
		   break;
		case 3://采购数量
		   $sum3=$sum3+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
		case 4://特采数量
		   $sum4=$sum4+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
	    case 5://入库数量
		   $c2TEMP=$c2TEMP+$Qty;	$sum5=$sum5+$Qty;
		   break;
		case 6://备品转入
		   $c2TEMP=$c2TEMP+$Qty;	$sum6=$sum6+$Qty;	$c3TEMP=$c3TEMP+$Qty;
		   break;
		case 7://领料数量
		   $c2TEMP=round($c2TEMP-$Qty,$Decimals);	$sum7=$sum7+$Qty;
		   break;
	    case 8: //已出货数量
	         $sum8=$sum8+$Qty;
			 break;
	    case 9://报废数量
		   $c2TEMP=round($c2TEMP-$Qty,$Decimals);	$sum9=$sum9+$Qty; $c3TEMP=round($c3TEMP-$Qty,$Decimals);
		   break;
		case 10://退换数量
		   $c2TEMP=round($c2TEMP-$Qty,$Decimals);	$sum10=$sum10+$Qty;
		   break;
		case 11://补仓数量
		   $c2TEMP=$c2TEMP+$Qty;	$sum11=$sum11+$Qty;	
		   	$c3Row=$c3TEMP>=0?$c3TEMP:0;
			
			$colData[$DateTemp][]="$c2TEMP";
			$colData[$DateTemp][]="$c3Row";

		   break;
	 }
	  $temDate = $DateTemp;
}

if($ColTemp!=$NumOfCol){//上一行未结束
	for($ColTemp=$ColTemp+1;$ColTemp<=$NumOfCol;$ColTemp++){
		$colData[$DateTemp][]="";
		}
		$c3Row=$c3TEMP>=0?$c3TEMP:0;
		$colData[$DateTemp][]="$c2TEMP";
			$colData[$DateTemp][]="$c3Row";
}

//采购未回数量=（采购数量+特采数量+退换数量）-（入库数量+补仓数量）
$Mantissa=($sum2+$sum3)-($sum4);
//可用库存=（初始库存+采购数量+特采数量+备品转入）-（订单需求+报废数量）
$OrderSurplus=($dStockQty+$sum2+$sum3+$sum5)-($sum1+$sum8);
 
//计算已出货数量
$ship_Result = mysql_query("SELECT SUM(G.OrderQty) AS Qty FROM cg1_stocksheet G,yw1_ordersheet S 
WHERE G.POrderId=S.POrderId AND S.Estate=0 AND G.StuffId='$StuffId'",$link_id);
if($ship_Row = mysql_fetch_array($ship_Result)){
	$ship_Qty1=$sum1-$ship_Row["Qty"];
	$ship_Qty2=$sum1-$ship_Qty1;
}

$hasError = 0;

	$tempE1 = $tempE2 = 0;
	if($tStockQty==$c2TEMP && $tStockQty>=0){
		//echo"正确";
		$tempE1 = 0;
		}
	else{
		$tempE1 = 1;
		$hasError = 1;
		//echo"<span class='redN'>不正确</span>";
		}
	
	if($oStockQty==$c3TEMP){
		//echo"正确";
		$tempE2 = 0;
		}
	else{
		$hasError = 1;
		$tempE2 = 1;
		//echo"<span class='redN'>不正确</span>";
}

	//145权限(库存更正)
	if($tStockQty!=$c2TEMP || $oStockQty!=$c3TEMP){
		if(($c2TEMP>=0 || $c3TEMP>=0) || ($c2TEMP<0 && $tStockQty>0) ||  ($c3TEMP<0 && $oStockQty>0)){
			$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=145 and UserId=$LoginNumber LIMIT 1",$link_id);
			if($TRow = mysql_fetch_array($TResult) || $LoginNumber == '11008'){
				//echo"<input type='button' name='Submit' value='更正库存' onClick='javascript:ErrorCorrection();'>";
				} 
			}
		}
		krsort($colData);
		//$colData = array_merge(array("合  计:"=>),$colData);
															 
		$sortedArr[]=array("合  计:","$sum1","$sum2","$sum3","$sum4","$sum5","$sum6",
							 "$sum7","$sum8","$sum9","$sum10","$sum11","$c2TEMP".($tempE2==1?"|#FF0000":""),"$c3Row".($tempE2==1?"|#FF0000":""));
		foreach($colData as $key => $val) {
			$temp = array_merge(array($key),$val);
			$sortedArr[]=$temp;
		}
		

	 $jsonArray = array("ColData"=>$sortedArr,"ColTitle"=>$colTitle,"Sheet"=>"2",
	 					 "Segment"=>array("Segmented"=>array("配件图","配件详情","数据分析表","BOM关联"),
						 "SegmentedId"=>array("0","1","2","3")),
						 "hasError"=>"$hasError","Args"=>"$StuffId|$c2TEMP|$c3Row");
	}
		break;
	case "1":
	{
		$editStuffId = $StuffId;
	$detailArr = array();
		$editStuffInfo = mysql_query("
	select T.TypeName,S.TypeId,U.Name as Unit,S.StuffCname,S.Price,S.Spec,S.Weight,S.DevelopState,
	C.PreChar,P.Forshort ,M.Name as Operator,DATE_FORMAT(S.created,'%Y-%m-%d') as Created
	from $DataIn.stuffdata S 
   left join $DataIn.bps B on B.StuffId=S.StuffId 
left join $DataIn.stufftype T on S.TypeId=T.TypeId
left join $DataIn.stuffunit U on U.Id=S.Unit
left join $DataIn.providerdata P on P.CompanyId=B.CompanyId
      LEFT JOIN $DataPublic.currencydata    C         ON C.id =P.currency
	   LEFT JOIN $DataPublic.staffmain    M         ON M.Number =S.Operator
   where S.StuffId=$StuffId
	");
	$editTypeId= $editUnit= $editStuffCname = $editPrice = $editDevelopState = $editCompanyId = "";
	if ($editStuffInfoRow = mysql_fetch_assoc($editStuffInfo)) {
		$editTypeName       = $editStuffInfoRow["TypeName"];
		$editOperator     = $editStuffInfoRow["Operator"];
		$editCreated      = $editStuffInfoRow["Created"];
		$editTypeId       = $editStuffInfoRow["TypeId"];
		$editUnit         = $editStuffInfoRow["Unit"];
		$editStuffCname   = $editStuffInfoRow["StuffCname"];
		$editPrice        = $editStuffInfoRow["PreChar"].$editStuffInfoRow["Price"];
		$editDevelopState = $editStuffInfoRow["DevelopState"];
		$editCompany   = $editStuffInfoRow["Forshort"];
		$editSpec         = $editStuffInfoRow["Spec"];
		$editWeight       = $editStuffInfoRow["Weight"];
	} 
	/*objective C 
	NSString *const Key_Dict_EditType    = @"EditType";
NSString *const Key_Dict_PlaceHolder = @"PlaceHolder";
NSString *const Key_Dict_FiledName   = @"FiledName";
NSString *const Key_Dict_FieldKey    = @"FieldKey";
NSString *const Key_Dict_FieldVal    = @"FieldVal";
NSString *const Key_Dict_FieldVals    = @"FieldVals";
NSString *const Key_Dict_Content     = @"ContentTxt";


	*/
	
	$detailArr[]=array("FiledName"=>"配件名称","PlaceHolder"=>"","ContentTxt"=>"$editStuffCname");
	$detailArr[]=array("FiledName"=>"配件类型","PlaceHolder"=>"","ContentTxt"=>"$editTypeName");
	$detailArr[]=array("FiledName"=>"参考买价","PlaceHolder"=>"","ContentTxt"=>"$editPrice");
	$detailArr[]=array("FiledName"=>"单      位","PlaceHolder"=>"","ContentTxt"=>"$editUnit");

		$stuffpropertys = array();
		$propIdImagePrefix = "property_";
    $propertysql = mysql_query("SELECT property FROM $DataIn.stuffproperty 
	
	WHERE StuffId=$editStuffId");
			while ($propertysqlRow = mysql_fetch_assoc($propertysql)) {
				$stuffpropertys[]="property_".$propertysqlRow["property"];
			}
				$detailArr[]=array("FiledName"=>"配件属性","PlaceHolder"=>"","ContentTxt"=>"","others"=>array("imgs"=>$stuffpropertys));
				
	$detailArr[]=array("FiledName"=>"开发状态","PlaceHolder"=>"","ContentTxt"=>$editDevelopState==1?"是":"否");
	
	$DevelopTargetWeek = "";
			$DevelopCompany = "";
			if (in_array("property_11",$stuffpropertys)) {
					$developRow = mysql_fetch_assoc(mysql_query("SELECT yearweek(D.Targetdate,1) as targetWeek,C.Forshort FROM $DataIn.stuffdevelop D 
					left join  $DataIn.trade_object  C on C.CompanyId=D.CompanyId
					 WHERE StuffId=$editStuffId"));
					$DevelopTargetWeek = $developRow["targetWeek"];
					$DevelopTargetWeek = substr($DevelopTargetWeek,4,2)."周";
			$DevelopCompany = $developRow["Forshort"];
			$detailArr[]=array("FiledName"=>"开  发  周","PlaceHolder"=>"","ContentTxt"=>"$DevelopTargetWeek");
			$detailArr[]=array("FiledName"=>"客      户","PlaceHolder"=>"","ContentTxt"=>"$DevelopCompany");
			
			}
	
	$detailArr[]=array("FiledName"=>"供  应  商","PlaceHolder"=>"","ContentTxt"=>"$editCompany","hasLine"=>"1");
	
	$formList = array();

$TypeId = $editTypeId;
$needSource = -1;
$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,P1.name as BuyName
 ,T.DevelopGroupId,GP.GroupName,T.DevelopNumber,P2.Name as DevName,
 T.Position,M.CheckSign  FROM $DataIn.StuffType T 
LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position 
left join $DataPublic.staffmain P1 on P1.Number=T.BuyerId
left join $DataPublic.staffmain P2 on P2.Number=T.DevelopNumber
left join $DataIn.staffgroup  GP on GP.GroupId=P2.GroupId
WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
$BuyerId=$checkResult["BuyerId"];
$DevelopGroupId=$checkResult["DevelopGroupId"];
$DevelopNumber=$checkResult["DevelopNumber"];
$SendFloor=$checkResult["Position"];
$CheckSign=$checkResult["CheckSign"];
$BuyName=$checkResult["BuyName"];
$GroupName=$checkResult["GroupName"];
$DevName=$checkResult["DevName"];

$SendFloor=$SendFloor==""?0:$SendFloor;
include "../../model/subprogram/stuff_GetFloor.php";
$CheckSign=$CheckSign==""?0:$CheckSign;
$DevelopGroupId=$DevelopGroupId==""?0:$DevelopGroupId;
$DevelopNumber=$DevelopNumber==""?0:$DevelopNumber;

$Pjobid=	$GicJobid=$DevelopGroupId;
$PicNumber=	$GicNumber=$DevelopNumber;

$GCField=array(-1,-1);	//系统默认
$ForcePicSpe = -1;
$GCjobid=$GCField[0];
$GcheckNumber=$GCField[1];


$jhDays=0;

$StuffEname="";$BoxPcs ="0";$Remark="";
/*
NSString *const Key_Dict_EditType    = @"EditType";
NSString *const Key_Dict_PlaceHolder = @"PlaceHolder";
NSString *const Key_Dict_FiledName   = @"FiledName";
NSString *const Key_Dict_FieldKey    = @"FieldKey";
NSString *const Key_Dict_FieldVal    = @"FieldVal";
NSString *const Key_Dict_Content     = @"ContentTxt";


*/

$FormNames = array("下单需求","开发负责人","图档审核","采      购","送货楼层","品检方式");
$FormTexts = array("","$GroupName-$DevName","","$BuyName","$SendFloor","全检");

$countA = 6;
for ($i=0; $i < $countA; $i ++) {
	$detailArr[] = array("EditType"=>"6","PlaceHolder"=>"系统默认","FiledName"=>$FormNames[$i],"ContentTxt"=>$FormTexts[$i],"hasLine"=>($countA-1 == $i)?"1":"0");
}

$connectedSql = mysql_query("select A.GoodsName 
from $DataIn.cut_die D
left join  $DataPublic.nonbom4_goodsdata A on A.GoodsId=D.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId

where D.StuffId='$StuffId'
");
$i = 0;
while ($connectedSqlRow = mysql_fetch_assoc($connectedSql)) {
	$GoodsName = $connectedSqlRow["GoodsName"];
	
	$detailArr[]=array("FiledName"=>$i==0?"模具关联":"","PlaceHolder"=>"","ContentTxt"=>"$GoodsName");
	$i ++;
}
if ($i==0) {$detailArr[]=array("FiledName"=>$i==0?"模具关联":"","PlaceHolder"=>"","ContentTxt"=>"");}

	$countTemp = count($detailArr);
	$detailArr[$countTemp-1]["hasLine"]="1";


	$detailArr[]=array("FiledName"=>"操  作  人","PlaceHolder"=>"","ContentTxt"=>"$editOperator","FieldVal"=>"$editCreated");
			
			
	$jsonArray = array("detail"=>$detailArr,"Sheet"=>"1");
	}
		break;
	case "3":
	{
	   $IdList =array();
        get_semifinished_relation($StuffId,$DataIn,$link_id,$IdList,0);
       if (count($IdList)>0){
	      $mStuffIds= implode(',', $IdList);
	      $StuffId = $StuffId . ',' . $mStuffIds;
       }
       
		$pandsSql = mysql_query("
		
select C.Forshort,P.cName,P.eCode,A.ProductId ,sum(A.CountOrder) CountOrder ,sum(A.OrderAll) OrderAll ,max(A.OrderDate) OrderDate,P.Estate
from 
(
select 
	PD.ProductId ,0 CountOrder,0 OrderAll,'' OrderDate 
	from $DataIn.pands PD 
	where StuffId  IN ($StuffId)  
union all  

select 
	ST.ProductId ,sum(1) CountOrder ,sum(ST.Qty) OrderAll ,max(M.OrderDate) OrderDate 
	from $DataIn.cg1_stocksheet S   
	left join  $DataIn.yw1_ordersheet ST on ST.POrderId=S.POrderId  
	left join  $DataIn.yw1_ordermain M on M.OrderNumber=ST.OrderNumber   
	
	where S.StuffId IN ($StuffId) 
)
 A 
left join $DataIn.productdata P on P.ProductId=A.ProductId 
left join $DataPublic.trade_object C on C.CompanyId = P.CompanyId 
group by A.ProductId order by OrderDate desc,ProductId desc;
		
		");
$pandsArray = array();
		while ($pandsSqlRow = mysql_fetch_assoc($pandsSql)) {
			$Forshort = $pandsSqlRow["Forshort"];
			$cName = $pandsSqlRow["cName"];
			$eCode = $pandsSqlRow["eCode"];
			$ProductId = $pandsSqlRow["ProductId"];
			$CountOrder = $pandsSqlRow["CountOrder"];
			$OrderAll = $pandsSqlRow["OrderAll"];
			$OrderAll = $OrderAll>0 ? number_format($OrderAll) : "";
			$CountOrder = $CountOrder > 0 ? "(".$CountOrder.")" : "0";
			$qty_count = $OrderAll.$CountOrder;
			$OrderDate = $pandsSqlRow["OrderDate"];
			$Estate = $pandsSqlRow["Estate"];
			if ($ProductId>0)
		$pandsArray []= array("forshort"=>"$Forshort","cName"=>"$cName","eCode"=>"$eCode","estate"=>"$Estate","qty_count"=>"$qty_count","orderdate"=>"$OrderDate","productid"=>"$ProductId");
			
		
		}
		$jsonArray= array("Sheet"=>"3","connect"=>$pandsArray);
		/*t[@"forshort"]) {
                bomCell.lbl4Title.text = cellDict[@"forshort"];
            } else {
                bomCell.lbl4Title.text = @"";
            }
            if (cellDict[@"productid"] && cellDict[@"productid"]) {
                bomCell.lbl4Name.text = [NSString stringWithFormat:@"%@-%@",cellDict[@"productid"],cellDict[@"cName"]];
            } else {
                bomCell.lbl4Name.text = @"";
            }
            if (cellDict[@"eCode"]) {
                bomCell.lbl4Ecode.text = cellDict[@"eCode"];
            } else {
                bomCell.lbl4Ecode.text = @"";
            }
            bomCell.img4Fobidden.hidden = ![cellDict[@"estate"] integerValue] == 0;
            
*/
	}
		break;
	default : break;
}








	 ?>