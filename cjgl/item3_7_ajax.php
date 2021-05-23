<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$Log_Item="订单状态检查";			
$Log_Funtion="状态更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;

//步骤2：
switch($ActionId*1){
     
   case 1:
   case 11:
   case 12:
       $setSTR=$ActionId==1?"scFrom=0,Estate=2":"scFrom=0";
       if ($ActionId==12) $setSTR="scFrom=0,Estate=0";
       
       $UpdateSql="Update $DataIn.yw1_ordersheet SET  $setSTR  WHERE POrderId='$POrderId'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
	    $Log="订单流水号: $POrderId 的状态更新成功(SET scFrom=0).</br>";
            $OperationResult="Y";
	    }
	else{
	    $Log="订单流水号: $POrderId 的状态更新失败.</br>";
	    $OperationResult="N";
	}
        echo $OperationResult;
     break;
 
   case 2:
       $UpdateSql="Update $DataIn.yw1_ordersheet SET scFrom=2,Estate=1 WHERE POrderId='$POrderId'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
	    $Log="订单流水号: $POrderId 的状态更新成功(SET scFrom=2).</br>";
            $OperationResult="Y";
	    }
	else{
	    $Log="订单流水号: $POrderId 的状态更新失败.</br>";
	    $OperationResult="N";
	}
        echo $OperationResult;
     break;
 
      case 3:
       $UpdateSql="Update $DataIn.yw9_blsheet SET Estate=1 WHERE POrderId='$POrderId'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
	    $Log="订单流水号: $POrderId 的备料状态更新成功(SET Estate=1).</br>";
            $OperationResult="Y";
	    }
	else{
	    $Log="订单流水号: $POrderId 的备料状态更新失败.</br>";
	    $OperationResult="N";
	}
        echo $OperationResult;
     break;
 
   case 5:
     $upResult = mysql_query("SELECT S.OrderPO,S.POrderId,S.Qty,S.Estate,S.scFrom,P.cName,P.InspectionSign 
          FROM $DataIn.yw1_ordersheet S 
          LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
          WHERE S.POrderId=$POrderId ",$link_id);

     if($upData = mysql_fetch_array($upResult)){
	$OrderPO=$upData["OrderPO"];
	//$POrderId=$upData["POrderId"];
	$cName=$upData["cName"];
	$Estate=$upData["Estate"];
    $scFrom=$upData["scFrom"];
    $Qty =$upData["Qty"];
    $InspectionSign=$upData["InspectionSign"];
        
        switch($Estate){
           case 0:$EstateSTR="已出货(0);"; break;
           case 1:$EstateSTR=$scFrom==0?"未品检(1);":"可生产订单(1);"; $EstateSTR=$InspectionSign==1?"客户未验收(1);":$EstateSTR;  break;
           case 2:$EstateSTR="待出订单(2);"; break;
           case 4:$EstateSTR="已生成出货单(4);"; break;
           default:$EstateSTR="其它状态($Estate);"; break;
        }
        //其它状态
        $TypeSTR="";
        $CheckSql=mysql_query("SELECT E.Type FROM $DataIn.yw2_orderexpress E WHERE E.POrderId='$POrderId'",$link_id);
	if($CheckRow=mysql_fetch_array($CheckSql)){
           do{
           $Type=$CheckRow["Type"]; 
           switch($Type){
               case 2:$TypeSTR.="未确定订单(2);";break;
               case 7:$TypeSTR.="加急订单(7);";break;
               case 9:$TypeSTR.="需修改订单(9);";break;
               default:$EstateSTR.="其它状态($Type);"; break;
             }
           }while($CheckRow=mysql_fetch_array($CheckSql)); 
        }
        //生产状态
        switch($scFrom){
           case 0:$scFromSTR="已生产(0);"; break;
           case 1:$scFromSTR="待生产(1);"; break;
           case 2:$scFromSTR="生产中(2);"; break;
           case 3:$scFromSTR="暂停生产(3);"; break;
           default:$scFromSTR="其它状态($scFrom);"; break;
        }
        
    //工序总数
	$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND G.Level=1 AND T.mainType=3",$link_id));
	$gxQty=$CheckgxQty["gxQty"];
        
	//已完成的工序数量
        
	$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty 
	        FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.yw1_scsheet S ON S.sPOrderId=C.sPOrderId 
			WHERE C.POrderId='$POrderId' AND S.ActionId='101'",$link_id));
	$scQty=$CheckscQty["scQty"];
     
     $Inspection=0;
      if ( $scQty>0 && $InspectionSign==1){
          $checkInspection=mysql_query("SELECT Inspection FROM $DataIn.yw1_productinspection WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1",$link_id);
          if($checkInspectionRow = mysql_fetch_array($checkInspection)){
                $Inspection=$checkInspectionRow["Inspection"];
          }
      }
    
     $scRemark="";$scInput="&nbsp;";$scOnlick="";
     
	if($gxQty==$scQty && $scFrom>0){
        
             if ($InspectionSign==0 || $Inspection==1){
		          $scRemark="已完成生产,生产状态标记($scFrom)出现异常！";
	             $scInput="<img src='../images/register.png' width='30' height='30'>";
	             $scOnclick=" onclick='RegisterEstate(this,$POrderId,1)'";
             }
             else{
                  $scRemark="已完成生产,生产状态标记($scFrom)出现异常！";
                  $scInput="<img src='../images/register.png' width='30' height='30'>";
                  $scOnclick=" onclick='RegisterEstate(this,$POrderId,11)'";
             }
	}
	
    if($gxQty>$scQty && ($scFrom==0 || $Estate<>1) && $Estate>0){
		        if ($scFrom==0){
			         $scRemark="未完成生产,生产状态标记($scFrom)出现异常！";
		        }
	           if ($Estate<>1){
		             $scRemark="未完成生产,订单状态标记($Estate)出现异常！";
	           }
             $scInput="<img src='../images/register.png' width='30' height='30'>";
             $scOnclick=" onclick='RegisterEstate(this,$POrderId,2)'";
	}
        
        if($gxQty<$scQty){
	     $scRemark="已登记生产数量($scQty)大于订单需求数量($gxQty),生产登记出现异常！";
	}
	
	//检查是否已出货
	$CheckShipQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS shipQty FROM ch1_shipsheet WHERE POrderId='$POrderId'",$link_id));
	$shipQty=$CheckShipQty["shipQty"]==""?0:$CheckShipQty["shipQty"];
	
    if ($Qty==$shipQty && $Estate>0) {
	    $TypeSTR="已出货,订单状态标记($Estate)出现异常！";
	    $EstateInput="<img src='../images/register.png' width='30' height='30'>";
        $EstateOnclick=" onclick='RegisterEstate(this,$POrderId,12)'";
    } 
        
	//检查领料记录 备料总数与领料总数比较
	$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType 
				WHERE G.POrderId='$POrderId' AND G.Level=1 AND M.blSign=1",$link_id));
	$blQty=$CheckblQty["blQty"]==""?0:$CheckblQty["blQty"];
        
	$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND G.Level=1 AND K.Estate=0 ",$link_id));
	$llQty=$CheckllQty["llQty"];
        
        $nollQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS nollQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND G.Level=1 AND K.Estate>0  ",$link_id));
	$nollQty =$nollQty["nollQty"]==""?0:$nollQty["nollQty "];
        $blOkQty=$nollQty+$llQty;
        
        $llRemark="";
        if ($blOkQty==0){
            $blRemark="未备料！";
        }
        else{
          if($blQty==$blOkQty){
            $blRemark="<span style='color:#1B9E04'>需备料数量: $blQty ;已备料数量: $blOkQty ;备料已完成！</span></BR>";
	   }
           
          if($blQty>$blOkQty){
            $blRemark="需备料数量: $blQty ;已备料数量: $blOkQty ;备料中！</BR>";
	   }
	  if($blQty<$blOkQty){
	      $blRemark="<span style='color:#FF0000'>已备料数量($blOkQty)大于需备料数量($blQty),备领料异常！</span></BR>";
	  }
          
          if ($nollQty==0 && $llQty==$blQty){
              $llRemark="<span style='color:#1B9E04'>领料数量:$llQty;领料已完成！</span>";
          }else{
              $llRemark="领料数量: 已确定($llQty),未确定($nollQty);领料中！";
          }
        }

        
        $blInput="&nbsp;";$blOnclick="";
       
        
        if ($scFrom==0 && $Estate==1 && $scOnclick==""){
            if ($InspectionSign==0 || $Inspection==1){
	           $EstateInput="<img src='../images/register.png' width='30' height='30'>";
               $EstateOnclick=" onclick='RegisterEstate(this,$POrderId,1)'";
             }
        }
        
  echo "</br><div style='width:530px;text-align:left;'><b>订单PO：</b>$OrderPO</div></br>";
  echo "<div style='width:530px;text-align:left;'><b>产品名称：</b>$cName</div></br>";
        $i=1;
   echo"<table id='ContentList'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'><tr>
        <td width='30' height='20' class='A1111'><b>序号</b></td>
	<td width='100' align='center' class='A1101'><b>检查项目</b></td>
	<td width='300' align='center' class='A1101'><b>状态信息</b></td>
	<td width='100' align='center' class='A1101'><b>操作</b></td>
	</tr>";   
   echo"<tr><td  align='center' height='30' class='A0111'>$i</td>
        <td  align='left' class='A0101'>订单状态(Estate)</td>
        <td  align='left' class='A0101'>$EstateSTR <span style='color:#FF0000'> $TypeSTR </span></td>
        <td  align='center' class='A0101' $EstateOnclick>$EstateInput&nbsp;</td></tr>";
   
   $i++; if ($scRemark!="") $scRemark="<span style='color:#FF0000'>" . $scRemark . "</span>";
   echo"<tr><td  align='center' height='30' class='A0111'>$i</td>
        <td  align='left' class='A0101'>生产状态(scFrom)</td>
        <td  align='left' class='A0101'>$scFromSTR $scRemark</td>
        <td  align='center' class='A0101' $scOnclick>$scInput</td></tr>";
   $i++;
   echo"<tr><td  align='center' height='30' class='A0111'>$i</td>
        <td  align='left' class='A0101'>备料状态</td>
        <td  align='left' class='A0101'>$blRemark</td>
        <td  align='center' class='A0101' $blOnclick>$blInput</td></tr>";
   $i++;
   if ($llRemark=="") $llRemark="未有领料信息!";
   echo"<tr><td  align='center' height='30' class='A0111'>$i</td>
        <td  align='left' class='A0101'>领料状态</td>
        <td  align='left' class='A0101'>$llRemark</td>
        <td  align='center' class='A0101'>&nbsp;</td></tr>";
   echo "</table>";
 
   }
  else{
       echo "<p><span style='color:#FF0000'>未有流水号为: $POrderId 的订单信息！请检查流水号是否正确？</span></p>";
   } 
   break;
}

if ($ActionId<5){
    $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
    $IN_res=@mysql_query($IN_recode);
}
?>
