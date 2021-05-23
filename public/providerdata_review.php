<?php 
/*
$DataPublic.currencydata 二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增供应商评审记录");//需处理
$fromWebPage=$funFrom."_read";	
$nowWebPage =$funFrom."_review";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

 $sheetResult=mysql_query("SELECT P.CompanyId,P.Forshort,S.Quality FROM $DataIn.trade_object P 
         LEFT JOIN $DataIn.providersheet S ON P.CompanyId=S.CompanyId 
         WHERE P.Id=$Id  LIMIT 1",$link_id);
  if ($data_Row = mysql_fetch_array($sheetResult)){
      $CompanyId=$data_Row["CompanyId"];
      $Forshort=$data_Row["Forshort"];
      $QualityFirst=$data_Row["Quality"];
  }
  
 $Year=$Year==""?date('Y')-1:$Year;
 
 $ReviewResult=mysql_query("SELECT * FROM $DataIn.providerreview WHERE CompanyId=$CompanyId AND Year='$Year'",$link_id);
 if($Review_Row = mysql_fetch_array($ReviewResult)){
    
     $Quality=$Review_Row["Quality"];
     if ($Quality>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Quality'",$link_id));
            $QualitySTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"];   
            $Quality=5;
         }
        else{
            $QualitySTR="";      
        }
        
     $Normative=$Review_Row["Normative"];
     if ($Normative>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Normative'",$link_id));
            $NormativeSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"]; 
            $Normative=5;
         }
        else{
            $NormativeSTR="";     
        }
        
     $Effect=$Review_Row["Effect"];
     if ($Effect>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Effect'",$link_id));
            $EffectSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"]; 
            $Effect=5;
         }
        else{
            $EffectSTR="";      
        }
        
     $Qos=$Review_Row["Qos"];
     if ($Qos>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Qos'",$link_id));
            $QosSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"]; 
            $Qos=5;
         }
        else{
            $QosSTR="";      
        }
      
      $Results=$Review_Row["Results"];
      if ($Results>99){
            $checkGrade=mysql_fetch_array(mysql_query("SELECT Id,Moregrade FROM $DataPublic.qualitymoregrade WHERE Id='$Results'",$link_id));
            $ResultsSTR=$checkGrade["Moregrade"]==""?"&nbsp;":$checkGrade["Moregrade"];
            $Results=5;
         }
        else{
            $ResultSTR="";     
        }
      $Reason=$Review_Row["Reason"];
      $Estate=$Review_Row["Estate"];
      switch($Estate){
        case 1: 
            $EstateSTR="已评；未审核"; 
            break;
        case 2:
            $EstateSTR="审核未通过"; 
            break;
        default:
           $EstateSTR="已评；已审核"; 
           $SaveSTR="NO";
       }
     } 
 else{
       $Quality=2;$Normative=2;$Effect=2;$Qos=2;$Results=2;
       $EstateSTR="未评审";
       $Estate=1;
       $Reason="";
 }
 
  $Grade_Result = mysql_query("SELECT Id,Grade FROM $DataPublic.qualitygrade WHERE Estate=1 AND Type=1 order by Id",$link_id);
  if ($Grade_Row = mysql_fetch_array($Grade_Result)){
      do{
          $Grade[]=$Grade_Row;
      }while($Grade_Row = mysql_fetch_array($Grade_Result));
  }

$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId,Id,$Id";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5">
            <tr>
           <td scope='col' align="center">客户名称:<?php  echo $Forshort ?>(<?php  echo $CompanyId ?>)</td>
           <td scope='col' colspan='5'>
         <?php 
            if ($QualityFirst>0){
                 echo "<select name='Year' id='Year' onChange='document.form1.submit();'>" ;
                 $sYear=date("Y");
                 for ($i=0;$i<5;$i++){
                    $sYear=$sYear-1;
                    if ($sYear==$Year){
                           echo "<option value='$sYear' selected>$sYear</option>";
                         }
                       else{
                           echo "<option value='$sYear'>$sYear</option>";
                       }
                }
                echo "</select>年供货评审(<b>$EstateSTR</b>):" ;
               // echo "<select name='Year' id='Year'><option value='$Year'>$Year</option>" ;
               // $Year=$Year-1;
               // echo "<option value='$Year'>$Year</option></select>年供货评审:" ;
            }
            else{
                 echo "<select name='Year' id='Year'><option value='0'>首次</option></select>供货评审:";
            }
           
         ?>
         <input  type='hidden'  name='CompanyId' id='CompanyId' value="<?php  echo $CompanyId ?>"/>
        </td>
       </tr>
       <tr>
            <td scope="col" align="right" width="150">质量:</td>
        <?php     
               $ListArray=$Grade;$otherGrade="";
	       while (list($keyid,$Value) = each($ListArray))
               {
                   $checkFlag=$Value[0]==$Quality?"checked":"";
                   if ($Value[0]==5){
                       if ($Quality==5) $QualitySTR=" value='$QualitySTR' ";else $QualitySTR= "disabled style='display:none;'";
                       $otherGrade=":<input  type='text' width='50px;' name='oQuality' id='oQuality' $QualitySTR max='20' dataType='Require'  Msg='未填写原因'/>";
                   }
                   echo "<td scope='col'><input  type='radio'  name='Quality' id='Quality' onclick='selCheck(this)' value='$Value[0]' $checkFlag/>$Value[1] $otherGrade</td>";
               }
        ?>
       </tr>
       <tr>
            <td scope="col" align="right" width="150">送货时间以及规范性:</td>
        <?php     
               $ListArray=$Grade;$otherGrade="";
	       while (list($keyid,$Value) = each($ListArray))
               {
                   $checkFlag=$Value[0]==$Normative?"checked":"";
                   if ($Value[0]==5){
                       if ($Normative==5) $NormativeSTR=" value='$NormativeSTR' ";else $NormativeSTR= "disabled style='display:none;'";
                       $otherGrade=":<input  type='text' width='50px;' name='oNormative' id='oNormative' $NormativeSTR  max='20' dataType='Require' Msg='未填写原因'/>";
                   }
                       echo "<td scope='col'><input  type='radio'  name='Normative' id='Normative' onclick='selCheck(this)' value='$Value[0]' $checkFlag/>$Value[1] $otherGrade</td>";
               }
        ?>
       </tr>
        <tr>
            <td scope="col" align="right" width="150">投入生产后的效果:</td>
        <?php     
               $ListArray=$Grade;$otherGrade="";
	       while (list($keyid,$Value) = each($ListArray))
               {
                   $checkFlag=$Value[0]==$Effect?"checked":"";
                   if ($Value[0]==5){
                      if ($Effect==5) $EffectSTR=" value='$EffectSTR' ";else $EffectSTR= "disabled style='display:none;'";
                       $otherGrade=":<input  type='text' width='50px;' name='oEffect' id='oEffect' $EffectSTR max='20' dataType='Require' Msg='未填写原因'/>";
                   }
                   echo "<td scope='col'><input  type='radio'  name='Effect' id='Effect' onclick='selCheck(this)' value='$Value[0]' $checkFlag/>$Value[1] $otherGrade</td>";
               }
        ?>
       </tr>
        <tr>
            <td scope="col" align="right" width="150">服务情况:</td>
        <?php     
               $ListArray=$Grade;$otherGrade="";
	       while (list($keyid,$Value) = each($ListArray))
               {
                   $checkFlag=$Value[0]==$Qos?"checked":"";
                   if ($Value[0]==5){
                       if ($Qos==5) $QosSTR=" value='$QosSTR' ";else $QosSTR= "disabled style='display:none;'";
                       $otherGrade=":<input  type='text' width='50px;' name='oQos' id='oQos' $QosSTR max='20' dataType='Require' Msg='未填写原因'/>";
                   }
                   echo "<td scope='col'><input  type='radio'  name='Qos' id='Qos' onclick='selCheck(this)' value='$Value[0]' $checkFlag/>$Value[1] $otherGrade</td>";
               }
        ?>
       </tr>
        <tr>
            <td scope="col" align="right" width="150">评定结论:</td>
        <?php     
               $ListArray=$Grade;$otherGrade="";
	       while (list($keyid,$Value) = each($ListArray))
               {
                   $checkFlag=$Value[0]==$Results?"checked":"";
                   if ($Value[0]==5){
                       if ($Results==5) $ResultsSTR=" value='$ResultsSTR' ";else $ResultsSTR= "disabled style='display:none;'";
                       $otherGrade=":<input  type='text' width='50px;'  name='oResults' id='oResults' $ResultsSTR max='20' dataType='Require' Msg='未填写原因'/>";
                   } 
                   echo "<td scope='col'><input  type='radio'  name='Results' id='Results' onclick='selCheck(this)' value='$Value[0]' $checkFlag/>$Value[1] $otherGrade</td>";
               }
        ?>
       </tr>
       <?php 
       if ($QualityFirst>0){  
            echo "<tr><td scope='col' align='right'>主要原因:</td><td scope='col' colspan='5'><textarea name='Reason'  cols='68' rows='3' id='Reason' dataType='Require' Msg='未填写主要原因'>$Reason</textarea></td></tr>";
            }
       ?>
        </table>
     
  </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
  function selCheck(e){
     var eval=e.value;
     var inputId="o"+e.name;
     var inputList=document.getElementById(inputId);
     if (eval==5){
         inputList.disabled=false;
         inputList.style.display="";
     }else{
         inputList.disabled=true;
         inputList.style.display="none";
     }
  }
</script>