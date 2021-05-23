<?php   
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=550;
ChangeWtitle("$SubCompany Shipment");
$funFrom="shipment";
$nowWebPage=$funFrom."_read";
$Th_Col="&nbsp;|60|NO.|40|DeliveryDate|120|InvoiceNO|150|InvoiceFile|120|PackingList|120|Shipping Mark|100|Qty|100|Amount|100|Destination|100|T/T|100|Cartons|100|WG|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
$sumCols=$sumCol==""?"6":("".$sumCol);			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
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
		                $SearchRows.=" and M.CompanyId in ('100241','100262')";
                    break;
                    
          default:
    	               $SearchRows.=" and M.CompanyId='$myCompanyId'";
                    break;
	}
	
	
	
	   if ($myCompanyId==100262 || $myCompanyId==100241){   
            $ChangeCompanyId=$ChangeCompanyId==""?$myCompanyId:$ChangeCompanyId;
			$ChangeCompanyIdStr="ChangeCompanyId".$ChangeCompanyId;
			$$ChangeCompanyIdStr="selected"; 
            echo "<select id='ChangeCompanyId' name='ChangeCompanyId' onchange='document.form1.submit()'>";
              echo "<option value='0' $ChangeCompanyId0>ALL</option> ";
              echo "<option value='100262' $ChangeCompanyId100262>GHC WD</option> ";
              echo "<option value='100241' $ChangeCompanyId100241>GHC BR</option> ";
              echo "</select>&nbsp;";
              
              if($ChangeCompanyId>0){
                  $SearchRows.="AND M.CompanyId ='$ChangeCompanyId'";
                 }
      }
        
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{			
			$dateValue=date("M-y",strtotime($dateRow["Date"]));
			$StartDate=date("Y-m-01",strtotime($dateRow["Date"]));
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$CurchooseDate=date("Y-m",strtotime($StartDate));
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
	}
	
	if($myCompanyId==1066 || $myCompanyId==1084){
		echo"&nbsp; &nbsp; &nbsp; <a href='invoice_toexcel.php?chooseDate=$CurchooseDate'>ToExcel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
		
}
echo $CencalSstr;
//步骤5：
if($cn==0){//过滤扣款单
	$CreditNoteStr=" AND M.Sign>0";
	}
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,C.Forshort,D.Rate ,M.Ship,M.ShipType
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.currencydata D ON D.Id=C.Currency 
WHERE 1 $SearchRows $CreditNoteStr ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//合计初始化
$AmountSUM=0;
$BoxQtySUM=0;
$mcWGSUM=0;
$QtySUM=0;
	do{
		$OrderSignColor="";
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Rate=$myRow["Rate"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
        $PackingFile="&nbsp;";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT SUM(BoxQty) AS BoxQty   FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
            $BoxQty=$PackingRow["BoxQty"];
            $BoxQtySUM+=$BoxQty;
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='../admin/ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>View</a>";
                        
          $PackingFile="<a href='../admin/ch_shippinglist_packtocsv.php?Id=$Id' target='_blank'>CSV</a>";
          $PackingFile.="&nbsp;&nbsp;<a href='../admin/ch_shippinglist_packtoxml.php?Id=$Id' target='_blank'>XML</a>";
			}
        $BoxQty = $BoxQty ==""?"&nbsp;":$BoxQty;
		//Invoice查看
		//加密参数
		
                if ($InvoiceFile==0){
                    $InvoiceFile=$InvoiceNO;
                    $PackingFile="&nbsp;";
                }else{
                    $f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		            $d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
                    $InvoiceFile="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>PDF</span>";
                    $InvoiceFile.="&nbsp;&nbsp;<a href='../admin/ch_shippinglist_tocsv.php?Id=$Id' target='_blank'>CSV</a>";
                    $InvoiceFile.="&nbsp;&nbsp;<a href='../admin/ch_shippinglist_toxml.php?Id=$Id' target='_blank'>XML</a>";
                }
		
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		//$Days=CountDays($Date,0)==0?"Today":CountDays($Date,0);
		$Date=date("d-M-Y",strtotime($Date));
		$Locks=$myRow["Locks"];		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount ,SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"]);
		$ShipQty=$checkAmount["ShipQty"];
		$QtySUM+=$ShipQty;
		$AmountSUM=sprintf("%.2f",$AmountSUM+$Amount);
		$rmbAmount=sprintf("%.2f",$Rate*$Amount);
		$rmbAmountSUM=sprintf("%.2f",$rmbAmountSUM+$rmbAmount);
		$showPurchaseorder="<img onClick='cOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		//中港运费处理
		$mcWG="&nbsp;";	
		$checkFreight=mysql_query("SELECT mcWG FROM $DataIn.ch4_freight_declaration WHERE chId='$Id' LIMIT 1",$link_id);
		if($FreightRow=mysql_fetch_array($checkFreight)){
			$mcWG=$FreightRow["mcWG"];
			}
		
		//已收款
		$chId=$mainRows["chId"];
		$checkShipAmount=mysql_query("SELECT SUM(S.Amount) AS ShipAmount,concat(M.Remark) AS Remark 
		FROM $DataIn.cw6_orderinsheet S 
		LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=S.Mid
		WHERE S.chId='$Id' GROUP BY S.chId",$link_id);
		if (mysql_num_rows($checkShipAmount)>0){
		      $Remark=mysql_result($checkShipAmount,0,"Remark");
		      $ShipAmount=mysql_result($checkShipAmount,0,"ShipAmount");
		      $Remark="<img src='../images/remark.gif' alt='$Remark' width='18' height='18'>";
		      $ShipAmount=sprintf("%.2f",$ShipAmount);
		  }
		  else{
			  $Remark="&nbsp;";
			  $ShipAmount="&nbsp;";
		  }
	
		
		$ShipAmount=$ShipAmount==""?"&nbsp;":sprintf("%.2f",$ShipAmount);
		if($ShipAmount!="&nbsp;"){
			$cwAmountSUM=sprintf("%.2f",$cwAmountSUM+$ShipAmount);
			if($Amount==$ShipAmount){
				$ShipAmount="<span class='greenB'>$ShipAmount</span>";
				$OrderSignColor="bgColor='#339900'";
				}
			else{
				$ShipAmount="<span class='yellowB'>$ShipAmount</span>";
				$OrderSignColor="bgColor='#FFCC00'";
				}
			}
		/*$Ship=$myRow["Ship"];
		 //出货方式
	   if ($Ship>=0){
		    $Ship="<image src='../images/ship$Ship.png' style='width:20px;height:20px;'/>";
	    }
		switch($myRow["ShipType"]){
			case "credit":
			   $Ship="<img src='../images/Credit note.png' title='SF' width='20' height='20'>";break;
			   break;
			   case "debit":
			   $Ship="<img src='../images/Debit note.png' title='SF' width='20' height='20'>";break;
			   break;
		}	*/	
		
		$ToOutName="";
		
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$Id'",$link_id);

		if ($Outmyrow = mysql_fetch_array($OutResult)) {
		
			$ToOutName=$Outmyrow["ToOutName"];
		}else{
			$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
									  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
									  WHERE  O.POrderId IN (SELECT S.POrderId FROM $DataIn.ch1_shipsheet S 
									  LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid WHERE M.Id ='$Id') AND O.Mid=0 ",$link_id);

			if ($Outmyrow = mysql_fetch_array($OutResult)) { 
				$ToOutName=$Outmyrow["ToOutName"];
			}			
		}
		
		if($ToOutName!="" && $enRemark!="" ){  //DEASIA不同客户出不同的Delivery Reference NO: 
			$enField=explode("|",$enRemark);
			if(count($enField)>1){
				$ToOutName=$ToOutName."(<span class=\"redB\">$enRemark</span>)";
			}else{
				$ToOutName=$ToOutName."($enRemark)";
			}
		}
		
		if($ToOutName==""){
			$ToOutName="&nbsp;";
		}
		
		

		$ValueArray=array(
			array(0=>$Date, 			1=>"align='center'"),
			array(0=>$InvoiceNO.$Ship,1=>"align='left'"),
			array(0=>$InvoiceFile,1=>"align='center'"),
            array(0=>$PackingFile,1=>"align='center'"),
			array(0=>$BoxLable,		1=>"align='center'"),
			array(0=>$ShipQty,		1=>"align='center'"),
			array(0=>$Amount, 		1=>"align='right'"),
			array(0=>$ToOutName, 	1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'"),			
			array(0=>$BoxQty,		1=>"align='center'"),
			array(0=>$mcWG,			1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	$mcWGSUM=sprintf("%.2f",$mcWGSUM);

	
	$m=1;
	$ValueArray=array(
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
			array(0=>"&nbsp;"	),
            array(0=>"&nbsp;"	),
			array(0=>$QtySUM, 		1=>"align='right'"),
			array(0=>$AmountSUM, 		1=>"align='right'"),
			array(0=>"&nbsp;&nbsp;"	),	
			array(0=>"&nbsp;"	),	
			array(0=>$BoxQtySUM,		1=>"align='center'"),
			array(0=>$mcWGSUM,			1=>"align='right'")
		);	
	$ShowtotalRemark="TOTAL";
	$isTotal=1;
	include "../model/subprogram/read_model_total.php";	
}	
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
echo"<input name='sumAmount' type='hidden' id='sumAmount'>";
?>
<script>
function cOrhOrder(e,f,Order_Rows,ShipId,RowId){
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
		if(ShipId!=""){			
			var url="../public/client_shiporder_ajax.php?ShipId="+ShipId+"&RowId="+RowId; 
		　	var show=eval("showStuffTB"+RowId);
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;					
					show.innerHTML=BackData;
					}
				}
			ajax.send(null); 
			}
		}
	}
</script>
