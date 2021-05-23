<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
    <?php
    include "../model/characterset.php";
    include "../basic/chksession.php";
    include "../basic/parameter.inc";
    include "../model/modelfunction.php";
    echo "<link rel='stylesheet' href='../model/css/sharing.css'>";
    echo "<link rel='stylesheet' href='supplier_left.css'>";

    //获取第几周的开始、结束时间
    /*
    function GetWeekDate($Weeks,$dateFormat)
     {
           $year=substr($Weeks, 0,4);
           $week=substr($Weeks, 4,2);
           $timestamp = mktime(0,0,0,1,1,$year);
           $dayofweek = date("w",$timestamp);
           if( $week != 1){
                    $distance = ($week-1)*7-$dayofweek+1;
           }
          else{
                  $distance=$dayofweek-7;
          }
          $passed_seconds = $distance * 86400;
          $timestamp += $passed_seconds;
          $firt_date_of_week = date("$dateFormat",$timestamp);
          $distance =6;
          $timestamp += $distance * 86400;
          $last_date_of_week = date("$dateFormat",$timestamp);

          return array($firt_date_of_week,$last_date_of_week);
     }
     */
    ?>
</head>
<script type="text/javascript">
function liClick(e) {
    var lists = document.getElementsByName("mainLi");
    for (var i = 0; i < lists.length; i++) {
        lists[i].style.backgroundColor = "#EEEEEE";
    }

    lists = document.getElementsByName("subLi");
    for (var i = 0; i < lists.length; i++) {
        lists[i].style.backgroundColor = "#DDDDDD";
    }

    var weekLi = document.getElementById("weekLi");
    if (weekLi != undefined) weekLi.style.backgroundColor = "#CCFF99";

    e.style.backgroundColor = "#FFFFFF";
}
</script>
<body>
<div class="demo-list">
  <!--<h2>功能菜单</h2>-->
  <ul>
    <li name='mainLi' id='mainLi' style='background:#fff;' onclick='liClick(this)'>
      <a href="supplier_start.php" target="mainFrame">首页</a></li>
      <?php
      // $Login_Id=56;

      $rMenuResult = mysql_query("
			SELECT P.ModuleId,P.IsPrice,F.ModuleName,F.AutoName
			FROM $DataIn.sys4_gysfunpower P
			LEFT JOIN $DataIn.sys4_gysfunmodule F ON F.ModuleId=P.ModuleId 
			LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
			WHERE 1 AND P.UserId='$Login_Id' AND F.Estate=1 ORDER BY F.ModuleId
			", $link_id);
      /*
            $rMenuResult = mysql_query("
            SELECT F.ModuleId,F.ModuleName,F.AutoName
            FROM $DataIn.sys4_gysfunmodule F
            WHERE 1 AND F.Estate=1 ORDER BY F.ModuleId
            ",$link_id);//AND F.ModuleId>'100010'
            */
      if ($rMenuRow = mysql_fetch_array($rMenuResult)) {
          $i = 1;
          $S_IsPrice = 0;
          //session_register("S_IsPrice");
          $_SESSION["S_IsPrice"] = $S_IsPrice;
          do {
              $AutoName = $rMenuRow["AutoName"];
              $ModuleId = $rMenuRow["ModuleId"];//加密
              $Mid = anmaIn($ModuleId, $SinkOrder, $motherSTR);
              $IsPrice = $rMenuRow["IsPrice"];
              //$S_IsPrice=1;
              $IsPrice = anmaIn($IsPrice, $SinkOrder, $motherSTR);
              $ModuleName = $rMenuRow["ModuleName"];
              if ($AutoName != 0) {
                  include "../model/subprogram/mycompany_info.php";
                  if ($AutoName == 1) {
                      $ModuleName = $S_Forshort . $ModuleName;
                  }
                  else {
                      $ModuleName = $ModuleName . $S_Forshort;
                  }
              }

              $subTitle = "";
              $subDateTitle = "";
              switch ($ModuleId) {
                  case "100011"://未出
                      $cgQtySql = mysql_fetch_array(mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 and S.Mid>0 AND  S.CompanyId='$myCompanyId' ", $link_id));

                      $rkTemp = mysql_fetch_array(mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
			               LEFT JOIN $DataIn.cg1_stocksheet S ON  S.StockId=R.StockId 
		                   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		                   LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	                       WHERE S.CompanyId='$myCompanyId' AND S.Mid>0", $link_id));
                      //已送未入库
                      $shSql = mysql_fetch_array(mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
									   LEFT JOIN $DataIn.gys_shmain S ON S.Id=G.Mid
									   WHERE 1 AND G.SendSign=0 AND G.Estate>0 AND S.CompanyId='$myCompanyId'", $link_id));
                      $nochQty = $cgQtySql["Qty"] - $rkTemp["Qty"] - $shSql["Qty"];
                      $nochQty = $nochQty < 0 ? 0 : $nochQty;
                      $subTitle = number_format($nochQty) . " pcs";
                      break;

                  case "100013"://退货审核
                      $thSql = mysql_fetch_array(mysql_query("SELECT count(*) AS nums FROM $DataIn.ck2_thmain M 
				           LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid=M.Id
				            WHERE  M.CompanyId='$myCompanyId' AND S.Id>0 AND NOT EXISTS (SELECT R.Mid FROM $DataIn.ck2_threview R WHERE  R.Mid=S.Id )", $link_id));
                      $subTitle = $thSql["nums"] == 0 ? "" : $thSql["nums"];
                      break;
                  case "100014"://未结付货款
                      $FKSql = mysql_fetch_array(mysql_query("SELECT SUM(ROUND((S.AddQty+S.FactualQty)*S.Price,2)) as Amount  FROM $DataIn.cw1_fkoutsheet S WHERE  S.CompanyId='$myCompanyId' AND S.Estate =3 AND S.Month!=''", $link_id));
                      $FK_Amount = $FKSql["Amount"];
                      $subTitle = "¥ " . number_format($FK_Amount, 2);
                      //最后一次付款
                      $PayResult = mysql_query("SELECT  PayDate,PayAmount  FROM $DataIn.cw1_fkoutmain WHERE  CompanyId='$myCompanyId' AND TIMESTAMPDIFF(DAY,PayDate,NOW())<=7 ", $link_id);
                      if ($PayRow = mysql_fetch_array($PayResult)) {
                          $subTitle = "$subTitle<br><span style='float:right;color:#3D733D;'><font style='font-size:12px;font-weight: normal;'>" . $PayRow["PayAmount"] . "</font><img src='images/icon_new.gif'/></span>";
                          $subDateTitle = "<br><font style='font-size:12px;font-weight: normal;'>" . $PayRow["PayDate"] . "</font>";
                      }
                      break;

                  case "100017"://客供未出
                      $SumCgQty = 0;
                      $SumRkQty = 0;
                      $SumShQty = 0;
                      $myResult = mysql_query("SELECT S.StuffId,SUM(S.FactualQty+S.AddQty) AS Qty
                       FROM $DataIn.cg1_stocksheet S
                       LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                       LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=A.StuffId AND OP.Property=2
                       WHERE 1  and S.Mid=0  AND S.CompanyId='$myCompanyId'  AND IFNULL(OP.Property,0)=2     GROUP BY S.StuffId ", $link_id);
                      while ($myRow = mysql_fetch_array($myResult)) {
                          $StuffId = $myRow["StuffId"];
                          $cgQty = $myRow["Qty"];
                          $SumCgQty += $cgQty;

                          $rkTemp = mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
                                    LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
                                    WHERE  1   and S.Mid=0  AND  R.StuffId='$StuffId'  AND S.CompanyId='$myCompanyId'   ", $link_id);
                          $rkQty = mysql_result($rkTemp, 0, "Qty");
                          $rkQty = $rkQty == "" ? 0 : $rkQty;
                          $SumRkQty += $rkQty;

                          $shSql = mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
									   LEFT JOIN $DataIn.gys_shmain M ON M.Id=G.Mid
									   LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId 
									   WHERE 1 AND G.SendSign=0 AND G.Estate>0 and S.Mid=0  AND G.StuffId=$StuffId AND  M.CompanyId='$myCompanyId'   ", $link_id);
                          $shQty = mysql_result($shSql, 0, "Qty");
                          $shQty = $shQty == "" ? 0 : $shQty;
                          $SumShQty += $shQty;
                      }
                      $nochQty = $SumCgQty - $SumRkQty - $SumShQty;
                      $subTitle = number_format($nochQty) . " pcs";
                      break;

              }
              $subTitle = $subTitle == "" ? "" : " <span style='color:#f00;float:right;margin-right:10px;'>" . $subTitle . "</span>";
              echo "<li  name='mainLi'  id='mainLi' onclick='liClick(this)'><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice'  target='mainFrame'> $subTitle $ModuleName $subDateTitle</a>";

              //按交期显示未送采购单
              if ($ModuleId == 100011) {
                  $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek", $link_id));
                  $curWeeks = $dateResult["CurWeek"];

                  $cgWeekResult = mysql_query("SELECT A.Weeks,SUM(A.Qty-A.rkQty) AS Qty,
					    SUM(IF(A.Type=7,A.Qty,0)) AS kQty,SUM(IF(A.Type=7,1,0)) AS kCount,Count(*) AS cgCount,
					    SUM(IF(A.Mid>0,0,A.Qty)) AS newQty,SUM(IF(A.Mid>0,0,1)) AS newCount,
					    SUM(IF(ReduceWeeks=0,A.Qty,0)) AS dQty,SUM(IF(ReduceWeeks=0,1,0)) AS dCount 
					     FROM (
									SELECT  YEARWEEK(S.DeliveryDate,1) AS Weeks,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty,E.Type,R.Mid,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks 
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									        WHERE  S.Mid>0 AND  S.CompanyId='$myCompanyId' GROUP BY S.StockId
									    UNION ALL 
									          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty 
									          FROM $DataIn.gys_shsheet S 
									          LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
									          WHERE  S.SendSign=0 AND S.Estate>0  and M.CompanyId='$myCompanyId' GROUP BY S.StockId
									   )B 
									   LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=B.StockId  
									   LEFT JOIN $DataIn.cg2_orderexpress E ON E.StockId=B.StockId AND E.Type=7 
									   LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
                                       LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=S.POrderId AND W.ReduceWeeks=0
									   GROUP BY B.StockId  
									)A WHERE A.Qty>A.rkQty group by A.Weeks ", $link_id);
                  if ($cgWeekRow = mysql_fetch_array($cgWeekResult)) {
                      echo "<li> <div class=''><ul>";
                      do {
                          //$curWeeks=date("Y") . date('W');
                          $Weeks = $cgWeekRow["Weeks"];

                          if ($Weeks > 0) {
                              $WeekSTR = "<span style='margin-left:10px;'>" . substr($Weeks, 4, 2) . "周<span>";

                              $dateArray = GetWeekToDate($Weeks, "m/d");
                              $dateSTR = $dateArray[0] . "-" . $dateArray[1];
                          }
                          else {
                              $WeekSTR = "交期待定";
                              $Weeks = "NODATE";
                              $dateSTR = "";
                          }

                          $WeekCount = $cgWeekRow["cgCount"];
                          $WeekQty = "<span style='float:right;'>" . number_format($cgWeekRow["Qty"]) . "<span style='color:#000;'>($WeekCount)</span></span>";

                          $rows = 0;

                          $newQty = $cgWeekRow["newQty"];
                          $newCount = $cgWeekRow["newCount"];
                          if ($newQty > 0) {
                              $WeekQty = "<span style='float:right;color:#3D733D'>$newQty<span style='color:#000;'>($newCount)</span><img src='images/icon_new.gif'/></span><br> $WeekQty";
                              $rows++;
                          }

                          $Week_kQty = $cgWeekRow["kQty"];
                          $Week_kCount = $cgWeekRow["kCount"];

                          $Week_dQty = $cgWeekRow["dQty"];
                          $Week_dCount = $cgWeekRow["dCount"];


                          if ($Week_kQty > 0 || $Week_dQty > 0) {
                              $WeekQty .= "<br>";
                              if ($Week_kQty > 0) $WeekQty .= "<span style='float:right;'>" . number_format($Week_kQty) . "<span style='color:#000;'>($Week_kCount)</span><img src='images/icon_hurry.gif'/></span>";
                              if ($Week_dQty > 0) $WeekQty .= "<span style='float:right;color:#308CC0;'>" . number_format($Week_dQty) . "<span style='color:#000;'>($Week_dCount)</span><img src='images/icon_week.gif' style='width:20px;height:20px;margin-top:-3px;'/></span>";
                              $rows++;
                          }

                          $subTitle = $Weeks >= $curWeeks ? "<span style='float:right;margin-right:10px;'>$WeekQty</span>" : " <span style='color:#f00;float:right;margin-right:10px;'>" . $WeekQty . "</span>";
                          $dateSTR = "<span style='font-size:10px;color:#888888;'>$dateSTR</span>";

                          $liHeight = $rows > 1 ? "style='height:50px;'" : "style='height:35px;'";
                          if ($curWeeks == $Weeks) {
                              echo "<li id='weekLi' onclick='liClick(this)' $liHeight ><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice&IsWeeks=$Weeks'  target='mainFrame'> $subTitle $WeekSTR<br>$dateSTR</a></li>";
                          }
                          else {
                              echo "<li name='subLi' id='subLi'  onclick='liClick(this)' $liHeight><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice&IsWeeks=$Weeks'  target='mainFrame'>$subTitle$WeekSTR<br>$dateSTR</a></li>";
                          }
                      } while ($cgWeekRow = mysql_fetch_array($cgWeekResult));
                      echo "</ul></div></li>";
                  }
              }

              //按交期显示未送采购单
              if ($ModuleId == 100017) {
                  $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek", $link_id));
                  $curWeeks = $dateResult["CurWeek"];

                  $cgWeekResult = mysql_query("SELECT A.Weeks,SUM(A.Qty-A.rkQty) AS Qty,
					    SUM(IF(A.Type=7,A.Qty,0)) AS kQty,SUM(IF(A.Type=7,1,0)) AS kCount,Count(*) AS cgCount,
					    SUM(IF(A.Mid>0,0,A.Qty)) AS newQty,SUM(IF(A.Mid>0,0,1)) AS newCount,
					    SUM(IF(ReduceWeeks=0,A.Qty,0)) AS dQty,SUM(IF(ReduceWeeks=0,1,0)) AS dCount 
					     FROM (
									SELECT  YEARWEEK(S.DeliveryDate,1) AS Weeks,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty,E.Type,R.Mid,IFNULL(W.ReduceWeeks,1) AS ReduceWeeks 
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet  S 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
                                             LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
									        WHERE   S.Mid=0 AND  S.CompanyId='$myCompanyId' AND IFNULL(OP.Property,0)=2  GROUP BY S.StockId
									    UNION ALL 
									          SELECT  S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty 
									          FROM $DataIn.gys_shsheet S 
									          LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
									          WHERE  S.SendSign=0 AND S.Estate>0  and M.CompanyId='$myCompanyId' GROUP BY S.StockId
									   )B 
									   LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=B.StockId  
									   LEFT JOIN $DataIn.cg2_orderexpress E ON E.StockId=B.StockId AND E.Type=7 
									   LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
                                       LEFT JOIN $DataIn.yw2_cgdeliverydate W ON W.POrderId=S.POrderId AND W.ReduceWeeks=0
									   GROUP BY B.StockId  
									)A WHERE A.Qty>A.rkQty group by A.Weeks ", $link_id);
                  if ($cgWeekRow = mysql_fetch_array($cgWeekResult)) {
                      echo "<li> <div class='sub-list'><ul>";
                      do {
                          //$curWeeks=date("Y") . date('W');
                          $Weeks = $cgWeekRow["Weeks"];

                          if ($Weeks > 0) {
                              $WeekSTR = "<span style='margin-left:10px;'>" . substr($Weeks, 4, 2) . "周<span>";

                              $dateArray = GetWeekDate($Weeks, "m/d");
                              $dateSTR = $dateArray[0] . "-" . $dateArray[1];
                          }
                          else {
                              $WeekSTR = "交期待定";
                              $Weeks = "NODATE";
                              $dateSTR = "";
                          }

                          $WeekCount = $cgWeekRow["cgCount"];
                          $WeekQty = "<span style='float:right;'>" . number_format($cgWeekRow["Qty"]) . "<span style='color:#000;'>($WeekCount)</span></span>";

                          $rows = 0;

                          $newQty = $cgWeekRow["newQty"];
                          $newCount = $cgWeekRow["newCount"];
                          if ($newQty > 0) {
                              $WeekQty = "<span style='float:right;color:#3D733D'>" . number_format($newQty) . "<span style='color:#000;'>($newCount)</span><img src='images/icon_new.gif'/></span><br> $WeekQty";
                              $rows++;
                          }

                          $Week_kQty = $cgWeekRow["kQty"];
                          $Week_kCount = $cgWeekRow["kCount"];

                          $Week_dQty = $cgWeekRow["dQty"];
                          $Week_dCount = $cgWeekRow["dCount"];


                          if ($Week_kQty > 0 || $Week_dQty > 0) {
                              $WeekQty .= "<br>";
                              if ($Week_kQty > 0) $WeekQty .= "<span style='float:right;'>" . number_format($Week_kQty) . "<span style='color:#000;'>($Week_kCount)</span><img src='images/icon_hurry.gif'/></span>";
                              if ($Week_dQty > 0) $WeekQty .= "<span style='float:right;color:#308CC0;'>" . number_format($Week_dQty) . "<span style='color:#000;'>($Week_dCount)</span><img src='images/icon_week.gif' style='width:20px;height:20px;margin-top:-3px;'/></span>";
                              $rows++;
                          }

                          $subTitle = $Weeks >= $curWeeks ? "<span style='float:right;margin-right:10px;'>$WeekQty</span>" : " <span style='color:#f00;float:right;margin-right:10px;'>" . $WeekQty . "</span>";
                          $dateSTR = "<span style='font-size:10px;color:#888888;'>$dateSTR</span>";

                          $liHeight = $rows > 1 ? "style='height:50px;'" : "style='height:35px;'";
                          if ($curWeeks == $Weeks) {
                              echo "<li id='weekLi' onclick='liClick(this)' $liHeight ><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice&IsWeeks=$Weeks'  target='mainFrame'> $subTitle $WeekSTR<br>$dateSTR</a></li>";
                          }
                          else {
                              echo "<li name='subLi' id='subLi'  onclick='liClick(this)' $liHeight><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice&IsWeeks=$Weeks'  target='mainFrame'>$subTitle$WeekSTR<br>$dateSTR</a></li>";
                          }
                      } while ($cgWeekRow = mysql_fetch_array($cgWeekResult));
                      echo "</ul></div></li>";
                  }
              }

              $i++;
          } while ($rMenuRow = mysql_fetch_array($rMenuResult));

          $eResult = mysql_fetch_array(mysql_query("SELECT count(*) AS nums FROM (SELECT E.Id FROM $DataIn.errorcasedata E
                        LEFT JOIN $DataIn.casetostuff C ON C.cId=E.Id
						LEFT JOIN $DataIn.stuffprovider S ON S.StuffId=C.StuffId
						LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=S.CompanyId
                        WHERE E.Estate=1 AND E.Type=2  AND L.Id=$Login_P_Number 
						GROUP BY E.Id ) A", $link_id));
          $eCaseNums = $eResult["nums"] == 0 ? "" : "<span style='color:#f00;float:right;margin-right:10px;'>" . $eResult["nums"] . "</span>";

          echo "<li onclick='liClick(this)'><a href='supplier_errorcase_read.php'  target='mainFrame'>$eCaseNums 检讨报告</a></li>";
      }
      else {
          echo "<li ><a href='about:'  target='mainFrame'><span style='color:#f00;'>未设可访问项目</span></a></li>";
      }
      ?>
  </ul>
</div>

<div class="info">
  <b>研砼公司联系信息:</b>
    <?php
    /*
      $pResult =  mysql_fetch_array(mysql_query("SELECT M.Name,M.ExtNo,M.Mail,S.Mobile
        FROM  $DataIn.linkmandata L
        LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=L.CompanyId
        LEFT JOIN $DataPublic.staffmain M ON P.Operator=M.Number
        LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
         WHERE L.Id='$Login_P_Number' ORDER BY L.Id LIMIT 1",$link_id));
         */
    $pResult = mysql_fetch_array(mysql_query("SELECT M.Estate,M.Name,M.ExtNo,M.Mail,S.Mobile 
                 FROM $DataIn.cg1_stockmain G
                 LEFT JOIN $DataPublic.staffmain M ON G.BuyerId=M.Number  
                 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number  
                  WHERE G.CompanyId='$myCompanyId' ORDER BY G.Date DESC LIMIT 1", $link_id));
    if ($pResult["Estate"] == 0) {
        $pResult = mysql_fetch_array(mysql_query("SELECT M.Name,M.ExtNo,M.Mail,S.Mobile 
				                 FROM  $DataIn.linkmandata L 
				                 LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=L.CompanyId  
				                 LEFT JOIN $DataPublic.staffmain M ON P.Staff_Number=M.Number  
				                 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number  
				                 WHERE L.Id='$Login_P_Number' ORDER BY L.Id LIMIT 1", $link_id));
    }

    $Name = $pResult["Name"];
    $ExtNo = $pResult["ExtNo"];
    $Mail = $pResult["Mail"];
    $Mobile = $pResult["Mobile"];
    echo "<br><span>采&nbsp;&nbsp;购:   $Name </span><br>";
    echo "<span>电&nbsp;&nbsp;话:  0755-61139580-$ExtNo</span><br>";
    echo "<span>手&nbsp;&nbsp;机:  $Mobile</span><br>";
    echo "<span>邮&nbsp;&nbsp;箱:  $Mail</span><br>";
    ?>
  <b>附:<a href='mc_hzdoc_read.php' target='mainFrame'>公司资料</a></b>
</div>
</body>
</html>
