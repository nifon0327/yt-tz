<?php   
//电信-zxq 2012-08-01
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
//$Th_Col="选项|40|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|出货方式|50|待出备注|110|转发对象名称|150|售价|60|出货数量|60|金额|60|订单日期|70";
$Th_Col="选项|40|序号|40|中文名|220|Product Code/Description|220|出货方式|50|待出备注|110|售价|60|出货数量|60|金额|60|订单日期|70";

$ColsNumber=12;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
$SearchSTR=0;		//不允许搜索
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$SearchRows1=" and SP.Estate='1'  AND SP.ShipId=0 ";
$SearchRows2=" and S.Estate='1' ";

//筛选 by.xyg 2018.08.19
//项目
$CompanySql=" SELECT * FROM (
		SELECT M.CompanyId,T.Forshort
		FROM $DataIn.ch1_shipsplit SP 
        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
		LEFT JOIN trade_object T ON T.CompanyId = M.CompanyId
		WHERE 1 $SearchRows1  AND K.tStockQty >= SP.Qty AND SP.shipSign =1 
	    UNION ALL 
		SELECT '' AS Forshort,'' AS CompanyId
		FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2 ) A  WHERE 1	GROUP BY A.Forshort";
$CompanyVal = mysql_query($CompanySql,$link_id);
if ($CompanyRow = mysql_fetch_array($CompanyVal)){
    $CompanyList="<select name='Jid' id='Jid' onChange='ResetPage(1,4)'>";
    do{
        $theCompany=$CompanyRow["Forshort"];
        $theCompanyId = $CompanyRow["CompanyId"];
        $Jid=$Jid==""?$theCompanyId:$Jid;
        if($theCompanyId==$Jid){
            $CompanyList.="<option value='$theCompanyId' selected>$theCompany</option>";
            $SearchRows1.=" AND M.CompanyId='$theCompanyId'";
            $SearchRows2.="  AND S.CompanyId='$theCompanyId'";
        }
        else{
            $CompanyList.="<option value='$theCompanyId'>$theCompany</option>";
        }
    }while($CompanyRow = mysql_fetch_array($CompanyVal));
    $CompanyList.="</select>";
}

//栋层
$cNameSql=" SELECT *  FROM (
		SELECT substring_index(P.cName,'-',2) as cName
		FROM $DataIn.ch1_shipsplit SP 
        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
		WHERE 1 $SearchRows1  AND K.tStockQty >= SP.Qty AND SP.shipSign =1 
	    UNION ALL 
		SELECT '' AS cName
		FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2 ) A  WHERE 1	GROUP BY A.cName";
$cNameVal = mysql_query($cNameSql,$link_id);
if ($cNameRow = mysql_fetch_array($cNameVal)){
    $cNameList="<select name='BuildFloor' id='BuildFloor' onChange='ResetPage(1,4)'>";
    do{
        $thecName=$cNameRow["cName"];
        $BuildFloor=$BuildFloor==""?$thecName:$BuildFloor;
        if($thecName==$BuildFloor){
            $cNameList.="<option value='$thecName' selected>$thecName</option>";
            $SearchRows1.=" AND P.cName LIKE '%$thecName%' ";
        }
        else{
            $cNameList.="<option value='$thecName'>$thecName</option>";
        }
    }while($cNameRow = mysql_fetch_array($cNameVal));
    $cNameList.="</select>";
}

echo "$CompanyList  $cNameList";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
//List_Title($Th_Col,"1",0);


