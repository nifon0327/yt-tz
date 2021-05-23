<?php
//电信---yang 20120801
//代码共享-EWEN 2012-08-12
include "../model/modelhead.php";
$funFrom = "popedom";
$nowWebPage = $funFrom . "_read";
$_SESSION["nowWebPage"] = $nowWebPage;

// by qianyunlai.com
if ($_SESSION['Login_uName'] == 'admin') {
    $Keys = 31;
}

$ColsNumber = 5;
$Th_Col = "选项|50|顶/底部项目|150|右侧项目|150|功能模块|200|权限|300";
$Field = explode("|", $Th_Col);
$Count = count($Field);
$widthArray = array();
for ($i = 0; $i < $Count; $i++) {
    $i = $i + 1;
    $widthArray[] = $Field[$i];
    $tableWidth = $tableWidth + $Field[$i];
}
//还要关注modelfuction.php 中的标题显示  function List_Title($Th_Col,$Sign,$Height){
if (isFireFox() == 1) {   //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
    $tableWidth = $tableWidth + $Count * 2;
}
if (isSafari6() == 1) {
    $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
}
if (isGoogleChrome() == 1) {
    $tableWidth = $tableWidth + ceil($Count * 1.5);
}
$tableMenuS = 550;
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" method="post" action="">
  <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td class='timeTop' id="menuT1" width="<?php echo $tableMenuS ?>">
          <?php

          $result = mysql_query("SELECT * FROM (
		SELECT A.Id,concat(C.CShortName,'-',D.Name,': ',B.Name) AS Name ,B.cSign,D.SortId,B.Name AS LinkName
		FROM $DataIn.usertable A 
		LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
		LEFT JOIN $DataPublic.companys_group C ON C.cSign=B.cSign
		LEFT JOIN $DataPublic.branchdata D ON D.Id=B.BranchId
		WHERE 1 AND A.uType=1 AND B.Estate>0
	UNION ALL
		SELECT A.Id,concat('外部人员: ',B.Name) AS Name,'0' AS cSign,'1' AS SortId,B.Name AS LinkName
		FROM $DataIn.usertable A
		LEFT JOIN $DataIn.ot_staff B ON A.Number=B.Number 
		WHERE A.uType=4 AND B.Estate>0
		)Z ORDER BY cSign DESC,SortId,convert(LinkName using gbk) asc", $link_id);
          if ($myrow = mysql_fetch_array($result)) {
              echo " <select name='User' id='User' onchange='document.form1.submit();'>";
              $i = 1;
              do {
                  $UserId = $myrow["Id"];
                  $Name = $myrow["Name"];
                  $User = $User == "" ? $UserId : $User;
                  if ($UserId == $User) {
                      echo "<option value='$UserId']' selected>$i-$Name</option>";
                  }
                  else {
                      echo "<option value='$UserId']'>$i-$Name</option>";
                  }
                  $i++;
              } while ($myrow = mysql_fetch_array($result));
              echo "</select>";
          }
          ?>
      </td>
      <td><label for="checkall"><input type="checkbox" id="checkall">全选</label></td>
      <td width="150" id="menuT2" align="center" class="">
        <table border="0" align="center" cellspacing="0">
          <tr>
            <td class="readlink">
                <?php
                //权限设定
                echo "<nobr>";
                if (($Keys & mUPDATE) || ($Keys & mLOCK)) {
                    echo "<span onClick='upDateValue()' $onClickCSS>更新</span>&nbsp;";
                }
                echo "</nobr>";
                ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td height='5' colspan='6' class='A0011'>&nbsp;</td>
    </tr>
  </table>
    <?php
    //一级项目
    List_Title($Th_Col, "1", 0);
    $Result = mysql_query("SELECT ModuleId,ModuleName FROM $DataPublic.funmodule WHERE 1 and TypeId<4 AND (cSign='0' OR cSign='$Login_cSign')  AND Estate>0 order by TypeId,OrderId", $link_id);
    $i = 1;
    $j = 1;//复选框序号
    if ($Result)
        if ($myrow = mysql_fetch_array($Result)) {
            do {
                $m = 1;
                $RowsA = 1;
                $ModuleId = $myrow["ModuleId"];
                $ModuleName = $myrow["ModuleName"];
                //1级权限读取
                $checkResult = mysql_query("SELECT Id FROM $DataIn.upopedom WHERE 1 and UserId=$User and ModuleId=$ModuleId and Action>0 order by Id LIMIT 1", $link_id);
                if ($chexkRow = mysql_fetch_array($checkResult)) {
                    $ActionSTR1 = "checked";
                }
                else {
                    $ActionSTR1 = "";
                }
                //$Id=$myrow["Id"];

                //二级菜单
                $Result2 = mysql_query("SELECT A.dModuleId,B.ModuleName 
		FROM $DataPublic.modulenexus A
		LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.dModuleId
		WHERE 1 AND A.ModuleId=$ModuleId AND (B.cSign='0' OR B.cSign='$Login_cSign') AND B.Estate>0 ORDER BY A.ModuleId,A.OrderId", $link_id);
                //统计三级菜单的数目
                $Numbers2 = @mysql_num_rows($Result2);
                echo "<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'><tr>";
                echo "<td class='A0111' width='$Field[$m]' height='20' align='center'>$i</td>";
                $m = $m + 2;
                $Pre1 = $j;//作为下级的上级
                $Choose1 = "<input name='checkid[$j]' type='checkbox' id='$j' value='0,$j,$Numbers2,1,$ModuleId' onclick='Chooserow(this)' $ActionSTR1> <LABEL for='$j'>$ModuleName</LABEL>";
                $j++;
                echo "<td class='A0101' width='$Field[$m]'>$Choose1</td>";
                $m = $m + 2;
                $GradeA = 0;
                //二级菜单处理
                if ($myrow2 = mysql_fetch_array($Result2)) {
                    $GradeB = 1;
                    do {
                        $RowsB = 1;
                        $ModuleId2 = $myrow2["dModuleId"];
                        $ModuleName2 = $myrow2["ModuleName"];
                        //如果是非首行，则开新行
                        if ($RowsA != 1) {
                            echo "<tr>";
                        }
                        //三级菜单
                        $Result3 = mysql_query("SELECT A.dModuleId,B.ModuleName,B.Parameter 
				FROM $DataPublic.modulenexus A
				LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.dModuleId
				WHERE 1 AND A.ModuleId=$ModuleId2 AND (B.cSign='0' OR B.cSign='$Login_cSign') AND B.Estate>0 ORDER BY A.ModuleId,A.OrderId", $link_id);
                        //统计三级菜单的数目
                        $Numbers3 = @mysql_num_rows($Result3);
                        if ($Numbers3 > 1) {
                            $rowspanB = "rowspan='$Numbers3'";
                            $GradeA = $GradeA + $Numbers3;
                        }
                        else {
                            $GradeA = $GradeA + 1;
                            $rowspanB = "";
                        }
                        //输出二级项目
                        //2级权限读取
                        $checkResult2 = mysql_query("SELECT Id FROM $DataIn.upopedom WHERE 1 AND UserId=$User AND ModuleId=$ModuleId2 AND Action>0 ORDER BY Id LIMIT 1", $link_id);
                        if ($chexkRow2 = mysql_fetch_array($checkResult2)) {
                            $ActionSTR2 = "checked";
                        }
                        else {
                            $ActionSTR2 = "";
                        }

                        $Choose2 = "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre1,$j,$Numbers3,2,$ModuleId2' onclick='Chooserow(this)' $ActionSTR2> <LABEL for='$j'>$ModuleName2($ModuleId2)</LABEL>";
                        $Pre2 = $j;
                        $j++;
                        echo "<td class='A0101' width='$Field[$m]' $rowspanB>$Choose2</td>";
                        if ($myrow3 = mysql_fetch_array($Result3)) {
                            do {
                                $ModuleId3 = $myrow3["dModuleId"];
                                $ModuleName3 = $myrow3["ModuleName"];
                                $Parameter = $myrow3["Parameter"];
                                //同样非首行，则开新行
                                if ($RowsB != 1) {
                                    echo "<tr>";
                                }
                                //3级权限读取
                                $checkResult3 = mysql_query("SELECT Action FROM $DataIn.upopedom WHERE 1 AND UserId=$User AND ModuleId=$ModuleId3 AND Action>0 ORDER BY Id LIMIT 1", $link_id);
                                if ($chexkRow3 = mysql_fetch_array($checkResult3)) {
                                    $ActionSTR3 = "checked";
                                    $Action = $chexkRow3["Action"];
                                    if ($Action & mADD) {
                                        $ActionSTRa = "checked";
                                    }
                                    else {
                                        $ActionSTRa = "";
                                    }//2
                                    if ($Action & mUPDATE) {
                                        $ActionSTRb = "checked";
                                    }
                                    else {
                                        $ActionSTRb = "";
                                    }//4
                                    if ($Action & mDELETE) {
                                        $ActionSTRc = "checked";
                                    }
                                    else {
                                        $ActionSTRc = "";
                                    }//8
                                    if ($Action & mLOCK) {
                                        $ActionSTRd = "checked";
                                    }
                                    else {
                                        $ActionSTRd = "";
                                    }//16
                                }
                                else {
                                    $ActionSTR3 = "";
                                    $ActionSTRa = "";
                                    $ActionSTRb = "";
                                    $ActionSTRc = "";
                                    $ActionSTRd = "";
                                }
                                //输出三级项目
                                $m = $m + 2;
                                echo "<td class='A0101' width='$Field[$m]' height='20'>&nbsp;";
                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre2,$j,5,3,$ModuleId3' onclick='Chooserow(this)' $ActionSTR3><LABEL for='$j'>$ModuleName3($ModuleId3)</LABEL></td>";
                                $Pre3 = $j;
                                $j++;
                                $m = $m + 2;
                                echo "<td class='A0101' width='' height='20'>&nbsp;";

                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre3,$j,4,4,1' onclick='Chooserow(this)' $ActionSTR3><LABEL for='$j'>浏览</LABEL>&nbsp;";
                                $Pre4 = $j;
                                $j++;
                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre4,$j,0,5,2' onclick='Chooserow(this)' $ActionSTRa><LABEL for='$j'>新增</LABEL>&nbsp;";
                                $j++;
                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre4,$j,0,5,4' onclick='Chooserow(this)' $ActionSTRb><LABEL for='$j'>更新</LABEL>&nbsp;";
                                $j++;
                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre4,$j,0,5,8' onclick='Chooserow(this)' $ActionSTRc><LABEL for='$j'>删除</LABEL>&nbsp;";
                                $j++;
                                echo "<input name='checkid[$j]' type='checkbox' id='$j' value='$Pre4,$j,0,5,16' onclick='Chooserow(this)' $ActionSTRd><LABEL for='$j'>锁定</LABEL>";
                                $j++;
                                echo "</td></tr>";
                                $RowsB++;
                            } while ($myrow3 = mysql_fetch_array($Result3));
                        }
                        else {//没有设右侧子功能菜单
                            //输出三级项目
                            $m = $m + 2;
                            echo "<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
                            $m = $m + 2;
                            echo "<td class='A0101' width='' height='20'>&nbsp;未设定</td>";
                            echo "</tr>";
                        }

                    } while ($myrow2 = mysql_fetch_array($Result2));
                }
                else {
                    //输出二、三级项目
                    echo "<td class='A0101' width='$Field[$m]'>&nbsp;未设定</td>";
                    $m = $m + 2;
                    echo "<td class='A0101' width='$Field[$m]' height='20'>&nbsp;未设定</td>";
                    $m = $m + 2;
                    echo "<td class='A0101' width='' height='20'>&nbsp;未设定</td>";
                    echo "</tr>";
                }
                //重写首行的并行数
                if ($GradeA > 1) {
                    echo "<script>ListTable$i.rows[0].cells[0].rowSpan=$GradeA;ListTable$i.rows[0].cells[1].rowSpan=$GradeA;</script>";
                }
                echo "</table>";
                $i++;
            } while ($myrow = mysql_fetch_array($Result));
        }
    echo "<input name='IdCountNum' type='hidden' id='IdCountNum' value='$j'>";
    List_Title($Th_Col, "0", 0);
    pBottom($i - 1, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
    ChangeWtitle("$SubCompany 系统用户权限列表");
    ?>
  <script type=text/javascript>
  function upDateValue() {
      if (document.form1.User.value != "") {
          document.form1.action = "popedom_updated.php";
          document.form1.submit();
      }
      else {
          alert("未选择员工");
      }
  }

  function Chooserow(e) {
      var res = jQuery(e).val().split(',');
//      // 顶级分类管理
//      switch (res[0]) {
//          case '0':
//              var table0 = jQuery(e).parent().parent().parent().parent();
//              var check0 = jQuery(e).attr('checked');
//              if (check0 != undefined && check0 != false) {
//                  table0.find(':checkbox').prop('checked', check0);
//              } else {
//                  table0.find(':checkbox').removeAttr('checked');
//              }
//              break;
//      }
      // 二级三级管理
      switch (res[3]) {
          case '2':
              var td1 = jQuery(e).parent();
              var loop = td1.attr('rowspan');
              var check1 = jQuery(e).attr('checked');
              var loopTd = td1.parent();
              if (check1 != undefined && check1 != false) {
                  td1.next().find(':checkbox').prop('checked', check1);
                  td1.next().next().find(':checkbox').prop('checked', check1);
              } else {
                  td1.next().find(':checkbox').removeAttr('checked');
                  td1.next().next().find(':checkbox').removeAttr('checked');
              }
              for (var i = 1; i < loop; i++) {
                  loopTd = loopTd.next();
                  if (check1 != undefined && check1 != false) {
                      loopTd.find(':checkbox').prop('checked', check1);
                  } else {
                      loopTd.find(':checkbox').removeAttr('checked');
                  }
              }

              break;
          case '3':
              var td2 = jQuery(e).parent();
              var check2 = jQuery(e).attr('checked');
              if (check2 != undefined && check2 != false) {
                  td2.next().find(':checkbox').prop('checked', check2);
              } else {
                  td2.next().find(':checkbox').removeAttr('checked');
              }
              break;
      }
  }

  function CheckChoose(tempIndex, tempGrade) {
      var thisVALUE = form1.elements[tempIndex].value;
      var valueArray = thisVALUE.split(",");
      var PreIndex = valueArray[0] * 1;
      var nowIndex = valueArray[1] * 1;
      var NextCount = valueArray[2] * 1;
      var Grade = valueArray[3] * 1;
      var theIndex = tempIndex * 1;
      switch (tempGrade) {
          case 3:
              for (var j = 1; j <= NextCount; j++) {//3级处理
                  theIndex++;
                  if (form1.elements[theIndex].checked == true) {
                      return true;
                  }
                  else {
                      for (var m = 1; m <= 5; m++) {//处理权限部分
                          theIndex++;
                          if (form1.elements[theIndex].checked == true) {
                              return true;
                          }
                      }
                  }
              }
              break;
          case 2:
              for (var i = 1; i <= NextCount; i++) {//2级处理
                  theIndex++;
                  if (form1.elements[theIndex].checked == true) {
                      return true;
                  }
                  else {
                      var thisVALUE2 = form1.elements[theIndex].value;
                      var valueArray2 = thisVALUE2.split(",");
                      var nowIndex2 = valueArray2[1] * 1;
                      var NextCount2 = valueArray2[2] * 1;
                      var Grade2 = valueArray2[3] * 1;
                      for (var j = 1; j <= NextCount2; j++) {//3级处理
                          theIndex++;
                          if (form1.elements[theIndex].checked == true) {
                              return true;
                          }
                          else {
                              for (var m = 1; m <= 5; m++) {//处理权限部分
                                  theIndex++;
                                  if (form1.elements[theIndex].checked == true) {
                                      return true;
                                  }
                              }
                          }
                      }
                  }
              }
              break;
      }
      return false;
  }
  $('#checkall').change(function () {
      var checkAll = $('#checkall').attr('checked');
      if (checkAll != undefined && checkAll != false) {
          $(":checkbox").attr('checked', checkAll);

      } else {
          $(":checkbox").removeAttr('checked');


      }


  });

  </script>