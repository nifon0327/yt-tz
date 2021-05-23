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
        $config = parse_ini_file("config.ini");
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
#region 报废

    /**
     * 获取需要报废的产品名称
     * @return array|null
     */
//    public function  getProductName()
//    {
//        $sql = "SELECT P.cName,S.ProductId
//                FROM yw1_ordersheet S
//                JOIN productdata P ON P.ProductId=S.ProductId";
//        return $this->format($sql);
//    }

    public function getDirector()
    {
        $sql = "SELECT Number as Id,Name FROM staffmain";
        return $this->format($sql);
    }

    /**
     * 获取报废构件
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $scrapDate
     * @return array|null
     */
    public function getScrapProduct($companyId = "", $buildNo = "", $orderPO = "", $typeId = "", $scrapDate = "")
    {
        $searchRows = "";
        if (!is_null($companyId) && $companyId != "") {
            $searchRows .= " AND R.CompanyId = $companyId ";
        }
        if (!is_null($buildNo) && $buildNo != "") {
            $searchRows .= " AND R.BuildNo = $buildNo ";
        }
        if (!is_null($orderPO) && $orderPO != "") {
            $searchRows .= " AND R.OrderPO = '$orderPO' ";
        }
        if (!is_null($typeId) && $typeId != "") {
            $searchRows .= " AND R.PTypeId = $typeId";
        }
        if (!is_null($scrapDate) && $scrapDate != "") {
            $searchRows .= " AND R.ScrapDate ='$scrapDate'";
        }
        $sql = "SELECT
                    R.Id,
                    IFNULL(C.Forshort, '未知项目') AS Forshort,
                    T.TypeName,
                    P.cName,
                    R.ScrapDate,
                    S.Estate,
                    R.ScrapAnalysis,
                    W. Name,
                    STM.Name AS Operator,
                    SM.Name AS Directory,
                    R.InvoicePath,
                    R.Modified
                FROM
                    scrap_product R
                LEFT JOIN trade_object C ON C.CompanyId = R.CompanyId
                LEFT JOIN producttype T ON T.TypeId = R.PTypeId
                LEFT JOIN productdata P ON P.ProductId = R.ProductId
                LEFT JOIN workshopdata W ON R.WorkshopId = W.Id
                LEFT JOIN ch1_shipsplit S ON S.POrderId = R.POrderId
                LEFT JOIN ch1_shipsheet SS ON SS.POrderId = R.POrderId
                LEFT JOIN staffmain SM ON SM.Number = R.Header
                LEFT JOIN staffmain STM ON STM.Number = R.Operator
                WHERE
                    1 $searchRows
                ORDER BY R.Id DESC";
        return $this->format($sql);
    }

    /**
     * 获取生产线
     * @return array|null
     */
    public function getWorkshop()
    {
        $sql = "SELECT Id,`Name` from workshopdata;";
        return $this->format($sql);
    }


    /**
     * 获取项目
     * @return array|null
     */
    public function getForshort()
    {
        $sql = "SELECT M.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort
                FROM ch1_shipsplit SP  
                LEFT JOIN  yw1_ordersheet S  ON S.POrderId=SP.POrderId
                LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
                LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId 
                LEFT JOIN  productdata P ON P.ProductId = S.ProductId
                LEFT JOIN  productstock K ON K.ProductId = P.ProductId
                WHERE 1  AND M.CompanyId IS NOT NULL AND K.tStockQty >= SP.Qty   GROUP BY M.CompanyId 
                UNION
                SELECT S.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort FROM ch5_sampsheet S 
                LEFT JOIN trade_object C ON C.CompanyId=S.CompanyId 
                WHERE 1  and S.Estate='1'";
        return $this->format($sql);
    }

    /**
     * 获取楼栋号
     * @return array|null
     */
    public function getBuildNo($companyId)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= "AND M.CompanyId = $companyId";
        }
        $sql = "SELECT M.BuildNo