function List_TitleYW($Th_Col,$Sign,$Height,$ToOutId,$CompanyId,$DataIn,$link_id,$nowWebPage){
	if($Height==1){		//高度自动
		$HeightSTR="";
		}
	else{
		$HeightSTR="height='25'";
		} 
	$Field=explode("|",$Th_Col);
	$Count=count($Field);
	if($Sign==1){
		$tId="id='TableHead'";
		}
	$tableWidth=0;
	// add by zx 2011-0326
	for ($i=0;$i<$Count;$i=$i+2){
		$j=$i;
		$k=$j+1;
		//$tableWidth+=$Field[$j];
		$tableWidth+=$Field[$k];
		//$tableWidth=$tableWidth+10;
		}
	if(isFireFox()==1){	 //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
	    //echo "FireFox";
		$tableWidth=$tableWidth+$Count*2;
	}
	
	if (isSafari6()==1){
	   $tableWidth=$tableWidth+ceil($Count*1.5)+1; 
	}
	
	
	if (isGoogleChrome()==1){
		$tableWidth=$tableWidth+ceil($Count*1.5);
	}	
	
	for ($i=0;$i<$Count;$i=$i+2){
		if($Sign==1){
			$Class_Temp=$i==0?"A1111":"A1101";}
		else{
			$Class_Temp=$i==0?"A0111":"A0101";}
		$j=$i;
		$k=$j+1;
		//$tableWidth+=$Field[$j];
		//$tableWidth+=$Field[$k];
                if (isSafari6()==0 ){
                    if($k==($Count-1)){  // add by zx 2011-0326  兼容IE,FIREFOX
                            $Field[$k]="";
                    }
                }
		$h=$j+2;
		if(($Field[$j]=="中文名"&& $Field[$h]=="&nbsp;") || $Field[$j]=="&nbsp;"){
				  if($Sign==1){$Class_Temp="A1100";}
				  else {$Class_Temp="A0100";}

		  }
		  
		if($Field[$j]=="转发对象名称"){	
		           $inForT="";							 				  
				   $ToOutNameResult = mysql_query("SELECT * from (
					SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
					FROM $DataIn.ch1_shipsplit   SP   
					LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid>0 AND D.Estate=1 AND SP.Estate>0  AND D.CompanyId='$CompanyId' 
					UNION ALL
					SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
					FROM $DataIn.yw7_clientOutData O
					LEFT JOIN  $DataIn.yw1_ordersheet S  ON O.POrderId=S.POrderId
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid=0 AND D.Estate=1 AND S.Estate>0  AND D.CompanyId='$CompanyId' ) A group by ToOutId
					
					",$link_id);
					/*
					echo "SELECT * from (
					SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
					FROM $DataIn.ch1_shipsplit   SP   
					LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid>0 AND D.Estate=1 AND SP.Estate>0  AND D.CompanyId='$CompanyId' 
					ALL
					SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
					FROM $DataIn.yw7_clientOutData O
					LEFT JOIN  $DataIn.yw1_ordersheet S  ON O.POrderId=S.POrderId
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid=0 AND D.Estate=1 AND S.Estate>0  AND D.CompanyId='$CompanyId' ) A group by ToOutId";
					*/
		          if($ToOutNameRow = mysql_fetch_array($ToOutNameResult)){
		          $inForT="<select name='ToOutId' id='ToOutId' onchange='document.form1.submit();'>";
		          $inForT.="<option value='' selected> $Field[$j] </option>";
				  do{
					      
					      $thisToOutId=$ToOutNameRow["Id"];
					      if($ToOutId==$thisToOutId){
					      	$inForT.= "<option value='$ToOutNameRow[Id]' selected>$ToOutNameRow[Forshort]-$ToOutNameRow[Name]</option>";
					      }else{
						    $inForT.= "<option value='$ToOutNameRow[Id]'>$ToOutNameRow[Name]</option>";  
					      }
					  } while($ToOutNameRow = mysql_fetch_array($ToOutNameResult));
					  $inForT.="</select>&nbsp;";
			      }
				  else{
					 $inForT.="$Field[$j]"; 
				  }
				 
				 
				 $TableStr.="<td width='$Field[$k]' Class='$Class_Temp'> $inForT </td>";
			
		}else{
			$TableStr.="<td width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
		}
	}
	echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr $HeightSTR class='' align='center'>".$TableStr."</tr></table>";
	if($Sign==0){
		echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
		}
}
//***********************************************
//List_TitleYW($Th_Col,"1",1,$ToOutId,$CompanyId,$DataIn,$link_id,$nowWebPage);
List_TitleYW($Th_Col,"1",1,$ToOutId,$Jid,$DataIn,$link_id,$nowWebPage);
$SearchRowsK="";
if($ToOutId!=""){
	$SearchRowsK =" AND ((O.ToOutId='$ToOutId' AND O.Mid>0) OR (OP.ToOutId='$ToOutId' AND OP.Mid=0))  "; 
	//echo "SearchRows:$SearchRows";
}

if($ShipSign==-1){//待扣项目
	$mySql="SELECT '' AS OrderNumber,S.CompanyId,PO AS OrderPO,S.Date AS OrderDate,'3' AS Type,S.Id,S.Number AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,S.Description AS cName,S.Description AS eCode 
	FROM $DataIn.ch6_creditnote S WHERE 1 $SearchRows2";
	}
