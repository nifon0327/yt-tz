<?php
$CompanyIdArray = array(1004,1066,1084,100359,100241,100262,1064,100361,100377);
switch($CompanyId){
       /*case "1090":
          include "ch_shippinglist_toexcel2.php"; // Avenir公司要Invocie PDF格式的EXCEL
       break;*/
     default:
        //  if(in_array($CompanyId, $CompanyIdArray)){
	          include "ch_shippinglist_toexcel1.php";//报关资料
        //  }
        break;
    }
 
?>