<?php 
$XDDate="";$CGDate="";$CKDate="";$PJDate="";
$XDEstate=0;$CGEstate=0;$CKEstate=0;
$Temp_today=date("Y-m-d");
$Temp_dateTime=date("Y-m-d H:i:s");
$XDRemark="";$CGRemark="";$CKRemark="";$PJRemark="";
if(($FactualQty!=0 || $AddQty!=0) && $mainType<2){//下采购单的配件
       //下单时间=配件下单时间-订单下单时间
        if($Date!=""){
                   $XDDate=ceil((strtotime($Date)-strtotime($OrderDate))/3600/24);
                   $XDEstate=1;
                   $XDRemark="title='订单下单日期:$OrderDate  配件下单日期: $Date '";
                 }
         else{
                   $XDDate=ceil((strtotime($Temp_today)-strtotime($OrderDate))/3600/24);
                   $XDRemark="title='订单下单日期:$OrderDate  配件下单日期:未下采购单 '";
                 }
        $LockResult=mysql_fetch_array(mysql_query("SELECT Locks,Date FROM $DataIn.cg1_lockstock WHERE StockId='$StockId'",$link_id));
        $LockDate=$LockResult["Date"];
        $Locks=$LockResult["Locks"];
        if($LockDate!=""){
                  if($Locks==1)$LDDate=ceil((strtotime($LockDate)-strtotime($OrderDate))/3600/24);
                  else $LDDate=ceil((strtotime($Temp_today)-strtotime($OrderDate))/3600/24);
                  $XDDate=$XDDate."(<span class='redB'>".$LDDate."</span>)d";
               }
        else{
                $XDDate=$XDDate."d";
              }
        //echo $XDDate."<br>";
        //采购时间（送完和未送完）送完=最后一次生成送货单时间-下单时间，未送完=今天-下单时间
         if($XDEstate==1){//已下单
                 $shResult=mysql_fetch_array(mysql_query("SELECT M.Date FROM $DataIn.gys_shmain M 
                  LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id 
                  WHERE S.StockId='$StockId' ORDER BY M.Date DESC LIMIT 1 ",$link_id));
                  $shDate=$shResult["Date"];
                  if($shDate!=""){
                            $CGDate=ceil((strtotime(substr($shDate,0,10))-strtotime($Date))/3600/24)."d";
                            $CGEstate=1;
                            $CGRemark="title='配件下单时间:$Date  生成送货单时间:".substr($shDate,0,10)."'";
                          }
                  else {
                           $CGRemark="title='配件下单时间:$Date 生成送货单时间:未生成送货单'";
                          $CGDate=ceil((strtotime($Temp_today)-strtotime($Date))/3600/24)."d";
                          }
               }
         //仓库时间
         if($CGEstate==1){//仓库时间  已生成送货单，时间为最后的送货单时间
                   $psResult=mysql_fetch_array(mysql_query("SELECT M.shDate  FROM $DataIn.gys_shdate M
                   LEFT JOIN $DataIn.gys_shsheet S ON S.Id=M.Sid  
                   WHERE S.StockId='$StockId' ORDER BY M.shDate  DESC LIMIT 1 ",$link_id));
                   $psDate=$psResult["shDate"];
                   if($psDate!=""){
                             $CKDate=ceil((strtotime($psDate)-strtotime($shDate))/3600)."h";
                             $CKRemark="title='生成送货单时间:$shDate  送货单审核时间:$psDate'";
                             $CKEstate=1;
                            }  
              }
          //品检时间：入库时间-送货单审核时间
         if($CKEstate==1){
               $rkResult=mysql_fetch_array(mysql_query("SELECT M.rkDate  FROM $DataIn.ck1_rkmain  M  
                LEFT JOIN $DataIn.ck1_rksheet S ON S.Mid=M.Id WHERE S.StockId=$StockId ORDER BY M.Date  DESC LIMIT 1",$link_id));
                $rkDate=$rkResult["rkDate"];
                if($rkDate!="")$PJDate=ceil((strtotime($rkDate)-strtotime($psDate))/3600)."h";
                else $PJDate=ceil((strtotime($Temp_dateTime)-strtotime($psDate))/3600)."h";
                $PJRemark="title='送货单审核时间:$psDate 配件入库时间:$rkDate'";
              }
      }
else{
         $XDDate="-";$CGDate="-";$PJDate="-";$CKDate="-";
       }
?>