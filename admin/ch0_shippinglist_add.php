<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 待处理订单列表");
$funFrom="ch0_shippinglist";
$From=$From==""?"add":$From;
$sumCols="8,9";			//求和列,需处理
$Th_Col="选项|40|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|220|售价|60|订单数量|60|金额|60|出货方式|60|转发对象名称|150|订单备注|110|生管备注|110|待出备注|110|订单日期|70";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;

$ActioToS="1";
$nowWebPage=$funFrom."_add";
//步骤3：
include "../model/subprogram/read_model_3.php";
//客户
//$SearchRows=" AND S.Estate>0 AND S.scFrom=0";//一定要生产完，且经审核
$SearchRows=" AND S.Estate>0  ";//一定要生产完，且经审核
//AND M.CompanyId IN (1074,100083,100072,100241,100262,1090,1073,100360,100361,100261,1094,100170,100392,100283,100284)
$clientResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId WHERE 1 $SearchRows GROUP BY M.CompanyId 
	",$link_id);
if($clientRow = mysql_fetch_array($clientResult)) {
	echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ch0_shippinglist_add\")'>";
	do{			
		$thisCompanyId=$clientRow["CompanyId"];
		$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
		$Forshort=$clientRow["Forshort"];
		if($CompanyId==$thisCompanyId){
			echo"<option value='$thisCompanyId' selected>$Forshort</option>";
			$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
			$SearchRows2.=" and S.CompanyId='$thisCompanyId' ";
			$ModelCompanyId=$thisCompanyId;
			}
		else{
			echo"<option value='$thisCompanyId'>$Forshort</option>";					
			}
		}while ($clientRow = mysql_fetch_array($clientResult));
	echo"</select>&nbsp;";
	}

include "subprogram/ch_amountshow.php";  //add by zx 20101116 统计相应的金额！ 国内报关的金额，MC 为Cel, DP为MCA  //输出  $Maxstr

$otherActions="<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\",\"admin\")' class='btn-confirm' style='width: auto;font-size: 12px'>生成出货单</span>&nbsp;";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr $MaxStr";
echo $otherActions;
//步骤5：
include "../model/subprogram/read_model_5.php";
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
				   
				   $ToOutNameResult = mysql_query("SELECT * from (
					SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
					FROM $DataIn.yw7_clientOutData O
					LEFT JOIN  $DataIn.yw1_ordersheet S  ON O.POrderId=S.POrderId
					LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
					WHERE  O.Mid=0 AND D.Estate=1 AND S.Estate>0  AND D.CompanyId='$CompanyId' ) A group by ToOutId
					",$link_id);
				   */
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
		          $inForT="<select name='ToOutId' id='ToOutId' onchange='RefreshPage(\"$nowWebPage\")'>";
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
List_TitleYW($Th_Col,"1",1,$ToOutId,$CompanyId,$DataIn,$link_id,$nowWebPage);

if($ToOutId!=""){
	$SearchRows.=" AND ((O.ToOutId='$ToOutId' AND O.Mid>0) OR (OP.ToOutId='$ToOutId' AND OP.Mid=0))  "; 
	//echo "SearchRows:$SearchRows";
	//$SearchRows.=" AND  (OP.ToOutId='$ToOutId' AND OP.Mid=0)  ";
	$mySql="
	SELECT M.OrderNumber,M.CompanyId,S.OrderPO,M.OrderDate,'1' AS Type,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,P.cName,P.eCode,P.TestStandard,S.dcRemark,S.Estate,S.ShipType,SP.Id as SPID
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN  $DataIn.ch1_shipsplit SP  ON SP.POrderId=S.POrderId
	 LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
    LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	WHERE 1 $SearchRows ";
	
	//echo "$mySql";
	
}

else{

$mySql="
	SELECT M.OrderNumber,M.CompanyId,S.OrderPO,M.OrderDate,'1' AS Type,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,P.cName,P.eCode,P.TestStandard,S.dcRemark,S.Estate,S.ShipType,'' as SPID
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
    LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 $SearchRows";
}
/*	
$mySql1="
	SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,SP.Id,S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard,SP.ShipType,S.dcRemark,SP.Qty AS thisQty,SP.OrderSign,PI.Leadtime,X.name as taxName,R.OrderPO as OutOrderPO 
	FROM $DataIn.ch1_shipsplit SP  
     INNER JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
     LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	 LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	 
	 LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	 LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
	 LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	WHERE   S.Estate>0   $SearchRows";
*/	

//echo "$mySql <br>";

//echo "$mySql1";

$myResult = mysql_query($mySql." $PageSTR",$link_id);


if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;		
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];
		
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
				$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			$ColbgColor="bgcolor='#0066FF'";
			}
		else{
			$ColbgColor="";
			}
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]==""?"&nbsp;":$myRow["PackRemark"]; 
		$sgRemark=$myRow["sgRemark"]==""?"&nbsp;":$myRow["sgRemark"];
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$TestStandard=$myRow["TestStandard"];
		include "../admin/Productimage/getPOrderImage.php";
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];
		
		$enRemark="";
	    $RemarkResult=mysql_query("SELECT Remark FROM $DataIn.yw2_orderremark WHERE POrderId='$POrderId' AND Type=1 LIMIT 1",$link_id);
	      if($RemarkRow=mysql_fetch_array($RemarkResult)){
	             $enRemark=$RemarkRow["Remark"];
	     }		
		
		$ToOutName="";
		
		$SPID=$myRow["SPID"];
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$SPID' AND O.ToOutId='$ToOutId' ",$link_id);
		//echo ""
		if ($Outmyrow = mysql_fetch_array($OutResult)) {
			//删除数据库记录
			//$Forshort=$myRow["Forshort"]; 
			$ToOutName=$Outmyrow["ToOutName"]."(拆)";
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
		
		/*
		$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ",$link_id);
		//echo "";
		if ($Outmyrow = mysql_fetch_array($OutResult)) {
			//删除数据库记录
			//$Forshort=$myRow["Forshort"]; 
			$ToOutName=$Outmyrow["ToOutName"];
		}			
        */

		if($ToOutName!="" && $enRemark!="" ){  //DEASIA不同客户出不同的Delivery Reference NO:  add by zx 2015-10-30 
			$enField=explode("|",$enRemark);
			if(count($enField)>1){
				$ToOutName=$ToOutName."(<span class=\"redB\">$enRemark</span>)";
			}else{
				$ToOutName=$ToOutName."($enRemark)";
			}
		}
		
		
		
		
		$OrderPO=$Type==2?"随货项目":$OrderPO;
		$checkidValue=$Id."^^".$Type;
		$Locks=1;
		$dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];

		$Estate=$myRow["Estate"];
		
		$ToOutNameStr="";
		//include "../model/subprogram/order_shiptype.php";
		include "../model/subprogram/order0_shiptype.php";
		
		if($ToOutNameStr==""){
			$ToOutNameStr="&nbsp;";
		}		

		
		$ValueArray=array(
			array(0=>$OrderPO,1=>"align='center'"),
			array(0=>$POrderId,1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$TestStandard),
			array(0=>$eCode,3=>"..."),
			array(0=>$Price,1=>"align='center'"),
			array(0=>$Qty,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$ShipType,1=>"align='left'"),
			array(0=>$ToOutName,1=>"align='center'"),
			array(0=>$PackRemark,1=>"align='left'"),
			array(0=>$sgRemark,1=>"align='left'"),
			array(0=>$dcRemark,1=>"align='left'"),
			array(0=>$OrderDate,1=>"align='center'")
			);
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>