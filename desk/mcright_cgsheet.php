<head>
  <link rel='stylesheet' href='../model/css/ac_ln.css'>
  <style>
    a {

    }

    ul {
      list-style: none;
      padding: 0;
    }
  </style>
</head>
<?php
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek", $link_id));
$curWeeks = $dateResult["CurWeek"];
$PassStr = "";
$CheckCgResult = mysql_query(" SELECT  * FROM (
SELECT Number,Name FROM $DataPublic.staffmain  WHERE  BranchId=4 AND Estate=1 AND offStaffSign=0 AND Number NOT IN (10007,10008)
UNION ALL 
SELECT  '0' AS Number,'客供' AS Name  
) A  WHERE 1 ORDER BY Number DESC", $link_id);
while ($CheckCgRow = mysql_fetch_array($CheckCgResult)) {
    if ($Count1 > 1) {
        $Count1++;
        $Style = "style='CURSOR: pointer'";
        $Mouses = "onmouseover='this.className=\"menu_title2\";' onmouseout='this.className=\"menu_title\";' onclick=menuSign($i,this,$Count1);";
        if ($i == 1) {
            $backimg = '';
            $SignStr = "";
        }
        else {
            $backimg = '';
            $SignStr = "style='DISPLAY: none;'";
        }
    }
    else {
        $backimg = '';
        $Mouses = "";
    }
    $cgNumber = $CheckCgRow["Number"];
    $cgName = $CheckCgRow["Name"];
    if ($cgNumber > 0) {
        $cgWeekResult = mysql_query("SELECT YEARWEEK(A.DeliveryDate,1) AS Weeks,SUM(A.Qty-A.rkQty) AS Qty,Count(*) AS cgCount
					     FROM (
									SELECT B.DeliveryDate,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.DeliveryDate,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 AND  S.BuyerId='$cgNumber'  AND M.CompanyId NOT IN (getSysConfig(106))  GROUP BY S.StockId
									   )B  GROUP BY B.StockId  
									)A WHERE A.Qty>A.rkQty group by YEARWEEK(A.DeliveryDate,1)", $link_id);

        echo "<TR $Style $Mouses><td id='Menu$i' class='menu_title' style='height:30px;line-height: 30px;'><span><i class='iconfont icon-jiantouyou'></i>&nbsp;&nbsp;$cgName</span></td></TR>";
        echo "<TR $SignStr id=subMenu$i ><TD>";
        if ($Login_P_Number == "old") {
            echo "<TABLE style='POSITION: relative;' cellSpacing=0 cellPadding=0 width=130 align=center ><TBODY>";
            if ($cgWeekRow = mysql_fetch_array($cgWeekResult)) {
                do {
                    $trColor = "";
                    $Weeks = $cgWeekRow["Weeks"];
                    if ($Weeks == $curWeeks) $trColor = "bgcolor=#CCFF99";
                    $WeekCount = $cgWeekRow["cgCount"];
                    if ($curWeeks > $Weeks) {
                        $WeekQty = "<span style='color:#f00;'>" . number_format($cgWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    else {
                        $WeekQty = "<span style='color:#fff;'>" . number_format($cgWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    //$WeeksTRS="Week ".substr($Weeks, 4,2);
                    $WeeksTRS = $Weeks > 0 ? "Week " . substr($Weeks, 4, 2) : "交期待定";
                    $SubModuleIdTemp = 0;
                    $PassStr = "&BuyerId=$cgNumber&Weeks=$Weeks";
                    $SubModuleId = anmaIn($SubModuleIdTemp, $SinkOrder, $motherSTR);//加密
                    echo "<TR $trColor><TD height=20><A onfocus=this.blur(); href='mainFrame.php?Id=$SubModuleId$PassStr' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'><div style='padding-top:5px;'><div style='text-indent:32px;float:left;width:50px; height:20px;'>$WeeksTRS</div><div align='right' style='float:left;width:80px; height:20px;'>$WeekQty</div></div></A></TD></TR>";
                } while ($cgWeekRow = mysql_fetch_array($cgWeekResult));
            }
            echo "</TBODY></TABLE>";
        }
        else {
            echo "<div><ul>";
            if ($cgWeekRow = mysql_fetch_array($cgWeekResult)) {
                do {
                    $trColor = "";
                    $Weeks = $cgWeekRow["Weeks"];
                    if ($Weeks == $curWeeks) $trColor = "bgcolor=#CCFF99";
                    $WeekCount = $cgWeekRow["cgCount"];
                    if ($curWeeks > $Weeks) {
                        $WeekQty = "<span style='color:#f00;'>" . number_format($cgWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    else {
                        $WeekQty = "<span style='color:#fff;'>" . number_format($cgWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    $WeeksTRS = $Weeks > 0 ? "Week " . substr($Weeks, 4, 2) : "交期待定";
                    $WeekSTR = "<span style='margin-top:4px;float:left'> " . $WeeksTRS . "</span>";

                    if ($Weeks > 0) {
                        $dateArray = GetWeekToDate($Weeks, "m/d");
                        $dateSTR = $dateArray[0] . "-" . $dateArray[1];
                        $dateSTR = "<span style='float: left;font-size:10px;color:#888888;margin-left:1px;'>$dateSTR</span>";
                        $WeekSTR .= "<br>" . $dateSTR;
                    }

                    $subTitle = $Weeks >= $curWeeks ? "<span style='float:right;margin-right:5px;margin-top:8px;'>$WeekQty</span>" : " <span style='color:#f00;float:right;margin-right:5px;margin-top:8px;'>" . $WeekQty . "</span>";

                    $SubModuleIdTemp = 0;
                    $PassStr = "&BuyerId=$cgNumber&Weeks=$Weeks";
                    $SubModuleId = anmaIn($SubModuleIdTemp, $SinkOrder, $motherSTR);//加密

                    $liHeight = "style='height:30px;'";
                    if ($curWeeks == $Weeks) {
                        echo "<li id='weekLi' name='weekLi' style='height:40px' ><a href='mainFrame.php?Id=$SubModuleId$PassStr'  target='mainFrame' class='link-font-style' style='width:100%;color: #ffffff !important;display:inline-block;text-indent:32px'>  $subTitle $WeekSTR</a></li>";
                    }
                    else {
                        echo "<li name='subLi' id='subLi' $liHeight><a href='mainFrame.php?Id=$SubModuleId$PassStr'  target='mainFrame' class='link-font-style' style='width:100%;color: #ffffff !important;display:inline-block;text-indent:32px'>$subTitle  $WeekSTR</a></li>";
                    }
                } while ($cgWeekRow = mysql_fetch_array($cgWeekResult));
                echo "</ul></div>";
            }
        }
        echo "</TD></TR>";
    }
    else {//客供类配件
        /*echo "SELECT YEARWEEK(A.DeliveryDate,1) AS Weeks,SUM(A.Qty-A.rkQty) AS Qty,Count(*) AS cgCount
                       FROM (
                                  SELECT B.DeliveryDate,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
                                     FROM (
                                      SELECT S.DeliveryDate,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty
                                            FROM $DataIn.cg1_stocksheet S
                                            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                                            LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
                                            LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
                                           WHERE  S.Mid=0  AND S.DeliveryDate!='0000-00-00'   AND IFNULL(OP.Property,0)=2  GROUP BY S.StockId
                                          UNION ALL
                                            SELECT  G.DeliveryDate,S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty
                                            FROM $DataIn.gys_shsheet S
                                            LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                                            LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
                                            LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
                                            WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0   AND G.Mid=0  AND IFNULL(OP.Property,0)=2 GROUP BY S.StockId
                                     )B  GROUP BY B.StockId
                                  )A WHERE A.Qty>A.rkQty group by YEARWEEK(A.DeliveryDate,1)";*/
        echo "<TR $Style $Mouses><td id='Menu$i' class='menu_title' style='height:30px;line-height: 30px;'><span><i class='iconfont icon-jiantouyou'></i>&nbsp;&nbsp;$cgName</span></td></TR>";
        echo "<TR $SignStr id=subMenu$i ><TD bgcolor=''>";
        /*
              $clientWeekResult= mysql_query("SELECT YEARWEEK(A.DeliveryDate,1) AS Weeks,SUM(A.Qty-A.rkQty) AS Qty,Count(*) AS cgCount
                           FROM (
                                      SELECT B.DeliveryDate,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
                                         FROM (
                                          SELECT S.DeliveryDate,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty
                                                FROM $DataIn.cg1_stocksheet S
                                                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                                                LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
                                                LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
                                               WHERE  S.Mid=0    AND OP.Property=2  GROUP BY S.StockId
                                              UNION ALL
                                                SELECT  G.DeliveryDate,S.StockId,0 AS Qty,0 AS rkQty,SUM(S.Qty) AS SendQty
                                                FROM $DataIn.gys_shsheet S
                                                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                                                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
                                                LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
                                                WHERE  S.SendSign=0 AND S.Estate>0 AND  S.StockId>0   AND G.Mid=0  AND OP.Property=2 GROUP BY S.StockId
                                         )B  GROUP BY B.StockId
                                      )A WHERE A.Qty>A.rkQty group by YEARWEEK(A.DeliveryDate,1)",$link_id);
          */
        $clientWeekResult = mysql_query("SELECT YEARWEEK(A.DeliveryDate,1) AS Weeks,SUM(A.Qty-A.rkQty) AS Qty,Count(*) AS cgCount
					     FROM (
									SELECT B.DeliveryDate,B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.DeliveryDate,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
									          LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=D.StuffId AND OP.Property=2
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid=0    AND OP.Property=2  GROUP BY S.StockId
									   )B  GROUP BY B.StockId  
									)A WHERE A.Qty>A.rkQty group by YEARWEEK(A.DeliveryDate,1)", $link_id);
        if ($Login_P_Number == "old") {
            echo "<TABLE style='POSITION: relative;' cellSpacing=0 cellPadding=0 width=130 align=center ><TBODY>";
            if ($cgClientWeekRow = mysql_fetch_array($clientWeekResult)) {
                do {
                    $trColor = "";
                    $Weeks = $cgClientWeekRow["Weeks"];
                    if ($Weeks == $curWeeks) $trColor = "bgcolor=#CCFF99";
                    $WeekCount = $cgClientWeekRow["cgCount"];
                    if ($curWeeks > $Weeks) {
                        $WeekQty = "<span style='color:#f00;'>" . number_format($cgClientWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    else {
                        $WeekQty = "<span style='color:#fff;'>" . number_format($cgClientWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    $WeeksTRS = $Weeks > 0 ? "Week " . substr($Weeks, 4, 2) : "交期待定";
                    $SubModuleIdTemp = 0;
                    $PassStr = "&BuyerId=$cgNumber&Weeks=$Weeks";
                    $SubModuleId = anmaIn($SubModuleIdTemp, $SinkOrder, $motherSTR);//加密
                    echo "<TR $trColor><TD height=20><A onfocus=this.blur(); href='mainFrame.php?Id=$SubModuleId$PassStr' target='mainFrame' onclick='ClickTotal(this,1,$SubModuleIdTemp)'><div style='padding-top:5px;'><div style='float:left;width:50px; height:20px;'>$WeeksTRS</div><div align='right' style='float:left;width:80px; height:20px;'>$WeekQty</div></div></A></TD></TR>";
                } while ($cgClientWeekRow = mysql_fetch_array($clientWeekResult));
            }
            echo "</TBODY></TABLE>";
        }
        else {
            echo "<div><ul>";
            if ($cgClientWeekRow = mysql_fetch_array($clientWeekResult)) {
                do {
                    $trColor = "";
                    $Weeks = $cgClientWeekRow["Weeks"];
                    if ($Weeks == $curWeeks) $trColor = "bgcolor=#CCFF99";
                    $WeekCount = $cgClientWeekRow["cgCount"];
                    if ($curWeeks > $Weeks) {
                        $WeekQty = "<span style='color:#f00;'>" . number_format($cgClientWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    else {
                        $WeekQty = "<span style='color:#fff;'>" . number_format($cgClientWeekRow["Qty"]) . "</span>" . " <span style='color:#fff;'>($WeekCount)</span>";
                    }
                    $WeeksTRS = $Weeks > 0 ? "Week " . substr($Weeks, 4, 2) : "交期待定";
                    $WeekSTR = "<span> " . $WeeksTRS . "</span>";

                    if ($Weeks > 0) {
                        $dateArray = GetWeekToDate($Weeks, "m/d");
                        $dateSTR = $dateArray[0] . "-" . $dateArray[1];
                        $dateSTR = "<span style='float: left;font-size:10px;color:#888888;margin-left:1px;'>$dateSTR</span>";
                        $WeekSTR .= "<br>" . $dateSTR;
                    }

                    $subTitle = $Weeks >= $curWeeks ? "<span style='float:right;margin-right:5px;margin-top:8px;'>$WeekQty</span>" : " <span style='color:#f00;float:right;margin-right:5px;margin-top:8px;'>" . $WeekQty . "</span>";

                    $SubModuleIdTemp = 0;
                    $PassStr = "&BuyerId=$cgNumber&Weeks=$Weeks";
                    $SubModuleId = anmaIn($SubModuleIdTemp, $SinkOrder, $motherSTR);//加密

                    $liHeight = "style='height:30px;'";
                    if ($curWeeks == $Weeks) {
                        echo "<li id='weekLi' name='weekLi' style='height:40px' ><a href='mainFrame.php?Id=$SubModuleId$PassStr'  target='mainFrame'>  $subTitle $WeekSTR</a></li>";
                    }
                    else {
                        echo "<li name='subLi' id='subLi' $liHeight><a href='mainFrame.php?Id=$SubModuleId$PassStr'  target='mainFrame'>$subTitle  $WeekSTR</a></li>";
                    }
                } while ($cgClientWeekRow = mysql_fetch_array($clientWeekResult));
                echo "</ul></div>";
            }
        }
        echo "</TD></TR>";
    }
    $i++;
}


?>