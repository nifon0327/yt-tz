<?php
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=21;				
$tableMenuS=600;
$funFrom="yw_ordership";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|55|序号|30|出货日期|110|交期|70|交货周期|60|交期差值|60|业务单号|120|PI回传单|120|送货单号|110|订单流水号|110|产品名称|150|产品属性|60|产品条码|140|单位|35|售价|80|数量|50|金额|100|包装说明|90|itf码|80|出货方式|70|报关方式|60|待出备注|110|转发对象名称|120|报价规则|450";
$sumCols="14,15";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,38";
$unColorCol=0;
//步骤3：
if($ClientId!=""){
	$ClientSTR="and M.CompanyId='$ClientId'";
	}
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";	
	$SearchRowsA="";
	$SearchRows=" and M.Estate='0'";	
	$bg_Result = mysql_query("SELECT Id,Name FROM $DataIn.taxtype  WHERE Id>1 AND Estate=1 ORDER BY Id",$link_id);
	if($bgRow = mysql_fetch_array($bg_Result,MYSQL_ASSOC)) {
		echo"<select name='TaxTypeId' id='TaxTypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='all' selected>全部</option>";
		do{			
			$BG_Id=$bgRow["Id"];
			$BG_Name=$bgRow["Name"];
			if($TaxTypeId==$BG_Id){
				echo"<option value='$BG_Id' selected>$BG_Name</option>";
				$SearchRowsA=" AND P.taxtypeId = '$BG_Id' ";
				}
			else{
				echo"<option value='$BG_Id'>$BG_Name</option>";			
				}
			}while($bgRow = mysql_fetch_array($bg_Result));
		echo"</select>&nbsp;";
		}
		//日期
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY  M.Date  ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
        echo"<option value='all' selected>全部时间</option>";
        do{
            $dateValue=$dateRow["Date"];
            $chooseDate=$chooseDate==""?$dateValue:$chooseDate;
            if($chooseDate==$dateValue){
                echo"<option value='$dateValue' selected>$dateValue</option>";
                $SearchRows.=" and  M.Date='$dateValue' ";
            }
            else{
                echo"<option value='$dateValue'>$dateValue</option>";
            }
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows 
 GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='all' selected>全部客户</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
            $CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
		//栋层
	$BuildFloorResult = mysql_query("SELECT distinct SUBSTRING_INDEX(P.cName,'-',2) as BuildFloor FROM $DataIn.ch1_shipsheet S
        LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        WHERE 1 AND S.Type='1' $SearchRows  $SearchRowsA ORDER BY BuildFloor",$link_id);
	if($BuildFloorRow = mysql_fetch_array($BuildFloorResult)) {
		echo"<select name='BuildFloor' id='BuildFloor' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='all' selected>全部栋层</option>";
		do{
			$thisBuildFloor=$BuildFloorRow["BuildFloor"];
            $BuildFloorRes=explode("-",$thisBuildFloor);

            $BuildFloor=$BuildFloor==""?$thisBuildFloor:$BuildFloor;
			if($BuildFloor==$thisBuildFloor){
				echo"<option value='$thisBuildFloor' selected>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
				$SearchRows.=" and P.cName like '$thisBuildFloor%' ";
				}
			else{
				echo"<option value='$thisBuildFloor'>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
				}
			}while ($BuildFloorRow = mysql_fetch_array($BuildFloorResult));
		echo"</select>&nbsp;";
		}
//类型
	$TypeResult = mysql_query("SELECT  P.TypeId,T.TypeName FROM $DataIn.ch1_shipsheet S
        LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
        WHERE 1 AND S.Type='1' $SearchRows  $SearchRowsA GROUP BY P.TypeId",$link_id);
	if($TypeRow = mysql_fetch_array($TypeResult)) {
		echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='all' selected>全部类型</option>";
		do{
			$thisTypeId=$TypeRow["TypeId"];
            $thisTypeName=$TypeRow["TypeName"];
            $TypeId=$TypeId==""?$thisTypeId:$TypeId;
			if($TypeId==$thisTypeId){
				echo"<option value='$thisTypeId' selected>$thisTypeName</option>";
				$SearchRows.=" and P.TypeId = '$thisTypeId' ";
				}
			else{
				echo"<option value='$thisTypeId'>$thisTypeName</option>";
				}
			}while ($TypeRow = mysql_fetch_array($TypeResult));
		echo"</select>&nbsp;";
		}

         $buySign=$buySign==""?0:$buySign;
         $buySignStr="buySign".$buySign;
          $$buySignStr="selected";
		echo"<select name='buySign' id='buySign' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='0' $buySign0>全部</option>";
		echo"<option value='1' $buySign1>自购</option>";
		echo"<option value='2' $buySign2>代购</option>";
		echo"<option value='3' $buySign3>客供</option>";
		echo"</select>&nbsp;";
        if($buySign>0){
				$SearchRows.=" and P.buySign='$buySign' ";
               }
	}
else{
		$SearchRows.=" and M.Estate='0'";	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理FILTER: revealTrans(transition=7,duration=0.5) blendTrans(duration=0.5);
$sumQty=0;
$sumAmount=0;
$sumTOrmb=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Date,M.InvoiceNO,M.InvoiceFile,
S.Id,S.Mid,S.POrderId,S.ProductId,S.Qty,S.Price,S.Type,S.YandN,
P.cName,P.eCode,P.TestStandard,P.bjRemark,P.buySign,U.Name AS Unit,
YS.OrderPO,YS.PackRemark,YS.DeliveryDate,YS.ShipType,E.Leadtime,E.Leadweek,E.PI,
YM.ClientOrder,YS.dcRemark,YM.OrderDate,P.Code,L.Id AS SplitId,BG.Name AS bgName  
FROM $DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.yw1_ordersheet YS ON YS.POrderId=S.POrderId
LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=YS.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.taxtype BG ON  BG.Id = P.taxtypeId 
LEFT JOIN $DataIn.productunit U ON U.Id=P.Unit
LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=YS.Id
LEFT JOIN $DataIn.ch1_shipsplit L ON L.ShipId=S.Id 
WHERE 1 AND S.Type='1' $SearchRows  $SearchRowsA ORDER BY M.Date DESC,M.CompanyId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$Dir=anmaIn("../download/teststandard",$SinkOrder,$motherSTR);
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$Id=$myRow["Id"];
		$MId=$myRow["Mid"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);			
		$TestStandard=$myRow["TestStandard"];
		$POrderId=$myRow["POrderId"];
		include "../admin/Productimage/getPOrderImage.php";
		
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];	
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
        if ($InvoiceFile==1){
             $dfname=urldecode($InvoiceNO);
	        $InvoiceFile=strlen($InvoiceNO)>20?"<a href=\"openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">$InvoiceNO</a>":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\" >$InvoiceNO</a>";
        }
        else{
	        $InvoiceFile="$InvoiceNO;";
        }		
		
		$PI=$myRow["PI"];
		$PIRebackFilePath="../download/pipdfreback/PIback" .$PI.".pdf";
		if(file_exists($PIRebackFilePath)){
            $f2=anmaIn("PIback".$PI.".pdf",$SinkOrder,$motherSTR);
            $d2=anmaIn("download/pipdfreback/",$SinkOrder,$motherSTR);	
            $PIReback="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>PIback" .$PI.".pdf</a>";
		}
        else{
            $PIReback="&nbsp;";
            
            $ClientOrder=$myRow["ClientOrder"];
			if($ClientOrder!=""){//原单在序号列显示
				$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
				$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
				$PIReback="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$ClientOrder</a>";
				}
        }
        
        $ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
			}
		else{
			$ClientOrder=$i;
			}
        
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$sumAmount=$sumAmount+$Amount;
		$PackRemark=$myRow["PackRemark"];
        $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];
        $bjRemark=$myRow["bjRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$ShipType=$myRow["ShipType"];
		$bgName =$myRow["bgName"]==""?"&nbsp;":$myRow["bgName"];
        $buySign=$myRow["buySign"];
        $Code = explode('|', $myRow["Code"]);
        $Code = $Code[1];
        switch($buySign){
               case "0":  $buySign="&nbsp;";break;
               case "1":  $buySign="<span>自购</span>";break;
               case "2":  $buySign="<span class='redB'>代购</span>";break;
               case "3":  $buySign="<span class='greenB'>客供</span>";break;
            }
		 //出货方式
	   if (is_numeric($ShipType)){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }
     else{
             $CheckShipTypeResult=mysql_fetch_array(mysql_query("SELECT ShipType  FROM $DataIn.ch1_shipsplit  WHERE ShipId=$Id",$link_id));
             $SplitShipType=$CheckShipTypeResult["ShipType"];
             if(strlen(trim($SplitShipType))>0){
		          $ShipType="<image src='../images/ship$SplitShipType.png' style='width:20px;height:20px;'/>";
                }
           else{
		          $ShipType="&nbsp;";
               }
         }
		$Date=$myRow["Date"];
        $ClientOrder=$myRow["ClientOrder"];
		if($ClientOrder!=""){//原单在序号列显示
			$f2=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
			$d2=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);		
			$ClientOrder="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
			}
		else{
			$ClientOrder=$i;
			}
		//$Date=CountDays($Date,0);
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];		
		$sumQty=$sumQty+$Qty;
                
        $Leadtime=$myRow["Leadtime"]; //PI交期
        
        if ($Leadtime!="" && strtotime($Leadtime)>0){
           $diffday=(strtotime($Date)-strtotime($Leadtime))/3600/24;
           if ($diffday<=0){
               if ($diffday<-5) {
                   $diffday="<div style='color:#00DD00;'>⬆  " . abs($diffday) . "天</div>";
                  }
              else{
                   $diffday="<span style='color:#00DD00;'>⬆  </span>" . abs($diffday) . "天";
                 }
            }
            else{
                if ($diffday>5) { 
                       $diffday="<div style='color:#FF0000;'>⬇  " . abs($diffday) . "天</div>";
                   }
                   else {
                       $diffday="<span style='color:#FF0000;'>⬇  </span>" . abs($diffday) . "天";
                   }
             }
        }          
        else{
           $diffday="&nbsp;"; 
           $Leadtime=$Leadtime==""?"&nbsp;":$Leadtime;
        }
        $Leadweek=$myRow["Leadweek"];
	    include "../model/subprogram/PI_Leadweek.php";
        
       //echo $day;
        $OrderDate=$myRow["OrderDate"];
         if ($OrderDate!="" && strtotime($OrderDate)>0){
                   $cycleDay=((strtotime($Date)-strtotime($OrderDate))/3600/24)."天";
                }          
                else{
                   $cycleDay="&nbsp;"; 
             }
		////////////////////////////
			//订单状态色：有未下采购单，则为白色,属性为可供的除外
			$checkColor=mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
             LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
			WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) AND IFNULL(OP.Property,0)=0  and G.PorderId='$POrderId'",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
				$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
				}
		else{//已全部下单，看领料数量
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//领料数量不等时，黄色
			$checkLL=mysql_fetch_array(mysql_query("SELECT SUM(L.Qty) AS LQty FROM $DataIn.ck5_llsheet L WHERE L.StockId LIKE '$POrderId%'",$link_id));
			$LQty=$checkLL["LQty"];
			$checkCK=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS GQty FROM $DataIn.cg1_stocksheet G WHERE G.POrderId='$POrderId'",$link_id));
			$GQty=$checkCK["GQty"];	
			if($GQty!=$LQty){
				$OrderSignColor="bgColor='#F5F5F5'";
				}
			}

        $itf == '';
        //计算itf14码
        $hasPOrderPrintParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' Order by Id Limit 1";
        $hasPOrderPrintParameterResult = mysql_query($hasPOrderPrintParameterSql);
        if(mysql_num_rows($hasPOrderPrintParameterResult) > 0){
            $hasPOrderPrintParameterRow = mysql_fetch_assoc($hasPOrderPrintParameterResult);
            $itf = $hasPOrderPrintParameterRow['itf'];
        }else{
            $productParameterSql = "Select * From $DataIn.productprintparameter Where   productId = '".$ProductId."' AND Estate = 1 Order by Id Limit 1";
            $productParameterResult = mysql_query($productParameterSql);
            if(mysql_num_rows($productParameterResult) > 0){
                $hasProductPrintParameterRow = mysql_fetch_assoc($productParameterResult);
                $itf = $hasProductPrintParameterRow['itf'];
            }else{
                $itf = '4';
            }
        }

         if($itf == ''){
            $itf = '4';
        }
        //echo $Code;
        $itfCode = 0;
        $newCode = substr($Code, 0, 12);
        $newCode = $itf . $newCode;
        $len = strlen($newCode);
        #$double = 0;
        #$single = 0;
        for($tempi=0; $tempi<$len; $tempi++){
            $temp = substr($newCode, $len-$tempi-1, 1);
            $pointValue = ($tempi+1)%2==0?1:3;
            $itfCode = $itfCode + ($pointValue*intval($temp));
        }
        $itfCode = $itfCode%10==0?0:10-($itfCode%10);
        $itf = $newCode.$itfCode;
        
        $ToOutName="&nbsp;";
		$SplitId=$myRow["SplitId"];	
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE  O.POrderId='$POrderId' AND O.Mid='$SplitId' ",$link_id);//
		//echo "";
		while($Outmyrow = mysql_fetch_array($OutResult)) {
			//删除数据库记录
			//$Forshort=$myRow["Forshort"]; 
			$ToOutName=$Outmyrow["ToOutName"];
		}

        // 出货数量+方量
        $sListSql = "SELECT sum(S.Qty) AS Qty,sum(P.Weight) AS Weight
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$MId' AND S.Type='1'";
        $sListResult = mysql_query($sListSql,$link_id);
        if($ret = mysql_fetch_array($sListResult)){
            $tQty = $ret['Qty'];
            $tWeight = $ret['Weight'];
        };

		//动态读取
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,0);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏配件采购明细资料. $GQty!=$LQty ' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//0:内容	1：对齐方式		2:单元格属性		3：截取
			$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
            array(0=>$Leadweek,		1=>"align='center'"),
            array(0=>$cycleDay,		1=>"align='center'"),
            array(0=>$diffday,		1=>"align='center'"),
			array(0=>$OrderPO, 1=>"align='center'"),
			array(0=>$PIReback,		1=>"align='center'"),
			array(0=>$InvoiceFile, 1=>"align='center'"),
			
			array(0=>$POrderId,		1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$buySign, 		1=>"align='center'"),
			array(0=>$eCode, 		3=>"..."),
			array(0=>$Unit, 		1=>"align='center'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Qty, 			1=>"align='right'"),
			array(0=>$Amount, 		1=>"align='right'"),
			array(0=>$PackRemark, 	3=>"..."),
            array(0=>$itf,   3=>"..."),
			array(0=>$ShipType,1=>"align='center'"),
			array(0=>$bgName,1=>"align='center'"),
			array(0=>$dcRemark,1=>"align='left'"),
			
			array(0=>$ToOutName,   1=>"align='center'"),
			array(0=>$bjRemark)
				);
		$checkidValue=$Id.'|'.$tWeight;
		//include "../model/subprogram/read_model_6.php";
	     include "subprogram/read_model_6_yw.php";
		echo $StuffListTB;
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
ChangeWtitle($SubCompany.$DefaultClient."客户已出明细列表");
include "../model/subprogram/read_model_menu.php";
?>