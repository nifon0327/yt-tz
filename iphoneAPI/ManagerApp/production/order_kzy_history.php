<?php 
//可备料
$curDate=date("Y-m-d");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
//$SearchRows="AND S.Estate=1 ";
if ($LoginNumber == '11965') {$SearchRows="";}
$OrderBySTR=" ORDER BY Weeks desc";


$LockTotalQty=0;$OverTotalQty=0;$OverCount=0;$blCount=0;$curCount=0;
$newData = array();
$mySql="SELECT M.CompanyId,M.OrderDate,M.OrderPO,S.POrderId,P.cName,
P.TestStandard,S.ProductId,S.Qty,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime, YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks 
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S  ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
WHERE 1 and S.scFrom<=0  $SearchRows   
and M.CompanyId='$CompanyId' and S.ProductId='$ProductId'
 GROUP BY S.Id $OrderBySTR";

  // echo $mySql;
  //  $curDate=date("Y-m-d");
    //$CountArray=array();
    $nextCount=0;$laterCount=0;
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            $oldCompanyId=$CompanyId;
          
            do 
            {	
                    $POrderId=$myRow["POrderId"];
                    $ProductId=$myRow["ProductId"];
                    $OrderPO=$myRow["OrderPO"];
                    $cName=$myRow["cName"];
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
                    $Price=$myRow["Price"];	

                    $OrderDate=$myRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $myRow["Leadtime"]);
                    $TestStandard=$myRow["TestStandard"];
                    include "order/order_TestStandard.php";
	                   include "../../admin/order_datetime.php";
	                   $BlDate=$R_blQty==$R_llQty?$lbl_Date:$kbl_Date;

	                     $Date=GetDateTimeOutString($BlDate,'');
	                     $DateColor=$kbl_Hours>=$default_blhours?"#FF0000":"";

                        $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
	                     if ($Leadtime!=""){
		                     $colorSign=$curDate>=$Leadtime?4:0;
	                     }
	                     else{
		                      $colorSign=0;
	                     }
                    
                    $OrderSignColor=0;$cgRemark="";
                    
                    if ($Locks==2)  $OrderSignColor=4; 					
					$Remark.=$cgRemark;	
					$CompanyId=$myRow["CompanyId"];
                      $ShipType=$myRow["ShipType"];
                      $timeColor=$curDate>=$Leadtime?"#FF0000":"";
                      $Leadtime=date("m/d",strtotime($Leadtime)) . "|$timeColor";
                      $QtySTR=number_format($Qty);
                      $Weeks=substr($myRow["Weeks"],4,2);
                      $bgColor=$myRow["Weeks"]<$curWeek?"#FF0000":"";//#00BA61
                       include "submodel/stuff_factualqty_bgcolor.php";
                      $tempArray=array(
                      "Id"=>"$POrderId","icon4"=>"scdj_11",
                      "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#0000FF"),
					    "Col2"=> array("Text"=>"$OrderPO"),
                      "Col4"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color")
                   );
                   $newData[]=array("Tag"=>"datas","data"=>$tempArray);
            } while($myRow = mysql_fetch_assoc($myResult));
   }


  $jsonArray=$newData; 
?>