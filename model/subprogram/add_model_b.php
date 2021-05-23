<?php //二合一已更新?>
<table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#F2F3F5">
  <tr>
    <td class="timeBottom" id="menuB1" width="<?php echo $tableMenuS ?>">　　</td>
    <td width="150" id="menuT2" align="center" class=''>
      <table border="0" align="center" cellspacing="0">
        <tr>
          <td class="readlink">
            <nobr>
                <?php
                echo $SaveFun;
                echo $CustomFun;//自定义功能
                if ($CheckFormURL == "thisPage") {
                    if ($SaveSTR != "NO") {
                        if (isset($ValidatorUd) && $ValidatorUd) {
                            $ClickFucUd = 'if(Validator.Validate(document.getElementById(document.form1.id),3,"", 3)){CheckForm();}';
                        }
                        else {
                            $ClickFucUd = 'CheckForm()';
                        }
                        echo "<span onClick='$ClickFucUd' $onClickCSS>　　保存</span>　　";
                    }
                    if ($ResetSTR != "NO") {
                        echo "<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
                    }
                }
                else {
                    if ($SaveSTR != "NO") {
                        $ErrorInfoModel = $ErrorInfoModel == "" ? 3 : $ErrorInfoModel;
                        echo "<span id='buttonSaveBtn' onClick='Validator.Validate(document.getElementById(document.form1.id),$ErrorInfoModel,\"$toWebPage\")' $onClickCSS>保存</span>　　";
                    }
                    if ($ResetSTR != "NO") {
                        echo "<span onClick='javascript:ReOpen(\"$nowWebPage\");' $onClickCSS>重置</span>";
                    }
                }
                if ($isBack != "N") {
                    echo "　　<span onClick='javascript:ReOpen(\"$fromWebPage\");' $onClickCSS>返回</span>";
                }
                ?>
            </nobr>
          </td>
        </tr>
      </table>
    </td>
  </tr>
    <?php
    $SearchRows = "";
    if ($Parameter != "") {
        PassParameter($Parameter);
    }
    ?>
  </form>
</table>
</div>
</body>
</html>