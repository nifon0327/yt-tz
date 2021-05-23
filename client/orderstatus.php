<?php
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=600;
$sumCols="8";			//求和列,需处理
//$funFrom="clientorder";
//$nowWebPage=$funFrom."_read";
$nowWebPage="Orderstatus";
switch($model){
	case "b":
		$Th_Col="&nbsp;|60|Item|35|PO#|70|Product Code|150|Product Description|350|Qty|55|Air/Sea|50|OrderDate|70|DeliveryWeek|70|PI|100";
		$ColsNumber=8;	
	break;
	case "c":
	case "d":
		$Th_Col="&nbsp;|60|Item|30|PO#|60|Product Code|150|Product Description|350|Price|45|Qty|45|Amount|65|Unit/<br>Carton|40|Air/Sea|40|OrderDate|70|PI DeliveryWeek|70|CL notes|80|AC notes|80|Order Age|50|Average Leadtime|50|Order History|80|Rejects|60|PI|100|Supplier Rating|80";
		$ColsNumber=18;
		break;
	default:
	   if($myCompanyId ==100262 || $myCompanyId ==100241){
		   
		$Th_Col="&nbsp;|60|Item|35|PO#|70|中文名|150|Product Code|150|Product Description|350|Price|50|Qty|60|Amount|80|ready stock|60|openQty|60|shipped|60|Order History|80|OrderDate|70|DeliveryWeek|70|Remark|70|PI|100";
		$ColsNumber=12;	 
		
	   }else{
		  $Th_Col="&nbsp;|60|Item|35|PO#|70|中文名|150|Product Code|150|Product Description|350|Price|50|Qty|60|Amount|80|Air/Sea|50|Average Leadtime|50|Order History|80|OrderDate|70|DeliveryWeek|70|Remark|70|PI|100";
		$ColsNumber=12;	 
	   }
		
		
		break;
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="0,1,2,3,20,21,22,6,7,8,9";
//步骤3：
include "../model/subprogram/read_model_3.php";

$subTableWidth=$tableWidth-30;
$chooseAction=$chooseAction==""?0:$chooseAction;
$chooseStr="chooseAction".$chooseAction;
$$chooseStr="selected";


$ChangeCompanyId=$ChangeCompanyId==""?$myCompanyId:$ChangeCompanyId;
$ChangeCompanyIdStr="ChangeCompanyId".$ChangeCompanyId;
$$ChangeCompanyIdStr="selected";

switch($myCompanyId){
       	case "1004":
       	case "1059":
       	case "1072":
       	case "1074":
             echo "<select id='chooseAction' name='chooseAction' onchange='document.form1.submit()'>";
             echo "<option value='0' $chooseAction0> All orders</option> ";
             echo "<option value='1' $chooseAction1>Ready to ship</option> ";
             echo "<option value='2' $chooseAction2>Ready To Pack</option> ";
             echo "<option value='3' $chooseAction3>Materials Ready</option> ";
             echo "<option value='4' $chooseAction4>Priority</option> ";
             echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
             if($myCompanyId!=1074){
                 echo"<a href='order_toexcel.php'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
              }
              if($chooseAction==1)$SearchRows.="AND S.Estate=2";
              if($chooseAction==4){
                   $orderexpress =" LEFT JOIN yw2_orderexpress EP ON EP.POrderId=S.POrderId";
                   $SearchRows.=" AND EP.Type=8";
               }
              else $orderexpress="";
      break;
          case "1081":
          case "1002":
          case "1080":
          case "1065":
          case "1091":
              echo "<select id='chooseAction' name='chooseAction' onchange='document.form1.submit()'>";
              echo "<option value='0' $chooseAction0> All orders</option> ";
              echo "<option value='1' $chooseAction1>Ready to ship</option> ";
              echo "<option value='2' $chooseAction2>Ready To Pack</option> ";
              echo "<option value='3' $chooseAction3>Materials Ready</option> ";
              echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
              echo"<a href='order_toexcel.php'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
              if($chooseAction==1)$SearchRows.="AND S.Estate=2";

          break;
          case "100262":
          case "100241":
              echo "<select id='ChangeCompanyId' name='ChangeCompanyId' onchange='document.form1.submit()'>";
              echo "<option value='0' $ChangeCompanyId0>ALL</option> ";
              echo "<option value='100262' $ChangeCompanyId100262>GHC WD</option> ";
              echo "<option value='100241' $ChangeCompanyId100241>GHC BR</option> ";
              echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;";
             // echo"<a href='order_toexcel.php'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
              $toExcelStr="<input type='button' value='toExcel'  onclick='toExcel2()'>";
           break;
     }
	 
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
}

