<style type="text/css">
<!--
.A0011{BORDER-BOTTOM: none;BORDER-top: 0px none;BORDER-LEFT:1px solid #999999;BORDER-RIGHT: 1px solid #999999;}
.ShowSheet{left:7px;width:820px;height:350px;overflow: scroll;overflow-x:hidden;}
-->
</style>
<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
ChangeWtitle("供应商评审标准记录");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<center>";
$sheetResult=mysql_query("SELECT P.CompanyId,P.Operator,F.Company,F.Address,F.Tel,F.Fax,S.LegalPerson,S.BLdate,S.TRCdate,S.PLdate,S.Description,S.Aptitudes,S.EAQF,S.Quality, S.Normative, S.Effect, S.Qos, S.Results 
FROM $DataIn.trade_object P 
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId 
LEFT JOIN $DataIn.providersheet S ON P.CompanyId=S.CompanyId 
WHERE P.CompanyId=$CompanyId  LIMIT 1",$link_id);
if($data_Row = mysql_fetch_array($sheetResult)){
	$CompanyId=$data_Row["CompanyId"];
	$CompanyName=$data_Row["Company"];
	$checkBuyer=mysql_fetch_array(mysql_query("SELECT BuyerId FROM $DataIn.cg1_stocksheet WHERE CompanyId='$CompanyId' ORDER BY Id LIMIT 1",$link_id));
    $BuyerId=$checkBuyer["BuyerId"];
	if($BuyerId!=""){
		$Operator=$BuyerId;
		include "../model/subprogram/staffname.php";
		$Buyer=$Operator;
		}
	else{
		$Buyer="&nbsp;";
		}
	$Address=$data_Row["Address"]==""?"&nbsp;":$data_Row["Address"];
    $Tel=$data_Row["Tel"]==""?"&nbsp;":$data_Row["Tel"];
    $Fax=$data_Row["Fax"]==""?"&nbsp;":$data_Row["Fax"];
    $LegalPerson=$data_Row["LegalPerson"]==""?"&nbsp;":$data_Row["LegalPerson"];
    $BLdate=$data_Row["BLdate"]==""?"&nbsp;":$data_Row["BLdate"];;
    $TRCdate=$data_Row["TRCdate"]==""?"&nbsp;":$data_Row["TRCdate"];
    $PLdate=$data_Row["PLdate"]==""?"&nbsp;":$data_Row["PLdate"];
    $Description=$data_Row["Description"]==""?"&nbsp;":$data_Row["Description"];
    $Aptitudes=$data_Row["Aptitudes"]==""?"&nbsp;":$data_Row["Aptitudes"];
    $EAQF=$data_Row["EAQF"]==""?"&nbsp;":$data_Row["EAQF"];
    $Quality=$data_Row["Quality"];
    $Normative=$data_Row["Normative"];
    $Effect=$data_Row["Effect"];
    $Qos=$data_Row["Qos"];
    $Results=$data_Row["Results"];  
    $checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Defaults=0 LIMIT 1",$link_id));
    $LinkName=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
    $Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
    }
	//评审等级
$Grade_Result = mysql_query("SELECT Id,Grade FROM $DataPublic.qualitygrade WHERE Estate=1 AND Type=1 order by Id",$link_id);
if($Grade_Row = mysql_fetch_array($Grade_Result)){
	do{
		$Grade[]=$Grade_Row;
      	}while($Grade_Row = mysql_fetch_array($Grade_Result));
	}
$QualitySTR=getGradeValue($Grade,$Quality);
$NormativeSTR=getGradeValue($Grade,$Normative);
$EffectSTR=getGradeValue($Grade,$Effect);
$QosSTR=getGradeValue($Grade,$Qos);
$ResultsSTR=getGradeValue($Grade,$Results);
//品检记录
$QcbadSTR="";
$QcResult=mysql_query("SELECT B.Id,B.StuffId,B.StockId,B.Date,B.Qty,B.Operator,D.StuffCname,D.Picture,G.BuyerId 
	FROM $DataIn.qc_badrecord B 
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=B.shMid 
    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=B.StuffId 
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=B.StockId 
    WHERE M.CompanyId=$CompanyId AND B.Qty>0 ORDER BY B.Date DESC",$link_id);
if($bad_Row = mysql_fetch_array($QcResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	do{
    	$Bid=$bad_Row["Id"];
        $Date=$bad_Row["Date"];
        $StuffId=$bad_Row["StuffId"];
        $StockId=$bad_Row["StockId"];
        $StuffCname=$bad_Row["StuffCname"];
        $Picture=$bad_Row["Picture"];
        if($Picture==1){//有PDF文件
			//$StuffCname="<a href='../download/stufffile/".$StuffId.".pdf' target='_blank'>$StuffCname</a>";
			include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
	  		}
		$Qty=$bad_Row["Qty"];
        $Operator=$bad_Row["BuyerId"];
        include "../model/subprogram/staffname.php";
        $BuyerName=$Operator;
        $Operator=$bad_Row["Operator"];
        include "../model/subprogram/staffname.php";
        $Operator=$Operator=="0"?"李莉":$Operator; //前期有未保存到验收人的情况         
        if(strlen($StockId)<14){
			$BuyerName=$Buyer;
         	}
		$Reason="";
        $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId   WHERE B.Mid='$Bid'",$link_id);
        while($cause_row = mysql_fetch_array($cause_Result)){
			$CauseId=$cause_row["CauseId"];
            if ($CauseId=="-1"){
				if ($Reason!="") $Reason.=" / ";
				$Reason.=$cause_row["Reason"];
                }
			else{
            	if ($Reason!="") $Reason.=" / ";
                $Reason.=$cause_row["Cause"];
            	}
        	}
        $Reason=$Reason==""?"&nbsp;":$Reason; 
		$QcbadSTR.="<tr><td height='25' width='100px' class='A0111'>$Date</td><td width='220px' class='A0101' align='left'>$StuffCname</td><td width='60px'  class='A0101'>$Qty</td><td width='200px' class='A0101' align='left'>$Reason</td><td width='80px' class='A0101'>直接退货</td><td width='60px'  class='A0101'>$Operator</td><td width='60px'  class='A0101'>$BuyerName</td>";
		}while($bad_Row = mysql_fetch_array($QcResult));
  	}
if($QcbadSTR==""){
	$QcbadSTR="<tr><td height='25' width='100px' class='A0111'>&nbsp;</td><td width='220px' class='A0101'>&nbsp;</td><td width='60px'  class='A0101'>&nbsp;</td><td width='200px' class='A0101'>&nbsp;</td><td width='80px' class='A0101'>&nbsp;</td><td width='60px'  class='A0101'>&nbsp;</td><td width='60px'  class='A0101'>&nbsp;</td>";
	}
  //年度评审记录 
$ReviewSTR="";
$ReviewResult=mysql_query("SELECT W.Year,Q.Grade,W.Results,W.Reason,W.Verifier,W.Approver,W.Date FROM $DataIn.providerreview W LEFT JOIN $DataPublic.qualitygrade Q ON Q.Id=W.Results WHERE W.CompanyId=$CompanyId  ORDER BY W.Year DESC",$link_id);
if($Review_Row = mysql_fetch_array($ReviewResult)){
	do{ 
        $Year=$Review_Row["Year"];
        $Results=$Review_Row["Results"];
        $Grade=$Review_Row["Grade"];
        if ($Results>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Results'",$link_id));
            $ResultSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"];        
         	}
        else{
            $ResultSTR=$Grade;     
        	}
        $Reason=$Review_Row["Reason"];
        $Operator=$Review_Row["Verifier"];
        include "../model/subprogram/staffname.php";
        $Verifier=$Operator;
        $Operator=$Review_Row["Approver"];
        include "../model/subprogram/staffname.php";
        $Approver=$Operator=="0"?"":$Operator;
        $Date=$Review_Row["Date"];
        $ReviewSTR.="<tr><td height='25' width='80px' class='A0111'>$Year 年</td><td width='260px' class='A0101'>$ResultSTR</td><td width='200px' align='left' class='A0101'>$Reason</td><td width='80px' class='A0101'>$Verifier</td><td width='80px'  class='A0101'>$Approver</td><td width='80px'  class='A0101'>$Date</td>";
      	}while($Review_Row = mysql_fetch_array($ReviewResult));
	}
if($ReviewSTR==""){
	$Year=date("Y");
	$ReviewSTR="<tr><td height='25' width='80px' class='A0111'>$Year 年</td><td width='260px' class='A0101'>&nbsp;</td><td width='200px'  class='A0101'>&nbsp;</td><td width='80px' class='A0101'>&nbsp;</td><td width='80px'  class='A0101'>&nbsp;</td><td width='80px'  class='A0101'>&nbsp;</td>";
  	}
echo "<table width='840' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
           <tr>
               <td height='40' colspan='4' align='center' style='Font-size:20px;Font-weight:bold;'>合格供应商评审标准记录</td>
           </tr>
           <tr>
              <td height='30' width='100' class='A1111'>供应商名称</td> 
              <td width='300' class='A1101'> $CompanyName</td> 
              <td width='100'  class='A1101'>地 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</td> 
              <td width='300' class='A1101'> $Address</td> 
           </tr>
           <tr>
              <td height='30' class='A0111'>公司电话</td> 
              <td class='A0101'> $Tel</td> 
              <td class='A0101'>联&nbsp;&nbsp;系&nbsp;&nbsp;人</td> 
              <td class='A0101'> $LinkName</td> 
           </tr>
            <tr>
              <td height='30' class='A0111'>公司传真</td> 
              <td class='A0101'> $Fax</td> 
              <td class='A0101'>联系人电话</td> 
              <td class='A0101'> $Mobile</td> 
           </tr>
           <tr>
              <td height='30' class='A0111'>企业法人</td> 
              <td class='A0101'> $LegalPerson</td> 
              <td class='A0101'>营业执照有效期</td> 
              <td class='A0101'> $BLdate</td> 
           </tr>
           <tr>
              <td height='30' class='A0111'>税务登记证有效期</td> 
              <td class='A0101'> $TRCdate</td> 
              <td class='A0101'>生产许可证有效期</td> 
              <td class='A0101'> $PLdate</td> 
           </tr>
           <tr>
               <td height='30' colspan='4' class='A0011'>供货方简介: $Description</td>
           </tr>
            <tr>
               <td height='30' colspan='4' class='A0011'>供货方已获资质: $Aptitudes</td>
           </tr>
            <tr>
               <td height='30' colspan='4' class='A0011'>供货方质量能力: $EAQF</td>
           </tr>
            <tr>
               <td height='30' colspan='3' class='A0110'>&nbsp;</td>
               <td class='A0101'>采购员: $Buyer</td>
           </tr>
            <tr>
               <td height='30' colspan='4' class='A0011'>首次供货评审:</td>
           </tr>
             <tr>
               <td height='160' colspan='4' class='A0111'>
                  <table  width='838' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
                       <tr>
                           <td  height='30' width='138px' >1、质&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量:</td> $QualitySTR
                       </tr>
                        <tr>
                           <td  height='30' width='138px' >2、送货时间以及规范性:</td> $NormativeSTR
                       </tr>
                       <tr>
                           <td  height='30' width='138px' >3、投入生产后的效果:</td> $EffectSTR
                       </tr>
                       <tr>
                           <td  height='30' width='138px' >4、服务情况:</td> $QosSTR
                       </tr>
                        <tr>
                           <td  height='30' width='138px' >5、评定结论:</td> $ResultsSTR
                       </tr>
                  </table>
               </td>
           </tr>
            <tr>
               <td height='30' colspan='4' class='A0011'>不规范记录:</td>
           </tr>
           <tr>
               <td  height='400' colspan='4' class='A0111'>
                    <table  width='800' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin:0 5px 0 15px;text-align:center;'>
                       <tr>
                           <td   height='25' width='100px' class='A1111'>时间</td> 
                           <td   width='220px' class='A1101'>配件名称</td>
                           <td   width='60px' class='A1101'>不良数量</td> 
                           <td   width='200px' class='A1101'>不良原因</td> 
                           <td   width='80px' class='A1101'>处理方法</td> 
                           <td   width='60px' class='A1101'>验收人</td> 
                           <td   width='60px' class='A1101'>采购员</td> 
                       </tr>
                  </table>
                  <div id='ShowDiv'  class='ShowSheet'>
                  <table  width='800' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin:0px 5px 5px 15px;text-align:center;'>
                        $QcbadSTR
                  </table>
                  </div>
               </td>
           </tr>
  <tr>
               <td height='30' colspan='4' class='A0011'>年度评审记录:</td>
           </tr>
           <tr>
               <td  height='150' colspan='4' class='A0111'>
                    <table  width='800' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin:0 5px 0 15px;text-align:center;'>
                       <tr>
                           <td   height='25' width='80px' class='A1111'>年度</td> 
                           <td   width='260px' class='A1101'>结论</td>
                           <td   width='200px'  class='A1101'>主要原因</td> 
                           <td   width='80px' class='A1101'>审核人</td> 
                           <td   width='80px' class='A1101'>批准人</td> 
                           <td   width='80px' class='A1101'>日期</td> 
                       </tr>
                  </table>
                  <div id='ShowDiv'  class='ShowSheet' style='height:140px;'>
                  <table  width='800' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin:0px 5px 5px 15px;text-align:center;'>
                       $ReviewSTR
                   </table>
                  </div>
               </td>
           </tr>
     </table>";
function getGradeValue($ListArray,$gradeId) { 
	$extendSTR=""; $otherSTR="";
   	$boxBlack="<span style='height:22px;width:22px;background:#000;border:1px solid #000;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;";
   	$boxWhite="<span style='height:22px;width:22px;background:#FFF;border:1px solid #000;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;";
   	if ($gradeId>99){
		$checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$gradeId'",$link_id));
      	$otherSTR=$checkGrade["Moregrade"]==""?"":":" . $checkGrade["Moregrade"]; 
   		}
	while (list($keyid,$Value) = each($ListArray)){
		if($Value[0]==$gradeId) {
			$extendSTR.="<td>" . $boxBlack . $Value[1] . "</td>";
           	}
       	else{
			if ($Value[0]==5){
				$extendSTR.="<td>" . $boxWhite . $Value[1] . $otherSTR . "</td>";  
           		}
			else{
				$extendSTR.="<td>" . $boxWhite . $Value[1] . "</td>"; 
				} 
        	}        
   		}
    return $extendSTR; 
	} 
?>
