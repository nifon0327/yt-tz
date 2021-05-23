<?
//产品资料查询
$mySql= "SELECT P.ProductId,P.cName,P.eCode,P.TestStandard,P.Weight 
	FROM `$DataIn`.`productdata` P
	where   P.ProductId='$ProductId' LIMIT 1";
 $myResult = mysql_query($mySql);
if($myRow = mysql_fetch_array($myResult))
   {
        $ProductId=$myRow["ProductId"];
        $eCode=$myRow["eCode"];
        $cName=$myRow["cName"];
        $TestStandard=$myRow["TestStandard"];
        $Weight=$myRow["Weight"];
        
        $productId=$ProductId;
      include "../../model/subprogram/weightCalculate.php";
      
      if ($Weight>0){
           $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
           $WeightSTR=$Weight>0?"$Weight|$boxPcs|$extraWeight":"";
      }
      else{
	      $WeightSTR="";
      }
                      
        if ($TestStandard*1>0){
            $ImagePath="download/teststandard/T$ProductId.jpg";
        }
        else{
           $ImagePath=""; 
        }             
        $jsonArray=array( "$ProductId","$cName","$eCode","$ImagePath","$WeightSTR"); 
}
?>