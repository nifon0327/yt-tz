<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */

class DbConnect
{
    public $conn;

    function __construct()
    {
        $config     = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

#region 权限校验
    public function apiAuth($openId, $tag)
    {
        $sql = "SELECT
                A.uName,
                A.Number,
                A.openid,
                B.NAME,
                D.NAME AS Branch,
                E.NAME AS RoleName
            FROM
                UserTable A
                LEFT JOIN staffmain B ON B.Number = A.Number
                LEFT JOIN companys_group C ON C.cSign = B.cSign
                LEFT JOIN branchdata D ON D.Id = B.BranchId
                LEFT JOIN ac_roles E ON E.id = A.roleId 
            WHERE
                1 
                AND A.uType = '1'
            AND A.openid='$openId' LIMIT 1";

        $result = $this->format($sql);

        switch ($tag) {
            //盘点
            case 1:
                return is_null($result) ? false : true;
                break;
            //出货
            case 2:

                if (!is_null($result) && ($result[0]["Branch"] == "IT中心" || $result[0]["Branch"] == "资材部")) {
                    return true;
                } else {
                    return true;
                }
        }
    }

#endregion

//region 出货功能

    /**
     * 获取扫码用户昵称
     */
    public function get_operator_name($openId)
    {
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        return $this->format($sql);
    }

    /**
     * 根据日期查询出货流水单号
     * @param $currentDate 查询日期
     * @param $CompanyId
     * @return array|null
     */
    public function get_invoice_no_by_date($currentDate, $CompanyId)
    {
        $sql = "select InvoiceNO from ch1_shipmain WHERE Estate='1' AND `Date`='$currentDate' and CompanyId in ($CompanyId)";
        return $this->format($sql);
    }

    /**
     * 根据日期查询出货流水单号
     * @param $currentDate 查询日期
     * @return array|null
     */
    public function get_invoice_no()
    {
        $sql = "select InvoiceNO from ch1_shipmain WHERE Estate='1'";
        return $this->format($sql);
    }

    /**
     * 根据出货流水单号查询相关信息
     * @param $invoiceNo 出货单号
     */
    public function get_invoice_info($invoiceNo)
    {
        $sql = "SELECT  DISTINCT M.Id,M.InvoiceNO,M.Wise,M.Date,C.Forshort,M.CarNo,p.cName,M.CarNumber
                FROM ch1_shipmain M
                LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
                LEFT JOIN ch1_shipsheet SS ON M.Id=SS.Mid 
                LEFT JOIN productdata P ON P.ProductId=SS.ProductId
                INNER JOIN producttype T ON T.TypeId=P.TypeId 
                WHERE 1 and M.Estate='1'
                and M.InvoiceNO='$invoiceNo'";
        return $this->format($sql);
    }

    /**
     * 获取车辆信息
     * @return array|null
     */
    public function get_car_no()
    {
        $sql = "SELECT Id,CarNo FROM cardata ORDER BY Id";
        return $this->format($sql);
    }

    public function get_for_short()
    {
        $sql = "select group_concat(CompanyId) CompanyId,Forshort from companyinfo where Forshort is not null and Forshort != '' group by Forshort order by id asc";
        return $this->format($sql);
    }

    public function get_date($CompanyId)
    {
        $sql = "select `Date` from ch1_shipmain WHERE Estate='1' and CompanyId in ($CompanyId)";
        return $this->format($sql);
    }

    //新增车牌
    public function create_car_no($carNo)
    {
        $Operator = 0;
        if (!$_SESSION['Login_P_Number']) {
            //throw new Exception("操作人获取失败");
        } else {
            $Operator = $_SESSION['Login_P_Number'];
        }

        $sql = "INSERT INTO cardata (OldId, cSign, TypeId, BrandId, UserSign, carListNo, CarNo, User, BuyStore, StoreNum, BuyDate, BuyAddress, BuyContact, Maintainer, Enrollment, DriveLic, Insurance, YueTong, OilCard, CheckTime, InsuranceDate, Estate, Locks, Date, Operator, PLocks, creator, created, modifier, modified) 
      VALUES ( 0, 0, 2, 2, 1, '', '$carNo', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '', '0000-00-00', '0000-00-00', 1, 0, CURDATE(), $Operator, 0, NULL, NULL, NULL, NULL);";

        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        } else {
            throw new Exception("新增车牌失败！");
        }
    }

    /**
     * 出货更新
     * @param $CarNo        车辆编号
     * @param $DateTime     出货时间
     * @param $invoiceNo    出货单号
     * @param $imageUrl     出货图片
     * @param $GZG          工字钢
     * @param $MF           木方
     * @return array|null
     */
    public function update_invoice_estate($carNo, $dateTime, $invoiceNo, $imageUrl, $GZG, $MF)
    {
        $sql = "UPDATE ch1_shipmain M
                LEFT JOIN ch1_shipsheet C ON C.Mid = M.Id
                LEFT JOIN ch5_sampsheet S ON S.SampId = C.POrderId
                SET M.CarNo = '$carNo',
                 M.Estate = '0',
                 M.OPdatetime = '$dateTime',
                 S.Estate = '0',
                 M.ImageUrl = '$imageUrl',
                 M.GZG = $GZG,
                 M.MF = $MF
                WHERE M.InvoiceNO='$invoiceNo'";
        if ($this->conn->query($sql)) {
            //更新出货单表yw1_ordersheet
            $updateSql = "  UPDATE yw1_ordersheet S
                        LEFT JOIN (
                            SELECT
                                IFNULL(SUM(C.Qty), 0) AS shipQty,
                                C.POrderId
                            FROM
                                ch1_shipsheet C
                            LEFT JOIN ch1_shipmain M ON M.Id = C.Mid
                            WHERE 1
                            AND M.Estate = 0
                            AND C.POrderId IN (
                                SELECT POrderId FROM ch1_shipsheet
                                WHERE M.InvoiceNO='$invoiceNo')
                            GROUP BY C.POrderId) A ON A.POrderId = S.POrderId
                        SET S.Estate = 0
                        WHERE
                            S.Qty = A.shipQty";
            return $this->conn->query($updateSql);
        } else {
            return false;
        }
    }

//endregion

//region 通用方法
    public function format($sql)
    {
        $result_query = $this->conn->query($sql);
        $result       = [];
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
//endregion
}