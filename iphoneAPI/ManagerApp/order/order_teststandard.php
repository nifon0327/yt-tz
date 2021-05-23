<?php 
 /*
 功能模块:产品标准图、产品名称显示颜色
 传入参数:$POrderId,$TestStandard
 输出参数:$TestStandardColor,$TestStandardFile,$TestStandardIcon
 */

if ($TestStandard*1>0){
    $TestStandardFile="download/teststandard/T$ProductId.jpg";
    $TestStandardIcon="download/productIcon/" . $ProductId . ".jpg";  
    

       if(file_exists('../../download'. "/productIcon/" . $ProductId . '.png')){
	       $TestStandardIcon ="download/productIcon/" . $ProductId . ".png"; 
       }

}
else{
   $TestStandardFile=""; 
   $TestStandardIcon=""; 
}

switch ($TestStandard*1) {
    case 1:
           //检查是否需更改标准图
			$checkTSResult=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9'",$link_id);
			if(mysql_num_rows($checkTSResult)){	
			     $TestStandardColor="#FF00FF";//紫色
			}
			else{
				 $TestStandardColor="#FFA500";//橙色
			}
        break;
    case 2:
        $TestStandardColor="#0000FF";//蓝色
        break;
    case 3://紫色
        $TestStandardColor="#FF00FF";
        break;
    case 4://紫红色
       $TestStandardColor="#FF6633";
         break;
    default:
        $TestStandardColor="#000000";
        break;
}


 ?>