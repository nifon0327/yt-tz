<?php   
/*
参数：
    1、$FromWebPage 来自引用页面标识
    2、$POrderId 订单流水号
    3、$OrderDate 下单日期
*/

//默认时间设置
$default_blhours=6;   //小时计算
$default_jghours=24;   //小时计算 
$default_schours=24;   //小时计算 

$DateTime=date("Y-m-d H:i:s");

switch($FromWebPage){
	 case "KBL":
	        //获得备料时间
            $AbleDateResult=mysql_query("SELECT ableDate  FROM $DataIn.ck_bldatetime WHERE POrderId='$POrderId' ",$link_id);
            if($AbleDateRow=mysql_fetch_array($AbleDateResult)){
                     $kbl_Date=$AbleDateRow["ableDate"];
                     $kbl_Date=$kbl_Date=="0000-00-00 00:00:00"?$DateTime:$kbl_Date;
           }
           else{
                    $kbl_Date=$DateTime;
           }
           
           $kbl_Hours=floor((strtotime($DateTime)-strtotime($kbl_Date))/3600);
           $bl_cycle=$kbl_Hours>=$default_blhours?"<span class='redB'>$kbl_Hours</span>h":$kbl_Hours."h";
	     break;
	 case "LBL":
	      //获取最后备料时间
	       $LastDateResult=mysql_fetch_array(mysql_query("SELECT Max(L.Date) AS LastBlDate
	        FROM  $DataIn.cg1_stocksheet G 
	        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
	        WHERE G.POrderId='$POrderId' ",$link_id));
	        $lbl_Date=$LastDateResult["LastBlDate"];
	        
	        $lbl_Hours=floor((strtotime($DateTime)-strtotime($lbl_Date))/3600); 
	        $sc_cycle=$lbl_Hours>=$default_blhours?"<span class='redB'>$lbl_Hours</span>h":$lbl_Hours."h";
	     break;
 }
?>