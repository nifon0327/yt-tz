<?php
/**
 * Created by PhpStorm.
 * User: zf
 * Date: 2019/2/25
 * Time: 17:08
 */

/**
 * Class AccountWebservice
 * @soap
 */
class AccountWebservice
{
    /**
     * @return string
     * @soap
     */
    public function show()
    {
        return 'the data you request!';
    }

    /**
     * @soap
     * @param $name
     * @return string
     */
    public function getUserInfo($name)
    {
        return 'name is ' . $name;
    }

    /**
     * @param $code
     * @param $status 1 -- 审核通过 , 0 -- 审核不通过
     * @return int
     *
     */
    public function setAuditStatus($code, $status)
    {
        global $link_id;
        defined('IN_COMMON') || include '../basic/common.php';
        include_once "../basic/parameter.inc";

        $accountStatus = 3;

        //return "code:$code,status:$status";
        if ($status == '1') {
            $accountStatus = 2;
        }
        //TODO 需检查

        $mySql = "update ch_account set status='" . $accountStatus . "' where run_id=" . $code;
        mysql_query($mySql, $link_id);
        return 1;
    }

    /**
     * @param $id
     * @return bool
     */
    public function callSendAccount($id)
    {
        global $link_id,$DataIn;
        defined('IN_COMMON') || include '../basic/common.php';
        include_once "../basic/parameter.inc";

        //主表
        $mySql = "
            SELECT *
            FROM 
            ch_account
            WHERE
            id='$id'
            ";
        $statement = array();


        //echo "a";
        $myResult = mysql_query($mySql, $link_id);
        if ($myRow = mysql_fetch_array($myResult)) {
            $statement['ACCOUNT_NAME'] = $myRow['ACCOUNT_NAME'];
            $statement['PROJECT_NAME'] = $myRow['PROJECT_NAME'];
            $statement['SUPPLY'] = $myRow['SUPPLY'];
            $statement['SUPPLY_USER'] = $myRow['SUPPLY_USER'];
            $statement['SUPPLY_DATE'] = $myRow['SUPPLY_DATE'];
            $statement['RECEIVING'] = $myRow['RECEIVING'];
            $statement['RECEIVING_USER'] = $myRow['RECEIVING_USER'];
            $statement['RECEIVING_DATE'] = $myRow['RECEIVING_DATE'];
            $statement['CG_ACCOUNT_REMARK'] = $myRow['CG_ACCOUNT_REMARK'];
            $statement['ACCOUNT_CREATEUSER'] = $myRow['ACCOUNT_CREATEUSER'];
            $statement['ACCOUNT_CREATEDATE'] = $myRow['ACCOUNT_START_DATE'];
            $statement['ACCOUNT_DEADLINE'] = $myRow['ACCOUNT_END_DATE'];
            $statement['CG_ACCOUNT_CODE'] = $myRow['RUN_ID'];

        } else {
             //echo "数据不存在";
            return "0:主表数据不存在";
        }
        $startValue = $myRow['ACCOUNT_START_DATE'];
        $endValue = $myRow['ACCOUNT_END_DATE'];
        $clientValue = $myRow['COMPANY_ID'];
       // echo "b";
//子表
        $mySql = "
            SELECT 
            Kind,Sum(Weight) as SumWeight
            FROM
            (
            SELECT
                SUBSTRING_INDEX(SUBSTRING_INDEX ( O.OrderPO, '-', -2 ) ,'-',1) AS Building,
                SPLIT_STR(P.eCode,'-',3) as Kind,
                SUM(P.Weight) AS Weight
            FROM
                $DataIn.ch1_shipsheet S
            LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid
            LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId = S.POrderId
            LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber = O.OrderNumber
            LEFT JOIN $DataIn.yw3_pisheet E ON E.oId = O.Id
            LEFT JOIN $DataIn.productdata P ON P.ProductId = O.ProductId
            WHERE
                S.Mid IN (
                    SELECT
                        M.Id
                    FROM
                        $DataIn.ch1_shipmain M
                    WHERE
                        1
                    AND M.Estate = '0'
                    AND M.Date >= '$startValue'
                    AND M.Date <= '$endValue'
                    AND M.CompanyId = '$clientValue'
                )
            AND S.Type = '1'
            GROUP BY
                Building,Kind
                ) STI 
                group  by Kind";

//echo $mySql;die;
        $accountInfo = array();
        $myResult = mysql_query($mySql, $link_id);
        if ($myRow = mysql_fetch_array($myResult)) {
            do {
                $accountInfo[] = array(
                    'CG_ACCOUNT_INFO_NAME' => $myRow['Kind'],
                    'CG_ACCOUNT_INFO_COUNT' => $myRow['SumWeight'],
                );
            } while ($myRow = mysql_fetch_array($myResult));

        } else {

            return  "0:子表数据不存在";
        }


        $wsdl_url = "http://106.15.180.165:8080/yt/ytInterface/DZD?wsdl";
        $client = new SoapClient($wsdl_url, array("trace" => 1, "exception" => 1));

        $AccountArray = array(
            "CG_ACCOUNT" => array($statement),
            "CG_ACCOUNT_INFO" => $accountInfo
        );
        $json = json_encode($AccountArray, JSON_UNESCAPED_UNICODE);
//echo "接口地址：<br/>".$wsdl_url."<br/>";
//echo "调用方法：<br/>reception<br/>";
        //echo "传输的数据：<br/>".$json."<br/>";

        $result = $client->reception(array("json" => $json));
//echo "<br/>";
//保存结果,修改状态
//echo "返回结果：<br/>".$result->return."<br/>";

        $r = $result->return;
        $rA = explode(":", $r);

        if ($rA[0] == 1) {
            $mySql = "update ch_account set STATUS='1' where id=" . $id;
            mysql_query($mySql, $link_id);
        }
        return "返回结果：<br/>" . $result->return . "<br/>";

    }

    public function callDeleteAccount($id)
    {
        global $link_id;
        defined('IN_COMMON') || include '../basic/common.php';
        include_once "../basic/parameter.inc";

        $wsdl_url = "http://106.15.180.165:8080/yt/ytInterface/DZD?wsdl";
        $client = new SoapClient($wsdl_url, array("trace" => 1, "exception" => 1));

//echo "接口地址：<br/>".$wsdl_url."<br/>";
//echo "调用方法：<br/>delete<br/>";
        //echo "传输的数据：<br/>".$id."<br/>";

        $result = $client->delete(array("json" => $id));
        //var_dump($result);
        $r = $result->return;
        $rA = explode(":", $r);
        $rA[0] = 1;
        if ($rA[0] == 1) {

            $mySql = "delete from ch_account where id=" . $id;
            //echo $mySql;
            mysql_query($mySql, $link_id);

            return true;
        } else {
            return false;
        }

    }


}
