<?php 
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|80|箱数|60|重量(KG)|100|出货日期|80|出货方式|60|货运信息|120|操作员|50";

$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch=$From!="slist"?"":$sSearch;
$sSearch.=$Bid==""?"":" and M.CompanyId='$Bid'";
$SearchSTR=0;
//出货月份
if($chooseDate=="全部")$SelectStr="Selected";else $SelectStr="";
$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M 
WHERE 1 AND M.Estate='0' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
if($dateRow = mysql_fetch_array($date_Result)) {
	echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"ch_shipmain_s1\")'>";
	do{			
		$dateValue=date("Y-m",strtotime($dateRow["Date"]));
		$StartDate=$dateValue."-01";
		$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
		$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
		if($chooseDate==$dateValue){
			echo"<option value='$dateValue' selected>$dateValue</option>";
			$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
			}
		else{
			echo"<option value='$dateValue'>$dateValue</option>";					
			}
		}while($dateRow = mysql_fetch_array($date_Result));
	echo "<option value='全部' $SelectStr>全部</option>";
	echo"</select>&nbsp;";
 }
 

$client_Result = mysql_query("SELECT M.CompanyId,T.Forshort 
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
WHERE 1 AND M.Estate='0' $SearchRows GROUP BY M.CompanyId ",$link_id);
if($clientRow = mysql_fetch_array($client_Result)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ch_shipmain_s1\")'>";
	echo "<option value='全部' Selected>全部</option>";
	do{			
		$thisCompanyId = $clientRow["CompanyId"];
		$thisForshort = $clientRow["Forshort"];
		//$CompanyId = $CompanyId==""?$thisCompanyId:$CompanyId;
		if($thisCompanyId==$CompanyId){
			echo"<option value='$thisCompanyId' selected>$thisForshort</option>";
			$SearchRows.=" and M.CompanyId ='$thisCompanyId'";
			}
		else{
			echo"<option value='$thisCompanyId'>$thisForshort</option>";					
			}
		}while($clientRow = mysql_fetch_array($client_Result));
	echo"</select>&nbsp;";
 }
 
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
//$InData=$Action==3?"$DataIn.ch3_forward":"$DataIn.ch4_freight";
switch($Action)
{
	case 3:
		$InData="$DataIn.ch3_forward";
		$Datesheet="$DataIn.ch3_forward_invoice";
		break;
	case 12:
		$InData="$DataIn.ch12_declaration";
		break;
	default:
		$InData="$DataIn.ch4_freight_declaration";
		$Datesheet="$DataIn.ch4_freight_invoice";
		break;
		
}
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
switch($Action){
      case 12:$mySql="SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,
                      M.Operator,C.Forshort,M.ShipType AS Ship 
                      FROM $DataIn.ch1_shipmain M
                      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
                      LEFT JOIN $InData F ON F.chId=M.Id
                      WHERE 1 AND I.chId IS NULL $SearchRows ORDER BY M.Date DESC";
      break;
      default:$mySql="SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,
	                  M.Operator,C.Forshort,M.Ship 
	                  FROM $DataIn.ch1_shipmain M
                      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
                      LEFT JOIN $Datesheet I ON I.chId=M.Id 
                      LEFT JOIN $InData F ON F.Id=I.Mid
                      WHERE 1  AND I.chId IS NULL $SearchRows  ORDER BY M.Date DESC";
     break;
   }
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='../admin/ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$d1=anmaIn("invoice",$SinkOrder,$motherSTR);		
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		if($CompanyId==1001){
			$d2=anmaIn("invoice/mca",$SinkOrder,$motherSTR);
			$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>★</span>";
			}
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"]);
		
		$Ship=$myRow["Ship"];
		$ShipType = 0 ;
		$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$Ship'",$link_id);
		if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		      $ShipName=$shipTypeRow["Name"];
		      if(strpos($ShipName,"air")>-1 || strpos($ShipName,"Air")>-1){
			      $ShipType  = 1;
		      }
		      if(strpos($ShipName,"sea")>-1 || strpos($ShipName,"Sea")>-1){
			      $ShipType  = 2;
		      }
              $Ship="<image src='../images/ship$Ship.png' style='width:20px;height:20px;' title='$ShipName'/>";
          }
          else{
	        $Ship="";  
          }
		//出货重量和件数:从装箱明细中取
		$totalWG = 0;
		$totalBoxQty = 0 ;
		$totalCube = 0 ;
		$BoxQty ="&nbsp;";
		$checkResult =mysql_query("SELECT P.BoxRow,P.BoxQty,(P.BoxQty*P.WG) AS WG,P.BoxSpec
		FROM $DataIn.ch2_packinglist P WHERE P.Mid='$Id'",$link_id);
		while($checkRow = mysql_fetch_array($checkResult)){
		  $BoxRow=$checkRow["BoxRow"];
		  if($BoxRow!=0){
			  $WG=$checkRow["WG"]==0?0:$checkRow["WG"];
	          $BoxQty=$checkRow["BoxQty"]==0?0:$checkRow["BoxQty"];
	          $BoxSpec=$checkRow["BoxSpec"];
		  }else{
			  $WG =0;$BoxQty=0;$BoxSpec="";
		  }
		  
		
			//计算体积
            if (substr_count($BoxSpec,"*")>0){
                 $BoxSpec=explode("*",substr($BoxSpec,0,-2));
            }else{
                 $BoxSpec=explode("×",substr($BoxSpec,0,-2));
               
            }
            $ThisCube=$BoxSpec[0]*$BoxSpec[1]*$BoxSpec[2];
            $totalCube=$totalCube+$ThisCube*$BoxQty;//总体积           
		    $totalWG+=$WG;
		    $totalBoxQty+=$BoxQty;
		}
			
		$totalCubeKG=sprintf("%.2f",$totalCube/6000);
		$totalCube=sprintf("%.2f",$totalCube/1000000);	
		$checkidValue=$Id."^^".$InvoiceNO."^^".$totalWG."^^".$totalBoxQty."^^".$totalCube."^^".$totalCubeKG."^^".$ShipType;
		$ValueArray=array(
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$InvoiceFile,1=>"align='center'"),
			array(0=>$BoxLable,1=>"align='center'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$totalBoxQty,1=>"align='right'"),
			array(0=>$totalWG,1=>"align='right'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Ship,1=>"align='center'"),
			array(0=>$Wise),
			array(0=>$Operator,1=>"align='center'")
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