<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" >
<meta name="format-detection" content="telephone=no" />
<title>品检报告</title>
<style>
.table_font{
 font-size: 12px;
}

.textColor{
	color: #308CC0;
	height: 25px;
	line-height: 25px;
	vertical-align: top;
}

.textRight{
  text-align: right;
}

.table_head{
	background-color: rgba(156,195,222,0.40);
	height: 35px;
	line-height: 35px;
	vertical-align: middle;
	color: #308CC0;
}

.table_head td{
   border-top: 1px  solid #308CC0;
}

.table_tr{
	height: 35px;
	line-height: 35px;
	vertical-align: middle;
	
}
.table_tr td{
	border-bottom: 1px  dashed #308CC0;
}

</style>
<?php 
include "../../../basic/parameter.inc";
$checkResult=mysql_query("SELECT  B.Id,B.StockId,B.StuffId,B.shQty,B.CheckQty,B.Qty,B.Date,M.BillNumber,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator    
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=B.Operator  
			LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=B.StockId  
			WHERE  B.Id='$Id' LIMIT 1 ",$link_id);
$dataArray=array();
if($checkRow = mysql_fetch_array($checkResult)) 
  {
            $Id=$checkRow["Id"];
            $Date=date("Y-m-d",strtotime($checkRow["Date"]));
            $StockId=$checkRow["StockId"];
            $BillNumber=$checkRow["BillNumber"];
            $StuffId=$checkRow["StuffId"];
            $StuffCname=$checkRow["StuffCname"];
            $Forshort=$checkRow["Forshort"];
            $Picture=$checkRow["Picture"];
            $Operator=$checkRow["Operator"];
            $shQty=$checkRow["shQty"];
            $Qty=$checkRow["Qty"];
            $Percent=$shQty>0?round(($shQty-$Qty)/$shQty*100,1):"";
            
            $PercentColor=$Percent>=90?"#00A945":"#FF0000";
            
            $weeks=date('w',strtotime($Date));
            
            if ($factoryCheck=='on' && ($weeks==6 || $weeks==0)){
	           $Date=date("Y-m-d",strtotime("$Date -2 day"));
            }
   
            $shQtySTR=number_format($shQty);
             echo "<table border='0' cellpadding='0' cellspacing='0'  width='100%' class='table_font'>";
             echo "<tr><td width='70' class='textColor'>配件名称:</td>
                               <td colspan='3'>$StuffId-$StuffCname</td>
                       </tr>";
             echo "<tr><td class='textColor'>供&nbsp;&nbsp;应&nbsp;&nbsp;商:</td>
                        <td width='120'>$Forshort</td>
                        <td width='40' class='textColor'>NO :</td>
                         <td width='80' class='textRight'>$BillNumber</td></tr>";
               echo "<tr><td class='textColor'>送货数量:</td>
                        <td width='130'>$shQtySTR</td>
                        <td width='40'>&nbsp;</td>
                         <td width='80' class='textRight'>$Operator</td></tr>";    
             echo "<tr><td class='textColor'>合&nbsp;&nbsp;格&nbsp;&nbsp;率:</td>
                        <td width='120' style='color:$PercentColor;'>$Percent%</td>
                        <td width='40'>&nbsp;</td>
                         <td width='80' class='textRight'>$Date</td></tr></table>"; 
                                              
        $badSql="SELECT B.Id,B.Qty AS badQty,IF(B.CauseId='-1',5656565,B.CauseId) AS CauseId,B.Reason,B.Picture AS BadPicture,T.Cause,T.Picture  
                         FROM $DataIn.qc_badrecord S 
                         LEFT JOIN $DataIn.qc_badrecordsheet B ON B.Mid=S.Id  
                         LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  
                         WHERE S.Id='$Id' order by CauseId"; 
    $badResult=mysql_query($badSql,$link_id);
    if($badRow=mysql_fetch_array($badResult)){ 
        echo "<table border='0' cellpadding='0' cellspacing='0'  width='100%' style='padding-top:10px;'  class='table_font'>";
        echo "<tr class='table_head'>
                            <td width='200'>不良原因</td>
				            <td  width='60'>不良数</td>
				           <td  width='60'>不良比例</td></tr>";
      $i=1;
      do{
           $badQty=$badRow["badQty"]==0?"-":$badRow["badQty"];
	       $CauseId=$badRow["CauseId"];
           $Cause=$badRow["Cause"]==""?"&nbsp;":$badRow["Cause"];
           if ($CauseId=='5656565'){
              $Reason=$badRow["Reason"];
              $Cause=$Reason; 
           }
          /*
           $Picture=$badRow["Picture"];
           if ($Picture!=""){
                   $File=anmaIn($Picture,$SinkOrder,$motherSTR);
		   $Dir="download/qccause/";
		   $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
		   $Cause="<a href='#' onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;'>$Cause</a>";
           }
           */
           
           $Bid=$badRow["Id"];
           $badRate=$shQty!=0?sprintf("%.1f",$badQty/$shQty*100)."%":"0.0%";
           $badRate=$badQty==0?"-":$badRate;
           
           $BadPicture=$badRow["BadPicture"];
           /*
            if ($BadPicture==1){
                $badQty="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
           }
           else{
               $checkPicSql=mysql_query("SELECT F.Picture  FROM $DataIn.qc_badrecordfile F  WHERE F.Mid='$Bid' ",$link_id);
               if($checkPicRow=mysql_fetch_array($checkPicSql)){ 
                       $badQty="<a href='#' onClick='OpenOrLoad(\"\",\"\",\"$Bid\",\"qcbad\")' style='CURSOR: pointer;'>$badQty</a>";
               }  
           }
           */
                     
          echo  "<tr  class='table_tr'>
	                <td class='A0101'>$Cause</td>
                    <td class='A0101' align='center'>$badQty</td>
                   <td class='A0101' align='center'>$badRate</td>
            </tr>";
	    $i++;
        }while($badRow=mysql_fetch_array($badResult));  
        echo "</table>";
   }
   
   //取得公司信息
$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE Type='S'",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
		$Company=$CheckMyRow["Company"];
		$Tel=$CheckMyRow["Tel"];
		$Fax=$CheckMyRow["Fax"];
		$Address=$CheckMyRow["Address"];
		$ZIP=$CheckMyRow["ZIP"];
		echo "<div style='width:100%; position:fixed; left:0; bottom:5px;'>
                  <div style='float:right;font-size:10px;text-align:right;margin-right:8px;'>$Company <br>电话:$Tel &nbsp;&nbsp;传真:$Fax <br>$Address  邮政编码:$ZIP</div>
   </div>";
	}
}


?>
