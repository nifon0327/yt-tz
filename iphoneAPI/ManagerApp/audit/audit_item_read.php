<?php 
   //根据员工:$Number获得经理审核项目(ModuleId:(1044)的权限
   //ModelId，项目名称，未审数量，显示页面，1/0权限，1/0是否需填写退回原因，1/2区分主/子系统，审核时间限制 
  $test_cz = ( $LoginNumber==11965 ) ? 1 : 0;
  $Test_Sign=($LoginNumber==10868 || $test_cz==1)?1:0; 
  
   //取得权限
   $ReadAccessSign=4;
    include "user_access.php";  //用户权限
   $dataArray=array();$jsondata=array();$ServerId=0;
   $OverNums=0;$Nums=0;$hidden=1;$onTap=1;$onHidden=0;
   switch($NextPage){
      case 1:
            $NextPage++; 
		    include "audit_item_sub1.php";  		   
            if (count($jsonArray)>0)   break;
	case 2:	   
	        $NextPage++; 
	         include "audit_item_sub2.php";  		   
             if (count($jsonArray)>0)   break;
	case 3:	   
	        $NextPage++; 	
	         include "audit_item_sub3.php";  
		     if (count($jsonArray)>0)   break; 
	case 4:	   
	      $NextPage="END";
		    include "audit_item_sub4.php"; 
		   break;
}
?>
