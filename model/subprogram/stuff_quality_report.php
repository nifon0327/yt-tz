<?php
/*
OK$DataIn.电信---yang 20120801
*/

if ($_REQUEST['fm']=='app') {

} else {
	include "../../basic/chksession.php" ;
}


include "../../basic/parameter.inc";
include "../modelfunction.php";
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/factoryCheck/checkSkip.php');

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='../../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../pagefun.js' type=text/javascript></script>";
?>
<script  type="text/javascript">
function OpenOrLoad(d,f,Action,Type){
	var newnow = new Date().getTime();
	win=window.open("../../admin/openorload.php?d="+d+"&f="+f+"&Action="+Action+"&Type="+Type,newnow,"toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}
</script>
<?php
echo "<center>";
if (strlen($StockId)<14){
   $SearchRows=" S.Id='$Id' ";
   }
else{
    $SearchRows=" S.StockId='$StockId' ";
}
$mySql="SELECT S.Id,S.Date,S.StuffId,S.StockId,SUM(S.shQty) AS shQty,SUM(S.checkQty) AS checkQty,SUM(S.Qty) AS Qty,S.AQL,D.StuffCname,D.Picture,D.CheckSign,P.Company,A.Name AS  Operator 
    FROM $DataIn.qc_badrecord S 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid 
	LEFT JOIN $DataIn.companyinfo P ON M.CompanyId=P.CompanyId AND P.Type=8
    LEFT JOIN $DataIn.staffmain A ON A.Number=S.Operator 
	WHERE $SearchRows  GROUP BY S.Date ORDER BY S.Date DESC";
$j=1;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
$comResult=mysql_query($mySql,$link_id);
while($comRow=mysql_fetch_array($comResult)){
     $Mid=$comRow["Id"];
     $Date=$comRow["Date"];
    /******************验厂过滤********************/
    $groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 604 ";
    $groupLeaderResult = mysql_query($groupLeaderSql);
    $groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
    $Leader = $groupLeaderRow['GroupLeader'];
    $skip = false;
    if($FactoryCheck == 'on' and skipData($Leader, $Date, $DataIn, $DataPublic, $link_id)){
      continue;
    }else if($FactoryCheck == 'on'){
      $Date = substr($Date, 0, 10);
    }
    /***************************************/
     $StuffId=$comRow["StuffId"];
     $StockId=$comRow["StockId"];
     $StuffCname=$comRow["StuffCname"];
     $Picture=$comRow["Picture"];
     include "stuffimg_model.php";

     $Company=$comRow["Company"];
     $Qty=$comRow["Qty"];
     $shQty=$comRow["shQty"];
     $checkQty=$comRow["checkQty"];
     $Operator=$comRow["Operator"];
     $CheckSign=$comRow["CheckSign"];
     $AQL=$comRow["AQL"];
     //合格率
     $goodRate=sprintf("%.2f",($shQty-$Qty)/$shQty*100);

     if (strlen($StockId)<14){
         switch ($StockId){
           case -1:
               $StockId="补货单";
               break;
           case -2:
               $StockId="备品单";
               break;
        }
       //$StockId="";
     }
      if ($j>1) echo"<div style='PAGE-BREAK-AFTER: always'></div>"; //分页
        //输出表头
      if ($CheckSign==1 || $AQL==""){    //全检品检报告
          echo "<div style='margin-top: 5%'> </div>";
         echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
           <tr>
               <td height='30' colspan='4' align='center' style='Font-size:18px;'>$CompanyNameStr</td>
          </tr>
           <tr>
               <td height='40' colspan='4' align='center' style='Font-size:20px;Font-weight:bold;'>全检报告</td>
           </tr>
           <tr>
              <td height='22' width='70'>配件名称:</td> 
              <td width='370'>$StuffCname</td> 
              <td width='70'>流 水 号:</td> 
              <td width='130'>$StockId</td> 
           </tr>
           <tr>
              <td height='22'>配 件 ID:</td> 
              <td>$StuffId</td> 
              <td>品检日期:</td> 
              <td>$Date</td> 
           </tr>
            <tr>
              <td height='22'>供 应 商:</td> 
              <td>$Company</td> 
              <td>负 责 人:</td> 
              <td>$Operator</td> 
           </tr>
           <tr>
              <td height='22'>来料数量:</td> 
              <td>$shQty</td> 
              <td>&nbsp;</td> 
              <td>&nbsp;</td> 
           </tr>
          </table>";
           echo "<table width='640'  cellpadding='0' cellspacing='0'>
           <tr>
              <td  width='40'  align='center' class='A1111' height='30' ><b>序号</b></td>
	      <td  width='300' align='center' class='A1101'><b>不良原因</b></td>
              <td  width='100' align='center' class='A1101'><b>不良数</b></td>
              <td  width='100' align='center' class='A1101'><b>不良比例</b></td>
            </tr>";

     if (strlen($StockId)<14){
        // $StockId="";
         $badSql="SELECT B.Id,B.Qty AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,B.Picture AS BadPicture,T.Cause,T.Picture  
                         FROM $DataIn.qc_badrecordsheet B 
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE B.Mid='$Mid' order by CauseId";
         }
    else{
        $badSql="SELECT B.Id,SUM(B.Qty) AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,B.Picture AS BadPicture,T.Cause,T.Picture  
                         FROM $DataIn.qc_badrecord S 
                         LEFT JOIN $DataIn.qc_badrecordsheet B ON B.Mid=S.Id  
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE S.StockId='$StockId' AND S.Date='$Date' GROUP BY B.CauseId order by CauseId";
        }
   // echo $badSql;
    $badResult=mysql_query($badSql,$link_id);
    if($badRow=mysql_fetch_array($badResult)){

      $i=1;
      do{
           $badQty=$badRow["badQty"]==0?"-":$badRow["badQty"];
	   $CauseId=$badRow["CauseId"];
           $Cause=$badRow["Cause"]==""?"&nbsp;":$badRow["Cause"];
           if ($CauseId=='5656565'){
              $Reason=$badRow["Reason"];
              $Cause=$Reason;
           }

           $Picture=$badRow["Picture"];
           if ($Picture!=""){
                   $File=anmaIn($Picture,$SinkOrder,$motherSTR);
		   $Dir="download/qccause/";
		   $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);
		   $Cause="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;'>$Cause</a>";
           }

           $Bid=$badRow["Id"];
           $badRate=$shQty!=0?sprintf("%.1f",$badQty/$shQty*100)."%":"0.0%";
           $badRate=$badQty==0?"-":$badRate;

           $BadPicture=$badRow["BadPicture"];
            if ($BadPicture==1){
                $badQty="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
                /*
                $bFileName="Q".$Bid.".jpg";
                $bFile=anmaIn($bFileName,$SinkOrder,$motherSTR);
                $bDir="download/qcbadpicture/";
                $bDir=anmaIn($bDir,$SinkOrder,$motherSTR);
                $badQty="<a href='#' onClick='OpenOrLoad(\"$bDir\",\"$bFile\")' style='CURSOR: pointer;'>$badQty</a>";
                 *
                 */
           }
           else{
               $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
               if($checkPicRow=mysql_fetch_array($checkPicSql)){
                       $badQty="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
               }
           }

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
    else{
        echo  "<tr>
                 <td align='center' height='30' class='A0111'>1</td>
	         <td class='A0101'>&nbsp;</td>
                 <td class='A0101' align='center'>-</td>
                 <td class='A0101' align='center'>-</td>
            </tr>";
        echo "</table>";
        echo "<table width='640' border='0'><tr>
              <td height='30' align=left> 合格率:  <b style='color:#0A0;'>$goodRate% </b></td>
              </tr>
             <tr><td height='80'>&nbsp;</td></tr>
            </table>";
       }
    }
      else{ //生成抽检报告
          include "stuff_quality_report_1.php";
    }
}
?>
</center>

