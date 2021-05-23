<?php
//include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
include "../../model/myinfo_c.php";
include "../../admin/phpqrcode/qrcodelib.php";
$CardW=185;
?>
<style type="text/css">
<!--
html,body{
      -webkit-text-size-adjust:none;
      color:#000000;
      background-color:#FFFFFF;
      FONT-FAMILY:'黑体';
}
.style0 {font-size: 22px;font-weight: bold;}
.style8 {font-size: 20px;font-weight: bold;}
.style1 {font-size: 18px;font-weight: bold;}
.style2 {font-size: 13px;font-weight: bold;}
.style3 {font-size: 18px;font-weight: bold;}
.style4 {font-size: 16px;font-weight: bold;margin-left:8px;}
.style5 {font-size: 8px;font-weight: bold;margin-right:4px;FONT-FAMILY:'思源黑体';}
.style6 {font-size: 9px;font-weight: bold;}
.style7 {font-size: 8px;font-weight: bold;line-height:11px;}
.cardRow{
	margin:0;
	padding:0;
	position:relative;
	float:left;
	}
.card1{
     width:160px;
    height:240px;
    position:relative;
    float:left;
}

#bottom{ z-index:1000;width:100%; position:fixed; height:35px; left:0; bottom:0;vertical-align:bottom;}
-->
</style>
<?php
$checkid=explode(",",$Ids);
$rowCount=count($checkid);
for($i=0;$i<$rowCount;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		if ($page==1 || $page==3){
        	$code_level="H";$code_size=2.4;
			$Result = mysql_query("
				SELECT M.Id,M.Number,M.Name,M.Grade,M.KqSign,M.BranchId,M.JobId,M.Mail
				,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,S.Sex,S.Nation,S.Rpr,
				S.Education,S.Married,S.Birthday,S.Photo,S.IdcardPhoto,S.Idcard,S.Address,S.Postalcode,S.Tel,
				S.Mobile,S.Dh,S.Bank,S.Note
				FROM $DataPublic.staffmain M
				LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number WHERE M.Id=$Id LIMIT 1",$link_id);
			if($myrow = mysql_fetch_array($Result)){//输出图片
				$BranchId=$myrow["BranchId"];
				$B_Result = mysql_fetch_array(mysql_query("SELECT B.Name,C.Color,C.Picture 
				FROM $DataPublic.branchdata  B 
                LEFT JOIN $DataIn.branchcolor C ON C.BranchId=B.Id 
                where 1 and B.Id=$BranchId LIMIT 1",$link_id));
				$Branch=$B_Result["Name"];
				$Color=$B_Result["Color"]==""?"#000000":$B_Result["Color"];
				$Picture=$B_Result["Picture"];
                if ($Picture==1){
                	$Picture="../download/branch/card_" . $BranchId . ".jpg";
                    }
                else{
                	$Picture="../../download/branch/bg_1.jpg";
                    }
				$Number=$myrow["Number"];
                $JobId=$myrow["JobId"];
				$MyAddress=$myrow["Address"];
				$ComeIn=$myrow["ComeIn"];
				$ComeArray=explode("-",$ComeIn);
				$ComeTime=$ComeArray[0]."年".$ComeArray[1]."月".$ComeArray[2]."日";

				$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata 
				where 1 and Id=$JobId LIMIT 1",$link_id));
				$Job=$J_Result["Name"];
              	if ($myrow["Photo"]==1) {
		             $photoFile="<img src='../../download/staffphoto/p".$Number.".jpg' width='145' height='186' style='margin-top:5px;margin-left:3px;'>";
                     }
		        else{
                      $photoFile="<img src='../../download/staffphoto/p".$Number.".jpg' width='145' height='186' style='margin-top:5px;margin-left:3px;'>";
                      }
				//生成QR码
				$code_data=$Number;
                include "phpqrcode/createqrcode.php";
				echo" <div style='margin-left:-10px;'>
						<table width='$CardW' height='292' border='0' cellspacing='0' style='margin-left:-2px;'>
							<tr valign='middle'>
                        		<td height='30'  colspan='2' align='center'><div class='style1'>伟信达</div></td>
							</tr>
							<tr>
								<td colspan='2'  valign='top' align='center'><div style='width:145px;height:186px;overflow:hidden;'>$photoFile</div></td>
							</tr>
                        	<tr>
								<td height='30' align='left'>
								<span class='style8'>&nbsp;&nbsp;&nbsp;$myrow[Name]</span></td>
								<td align='right'><span class='style3'>$Job&nbsp;&nbsp;&nbsp;</span></td>
                        	</tr>
							<tr>
								<td valign='top' colspan='2' height='20' align='center'><span class='style2'>入职日期:$ComeTime </span></td>
							</tr>
						</table>
						</div>";
				if($page==3){
                	echo"<div style='PAGE-BREAK-AFTER: always'></div>";
                    }
                else{
                	if ($i<($rowCount-1))
						echo"<div style='PAGE-BREAK-AFTER: always'></div>";
                       }
                    }
				}
            if  ($page==2 || $page==3){
            	$Result = mysql_query("SELECT M.Number,S.Idcard 
					FROM $DataPublic.staffmain M 
					LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number WHERE M.Id=$Id LIMIT 1",$link_id);
				if($myrow = mysql_fetch_array($Result)){//输出图片
					$Number=$myrow["Number"];
                    //生成QR码
                    if ($page==2){
                    	$code_level="H";$code_size=2.4;
                        $code_data=$Number;
                        include "../../admin/phpqrcode/createqrcode.php";
                        }
					echo "<div class='card'>
                    		<table width='$CardW' height='292' border='0' cellspacing='0' style='margin-left:-1px;'>
                            	<tr valign='bottom' height='20'><td  align='left' class='style2' colspan='2'>持卡须知</td></tr>
                                <tr valign='top'>
									<td class='style7' colspan='2'>
										1.此卡仅为本公司员工考勤使用,不得转借</br>
										2.此卡上班时务必随身携带,妥善保存.如果丢<br>失,请告知人事重新办理.</br>
										3.禁止员工代打卡行为,一经发现,均予以开除.</br>
										4.请假需部门主管审批后方能生效,无故缺勤<br>者以旷工论处.</br>
										5.按上海市事业单位职员管理办法第二十条第<br>二项,连续旷工10个工作日或1年内累计旷工<br>超过20个工作日的,视为自动离职.</br>
										6.上班期间请严格遵守安全生产操作规程,做<br>到\"人人讲安全,事事为安全\".</br>
										7.上下班路途请遵守交通安全法,宁等3分种,<br>不抢1秒.</br>
										8.其它事项请参阅公司《规章制度》以及公告<br>的制度.
										</td>
                                  </tr>
                                  <tr>
                                  		<td valign='center' align='center'><img src='$qrcode_File' style='margin-top:-5px;'/></td>
										<td align='left' class='style7'>流塘派出所:84455298<br>西乡社保站:27944465<br>西乡劳动站:27796404<br>伟信达:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>TEL:0755-61139580<br>Fax:0755-61139585<br></td>
                                   </tr>
								   <tr><td class='style6' colspan='2' align='center'>上海市宝安区西乡镇宝民2路伟信达大厦</td></tr>
                            </table> 
                            </div>";
				if ($i<($rowCount-1))
					echo"<div style='PAGE-BREAK-AFTER: always'></div>";
				}
			}
		}
	}
  ?>
