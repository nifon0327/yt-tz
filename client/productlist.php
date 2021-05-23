<?php   
/*$DataIn.productdata $DataIn.trade_object$ DataIn.producttype */
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=7;
$tableMenuS=500;
ChangeWtitle("$SubCompany Product List");
$ClientAction=0;
$ChooseOut="N";
$toExcelStr="";
 
$CompanyIdArray1 = array(1004,1059,1072);
$CompanyIdArray2 = array(1081,1002,1080,1065);

$CompanyIdArray3 = array(100262,100241);
$CompanyIdArray4 = array(1066,1081,1002,1080,1065,1064,1071,1084,1074,1086,100111,1090,100262,100241);

if (in_array($myCompanyId, $CompanyIdArray1)){  
         $CompanySTR="and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072') ";
         $ClientAction=1;
         $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
        }
else{
	    if (in_array($myCompanyId, $CompanyIdArray2) ) {
		        $CompanySTR="and P.CompanyId in ('1081','1002','1080','1065')";
	           }
	    else {
               if($myCompanyId==1091) $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
    	        $CompanySTR=" AND P.CompanyId='$myCompanyId'";
	          }
      }
if($model!=""){
switch($model){
	case "a"://mco
       if ($ClientAction==1){//CEL 公司
           $Th_Col="Choose|50|No.|40|ID|50|中文名|220|Product Code|180|Price|50|P_Weight|60|Unit/<br>Carton|50|Box Weight|50|Average Leadtime|55|Est.Leadtime|80|Order History|80|Rejects|60|Date of Latest Order|80|Supplier Rating|80|ProviderInfo|120";
           $ColsNumber=13;
        }
        else{
          
	           $bjRemark_Cols=($myCompanyId==1080 || $myCompanyId==1081)?"|Quotation rules|200":"";
	           $Th_Col="Choose|40|No.|35|ID|50|中文名|280|Product Code|120|Price|60|P_Weight|60|Weight|60|Unit/<br>Carton|60|Barcode|150|productsize|120|Box Weight|50|Average Leadtime|55|Order History|80|StockQty|50|Date of Latest Order|100" . $bjRemark_Cols.'|HD pictures<br>(packing)|80|HD pictures<br>(non-packing)|80|FTP dowload HD picture|200';
	           $ColsNumber=17; 
               $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
              }
		break;
	case "b"://MCO1
		$Th_Col="No.|40|ID|50|中文名|220|Product Code|180|Description|250|Orginal<br>Price|60|P_Weight|60|Box Weight|50|Refund|50|Final<br>Price|60|Unit/<br>Carton|60|Barcode|170|Image|50|HD pictures|80";
		break;
	case "c"://MCO2
		$Th_Col="No.|40|ID|50|中文名|220|Product Code|180|Description|250|Final Price|90|P_Weight|60|Unit/<br>Carton|60|Box Weight|50|Barcode|150|Image|50|HD pictures|80";
		break;
	case "d"://CEL
		$Th_Col="No.|40|ID|60|中文名|220|Product Code|180|Price|70|P_Weight|60|Unit/<br>Carton|70|Order History|80|Date of Latest Order|120|Remark|50";
		$ColsNumber=9;
		break;
	case "e"://ECHO
		$Th_Col="No.|40|ID|50|中文名|220|Product Code|180|Product Description|250|Price|70|P_Weight|60|Unit/<br>Carton|70|Barcode|150|Image|50|HD pictures|80";
		break;
	case "f"://浏览MCA：带价格
		$Th_Col="No.|40|ID|50|中文名|220|Product Code|180|Product Description|250|Price|70|P_Weight|60|Unit/<br>Carton|70|Barcode|220|Image|50|HD pictures|80|Remark|50";
		$CompanySTR=" AND P.CompanyId='1001'";
		break;
	case "g"://浏览MCA
		$Th_Col="No.|40|ID|50|中文名|220|Product Code|180|Product Description|250|Unit/<br>Carton|70|Barcode|150|Image|50|HD pictures|80|Remark|50";
		$CompanySTR=" AND P.CompanyId='1001'";
		break;
	case "h"://CG
		  $Th_Col="Choose|50|No.|40|ID|60|中文名|220|Product Code|180|Price|70|P_Weight|60|Unit/<br>Carton|70|BoxSpec|120|volume|80|HD pictures<br>(packing)|80|HD pictures<br>(non-packing)|80|Order History|80|Date of Latest Order|100";
		  $ColsNumber=10;
          $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
		break;
	default://默认
		$Th_Col="No.|40|ID|60|中文名|220|Product Code|180|Price|70|P_Weight|60|Unit/<br>Carton|70|Order History|80|Date of Latest Order|80|Remark|50";
		$ColsNumber=9;
		break;
	}

	
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	
	if($myCompanyId==1091 ) {

		echo "<select name='zipstr' id='zipstr' onchange='ResetPage(this.name)'>";
		$result = mysql_query("select * from (
				SELECT substr(eCode,1,position('-' in eCode)-1) as zipstr FROM $DataIn.`productdata` WHERE `CompanyId`='1091' 
				group by substr(eCode,1,position('-' in eCode)-1)) A order by zipstr",$link_id);
		
		if($myrow = mysql_fetch_array($result)){
			do{
				$thezipstr=$myrow["zipstr"];
				//$zipstr=$zipstr==""?$thezipstr:$zipstr;
				if($zipstr==$thezipstr){
					if($zipstr!=""){
						echo"<option value='$thezipstr' selected>$thezipstr</option>";
					   	$SearchRows=" AND P.eCode like '$zipstr%'";
					}
					else{
						echo"<option value='$thezipstr' selected> Choose All </option>";	
					}
				 }
				 else{
					echo"<option value='$thezipstr'>$thezipstr</option>";
					}
				}while($myrow = mysql_fetch_array($result));
			}
		echo "</select> &nbsp;";	
		}
    if (in_array($myCompanyId, $CompanyIdArray1)){  //CEL-A OR CEL-B OR CEL-C
             $CELCompanyId=$CELCompanyId==""?0:$CELCompanyId;   
             $CelCompanyStr="CELCompanyId".$CELCompanyId;
             $$CelCompanyStr="selected";
       		 echo "<select name='CELCompanyId' id='CELCompanyId' onchange='ResetPage(this.name)'>";  
	         echo"<option value='0' $CELCompanyId0>全部</option>";  
	         echo"<option value='1004' $CELCompanyId1004>CL-A</option>";  
	         echo"<option value='1059' $CELCompanyId1059>CL-B</option>";  
	         echo"<option value='1072' $CELCompanyId1072>CL-C</option>";  
	     	 echo "</select> &nbsp;";	
            if($CELCompanyId>0)$CELStr=" AND P.CompanyId='$CELCompanyId'";
            else $CELStr="";
         }
     if (in_array($myCompanyId, $CompanyIdArray3)){   
            $ChangeCompanyId=$ChangeCompanyId==""?$myCompanyId:$ChangeCompanyId;
			$ChangeCompanyIdStr="ChangeCompanyId".$ChangeCompanyId;
			$$ChangeCompanyIdStr="selected"; 
            echo "<select id='ChangeCompanyId' name='ChangeCompanyId' onchange='document.form1.submit()'>";
              echo "<option value='0' $ChangeCompanyId0>ALL</option> ";
              echo "<option value='100262' $ChangeCompanyId100262>GHC WD</option> ";
              echo "<option value='100241' $ChangeCompanyId100241>GHC BR</option> ";
              echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
              
              if($ChangeCompanyId>0){
                  $CompanySTR="AND P.CompanyId ='$ChangeCompanyId'";
                 }else{
                    $CompanySTR="AND P.CompanyId IN (100262,100241)"; 
                 }
            $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
      }
      
      
}else{
	
	 if (in_array($myCompanyId, $CompanyIdArray3)){  
	 
	     $CompanySTR="AND P.CompanyId IN (100262,100241)"; 
	 }
}

