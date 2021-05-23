<?php 

	include "../../basic/parameter.inc";
	include "../../model/modelfunction.php";
	include "../../model/myinfo_c.php";
	include "../../admin/phpqrcode/qrcodelib.php";

	echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<link rel='stylesheet' href='../model/SearchDiv.css'>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";

?>

<style type="text/css">
<!--
html,body{
      -webkit-text-size-adjust:none;
      color:#000000;
      background-color:#FFFFFF;
}

.style0 {font-size: 22px;font-weight: bold;FONT-FAMILY:'黑体';}
.style1 {font-size: 14px;font-weight: bold;FONT-FAMILY:'黑体';}
.style2 {font-size: 13px;font-weight: bold;}
.style3 {font-size: 16px;font-weight: bold;FONT-FAMILY:'黑体';}
.style4 {font-size: 9px;font-weight: bold;margin-left:8px;}
.style5 {font-size: 8px;font-weight: bold;margin-right:4px;}
.style6 {font-size: 8px;font-weight: bold;}
.style7 {font-size: 9px;font-weight: bold;}
.cardRow{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
        margin-bottom:35px;
        width:210px;
        height:650px;
        border:0;
	}
.card{
    margin:10px 10px;
    width:153px;
    height:240px;
    float:left;
    display:inline;
    border:1px #000000 solid;
}

#bottom{ z-index:1000;width:650px; position:fixed; height:30px; left:0; bottom:0;vertical-align:bottom;} 

-->
</style>
<?php 

		//为ipad app添加
		$staffNum = $_GET["staffNum"];
		if($staffNum)
		{
			$idSqlStr = "Select Id from $DataPublic.staffmain Where Number = '$staffNum'";
			$idResult = mysql_query($idSqlStr);
			$idRow = mysql_fetch_assoc($idResult);
			$staffId = $idRow["Id"];
			$checkid = array($staffId);
		}
		
		//

        $checkIdStr="";
        $code_level="H";$code_size=2.4;
	for($i=0;$i<count($checkid);$i++){
			
			$Id=$checkid[$i];
			
            if ($checkIdStr=="") $checkIdStr=$Id; else $checkIdStr.="," . $Id;
			if ($Id!=""){
				$Result = mysql_query("
				SELECT 
				M.Id,M.Number,M.Name,M.Grade,M.KqSign,M.BranchId,M.JobId,M.Mail,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
				S.Sex,S.Nation,S.Rpr,S.Education,S.Married,S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,
				S.Mobile,S.Dh,S.Bank,S.Note
				FROM $DataPublic.staffmain M
				LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number WHERE M.Id=$Id LIMIT 1",$link_id);
				if($myrow = mysql_fetch_array($Result)){//输出图片
				  	
				$BranchId=$myrow["BranchId"];				
				$B_Result = mysql_fetch_array(mysql_query("SELECT B.Name,C.Color,C.Picture FROM $DataPublic.branchdata  B 
                                             LEFT JOIN $DataIn.branchcolor C ON C.BranchId=B.Id 
                                             where 1 and B.Id=$BranchId LIMIT 1",$link_id));
				$Branch=$B_Result["Name"];
				$Color=$B_Result["Color"]==""?"#000000":$B_Result["Color"];
                                $Picture=$B_Result["Picture"];
                                if ($Picture==1){
                                    $Picture="../../download/branch/card_" . $BranchId . ".jpg";
                                    }
                                 else{
                                    $Picture="../../download/branch/bg_1.jpg";
                                }
				$Number=$myrow["Number"];
                                
                                $JobId=$myrow["JobId"];
                                
				$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
				$Job=$J_Result["Name"];
                if ($myrow["Photo"]==1) {
		             $photoFile="<img src='../../download/staffphoto/p".$Number.".jpg' width='125' height='162' style='margin-top:5px;margin-left:3px;'>";
                     }
		        else{
                      $photoFile="<img src='../../download/staffphoto/p".$Number.".jpg' width='125' height='162' style='margin-top:5px;margin-left:3px;'>";
                      }
                                  //生成QR码
                                  $code_data=$Number;
                                  include "../../admin/phpqrcode/createqrcode.php";
                                     echo " <div class='cardRow'>";
                                    
					echo " <div class='card'>
					<table width='153' height='240' border='0' cellspacing='0'>
						<tr valign='middle'>
                        	<td height='30'  colspan='2' align='center'><div  class='style1'>$myCompany</div></td>
						</tr>
						<tr>
							<td colspan='2'  valign='top'>$photoFile</td>
						</tr>
                        <tr>
							<td height='30'><span class='style3'>$myrow[Name]</span></td><td><span class='style4'>$Job</span></td>
                        </tr>
					</table>";
					echo "</div>";
                                 
                                       echo " <div class='card'>";
                                          echo " <table width='153' height='240' border='0' cellspacing='0' style='margin-left:5px;'>
                                               <tr valign='middle' height='25'><td  align='center' class='style2'>持卡须知</td></tr>
                                               <tr valign='bottom'><td class='style7'>1、此卡为本公司员工考勤使用，不得转借</br>2、此卡上班时务必随身携带，妥善保存。如果丢失，请告知人事，重新办理。</br>3、禁止员工代打卡行为，一经发现，均予以开除。</br>4、禁止利用此卡在外败坏公司名誉，否则视情节轻重予以严惩。</br>5、陈经理手机+8613602665980(申诉,爆料,急难求助,限短信,请附加姓名,否则不做处理)。</br>6、请遵守法律法规，遵守公司规章制度。</br>7、上班期间需要严格遵守作业流程，注意生产安全，防止意外事件发生。</br>8、其它事项请参阅公司《规章制度》及公告的制度</td>
                                                  </tr>
                                                  <tr>
                                                   <td valign='bottom' align='right'><img src='$qrcode_File' style='margin-top:-5px;'/></td>
                                                  </tr>
                                              </table> 
                                         ";
                                        echo "</div></div>";
                                    
					}
				}
			}
                        
  ?>

<div  id="bottom">
    &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value="正面打印" onClick="window.open('staff_cardprintpage.php?page=1&Ids=<?php  echo $checkIdStr?>', '_blank');"/> 
    &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value="背面打印" onClick="window.open('staff_cardprintpage.php?page=2&Ids=<?php  echo $checkIdStr?>', '_blank');"/>
    &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value="双面打印" onClick="window.open('staff_cardprintpage.php?page=3&Ids=<?php  echo $checkIdStr?>', '_blank');"/>
</div>
</body>
</html>