echo $toExcelStr;
$searchtable="$DataIn.productdata|P|eCode|1|CEL"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无无
$searchfile="../model/subprogram/Quicksearch_ajax.php";
include "../model/subprogram/QuickSearch.php";

if($myCompanyId==1074) {
          echo "<iframe name='toExcelFrame' style='display:none;'></iframe>  ";
          echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='#' onclick='toExcel()'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;";
}
include "../model/subprogram/read_model_5.php";
$sumQty=0;
$sumSaleAmount=0;
$DefaultBgColor=$theDefaultColor;
$i=1;
$sRow=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$ClientAction=0;
switch($myCompanyId){
          case "1004"://CEL-A OR CEL-B OR CEL-C
          case "1059":
          case "1072":
                    $SearchRows.=" and (M.CompanyId='1004' OR M.CompanyId='1059'  OR M.CompanyId='1072') ";
                    $ClientAction =1;     
                    break;
          case "1081":
          case "1002":
          case "1080":
          case "1065":
		                $SearchRows.=" and M.CompanyId in ('1081','1002','1080','1065')";
                    break;
          case "100262":
          case "100241":
                    
		                if($ChangeCompanyId>0){
		                      $SearchRows.="AND M.CompanyId ='$ChangeCompanyId'";
		                     }else{
			                    $SearchRows.="AND M.CompanyId IN (100262,100241)"; 
		                     }
                    break;
                    
          default:
    	               $SearchRows.=" and M.CompanyId='$myCompanyId'";
          break;
}


