<?php 
//$DataIn.电信---yang 20120801
   
     $checkResult = mysql_query("SELECT L.Ac,L.Re,L.Lotsize,S.SampleSize 
                 FROM $DataIn.qc_levels L
                 LEFT JOIN  $DataIn.qc_lotsize S ON S.Code=L.Code     
                 WHERE L.AQL='$AQL' AND S.Start<='$shQty' AND S.End>='$shQty'",$link_id);
               
               if ($checkRow = mysql_fetch_array($checkResult)){
                   $ReQty=$checkRow["Re"];
                   $Lotsize=$checkRow["Lotsize"];
                   $SampleSize=$Lotsize>0?$Lotsize:$checkRow["SampleSize"];
                   
               }
      $ReQty=$ReQty==""?1:$ReQty;
      $checkQty=$checkQty==0?$SampleSize:$checkQty;
      
//生成抽检品检报告
           echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
           <tr>
               <td height='30' colspan='4' align='center' style='Font-size:18px;'>$CompanyNameStr</td>
          </tr>
           <tr>
               <td height='40' colspan='4' align='center' style='Font-size:20px;Font-weight:bold;'>抽检报告</td>
           </tr>
           <tr>
              <td height='22' width='70'>配件名称:</td> 
              <td width='370'>$StuffCname</td> 
              <td width='70'>流&nbsp;&nbsp;水&nbsp;&nbsp;号:</td> 
              <td width='130'>$StockId</td> 
           </tr>
           <tr>
              <td height='22'>配&nbsp;&nbsp;件&nbsp;&nbsp;ID:</td> 
              <td>$StuffId</td> 
              <td>品检日期:</td> 
              <td>$Date</td> 
           </tr>
            <tr>
              <td height='22'>供&nbsp;&nbsp;应&nbsp;&nbsp;商:</td> 
              <td>$Company</td> 
              <td>负&nbsp;&nbsp;责&nbsp;&nbsp;人:</td> 
              <td>$Operator</td> 
           </tr>
           <tr>
              <td height='22'>来料数量:</td> 
              <td>$shQty</td> 
              <td>抽样数量:</td> 
              <td>$checkQty</td> 
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
         $StockId="";//$ReStr="";  
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
    if ($ReQty>$Qty){$ReStr="<b style='color:#0A0;'>允收</b>"; }else{$ReStr="<b style='color:#F00;'>拒收</b>";}
   // echo $badSql;
    $badResult=mysql_query($badSql,$link_id);
    if($badRow=mysql_fetch_array($badResult)){ 
        
      $i=1;$badCauseList=0;
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
           $BadPicture=$badRow["BadPicture"];
            if ($BadPicture==1){
                $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
                /*
                   $bFileName="Q".$Bid.".jpg";
                   $bFile=anmaIn($bFileName,$SinkOrder,$motherSTR);
		   $bDir="download/qcbadpicture/";
		   $bDir=anmaIn($bDir,$SinkOrder,$motherSTR);			
		   $BadPicture="<a href='#' onClick='OpenOrLoad(\"$bDir\",\"$bFile\")' style='CURSOR: pointer;'>$badQty</a>";
                 * 
                 */
           }
           else{
                $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
               if($checkPicRow=mysql_fetch_array($checkPicSql)){ 
                       $BadPicture="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
               }  
           }
           $badRate=$checkQty!=0?sprintf("%.1f",$badQty/$checkQty*100)."%" :"0.0%";
           $badRate=$badQty==0?"-":$badRate;
           
          if ($badQty>0){
                      if ($BadPicture!=0)    $badQty=$BadPicture;
            echo  "<tr>
                 <td align='center' height='30' class='A0111'>$i</td>
	         <td class='A0101'>$Cause</td>
                 <td class='A0101' align='center'>$badQty</td>
                 <td class='A0101' align='center'>$badRate</td>
            </tr>";
            $badCauseList=1;
          }
	 $i++;
        }while($badRow=mysql_fetch_array($badResult)); 
        if ($badCauseList==0){
           echo  "<tr>
                 <td align='center' height='30' class='A0111'>1</td>
	         <td class='A0101'>&nbsp;</td>
                 <td class='A0101' align='center'>-</td>
                 <td class='A0101' align='center'>-</td>
            </tr>"; 
        }
        
        echo "</table>";
        
        echo "<table width='640' border='0'><tr>
              <td height='30' width='150' align=left> AQL:  <b><a href='../../cjgl/qc_levels.php' target='_blank'>$AQL</a> </b></td>
              <td  align=left> Ac/Re: $ReStr </td>
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
              <td height='30'  width='150' align=left> AQL:  <b><a href='../../cjgl/qc_levels.php' target='_blank'>$AQL<a></b></td>
              <td height='30'> Ac/Re: $ReStr </td>
              </tr>
             <tr><td height='80'>&nbsp;</td></tr>
            </table>"; 
 }
?>
    
