<?php
      include_once "tasks_function.php";
      include "../basic/parameter.inc";

      $SearchRows="";
      switch($Floor){
         case "3A":
         case "6":    $Floor=6; $Line="D"; break;
         case "17":  $Floor=17; $Line=$Line==""?"A":$Line; break;
	     default:     $Floor=3;$Line=$Line==""?"A":$Line; break;
      }

      $LineResult=mysql_fetch_array(mysql_query("SELECT C.Id  FROM  $DataIn.qc_scline C  WHERE  C.LineNo='$Line'  AND C.Floor='$Floor' LIMIT 1",$link_id));
      $LineId=$LineResult["Id"]==""?1:$LineResult["Id"];
      $SearchRows=" AND  H.LineId='$LineId' ";

    $ListSTR="";
 //品检任务
$myResult=mysql_query("SELECT  S.Id,S.StuffId,D.StuffCname,D.Picture,D.TypeId,D.FrameCapacity,YEARWEEK(G.DeliveryDate,1) AS Weeks,H.DateTime,
		   Max(C.Date) AS QcDate,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks,H.Estate,Max(T.shDate) AS shDate,A.Name  AS Operator,P.Forshort      
			FROM $DataIn.gys_shsheet S 
			INNER JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
			LEFT JOIN  $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataIn.qc_mission H ON H.Sid=S.Id 
			LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=G.POrderId AND W.ReduceWeeks=0
			LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
			LEFT JOIN $DataIn.gys_shdate T ON T.Sid=S.Id   
			LEFT JOIN $DataPublic.staffmain A ON A.Number=GM.BuyerId 
			WHERE  S.Estate=2  AND M.Floor='$Floor'  AND S.SendSign IN(0,1)  $SearchRows GROUP BY S.Id ORDER BY QcDate DESC,Estate,shDate,H.DateTime,Weeks,ReduceWeeks LIMIT 1",$link_id);

if($myRow = mysql_fetch_array($myResult)) {
           $StuffId=$myRow["StuffId"];
           $StuffCname=$myRow["StuffCname"];
           $Picture=$myRow["Picture"];
            $Operator=$myRow["Operator"];
            $Forshort=$myRow["Forshort"];
           $FrameCapacity=$myRow["FrameCapacity"]==0?"未设置":$myRow["FrameCapacity"] . " Pcs";

        if ($StuffId!=$OStuffId){
             //已收货数量
			 $checkRkQty= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS RkQty,COUNT(*) AS RkCount FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId>0",$link_id));
			 $RkQty=$checkRkQty["RkQty"]==""?0:number_format($checkRkQty["RkQty"]);
			 $RkCount=$checkRkQty["RkCount"]==""?0:$checkRkQty["RkCount"];

	           include "../basic/downloadFileIP.php";
	           if ($TestSign==1){
		           $ImagePath="$donwloadFileIP/download/stufffile/92421.gif";
	           }else{
		           $ImagePath="$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg";
	           }

	          // $img_mtime=date("y-m-d",filemtime($ImagePath));

	           $img_info = getimagesize($ImagePath);
	           $img_width=$img_info[0];
	           $img_height=$img_info[1];
	           $wScale=$img_width/1080;
	           $hScale=$img_height/1660;

	           if($wScale>=$hScale){
		           $SizeStr="width='1075' ";
	           }
	           else{
		           $SizeStr="height='1660' ";
	           }

	           $ListSTR="<center><img src='$ImagePath' $SizeStr></center>";
        }
}
$upTime=date("H:i:s");

if ($StuffId!=$OStuffId){
           $url="$donwloadFileIP/remoteDloadFile/R_getfiletime.php?StuffId=$StuffId";
		   $str=file_get_contents(iconv("UTF-8","GB2312",$url));
		   $img_mtime=iconv("GB2312","UTF-8",$str);
?>
 <input type='hidden' id='StuffId' name='StuffId' value='<?php echo $StuffId; ?>'>
<div id='headdiv' style='height:180px;'>
   <div class='cName2' style='width:240px;'> <?php echo "<span class='blue_color'>$StuffId-</span>";?></div>
   <div class='cName2' style='width:800px;'> <?php echo "$StuffCname";?></div>
   <div class='cName2' style='height:60px;margin-top:-10px;width:600px;'><span class='blue_color'><?php echo $Forshort;?></span></div>
   <div class='cName2' style='height:60px;margin-top:-10px;width:440px;text-align:right;'><span class='blue_color'><?php echo $Operator;?></span></div>
</div>
 <ul class='info'>
		       <li style=' border-right: 3px rgba(2,115,178,0.25) solid;'><img  src='image/mtime_1.png'  style='width: 60px;height: 60px;'/><span class='black_color'><?php echo $img_mtime; ?></span></li>
			   <li style=' border-right: 3px rgba(2,115,178,0.25) solid;'><img  src='image/rkqty_1.png' style='width: 60px;height: 60px;'/><span class='black_color'><?php echo $RkQty . "($RkCount)"; ?></span></li>
			   <li><img  src='image/frame_1.png' style='width: 60px;height: 60px;'/><span class='black_color' id='FrameCapacity'><?php echo  $FrameCapacity ; ?></span></li>
 </ul>

<div id='listdiv' style='overflow: hidden;height:1660px;width:1080px;'>
<?php echo $ListSTR;?>
</div>
<?php
}else{
		 if ($NewSign==1) {
		      echo $upTime . "|" . $FrameCapacity;
		 }
		 else  {echo $upTime;}
 }?>
