<?php 
//读取皮套项目权限
$itemArray=array();
$modelArray=array();  
$ServerId=2;         

$Row=1;$Col=1;
//取得权限
$userIdResult=mysql_query("SELECT Id FROM  $DataSub.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
if  ($userIdRow = mysql_fetch_array($userIdResult)){
        $userId=$userIdRow["Id"];
        $rMenuResult = mysql_query("SELECT A.ModuleId
                FROM $DataSub.upopedom A 
                LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
                WHERE A.Action>0 AND (B.TypeId=2  OR A.ModuleId IN('1012','1011','1078')) AND A.UserId='$userId' AND B.Estate=1 ORDER BY B.OrderId",$link_id);
   while ($rMenuRow = mysql_fetch_array($rMenuResult)){
                $ModuleId=$rMenuRow["ModuleId"];
                array_push($modelArray, $ModuleId);
   }
    $TResult02 = mysql_query("SELECT A.ItemId
			FROM $DataPublic.tasklistdata A
			LEFT JOIN $DataSub.taskuserdata B ON B.ItemId=A.ItemId
			WHERE  A.Estate=1  AND B.UserId='$LoginNumber' ",$link_id);
			while ($TRow02 = mysql_fetch_array($TResult02)){
			      $ItemId=$TRow02["ItemId"];
			      array_push($itemArray, $ItemId);
         }
    //默认权限
   $dataArray2[] = array("Id"=>"205","Name"=>"审计","ModuleId"=>"105","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
   $Col++;
}  

if (in_array("1044",$modelArray) || in_array("1245",$modelArray)  || in_array("1347",$modelArray)){
    $dataArray2[] = array("Id"=>"206","Name"=>"审核","ModuleId"=>"1044","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
    $Col++;
}

if (in_array("1011",$modelArray) || in_array("1078",$modelArray)){
	    $dataArray2[] = array("Id"=>"252","Name"=>"打样","ModuleId"=>"2502","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"4");
	    $Col++;
}

$oldRow=$Row;$Col=1;
if (in_array("173",$itemArray)){
    $Row=$Row==$oldRow?$oldRow+1:$Row;
    $dataArray2[] = array("Id"=>"209","Name"=>"订单","ModuleId"=>"109","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
    $Col++;
}
if (in_array("101",$itemArray)){
     $Row=$Row==$oldRow?$oldRow+1:$Row;
     if (versionToNumber($AppVersion)<322){
	     $dataArray2[] = array("Id"=>"210","Name"=>"未出","ModuleId"=>"110","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
     }
     else{
	     $checkSql="SELECT SUM(G.AddQty+G.FactualQty) AS wcQty,SUM(IFNULL(A.chQty,0)) AS chQty  
			       FROM  $DataSub.yw1_ordersheet S 
			       LEFT JOIN  $DataIn.cg1_stocksheet G  ON S.OrderNumber=G.StockId  
			       LEFT JOIN $DataSub.productdata P ON  P.ProductId=S.ProductId  
			       LEFT JOIN (
			                 SELECT C.POrderId,SUM(C.Qty) as chQty 
			                 FROM $DataSub.yw1_ordersheet Y 
			                 LEFT JOIN $DataSub.ch1_shipsheet C ON C.POrderId=Y.POrderId 
			                 WHERE Y.Estate>0 GROUP BY Y.POrderId
			       )A ON A.POrderId=S.POrderId 
			     WHERE   S.Estate>0 and P.TypeId>0";

		            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
		            $wcQty=$checkRow["wcQty"]==""?0:$checkRow["wcQty"]; 
		            $chQty=$checkRow["chQty"]==""?0:$checkRow["chQty"]; 
		            $wcQty-=$chQty;
		            
		            $Qty=$wcQty>0?round($wcQty/10000):0;
		            $Qty=$Qty>0?$Qty . "W":round($wcQty/1000)."k"; 
		             
		            $dataArray2[] = array("Id"=>"210","Name"=>"未出","ModuleId"=>"110","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col","IconType"=>"5","Value"=>"$Qty"); 
     }
     
     $Col++;
} 

if (in_array("104",$itemArray)){
      $Row=$Row==$oldRow?$oldRow+1:$Row;
	  $dataArray2[] = array("Id"=>"211","Name"=>"已出","ModuleId"=>"104","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
	  $Col++;
}
  
$oldRow=$Row;$Col=1;


if (in_array("128",$itemArray) || in_array("101",$itemArray)){
      $Row=$Row==$oldRow?$oldRow+1:$Row;
        //计算待定百分比(含订单锁、配件锁)
		 $OrderResult=mysql_fetch_array(mysql_query("SELECT SUM(IF(A.DeliveryDate='0000-00-00' OR A.Type=2 OR A.Locks>0 OR A.POrderId IS NULL, A.Qty,0)) AS TBCQty,SUM(A.Qty) AS Qty  
		FROM (
				SELECT G.DeliveryDate,(G.AddQty+G.FactualQty) AS Qty,E.Type,SUM(IF(L.locks=0,1,0)) AS Locks,C.POrderId
				FROM  $DataSub.yw1_ordersheet S 
				LEFT JOIN $DataIn.cg1_stocksheet G ON S.OrderNumber=G.StockId
				LEFT JOIN $DataSub.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
				LEFT JOIN $DataSub.cg1_stocksheet C ON C.POrderId=S.POrderId 
				LEFT JOIN $DataSub.cg1_lockstock L ON L.StockId=G.StockId AND L.locks=0 
				WHERE  S.Estate >0 GROUP BY S.POrderId
		)A  ",$link_id));
     $TBCQty=$OrderResult["TBCQty"]==""?0:$OrderResult["TBCQty"];
     $TotalQty=$OrderResult["Qty"]==""?0:$OrderResult["Qty"];
     $TBCPercent=$TotalQty>0?round($TBCQty/$TotalQty*100):0;
     
       $dataArray2[] = array("Id"=>"218","Name"=>"处理中","ModuleId"=>"128","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col","IconType"=>"2","Percent"=>"$TBCPercent");
    /*
	 $dataArray2[] = array("Id"=>"218","Name"=>"处理中","ModuleId"=>"128","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
	 */
	  $Col++;
 }
 
 if (in_array("1006",$modelArray)){
    $Row=$Row==$oldRow?$oldRow+1:$Row;
    $dataArray2[] = array("Id"=>"207","Name"=>"采购","ModuleId"=>"107","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col");
    $Col++;
  } 
 
if (in_array("1012",$modelArray)){
	$Row=$Row==$oldRow?$oldRow+1:$Row;
	$dataArray2[] = array("Id"=>"233","Name"=>"仓库","ModuleId"=>"133","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col"); 
	$Col++;
}
  
  if (in_array("1011",$modelArray)){
    $Row=$Row==$oldRow?$oldRow+1:$Row;
   $k=0;$n=1;$avgQty=0;
    $curDate=date("Y-m-d");
    $curscResult= mysql_fetch_array(mysql_query("SELECT SUM(D.Qty) AS Qty  FROM (
	             SELECT S.POrderId,S.Qty FROM $DataSub.sc1_cjtj S 
	             LEFT JOIN $DataSub.staffgroup G ON G.TypeId=S.TypeId  
	             WHERE G.ScType IN(8056,8058) AND S.Date='$curDate' GROUP BY S.Id 
	        )D  
		       LEFT JOIN $DataSub.yw1_ordersheet S ON S.POrderId =D.POrderId 
		       LEFT JOIN $DataSub.productdata P ON  P.ProductId=S.ProductId  
	           WHERE P.TypeId  IN(8056,8058)",$link_id)); 
    $curQty=$curscResult["Qty"]==""?0:$curscResult["Qty"]; 
    //10天的平均生产量     
    do{
          $scDate= date("Y-m-d",strtotime("$curDate  -$n   day"));
            $scResult=mysql_query("SELECT SUM(D.Qty) AS Qty  FROM  (
	            SELECT S.POrderId,S.Qty FROM $DataSub.sc1_cjtj S 
	            LEFT JOIN $DataSub.staffgroup G ON G.TypeId=S.TypeId  
	             WHERE G.ScType IN(8056,8058) AND S.Date='$scDate' GROUP BY S.Id 
	        )D  
	       LEFT JOIN $DataSub.yw1_ordersheet S ON S.POrderId =D.POrderId 
	       LEFT JOIN $DataSub.productdata P ON  P.ProductId=S.ProductId  
           WHERE P.TypeId  IN(8056,8058)",$link_id); 
            if ($scRow = mysql_fetch_array($scResult)){
               if ($scRow["Qty"]>0){
	               $avgQty+=$scRow["Qty"];
	               $k++;
               } 
            }
            $n++;
    }while($k<10 && $n<30);
	$avgQty=round($avgQty/10);
	$ScPercent=$avgQty>0?round($curQty/$avgQty*100):0;
	$ScPercent=$ScPercent>100?100:$ScPercent;		 
    $dataArray2[] = array("Id"=>"208","Name"=>"生产","ModuleId"=>"108","ServerId"=>"$ServerId","Estate"=>"1","Row"=>"$Row","Col"=>"$Col","IconType"=>"11","Percent"=>"$ScPercent");
    $Col++;
    if ($LoginNumber==11965 || $LoginNumber==10341 || versionToNumber($AppVersion)>=320) {
	    
	    
    $Row++;$Col=1;
 if (in_array("106", $itemArray) || in_array("1078",$modelArray)) {
   $Row=$Row==$oldRow?$oldRow+1:$Row;
	$dataArray2[] = array("Id" => "224", "Name" => "产品", "ModuleId" => "124", "ServerId" => "$ServerId", "Estate" => "1", "Row" => "$Row", "Col" => "$Col",'ImageId'=>'124');
	$Col++;
	$dataArray2[] = array("Id" => "228", "Name" => "配件", "ModuleId" => "128", "ServerId" => "$ServerId", "Estate" =>"1", "Row" => "$Row", "Col" => "$Col",'ImageId'=>'128');
	$Col++;
}
}

 if (in_array("1077",$modelArray)) {
  // $Row=$Row==$oldRow?$oldRow+1:$Row;
	//$tmpId=versionToNumber($AppVersion)>301?128:124;
	

    }
    
}

$Row2=$Row;
?>