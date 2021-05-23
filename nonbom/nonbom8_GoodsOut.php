<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件申领记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_GoodsOut";	
$_SESSION["nowWebPage"]=$nowWebPage; 
if($Ids==""){
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
}else{
$Lens=1;
}

    $CheckResult  =mysql_query("SELECT A.GoodsId  FROM $DataIn.nonbom8_outsheet A
    LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	WHERE A.Id IN ($Ids)",$link_id);
   $PropertySign=0;
   while($CheckRow= mysql_fetch_array($CheckResult)){
		$GoodsId=$CheckRow["GoodsId"];
		$PropertyResult=mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7",$link_id);  
		 if($PropertyRow=mysql_fetch_array($PropertyResult)){
		         $PropertySign=1; break;
		  }
}
if($PropertySign==1 && $Lens>1 ){
    $Parameter="Estate,$Estate,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
   echo "<body ><form name='form1' id='checkFrom' enctype='multipart/form-data' action='' method='post' >";
	if($Parameter!=""){
		PassParameter($Parameter);
		}
   echo "</from></body></html>";
   echo "<SCRIPT LANGUAGE=JavaScript>alert('固定资产只能单条发放!');ReOpen(\"$fromWebPage\");</script>";
}

if($Lens==1 && $PropertySign==1){
      include "nonbom8_GoodsOut_Proper.php";
}else{
       include "nonbom8_GoodsOut_updated.php";
}
?>