else{	//待出订单和随货样品
	$mySql=" SELECT * FROM (
		SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,SP.Id,S.OrderPO,S.POrderId,S.ProductId,SP.Qty,S.Price,S.PackRemark,
        P.cName,P.eCode,SP.ShipType ,S.dcRemark,SP.OrderSign
		FROM $DataIn.ch1_shipsplit SP 
        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SP.POrderId
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
		LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
		LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	    LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
		WHERE 1 $SearchRows1 $SearchRowsK AND K.tStockQty >= SP.Qty AND SP.shipSign =1 
	    UNION ALL 
		SELECT '' AS OrderNumber,S.CompanyId,S.Date AS OrderDate,'2' AS Type,S.Id,S.SampPO AS OrderPO,S.SampId AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,S.SampName AS cName,S.Description AS eCode,'' AS ShipType ,'' AS dcRemark,'' AS OrderSign
		FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2 ) A  WHERE 1 ORDER BY A.POrderId";
	}
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;	$theDefaultColor="";	
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
        $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];		
		$checkidValue=$Type."^^".$Id."^^".$OrderPO."^^".$cName."^^".$eCode."^^".$Price."^^".$Qty;
		$OrderPO=$Type==2?"随货项目":$OrderPO;
        $OrderSign=$myRow["OrderSign"];       
        if($OrderSign>0)$theDefaultColor="#FFAEB9";//#E9FFF5
        $ShipType=$myRow["ShipType"];
         //出货方式
	    if(strlen(trim($ShipType))>0){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }
		$Locks=1;$LockRemark="";
		if($Type==1){//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择
			//工序总数
		$checkShipRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ch1_shipsheet 
            WHERE POrderId='$POrderId'",$link_id));
        $shipQty = $checkShipRow["Qty"];
        if($shipQty+$Qty ==$thisQty){ //最后一次限制
				$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
					FROM $DataIn.cg1_stocksheet G
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE G.POrderId='$POrderId' AND G.level = 1 AND T.mainType=3",$link_id));
				$gxQty=$CheckgxQty["gxQty"];
				//已完成的工序数量
				$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = C.StockId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			    LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE C.POrderId='$POrderId' AND G.level = 1 AND T.mainType=3",$link_id));
				$scQty=$CheckscQty["scQty"];
				//echo $gxQty."|".$scQty;
				if($gxQty!=$scQty){//生产完毕
					$LockRemark="生产登记异常！";
					$Locks=0;//不能操作
					}
				//检查领料记录 备料总数与领料总数比较
				$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
					FROM $DataIn.cg1_stocksheet G
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE G.POrderId='$POrderId' AND G.Level = 1 AND G.blsign = 1",$link_id));
				$blQty=$CheckblQty["blQty"];
				$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
					FROM $DataIn.cg1_stocksheet G 										
					LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE G.POrderId='$POrderId' AND K.Estate=0 AND G.Level = 1 AND G.blsign = 1",$link_id));
				$llQty=$CheckllQty["llQty"];
				
				if($blQty!=$llQty){//领料完毕
					$LockRemark.="领料异常！";
					$Locks=0;//不能操作
					}
			  }
			  if ($ShipType==""){
			    $LockRemark.="业务未填出货方式。";
		        $Locks=0;//不能操作
		        }
		        /*$Leadtime=$myRow["Leadtime"];
				 if ($Leadtime==""){
					  $LockRemark.="未生成PI，不能出货。";
				      $Locks=0;//不能操作
				 }*/
				/* if ($TestStandardSign==0){
                    $LockRemark.="标准图状态不正常。";
		           $Locks=0;//不能操作
                  }*/
		}
		
		$ToOutName="&nbsp;";
		
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$Id'",$link_id);
		//echo ""
		if ($Outmyrow = mysql_fetch_array($OutResult)) {
			//删除数据库记录
			//$Forshort=$myRow["Forshort"]; 
			$ToOutName=$Outmyrow["ToOutName"];
		}else{
			$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
									  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
									  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ",$link_id);
			//echo "";
			if ($Outmyrow = mysql_fetch_array($OutResult)) {
				//删除数据库记录
				//$Forshort=$myRow["Forshort"]; 
				$ToOutName=$Outmyrow["ToOutName"];
			}			
		}
		
		$ValueArray=array(
//			array(0=>$OrderPO,
//					 1=>"align='center'"),
//			array(0=>$POrderId,
//					 1=>"align='center'"),
//			array(0=>$ProductId,
//					 1=>"align='center'"),
			array(0=>$cName,
					 3=>"..."),
			array(0=>$eCode,
					 3=>"..."),
			array(0=>$ShipType,
					 1=>"align='center'"),
		    array(0=>$dcRemark,1=>"align='left'"),
//		    array(0=>$ToOutName,
//					 1=>"align='center'"),
			array(0=>$Price,
					 1=>"align='center'"),
			array(0=>$Qty,					
					 1=>"align='center'"),
			array(0=>$Amount,
					 1=>"align='center'"),
			array(0=>$OrderDate,
					 1=>"align='center'")
			);
		
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>