switch($chooseAction){
      case 2:
      include "orders_pack.php";//Ready to pack;
      break;
      case 3:
      include "orders_mready.php";//Materials Ready
      break;
      default:
     $mySql="SELECT M.CompanyId,M.OrderDate,M.ClientOrder,
             S.Id,S.POrderId,S.OrderPO,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.Estate,S.Locks,PI.PI,PI.Leadtime,
             P.cName,P.eCode,P.Description,P.TestStandard,U.Name AS Unit,CD.PreChar,PI.Remark AS PIRemark 
             FROM $DataIn.yw1_ordermain M
             LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
             LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
             LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
             LEFT JOIN $DataIn.currencydata  CD ON CD.Id=C.Currency
             LEFT JOIN $DataIn.productunit U ON U.Id=P.Unit
             LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
             
             $orderexpress
             WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.OrderDate DESC";
     break;
   }
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$PI=$myRow["PI"];
                $ClientOrder=$myRow["ClientOrder"];
                if($ClientOrder!=""){
                    $f1=anmaIn($ClientOrder,$SinkOrder,$motherSTR);
		            $d1=anmaIn("download/clientorder/",$SinkOrder,$motherSTR);
                    $PI_link="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>PDF</span>";                  
                    }
                else{
                    if ($PI!=""){
                       $f1=anmaIn($PI.".pdf",$SinkOrder,$motherSTR);
		       $d1=anmaIn("download/pipdf/",$SinkOrder,$motherSTR);
                       $PI_link="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>PDF</span>"; 
                    }
                }
		if ($PI==""){
                    $PI="&nbsp;"; 
                    }
                else{
                    $PI_link.="&nbsp;&nbsp;<a href='../admin/yw_pi_tocsv.php?Id=$PI' target='_blank'>CSV</a>";
                    $PI_link.="&nbsp;&nbsp;<a href='../admin/yw_pi_toxml.php?Id=$PI' target='_blank'>XML</a>";
                    $PI=$PI_link;
                }
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$POrderId=$myRow["POrderId"];
		$cName=$myRow["cName"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$eCode=toSpace($myRow["eCode"]);
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$TestStandard="T".$ProductId.".jpg";
			$TestStandard=anmaIn($TestStandard,$SinkOrder,$motherSTR);
			$Dir=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);
			$TestStandard="<span onClick='OpenOrLoad(\"$Dir\",\"$TestStandard\")' style='CURSOR: pointer;color:#FF6633'>$eCode</span>";
			}
		else{
			$TestStandard=$eCode;
			}
		$Unit=$myRow["Unit"];
		$Qty=$myRow["Qty"];
        $PreChar=$myRow["PreChar"];
		$Price=sprintf("%.2f",$myRow["Price"]);
		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);
		$sumQty+=$Qty;
		$sumSaleAmount+=$thisSaleAmount;
		$PackRemark=$myRow["PackRemark"];
		$Leadtime=$myRow["Leadtime"];
		$PIRemark=$myRow["PIRemark"];
		$LeadbgColor="";
		if ($Leadtime==""){
			 $checkTimeResult=mysql_fetch_array(mysql_query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId'",$link_id));
			 $Leadtime=$checkTimeResult["Leadtime"]==""?"&nbsp;":$checkTimeResult["Leadtime"];
			 $LeadbgColor=$checkTimeResult["Leadtime"]==""?$LeadbgColor:" bgColor='#F7E200' ";
		}
		include "../model/subprogram/PI_Leadtime.php";
	    $Leadtime=$PIRemark==""?$Leadtime:"<div title='$PIRemark' style='color:#FF0000' >$Leadtime</div>";	
		$pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
		
		
		$ShipType=$myRow["ShipType"];
		 //出货方式
	   if (strlen(trim($ShipType))>0){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }
		else {
			$ShipType="&nbsp;";
		}
		$OrderDate=$myRow["OrderDate"];
		$OrderDate=date("Y-m-d",strtotime($OrderDate));
        $totalDate=str_replace("days","d",CountDays($OrderDate,0));
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		
		$checkEnRemarkRow = mysql_fetch_array(mysql_query("SELECT Remark,Date FROM $DataIn.yw2_orderremark WHERE POrderId='$POrderId' AND Type=1 ORDER BY ID DESC LIMIT 1", $link_id));
		$enRemark=$checkEnRemarkRow["Remark"]==""?"&nbsp;":$checkEnRemarkRow["Remark"];

		$thisSaleAmount=sprintf("%.2f",$Qty*$Price);//本订单卖出金额
		/*毛利计算*////////////
		$CompanyId=$myRow["CompanyId"];
		$currency_Temp = mysql_query("SELECT A.Rate,A.Symbol FROM $DataPublic.currencydata A LEFT JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1",$link_id);
		if($RowTemp = mysql_fetch_array($currency_Temp)){
			$Rate=$RowTemp["Rate"];//汇率
			$Symbol=$RowTemp["Symbol"];//货币符号
			}
		$thisTOrmbOUT=sprintf("%.4f",$thisSaleAmount*$Rate);//转成人民币的卖出金额



        ///////////////////////////////////
		//订单状态色
		$gxQty = "";
		$checkColor=mysql_query("SELECT G.Id FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 AND T.mainType<2 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0' ) 
		and G.PorderId='$POrderId' AND G.Level = 1 LIMIT 1",$link_id);
		if($checkColorRow = mysql_fetch_array($checkColor)){
			$OrderSignColor="bgColor='#FFFFFF'";//有未下需求单
			}
		else{//已全部下单，看生产数量		
			$OrderSignColor="bgColor='#339900'";	//设默认绿色
			//生产数量与工序数量不等时，黄色
			//入库总数
			$CheckRkQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS rkQty 
			FROM $DataIn.yw1_orderrk K  WHERE K.POrderId='$POrderId' ",$link_id));
			$rkQty=$CheckRkQty["rkQty"];

			if($rkQty!=$Qty){
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		$ColbgColor="";
		
		
		
		$CheckthisShipQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS ShipQty 
		FROM $DataIn.ch1_shipsheet C WHERE C.POrderId='$POrderId'",$link_id));
	    $thisShipQty=$CheckthisShipQty["ShipQty"];
	    $readShipQty = $rkQty -$thisShipQty;
	    $openQty = $Qty - $readShipQty -$thisShipQty;
	    $thisShipQty=$thisShipQty==""?"&nbsp;":$thisShipQty;
	    $readShipQty =$readShipQty==""?"&nbsp;":$readShipQty;
	    $readShipclick ="";
	    if($readShipQty ==""){
		    $readShipQty = "&nbsp;"; 
		    
	    }else{
		    if($readShipQty==$Qty){
			    $readShipQty= "<span class='greenB'>$readShipQty</span>";
		    }
		    
		    $readShipclick ="style='CURSOR: pointer;' onclick='ViewSplitOrder($POrderId,$ProductId)'";
		    
	    }
	    if($openQty==0){
		    $openQty = "&nbsp;";
		    $readShipQty= "<span class='greenB'>$readShipQty</span>";
	    }else{
		    $openQty = "<span class='redB'>$openQty</span>";
	    }
	    //检查装箱数量
		$checkNumbers=mysql_fetch_array(mysql_query("SELECT IFNULL(N.Relation,0) AS Relation
		FROM $DataIn.pands N
		LEFT JOIN $DataIn.stuffdata S ON S.StuffId=N.StuffId
		WHERE N.ProductId=$ProductId AND S.TypeId='9040'",$link_id));
		$BoxNums=$checkNumbers["Relation"];
		if($BoxNums!=0){
			   $BoxNumsArray=explode("/",$BoxNums);
			   $BoxNums=$BoxNumsArray[1];
			   }
		else{
			   $BoxNums="&nbsp;";
			   }
		//订单总数
		$checkAllQty= mysql_query("SELECT SUM(S.Qty) AS AllQty ,count(*) AS Orders FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.ProductId='$ProductId' ",$link_id);
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
          $ColbgColor="";$UrgentColor="";
			//加急订单
			$checkExpress=mysql_query("SELECT Type,Remark FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id",$link_id);
			if($checkExpressRow = mysql_fetch_array($checkExpress)){
				do{
					$Type=$checkExpressRow["Type"];
					$UPRemark=$checkExpressRow["Remark"];
					switch($Type){
						case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
						case 2:$ColbgColor="bgcolor='#FF0000'"; $OrderRemark="未确定产品 ".$UPRemark ;
								break;		//未确定产品
						}
					}while ($checkExpressRow = mysql_fetch_array($checkExpress));
				}

		//include "../model/subprogram/product_chjq.php";
        //$JqAvg=str_replace("days","d",$JqAvg);
		//图档显示
		include "../model/subprogram/stuffimg_Gfile.php";			
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";	
		//动态读取
     if($myCompanyId!=1091){
         $showPurchaseorder="<img onClick='cShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i)' name='showtable$i' src='../images/showtable.gif' alt='show or hidden the ordersheet.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
        }
else{
           $showPurchaseorder="";$StuffListTB="";
         }
		//0:内容	1：对齐方式		2:单元格属性		3：截取
          $CLStr="";$ACStr="";$PriorityStr="";
			if($ClientAction==1){//Cel的价格带上符号
                    $Price=$PreChar.$Price;
                    $thisSaleAmount=$PreChar.$thisSaleAmount;
                    include  "product_pj.php";//产品评价
                    $notesResult=mysql_fetch_array(mysql_query("SELECT CLnotes,ACnotes FROM $DataIn.yw1_orderclient WHERE POrderId='$POrderId'",$link_id));
//echo "SELECT CLnotes,ACnotes FROM $DataIn.yw1_orderclient WHERE POrderId='$POrderId';
                    $CLnotes=$notesResult["CLnotes"]==""?"&nbsp;":$notesResult["CLnotes"];
                    $CLStr="onclick='addnoteStr($POrderId,this,1)'";
                    $ACnotes=$notesResult["ACnotes"]==""?"&nbsp;":$notesResult["ACnotes"];
                    $ACStr="onclick='addnoteStr($POrderId,this,2)'";
		            //加急订单
		           $checkExpress=mysql_query("SELECT POrderId FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' and Type='8' ORDER BY Id",$link_id);
		          if($checkExpressRow = mysql_fetch_array($checkExpress)){
                            $theDefaultColor="#FFA6D2";  
                            $ExpressEstate=1;                         
		           	       }
                    else {
                            $ExpressEstate=0;       
                            }
                    $PriorityStr="onclick='addnoteStr($POrderId,this,3,$ExpressEstate,$i)' style='CURSOR: pointer;'";
             }
			$H="&nbsp;";//高清图
			
			switch($model){
				case "b":
				$ValueArray=array(
					array(0=>$OrderPO, 		1=>"align='center'"),
					array(0=>$TestStandard),
					array(0=>$Description),
					array(0=>$Qty, 			1=>"align='right'"),
					array(0=>$ShipType,		1=>"align='center'"),
					array(0=>$OrderDate,	1=>"align='center'"),
					array(0=>$Leadtime,	1=>"align='center' "),
                    array(0=>$PI, 			1=>"align='center'")
					);
					break;
				case "c":
				case "d":
				$ValueArray=array(
					array(0=>$OrderPO,		1=>"align='center'",2=>$PriorityStr),
					array(0=>$TestStandard),
					array(0=>$Description),
					array(0=>$Price, 		1=>"align='right'"),
					array(0=>$Qty, 			1=>"align='right'"),
					array(0=>$thisSaleAmount,1=>"align='right'"),
					array(0=>$BoxNums,1=>"align='right'"),
					array(0=>$ShipType,		1=>"align='center'"),
					array(0=>$OrderDate,	1=>"align='center'"),
					array(0=>$DeliveryDate, 1=>"align='center'", 3=>"..."),
					array(0=>$CLnotes, 1=>"align='center'",2=>$CLStr, 3=>"..."),
					array(0=>$ACnotes, 1=>"align='center'", 2=>$ACStr,3=>"..."),
					array(0=>$totalDate,	1=>"align='center'"),
					array(0=>$JqAvg,	1=>"align='center'"),
				    array(0=>$ShipQtySum, 		 1=>"align='right'",2=>$TempInfo),
				    array(0=>$ReturnedQty, 		 1=>"align='right'",2=>$TempInfo2),
					array(0=>$PI, 			1=>"align='center'"),
				    array(0=>$pjgif,		1=>"align='center'")
					);
				break;
			default:
			
			  if($myCompanyId ==100262 || $myCompanyId ==100241){
			    
			    $ValueArray=array(
						array(0=>$OrderPO, 		1=>"align='center'"),
						array(0=>$cName, 	),
						array(0=>$TestStandard),
						array(0=>$Description),
						array(0=>$Price,		1=>"align='right'"),
						array(0=>$Qty, 			1=>"align='right'"),
						array(0=>$thisSaleAmount,1=>"align='right'"),
						array(0=>$readShipQty,		1=>"align='right'",2=>$readShipclick),
						array(0=>$openQty,		1=>"align='right'"),
						array(0=>$thisShipQty,	    1=>"align='right'"),
		                array(0=>$ShipQtySum, 	1=>"align='right'",2=>$TempInfo),
						array(0=>$OrderDate,	1=>"align='center'"),
						array(0=>$Leadtime,	    1=>"align='center'"),
						array(0=>$enRemark,	    1=>"align='center'"),
              	        array(0=>$PI, 			1=>"align='center'"),
						);
			    
			    
			    }else{
				    
				    $ValueArray=array(
						array(0=>$OrderPO, 		1=>"align='center'"),
						array(0=>$cName, 	),
						array(0=>$TestStandard),
						array(0=>$Description),
						array(0=>$Price,		1=>"align='right'"),
						array(0=>$Qty, 			1=>"align='right'"),
						array(0=>$thisSaleAmount,1=>"align='right'"),
						array(0=>$ShipType,		1=>"align='center'"),
						array(0=>$JqAvg,	    1=>"align='center'"),
		                array(0=>$ShipQtySum, 	1=>"align='right'",2=>$TempInfo),
						array(0=>$OrderDate,	1=>"align='center'"),
						array(0=>$Leadtime,	    1=>"align='center'"),
						array(0=>$enRemark,	    1=>"align='center'"),
              	        array(0=>$PI, 			1=>"align='center'"),
						);
			    }
			    
			
					break;
				}
		$checkidValue=$Id;
		$Keys=31;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;		
		}while ($myRow = mysql_fetch_array($myResult));
		echo "<input type='hidden' id='IdCount' name='IdCount' value='$i'>";
	
	//合计
	$sumSaleAmount=sprintf("%.2f",$sumSaleAmount);
	$WidthSTR=$myCompanyId==1004?665:610;

	/*$m=1;
	switch($model){
		case "b":
		$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>$sumQty, 			1=>"align='right'"),
			array(0=>"&nbsp;&nbsp;"	),
			array(0=>"&nbsp;"	),array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	)
			);
			break;
		case "c":
		case "d":
		$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>$sumQty, 			1=>"align='right'"),
			array(0=>$sumSaleAmount,1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	)
			);
		break;
	default:
		$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>$sumQty, 			1=>"align='right'"),
			array(0=>$sumSaleAmount,1=>"align='right'"),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			//array(0=>"&nbsp;"	)
			);
		break;
		}	
	$ShowtotalRemark="TOTAL";
	$isTotal=1;
	
	include "read_model_total.php";*/
	
	}
else{
	noRowInfo($tableWidth);
	}
echo"<input name='sumAmout' type='hidden' id='sumAmount'>";
//步骤7：
echo '</div>';
ChangeWtitle("$SubCompany ORDER STATUS");
?>
<script>


function ViewSplitOrder(POrderId,ProductId){

	document.form1.action="order_split.php?POrderId="+POrderId+"&ProductId="+ProductId;
	document.form1.target="_blank";
	document.form1.submit();
	
}

function ViewChart(Pid,OpenType){
	document.form1.action="productdata_chart_english.php?Pid="+Pid+"&Type="+OpenType;
	document.form1.target="_blank";
	document.form1.submit();
	} 

function cShowOrHide(e,f,Order_Rows,POrderId,RowId){
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
		if(POrderId!=""){			
			var url="clientorder_ajax.php?POrderId="+POrderId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//订单状态更新
					switch(DataArray[1]){
						case "1"://白色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFFFFF";
							break;
						case "2"://黄色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFCC00";
							break;
						case "3"://绿色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#339900";
							break;
						}
					}
				}
			ajax.send(null); 
			}
		}
	 }


function addnoteStr(POrderId,e,Action,Estate,RowId){
       switch(Action){
           case 1:
             var message="please input CLnotes !";
             var notes=encodeURI(prompt(message));
             var url="orderstatus_ajax.php?POrderId="+POrderId+"&notes="+notes+"&ActionId="+Action;
           break;
           case 2:
             var message="please input ACnotes !";
             var notes=encodeURI(prompt(message));
             var url="orderstatus_ajax.php?POrderId="+POrderId+"&notes="+notes+"&ActionId="+Action;
           break;
           case 3:
             if(Estate==0)var message="Priority? Yes!";
             else var message="Priority? Cancel!";
             if(confirm(message)) var url="orderstatus_ajax.php?POrderId="+POrderId+"&ActionId="+Action+"&Estate="+Estate;
             else return false;
            break;
         }
	   var ajax=InitAjax();
　	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                    if(ajax.responseText=="Y"){
                          if(Action==1||Action==2){
                                   e.innerHTML=decodeURI(notes);
                                  }
                           else{
                                   if(Estate==0) eval("ListTable"+RowId).rows[0].cells[0].bgColor="#FFA6D2";         
                                   else   eval("ListTable"+RowId).rows[0].style.backgroundColor ="#FFFFFF";               
                                 }
                        }
                 }
           }
　	 ajax.send(null);
}


function toExcel(){
     var Ids="";
     var ListCheck=document.getElementsByName("checkid[]");
     for(var i = 0; i<ListCheck.length; i++) {
	  if (ListCheck[i].checked){
		  Ids+=i==0?ListCheck[i].value:","+ListCheck[i].value;
	  }
   }
   
   
   document.form1.action="order_toexcel2.php?Ids="+Ids;
   document.form1.target="toExcelFrame";
   document.form1.submit();
}

function toExcel2(){

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
               document.form1.action="order_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+allStr;
               alert( document.form1.action)
               document.form1.target="_self";
               document.form1.submit();	
              }
        else{
               if(k!=0){
                       document.form1.action="order_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+chooseStr;
                       document.form1.target="_self";
                       document.form1.submit();	
                      }
               }
}
</script>