echo $toExcelStr."&nbsp;&nbsp;";
echo $GHCStr;
$searchtable="productdata|P|eCode|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
include "../model/subprogram/QuickSearch.php";

include "../admin/subprogram/read_model_5.php";
$i=1;$j=1;
List_Title($Th_Col,"1",1);
$Keys=31;
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.productsize,P.Moq,P.CompanyId,
P.Description,P.Remark,P.TestStandard,P.Img_H,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,
P.Operator,C.Forshort,T.TypeName,C.Currency,CD.PreChar,P.MainWeight,P.Weight,P.bjRemark,S.tStockQty
FROM $DataIn.productdata P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata  CD ON CD.Id=C.Currency
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.productstock S ON S.ProductId = P.ProductId
WHERE 1 AND P.Estate=1 $SearchRows $CompanySTR  $CELStr  order by Estate DESC,Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];	
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
        $PreChar=$myRow["PreChar"];		
		$MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		$productsize=$myRow["productsize"]==""?"&nbsp;":$myRow["productsize"];
		$tStockQty=$myRow["tStockQty"]==0?"&nbsp;":$myRow["tStockQty"];
		
        $Weight=$myRow["Weight"]==0?"&nbsp;":$myRow["Weight"];
		$Price=sprintf("%.2f",$myRow["Price"]);
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$TestStandard=$myRow["TestStandard"];
        $Locks=$myRow["Locks"];
        $bjRemark=$myRow["bjRemark"]==""?"&nbsp;":$myRow["bjRemark"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$eCode="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633;'>$eCode</span>";
			}
		//高清图片检查	
	//	$Img_H=$myRow["Img_H"];  
        $Img_H1="&nbsp;";$Img_H2="&nbsp;";
		$Ftpdownload="";
		if(in_array($myCompanyId, $CompanyIdArray4)){
				$I_FilePath="../download/teststandard/";
				$SinkOrder="xacdefghijklmbnopqrstuvwyz";
				$ReferenceMark="abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				for($k=0;$k<32;$k++){
					$motherSTR[$k]=$ReferenceMark[rand(0,60)];
					}
       $Img_HResult=mysql_query("SELECT Picture,Type FROM $DataIn.productimg  WHERE  ProductId=$ProductId",$link_id);
       while($Img_HRow = mysql_fetch_array($Img_HResult)){
              $Img_HType=$Img_HRow["Type"];
              $Img_HPicture=$Img_HRow["Picture"];
            
              switch($Img_HType){
                      case "1": //带包装的高清图
				        $I_Filed1=anmaIn($Img_HPicture,$SinkOrder,$motherSTR);
				         $I_td=anmaIn($I_FilePath,$SinkOrder,$motherSTR);
				       
						  $Img_H1="<a href=\"../clientSOAP/DownloadFile.php?d=$I_td&f=$I_Filed1&Type=1\">downLoad</a>";
                         break;
                      case "2": //不带包装的高清图
				        $I_Filed2=anmaIn($Img_HPicture,$SinkOrder,$motherSTR);
				         $I_td=anmaIn($I_FilePath,$SinkOrder,$motherSTR);
				        
						  $Img_H2="<a href=\"../clientSOAP/DownloadFile.php?d=$I_td&f=$I_Filed2&Type=1\">downLoad</a>";
                         break;
                     }
              if($Ftpdownload==""){
				         $Ftpdownload="ftp://clientuser:client@113.105.80.226/$Img_HPicture";
                     }
                 else{
                        $Ftpdownload=$Ftpdownload."<br>"."ftp://clientuser:client@113.105.80.226/$Img_HPicture";
                       }
           }		
        $Ftpdownload = $Ftpdownload==""?"&nbsp;":$Ftpdownload;
				

			    }
        else{
                $Ftpdownload ="&nbsp;";
              }

		$checkImgSQL=mysql_query("SELECT COUNT(*) AS Number FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
		$Number=mysql_result($checkImgSQL,0,"Number");
		if($Number>0){
			    $Picture="<a href='productimg_view.php?ProductId=$ProductId' target='_blank'>View($Number pages)</a>";}
		else{
			    $Picture="&nbsp;";
              }		
		$FinalPrice=sprintf("%.4f",$Price+0.5);
		$Barcode=$myRow["Code"];$Barcode=$Barcode==""?"&nbsp;":$Barcode;
		//检查装箱数量
		$checkNumbers=mysql_fetch_array(mysql_query("SELECT IFNULL(N.Relation,0) AS Relation,S.Spec
		FROM $DataIn.pands N
		LEFT JOIN $DataIn.stuffdata S ON S.StuffId=N.StuffId
		WHERE N.ProductId=$ProductId AND S.TypeId='9040'",$link_id));
		$BoxNums=$checkNumbers["Relation"];
        $BoxSpec=$checkNumbers["Spec"];
		if($BoxNums!=0){
			   $BoxNumsArray=explode("/",$BoxNums);
			   $BoxNums=$BoxNumsArray[1];
			   }
		else{
			   $BoxNums="&nbsp;";
			   }
      if (substr_count($BoxSpec,"*")>0){
				     $Spec=explode("*",substr($BoxSpec,0,-2));
                                }else{
                                     $Spec=explode("×",substr($BoxSpec,0,-2));
                                   
                                }
       $ThisCube=$Spec[0]*$Spec[1]*$Spec[2];
       $ThisCube=sprintf("%.2f",$ThisCube/1000000);
       
       $Weight=(float)$myRow["Weight"];
      $productId=$ProductId;
      include "../model/subprogram/weightCalculate.php";
      if ($Weight>0){
           $extraWeight=$extraWeight == "error"?"&nbsp;":number_format($extraWeight+($Weight*$boxPcs)); 
      }
      else{
	      $extraWeight="&nbsp;";
      }
                      
		//订单总数
		$checkAllQty= mysql_query("SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.ProductId='$ProductId' GROUP BY OrderPO)A",$link_id);
		$AllQtySum=toSpace(mysql_result($checkAllQty,0,"AllQty"));
		$Orders=mysql_result($checkAllQty,0,"Orders");
		//已出货数量
		$checkShipQty= mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id);
		$ShipQtySum=toSpace(mysql_result($checkShipQty,0,"ShipQty"));
        $tempShipQtySum=$ShipQtySum;
		//百分比
		$TempInfo="style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
		if($AllQtySum>0){
			 $TempInfo.="title='ShipQty:$ShipQtySum,Order Frequency:$Orders'";
			}
       $ShipQtySum="<span class='yellowB'>".$ShipQtySum."</span>";
	    if($Orders>0){
				if($Orders<2){
					   $ShipQtySum=$ShipQtySum."<span class=\"redB\">($Orders)</span>";
					   }
				else{
					if($Orders>4){
						   $ShipQtySum=$ShipQtySum."<span class=\"greenB\">($Orders)</span>";
						   }
					else{
						   $ShipQtySum=$ShipQtySum."<span class=\"yellowB\">($Orders)</span>";	
						  }
					}
				}
//退货数量
		$checkReturnedQty= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE ProductId='$ProductId'",$link_id));
		$ReturnedQty=$checkReturnedQty["ReturnedQty"];			
		if($ReturnedQty>0 && $tempShipQtySum>0){
			//退货百分比
			$ReturnedPercent=sprintf("%.1f",(($ReturnedQty/$tempShipQtySum)*1000));
			if($ReturnedPercent>=5){
				$ReturnedQty="<span class=\"redB\">".$ReturnedQty."</span>";
				}
			else{
					if($ReturnedPercent>=2){
						$ReturnedQty="<span class=\"yellowB\">".$ReturnedQty."</span>";
						}
					else{
						$ReturnedQty="<span class=\"greenB\">".$ReturnedQty."</span>";
						}
					}
			$TempInfo2="style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\"Rates：$ReturnedPercent ‰\"";
			}
		else{
			$ReturnedQty="&nbsp;";
			$TempInfo2="";
			}
		//最后出货日期
       $MonthResult=mysql_fetch_array(mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,
             TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId 
             FROM $DataIn.ch1_shipmain M 
	         LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
            WHERE 1 AND  S.ProductId='$ProductId'",$link_id));
		$Months=$MonthResult["Months"];
		$LastMonth=$MonthResult["LastMonth"];
		if($Months!=NULL){
			if($Months<6){//6个月内绿色
				$LastShipMonth="<div class='greenB'>".$LastMonth."</div>";
				}
			else{
				if($Months<12){//6－12个月：橙色
					$LastShipMonth="<div class='yellowB'>".$LastMonth."</div>";
					}
				else{//红色
					$LastShipMonth="<div class='redB'>".$LastMonth."</div>";
					}
				}
			
			}
		else{//没有出过货
			    $LastShipMonth="&nbsp;";
			    }
		//*******************交货期
		include "../model/subprogram/product_chjq.php";
        $JqAvg=str_replace("days","d",$JqAvg);

			if($ClientAction==1){//Cel的价格带上符号
                    $Price=$PreChar.$Price;
             }
     if($myCompanyId==1085){
		$URL="productdata_bom_ajax.php";
        $theParam="ProductId=$ProductId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"client\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品BOM表' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
        }
else{
           $showPurchaseorder="";$StuffListTB="";
         }
/******************************************/
		switch($model){
	     case "a":
        //*********************产品评价
         include  "product_pj.php";
         $EstResult=mysql_fetch_array(mysql_query("SELECT Estleadtime FROM $DataIn.product_estleadtime WHERE ProductId='$ProductId' ",$link_id));
         $EstLeadtime=$EstResult["Estleadtime"]==""?"&nbsp;":$EstResult["Estleadtime"];
         $EstStr="onclick='addEstStr($ProductId,this)'";
             if ($ClientAction==1){//CEL 公司
                        $ChooseOut="";
				        $ProResult=mysql_fetch_array(mysql_query("SELECT ProInfo FROM $DataIn.product_proinfo WHERE ProductId='$ProductId' ",$link_id));
				        $ProviderInfo=$ProResult["ProInfo"]==""?"&nbsp;":$ProResult["ProInfo"];      
				        $ProviderStr="onclick='addProStr($ProductId,this)'";
		                $ValueArray=array(
				              array(0=>$ProductId.$pjStr, 		1=>"align='center'"),
				              array(0=>$cName,			    3=>"..."),
				              array(0=>$eCode),				
				              array(0=>$Price,	                1=>"align='right'"),
							  array(0=>$MainWeight,	1=>"align='right'"),
				              array(0=>$BoxNums,	        1=>"align='right'"),
				               array(0=>$extraWeight,	        1=>"align='right'"),
				              array(0=>$JqAvg,		                 1=>"align='center'"),
				              array(0=>$EstLeadtime, 		  1=>"align='right'",2=> $EstStr),
				              array(0=>$ShipQtySum, 		 1=>"align='right'",2=>$TempInfo),
				              array(0=>$ReturnedQty, 		 1=>"align='right'",2=>$TempInfo2),
				              array(0=>$LastShipMonth, 	 1=>"align='center'"),
				             array(0=>$pjgif,			                1=>"align='center'"),
				             array(0=>$ProviderInfo,			                1=>"align='center'",2=> $ProviderStr)
				              );
                         }
                else{
                       
                
	                       
	                       $ChooseOut="";
		                   $ValueArray=array(
				              array(0=>$ProductId, 		1=>"align='center'"),
				              array(0=>$cName,			3=>"..."),
				              array(0=>$eCode),				
				              array(0=>$Price."&nbsp;",	1=>"align='right'"),
							  array(0=>$MainWeight,	1=>"align='right'"), 
				              array(0=>$Weight,	1=>"align='right'"),
				              array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
					          array(0=>$Barcode,			1=>"align='left'"),
							  array(0=>$productsize,			1=>"align='left'"),
				              array(0=>$extraWeight,	        1=>"align='right'"),
							   array(0=>$JqAvg,		                 1=>"align='center'"),
				              array(0=>$ShipQtySum, 		1=>"align='right'",2=>$TempInfo),
				              array(0=>$tStockQty, 		1=>"align='right'"),
				              array(0=>$LastShipMonth, 		1=>"align='center'")
				              );
				              
				              if ($bjRemark_Cols!=""){
					               $ValueArray[]=array(0=>$bjRemark);
				              }
				              $ValueArray[] =  array(0=>$Img_H1,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'");
				              $ValueArray[] = array(0=>$Img_H2,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'");
						      $ValueArray[] = array(0=>$Ftpdownload);
                   
                         
                     }
			break;
			case "b"://MCO1
				$ValueArray=array(
						array(0=>$ProductId, 		1=>"align='center'"),
						array(0=>$cName,			3=>"..."),
						array(0=>$eCode),
						array(0=>$Description,		3=>"..."),
						array(0=>$Price."&nbsp;",	1=>"align='right'"),
						array(0=>$MainWeight,	1=>"align='right'"),
						array(0=>"0.5&nbsp;",	1=>"align='right'"),
						array(0=>$FinalPrice."&nbsp;",	1=>"align='right'"),
						array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
					    array(0=>$extraWeight,	        1=>"align='right'"),
						array(0=>$Barcode,			1=>"align='center'"),
						array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
						array(0=>$Picture,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'")
					);
				break;
			case "c";//MCO2
				$ValueArray=array(
					array(0=>$ProductId, 		1=>"align='center'"),
					array(0=>$cName,			3=>"..."),
					array(0=>$eCode),
					array(0=>$Description,		3=>"..."),
					array(0=>$FinalPrice."&nbsp;",	1=>"align='right'"),
					array(0=>$MainWeight,	1=>"align='right'"),
					array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				    array(0=>$extraWeight,	        1=>"align='right'"),
					array(0=>$Barcode,			1=>"align='center'"),
					array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
					array(0=>$Picture,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'")
					
					);
				break;
			case "d"://CEL
			$ValueArray=array(
				array(0=>$ProductId, 		1=>"align='center'"),
				array(0=>$cName,			3=>"..."),
				array(0=>$eCode),				
				array(0=>$Price."&nbsp;",	1=>"align='right'"),
				array(0=>$MainWeight,	1=>"align='right'"),
				array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				array(0=>$ShipQtySum, 		1=>"align='right'"),
				array(0=>$LastShipMonth, 		1=>"align='center'"),
				array(0=>$Remark,			1=>"align='center'")
				);
			break;
		case "e"://ECHO
				$ValueArray=array(
					array(0=>$ProductId, 		1=>"align='center'"),
					array(0=>$cName,			3=>"..."),
					array(0=>$eCode),
					array(0=>$Description,		3=>"..."),
					array(0=>$Price."&nbsp;",	1=>"align='right'"),
					array(0=>$MainWeight,	1=>"align='right'"),
					array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
					array(0=>$Barcode,			1=>"align='center'"),
					array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
					array(0=>$Picture,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'")
					);
			break;
		case "f"://浏览MCA
			$ValueArray=array(
				array(0=>$ProductId, 		1=>"align='center'"),
				array(0=>$cName,			3=>"..."),
				array(0=>$eCode),
				array(0=>$Description,		3=>"..."),
				array(0=>$Price."&nbsp;",	1=>"align='right'"),
				array(0=>$MainWeight,	1=>"align='right'"),
				array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				array(0=>$Barcode),
				array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Picture,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Remark,			1=>"align='center'")
				);
			break;
		case "g"://浏览MCA
			$ValueArray=array(
				array(0=>$ProductId, 		1=>"align='center'"),
				array(0=>$cName,			3=>"..."),
				array(0=>$eCode),
				array(0=>$Description,		3=>"..."),
				array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				array(0=>$Barcode),
				array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Picture,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Remark,			1=>"align='center'")
				);
			break;		
       case "h":
                  $ChooseOut="";
                $ValueArray=array(
				              array(0=>$ProductId, 		1=>"align='center'"),
				              array(0=>$cName,			3=>"..."),
				              array(0=>$eCode),				
				              array(0=>$Price."&nbsp;",	1=>"align='right'"),
							  array(0=>$MainWeight,	1=>"align='right'"),
				              array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				              array(0=>$BoxSpec, 		1=>"align='center'"),
				              array(0=>$ThisCube, 		1=>"align='center'"),
				              array(0=>$Img_H1,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				              array(0=>$Img_H2,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
				              array(0=>$ShipQtySum, 		1=>"align='right'"),
				              array(0=>$LastShipMonth, 		1=>"align='center'")
				              );
           break;
		default:
			$ValueArray=array(
				array(0=>$ProductId, 		1=>"align='center'"),
				array(0=>$cName,			3=>"..."),
				array(0=>$eCode),				
				array(0=>$Price."&nbsp;",	1=>"align='right'"),
				array(0=>$MainWeight,	1=>"align='right'"),
				array(0=>$BoxNums."&nbsp;",	1=>"align='right'"),
				array(0=>$ShipQtySum, 		1=>"align='right'"),
				array(0=>$LastShipMonth, 		1=>"align='center'"),
				array(0=>$Remark,			1=>"align='center'")
				);
			break;
			}
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
         echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
   echo "<input type='hidden' id='IdCount' name='IdCount' value='$i'>";
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);
include "../model/subprogram/read_model_menu.php";
}
else{
	echo"system error 0";
	}
?>
<script language="JavaScript" type="text/JavaScript">
function PubblicShowOrHide(e,f,Order_Rows,URL,theParam,RowId,FromT,FromDir){
	//alert(FromDir);
	e.style.display=(e.style.display=="none")?"":"none";
	var yy=f.src;
	if (yy.indexOf("showtable")==-1){
		f.src="../images/showtable.gif";
		Order_Rows.myProperty=true;
		}
	else{
		f.src="../images/hidetable.gif";
		Order_Rows.myProperty=false;
		//动态加入采购明细
		if(theParam!=""){	
		    if(FromDir !=null && FromDir!="" ){
				var url="../"+FromDir+"/"+URL+"?"+theParam+"&RowId="+RowId+"&FromT="+FromT; 
			}
			else {
				var url="../admin/"+URL+"?"+theParam+"&RowId="+RowId+"&FromT="+FromT; 
			}
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					  show.innerHTML=DataArray[0];
					}
				}
			ajax.send(null); 
			}
		}
	}

function ViewChart(Pid,OpenType){
	document.form1.action="productdata_chart_english.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();	
  }


//**********************************************
function pjclick(times,ProductId,tempTableId){
     var tableId=eval("ListTable"+tempTableId);
      var pjtimes=document.getElementById("pjtimes"+tempTableId).value;
       var url="product_pj_ajax.php?times="+times+"&ProductId="+ProductId+"&pjtimes="+pjtimes+"&ActionId=1";
	   var ajax=InitAjax();
　	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                if(ajax.responseText=="Y"){
                        switch(times){
                             case 1:
                                 if(pjtimes!=1){
                                        tableId.rows[0].cells[12].innerHTML="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,"+ProductId+","+tempTableId+")'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(2,"+ProductId+","+tempTableId+")'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,"+ProductId+","+tempTableId+")'>";
                                        document.getElementById("pjtimes"+tempTableId).value=1;
                                           }
                                  else  {
                                        tableId.rows[0].cells[12].innerHTML="<img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(1,"+ProductId+","+tempTableId+")'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(2,"+ProductId+","+tempTableId+")'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,"+ProductId+","+tempTableId+")'>";
                                        document.getElementById("pjtimes"+tempTableId).value=0;
                                          }
                                break;
                             case 2:
                                        tableId.rows[0].cells[12].innerHTML="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,"+ProductId+","+tempTableId+")'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(2,"+ProductId+","+tempTableId+")'><img src='../images/pj_gray.gif' style='CURSOR: pointer;' onclick='pjclick(3,"+ProductId+","+tempTableId+")'>";
                                        document.getElementById("pjtimes"+tempTableId).value=2;
                                break;
                             case 3: 
                                        tableId.rows[0].cells[12].innerHTML="<img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(1,"+ProductId+","+tempTableId+")'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(2,"+ProductId+","+tempTableId+")'><img src='../images/pj_yellow.gif' style='CURSOR: pointer;' onclick='pjclick(3,"+ProductId+","+tempTableId+")'>";
                                        document.getElementById("pjtimes"+tempTableId).value=3;
                                 break;
                             }
                      }
			   }
		  }
　	ajax.send(null);
}

//**********************************************导出ExCEL
function celtoExcel(){
    var k=0;
    var message="",chooseStr,allStr;
   var index=document.getElementById("IdCount").value;
        for (var i=1;i<index;i++){
	          var checkid="checkid"+i;
	           var checkStr=document.getElementById(checkid);
				if(checkStr.checked){
                   k++;
                   if(k==1)chooseStr=checkStr.value; 
                   else chooseStr=chooseStr+"^^"+checkStr.value;
                  }
                   if(i==1)allStr=checkStr.value;
                   else allStr=allStr+"^^"+checkStr.value;
			}	  
      if(k==0)message="All to Excel? if not ,please choose!";
      if(message!="" && confirm(message)){
               document.form1.action="productdata_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+allStr;
               document.form1.target="_self";
               document.form1.submit();	
              }
        else{
               if(k!=0){
                       document.form1.action="productdata_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+chooseStr;
                       document.form1.target="_self";
                       document.form1.submit();	
                      }
               }
               //alert("productdata_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+allStr);addProStr
}

function addEstStr(ProductId,e){
       var Estleadtime=encodeURIComponent(prompt("please input Est.Leadtime !"));
       var url="product_pj_ajax.php?ProductId="+ProductId+"&Estleadtime="+Estleadtime+"&ActionId=2";
	   var ajax=InitAjax();
　 	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                      if(ajax.responseText=="Y"){
                            e.innerHTML=Estleadtime;
                            }
                }
           }
　	 ajax.send(null);
}

function addProStr(ProductId,e){
       var ProInfo=encodeURIComponent(prompt("please input ProInfo !"));
       var url="product_pj_ajax.php?ProductId="+ProductId+"&ProInfo="+ProInfo+"&ActionId=5";
	   var ajax=InitAjax();
　 	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                      if(ajax.responseText=="Y"){
                            e.innerHTML=ProInfo;
                            }
                }
           }
　	 ajax.send(null);
}
</script>