FROM ch1_shipsplit SP
LEFT JOIN  yw1_ordersheet S  ON S.POrderId=SP.POrderId
LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN  productdata P ON P.ProductId = S.ProductId
LEFT JOIN  productstock K ON K.ProductId = P.ProductId
WHERE 1 $searchRows AND S.OrderPO IS NOT NULL AND K.tStockQty >= SP.Qty   GROUP BY M.BuildNo";
        return $this->format($sql);
    }

    /**
     * 获取楼层信息
     * @return array
     */
    public function getFloorNo($companyId = "", $buildNo = "")
    {
        $searchRows = "";
        if (!is_null($companyId) && $companyId != "") {
            $searchRows .= " AND M.CompanyId = $companyId ";
        }
        if (!is_null($buildNo) && $buildNo != "") {
            $searchRows .= " AND M.BuildNo = $buildNo ";
        }
        $floorList = array();
        $sql = "SELECT S.OrderPO
FROM ch1_shipsplit SP
LEFT JOIN  yw1_ordersheet S  ON S.POrderId=SP.POrderId
LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN  productdata P ON P.ProductId = S.ProductId
LEFT JOIN  productstock K ON K.ProductId = P.ProductId
WHERE 1 $searchRows  AND S.OrderPO IS NOT NULL AND K.tStockQty >= SP.Qty   GROUP BY S.OrderPO";
        $orderList = $this->format($sql);
        foreach ($orderList as $item) {
            $poField = explode("-", $item["OrderPO"]);
            $floor = array('OrderPO' => $item["OrderPO"], "Floor" => end($poField));
            if (!empty($poField)) {
                array_push($floorList, $floor);
            }
        }
        return $floorList;
    }

    /**
     * 获取类型
     * @return array|null
     */
    public function getType($companyId, $buildNo, $orderPO)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND M.CompanyId = $companyId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND M.BuildNo = $buildNo ";
        }
        if (!is_null($orderPO)) {
            $searchRows .= " AND S.OrderPO = '$orderPO' ";
        }
        $sql = "SELECT  P.TypeId,T.TypeName
