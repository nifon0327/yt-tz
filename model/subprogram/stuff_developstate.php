<?php
//配件开发状态显示
 $DevelopEstate=0;
 if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
if ($DevelopState==1){
	$DevelopResult=mysql_query("SELECT P.Targetdate,P.Finishdate,P.Estate,S.Name,G.GroupName,YEARWEEK(P.Targetdate,1) AS Weeks  FROM $DataIn.stuffdevelop P 
	                                                    LEFT JOIN $DataPublic.staffmain S ON S.Number=P.Number  
	                                                    LEFT JOIN $DataIn.staffgroup G ON G.GroupId=P.GroupId
														WHERE P.StuffId='$StuffId' LIMIT 1",$link_id);
       if($DevelopRow=mysql_fetch_array($DevelopResult)){
                $D_GroupName=$DevelopRow["GroupName"];
                $D_DevilopName=$DevelopRow["Name"];
                $D_Targetdate=$DevelopRow["Targetdate"];
                $D_Finishdate=$DevelopRow["Finishdate"];
                $D_Estate=$DevelopRow["Estate"];
                $D_Weeks=$DevelopRow["Weeks"];
	            $TempWeek=substr($D_Weeks, 4,2);
                $weekName="Week " . $TempWeek;
		        $week_Color=$D_Weeks<=$curWeeks ?"#FF0000":"#000000";
                $DevelopEstate=$D_Estate; //返回开发状态
                if ($D_Estate==1 || $D_Estate==2  || $D_Estate==3 ){ 
                   $StateSTR=$DevelopWeekState==1?$D_Weeks:$D_GroupName;
                   $StateSTR=$D_Weeks==""?"未设置":$StateSTR;
                   $DevelopState="<div onclick='ActionTo(\"develop\",1,\"develop\",\"_self\",0)' Title='责任人:$D_DevilopName;计划完成日期:$D_Targetdate' class='blueB' style='CURSOR: pointer'>$StateSTR</div>";
                  $DevelopStateStr="<div style='color:$week_Color;' title='责任人:$D_DevilopName;计划完成日期:$D_Targetdate'>$weekName</div>";
                }
                else{
	                $DevelopState="<div onclick='ActionTo(\"develop\",1,\"develop\",\"_self\",0)' Title='开发:$D_GroupName-$D_DevilopName;完成日期:$D_Finishdate' class='greenB' style='CURSOR: pointer'>√</div>";
                    $DevelopStateStr="<div style='color:$week_Color;' title='开发:$D_GroupName-$D_DevilopName;完成日期:$D_Finishdate'>$weekName</div>";
                }
         }
         else{
	          $DevelopState="<div onclick='ActionTo(\"develop\",1,\"develop\",\"_self\",0)' class='redB' style='CURSOR: pointer'>未分配</div>";
         }
}
else{
	   $DevelopState="-";
	  
}

$Develop_m="";
if($developFile!=""){  //有开发文档,则需要显示
	$developFile=anmaIn($developFile,$SinkOrder,$motherSTR);
	$tmpd=anmaIn("download/Stuffdevelopfile/",$SinkOrder,$motherSTR);	
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";	
	$developFile="<a href=\"$donwloadFileaddress?d=$tmpd&f=$developFile&Type=&Action=6\" target=\"download\" ><img src='../images/down.gif' style='background:#F00' alt='下载开发文档' width='18' height='18'></a>";
	$Develop_m="<div onclick='ActionTo(\"develop\",1,\"develop\",\"_self\",0)' class='redB' style='CURSOR: pointer'>分配</div>";
}

         
?>