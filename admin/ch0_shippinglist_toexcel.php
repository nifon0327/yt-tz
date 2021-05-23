<?php
switch($CompanyId){
      /* case "1090":
          include "ch0_shippinglist_toexcel2.php"; // Avenir公司要Invocie PDF格式的EXCEL
       break;*/
     
        
     default:
        include "ch0_shippinglist_toexcel1.php";//报关资料
        break;
    }
?>
