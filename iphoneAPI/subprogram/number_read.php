<?php 
//读取采购请款员工姓名
   include "../../basic/parameter.inc";
   $postInfo = $_POST["info"];
   $jsonArray = array();
   
  switch($postInfo){
     case "1047":
     case "fk":  
            $jsonArray[] = array( "","全 部"); 
            $Result=mysql_query("SELECT S.BuyerId,M.Name  
                    FROM $DataIn.cw1_fkoutsheet S 
                    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
                    WHERE S.Estate=2  GROUP BY S.BuyerId ",$link_id);
            while($myRow = mysql_fetch_array($Result)) 
            {	
                $BuyerId=$myRow["BuyerId"];
                $Name = $myRow["Name"];		
                $jsonArray[] = array( "$BuyerId","$Name");        
            }
      break;
  }
  
   echo json_encode($jsonArray);
?>