FROM ch1_shipsplit SP
LEFT JOIN  yw1_ordersheet S  ON S.POrderId=SP.POrderId
LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN  productdata P ON P.ProductId = S.ProductId
LEFT JOIN  productstock K ON K.ProductId = P.ProductId
INNER JOIN producttype T ON T.TypeId=P.TypeId 
WHERE 1 $searchRows AND S.OrderPO IS NOT NULL AND K.tStockQty >= SP.Qty   GROUP BY  P.TypeId";
        return $this->format($sql);
    }


    /**
     * 获取产品构件名
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $pName
     * @return array|null
     */
    public function searchCName($companyId, $buildNo, $orderPO, $typeId, $pName)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND M.CompanyId = $companyId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND M.BuildNo = $buildNo ";
        }
        if (!is_null($orderPO)) {
            $searchRows .= " AND S.OrderPO = '$orderPO' ";
        }
        if (!is_null($typeId)) {
            $searchRows .= " AND P.TypeId = $typeId";
        }
        if (!is_null($pName) && $pName != "") {
            $searchRows .= " AND P.cName LIKE '%$pName%'";
        }
        $sql = "SELECT  S.POrderId,S.Id, S.ProductId,P.cName
	FROM ch1_shipsplit SP
    LEFT JOIN  yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN  productdata P ON P.ProductId = S.ProductId
	LEFT JOIN  productstock K ON K.ProductId = P.ProductId
	WHERE 1 $searchRows  AND M.CompanyId IS NOT NULL AND K.tStockQty >= SP.Qty AND  S.Estate >0  AND SP.Estate=1";
        return $this->format($sql);
    }

    /**
     * 新增报废产品
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $productId
     * @param $scrapDate
     * @param $workshopId
     * @param $invoicePath
     * @param $scrapAnalysis
     * @return bool|mysqli_result
     */
    public function createScrapProduct($companyId, $buildNo, $orderPO, $typeId, $productId, $scrapDate, $workshopId, $invoicePath, $scrapAnalysis, $pOrderId,$headerId)
    {
        if(!$_SESSION['Login_P_Number'])
        {
            throw new Exception("操作人获取失败");
        }
        $Login_P_Number=$_SESSION['Login_P_Number'];
//        $Login_P_Number=1;

        $sql = "INSERT INTO scrap_product (ProductId,ScrapDate,InvoicePath,ScrapAnalysis,CompanyId,BuildNo,OrderPO,PTypeId,WorkshopId,POrderId,Header,Operator)
                VALUES($productId,'$scrapDate','$invoicePath','$scrapAnalysis',$companyId,$buildNo,'$orderPO',$typeId,$workshopId,'$pOrderId',$headerId,$Login_P_Number)";

        if ($this->conn->query($sql)) {
            //修改待出订单状态
            $usql = "UPDATE ch1_shipsplit SET Estate =11 WHERE POrderId ='$pOrderId'";
            return $this->conn->query($usql);
        }
        return false;
    }

    /**
     * 报废
     * @param $scrapProductId
     * @return bool|mysqli_result
     */
    public function updateScrapProduct($scrapProductIds)
    {
        $sql = "UPDATE ch1_shipsplit SET Estate =12 WHERE POrderId IN (SELECT POrderId FROM scrap_product WHERE Id IN ($scrapProductIds) )";
        $usql = "UPDATE scrap_product SET Modified =now() ; ";
        $ysql = "UPDATE yw1_ordersheet SET Estate =0 WHERE POrderId IN (SELECT POrderId FROM scrap_product WHERE Id IN ($scrapProductIds) )";
        if($this->conn->query($sql)&&$this->conn->query($ysql) ){
            return $this->conn->query($usql);
        }
        return false;

    }


    /**
     * 删除报废构件
     * @param $scrapProductIds
     * @return bool|mysqli_result
     */
    public function dropScrapProduct($scrapProductIds)
    {
        $sql = "UPDATE ch1_shipsplit SET Estate =1 WHERE POrderId IN (SELECT POrderId FROM scrap_product WHERE Id IN ($scrapProductIds) )";

        if($this->conn->query($sql)){
            $dsql = "delete from scrap_product WHERE Id in ($scrapProductIds)";
            return $this->conn->query($dsql);
        }
        return false;
    }


    #region 报废查询

    /**
     * 获取报废公司名
     * @return array|null
     */
    public function getScrapCompany()
    {
        $sql = "SELECT R.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort
                FROM scrap_product R 
                LEFT JOIN  trade_object C ON C.CompanyId=R.CompanyId GROUP BY R.CompanyId";
        return $this->format($sql);
    }

    /**
     * 获取楼栋号
     * @param $companyId
     * @return array|null
     */
    public function getScrapBuildNo($companyId)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND CompanyId = $companyId ";
        }

        $sql = "SELECT DISTINCT BuildNo FROM scrap_product WHERE 1 $searchRows";
        return $this->format($sql);
    }

    /**
     * 获取报废楼层
     * @param $companyId
     * @param $buildNo
     * @return array
     */
    public function getScrapFloorNo($companyId, $buildNo)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND CompanyId = $companyId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND BuildNo = $buildNo ";
        }
        $floorList = array();
        $sql = "SELECT DISTINCT OrderPO from scrap_product WHERE 1 $searchRows";
        $orderList = $this->format($sql);
        foreach ($orderList as $item) {
            $poField = explode("-", $item["OrderPO"]);
            $floor = array('OrderPO' => $item["OrderPO"], "Floor" => end($poField));
            if (!empty($poField)) {
                array_push($floorList, $floor);
            }
        }
        return $floorList;
    }

    /**
     * 获取报废类型
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @return array|null
     */
    public function getScrapType($companyId, $buildNo, $orderPO)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND R.CompanyId = $companyId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND R.BuildNo = $buildNo ";
        }
        if (!is_null($orderPO)) {
            $searchRows .= " AND R.OrderPO = '$orderPO' ";
        }
        $sql = "SELECT R.PTypeId TypeId,P.TypeName from scrap_product R
                LEFT JOIN producttype P ON R.PTypeId = P.TypeId WHERE 1 $searchRows";

        return $this->format($sql);
    }

    #endregion


#endregion

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
                    return false;
                }
        }
    }
#endregion

//region 通用方法
    public function format($sql)
    {
        $result_query = $this->conn->query($sql);
        $result = array();
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
