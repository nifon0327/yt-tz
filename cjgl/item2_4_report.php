<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='lightgreen/read_line.css'>";
echo "<center>";
if (strlen($StockId)<14){
   $SearchRows=" S.Id='$Id' ";
   }
else{
    $SearchRows=" S.StockId='$StockId' ";
}
$mySql="SELECT S.Id,S.Date,S.StuffId,S.StockId,SUM(S.shQty) AS shQty,SUM(S.Qty) AS Qty,D.StuffCname,P.Company,A.Name AS  Operator 
        FROM $DataIn.qc_badrecord S 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid 
	LEFT JOIN $DataIn.companyinfo P ON M.CompanyId=P.CompanyId 
        LEFT JOIN $DataPublic.staffmain A ON A.Number=S.Operator 
	WHERE $SearchRows GROUP BY S.Date ORDER BY S.Date DESC";
$j=1;
$comResult=mysql_query($mySql,$link_id);
while($comRow=mysql_fetch_array($comResult)){
     $Mid=$comRow["Id"];
     $Date=$comRow["Date"];
     $StuffId=$comRow["StuffId"];
     $StockId=$comRow["StockId"];
     $StuffCname=$comRow["StuffCname"];
     $Company=$comRow["Company"];
     $Qty=$comRow["Qty"];
     $shQty=$comRow["shQty"];
     $Operator=$comRow["Operator"];

     //合格率
     $goodRate=sprintf("%.2f",($shQty-$Qty)/$shQty*100);

     if (strlen($StockId)<14){
         $StockId="";
         $badSql="SELECT B.Qty AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,T.Cause 
                         FROM $DataIn.qc_badrecordsheet B 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE B.Mid='$Mid' order by CauseId";
         }
    else{
        $badSql="SELECT SUM(B.Qty) AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,T.Cause 
                         FROM $DataIn.qc_badrecord S 
                         LEFT JOIN $DataIn.qc_badrecordsheet B ON B.Mid=S.Id  
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE S.StockId='$StockId' AND S.Date='$Date' GROUP BY B.CauseId order by CauseId";
        }
   // echo $badSql;
    $badResult=mysql_query($badSql,$link_id);
    if($badRow=mysql_fetch_array($badResult)){
         if ($j>1) echo"<div style='PAGE-BREAK-AFTER: always'></div>"; //分页
        //输出表头
         echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
           <tr>
               <td height='30' colspan='4' align='center' style='Font-size:18px;'>上海市研砼包装有限公司</td>
          </tr>
           <tr>
               <td height='40' colspan='4' align='center' style='Font-size:20px;Font-weight:bold;'>品检报告</td>
           </tr>
           <tr>
              <td height='30' width='80'>配件名称:</td> 
              <td width='350'>$StuffCname</td> 
              <td width='80'>流&nbsp;&nbsp;水&nbsp;&nbsp;号:</td> 
              <td width='130'>$StockId</td> 
           </tr>
           <tr>
              <td height='20'>配&nbsp;&nbsp;件&nbsp;&nbsp;ID:</td> 
              <td>$StuffId</td> 
              <td>品检日期:</td> 
              <td>$Date</td> 
           </tr>
            <tr>
              <td height='30'>供&nbsp;&nbsp;应&nbsp;&nbsp;商:</td> 
              <td>$Company</td> 
              <td>负&nbsp;&nbsp;责&nbsp;&nbsp;人:</td> 
              <td>$Operator</td> 
           </tr>
          </table>";
      echo "<table width='640'  cellpadding='0' cellspacing='0'>
           <tr>
              <td  width='40'  align='center' class='A1111' height='30' ><b>序号</b></td>
	      <td  width='300' align='center' class='A1101'><b>不良原因</b></td>
              <td  width='150' align='center' class='A1101'><b>不良数</b></td>
              <td  width='150' align='center' class='A1101'><b>不良比例</b></td>
            </tr>";
      $i=1;
      do{
           $badQty=$badRow["badQty"];
	   $CauseId=$badRow["CauseId"];
           $Cause=$badRow["Cause"];
           if ($CauseId=='5656565'){
              $Reason=$badRow["Reason"];
              $Cause=$Reason;
           }
           $badRate=sprintf("%.1f",$badQty/$shQty*100)."%";
           $badRate=$badQty==0?"-":$badRate;
          echo  "<tr>
                 <td align='center' height='30' class='A0111'>$i</td>
	         <td class='A0101'>$Cause</td>
                 <td class='A0101' align='center'>$badQty</td>
                 <td class='A0101' align='center'>$badRate</td>
            </tr>";
	 $i++;
        }while($badRow=mysql_fetch_array($badResult));
        echo "</table>";
        echo "<table width='640' border='0'><tr>
              <td height='30' align=left> 合格率:  <b style='color:#0A0;'>$goodRate% </b></td>
              </tr>
             <tr><td height='80'>&nbsp;</td></tr>
            </table>";
        $j++;
    }
 }
?>
</center>

