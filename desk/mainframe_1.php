<?php
/*电信---yang 20120801
代码-EWEN
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$outArray = explode("|", $_GET["Id"]);
$RuleStr = $outArray[0];
$EncryptStr = $outArray[1];
$ModuleId = anmaOut($RuleStr, $EncryptStr);

if ($ModuleId == 0) {
    if (!isset($_SESSION["Keys"])) {
        $_SESSION["Keys"] = $Keys;
    }
    if (!isset($_SESSION["S_From"])) {
        $_SESSION["S_From"];

    }
    //清除页面搜索条件
    if (isset($_SESSION["SearchRows"])) {
        unset($_SESSION["SearchRows"]);
    }
    $S_From = 0;
    if ($BuyerId == 0) $Parameter = "Public/Cg_cgdmain_client.php?Weeks=$Weeks";//客供配件
    else $Parameter = "Public/Cg_cgdmain_tj.php?BuyerId=$BuyerId&Weeks=$Weeks";
    echo"<meta http-equiv=\"Refresh\" content='0;url=../".$Parameter."'>";
} else {

    $result = mysql_query("SELECT F.Parameter,U.Action FROM $DataIn.funmodule F,$DataIn.upopedom U WHERE U.UserId='$Login_Id' and U.ModuleId='$ModuleId' and F.ModuleId=U.ModuleId LIMIT 1", $link_id);
    if ($myrow = mysql_fetch_array($result)) {
        $Parameter = $myrow["Parameter"];
        $Keys = $myrow["Action"];

        if (!isset($_SESSION["Keys"])) {
            $_SESSION["Keys"] = $Keys;
        }
        if (!isset($_SESSION["S_From"])) {
            $_SESSION["S_From"];

        }
        //清除页面搜索条件
        if (isset($_SESSION["SearchRows"])) {
            unset($_SESSION["SearchRows"]);
        }

        $S_From = 0;
        echo "<meta http-equiv=\"Refresh\" content='0;url=../" . $Parameter . "'>";
    } else {
        echo "此页面不存在";
    }
}
?>