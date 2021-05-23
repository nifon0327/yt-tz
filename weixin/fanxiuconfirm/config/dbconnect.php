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
#region 返修

    /**
     * 获取需要返修的产品名称
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
     * 获取返修构件
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $reworkDate
     * @return array|null
     */
    public function getReworkProduct($companyId = "", $buildNo = "", $orderPO = "", $typeId = "", $reworkDate = "")
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
        if (!is_null($reworkDate) && $reworkDate != "") {
            $searchRows .= " AND R.ReworkDate ='$reworkDate'";
        }
        $sql = "SELECT
                    R.Id,
                    IFNULL(C.Forshort, '未知项目') AS Forshort,
                    T.TypeName,
                    P.cName,
                    R.ReworkDate,
                    S.Estate ss_Estate,
                    SMA.Estate sm_Estate,
                    R.ReworkAnalysis,
                    W. Name,
                    STM.Name AS Operator,
                    SM.Name AS Directory,
                    R.InvoicePath
                FROM
                    rework_product R
                LEFT JOIN trade_object C ON C.CompanyId = R.CompanyId
                LEFT JOIN producttype T ON T.TypeId = R.PTypeId
                LEFT JOIN productdata P ON P.ProductId = R.ProductId
                LEFT JOIN workshopdata W ON R.WorkshopId = W.Id
                LEFT JOIN ch1_shipsplit S ON S.POrderId = R.POrderId
                LEFT JOIN ch1_shipsheet SS ON SS.POrderId = R.POrderId
                LEFT JOIN ch1_shipmain SMA ON SMA.Id=SS.Mid 
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
     * 新增返修产品
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $productId
     * @param $reworkDate
     * @param $workshopId
     * @param $invoicePath
     * @param $reworkAnalysis
     * @return bool|mysqli_result
     */
    public function createReworkProduct($companyId, $buildNo, $orderPO, $typeId, $productId, $reworkDate, $workshopId, $invoicePath, $reworkAnalysis, $pOrderId,$headerId)
    {
        if(!$_SESSION['Login_P_Number'])
        {
            throw new Exception("操作人获取失败");
        }
        $Login_P_Number=$_SESSION['Login_P_Number'];

        $sql = "INSERT INTO rework_product (ProductId,ReworkDate,InvoicePath,ReworkAnalysis,CompanyId,BuildNo,OrderPO,PTypeId,WorkshopId,POrderId,Header,Operator)
                VALUES($productId,'$reworkDate','$invoicePath','$reworkAnalysis',$companyId,$buildNo,'$orderPO',$typeId,$workshopId,'$pOrderId',$headerId,$Login_P_Number)";

        if ($this->conn->query($sql)) {
            //修改待出订单状态
            $usql = "UPDATE ch1_shipsplit SET Estate =10 WHERE POrderId ='$pOrderId'";
            return $this->conn->query($usql);
        }
        return false;
    }

    /**
     * 修理完毕
     * @param $reworkProductId
     * @return bool|mysqli_result
     */
    public function updateReworkProduct($reworkProductIds)
    {
        $sql = "UPDATE ch1_shipsplit SET Estate =1 WHERE POrderId IN (SELECT POrderId FROM rework_product WHERE Id IN ($reworkProductIds) )";
        return $this->conn->query($sql);
    }


    /**
     * 删除返修构件
     * @param $reworkProductIds
     * @return bool|mysqli_result
     */
    public function dropReworkProduct($reworkProductIds)
    {
        $sql = "UPDATE ch1_shipsplit SET Estate =1 WHERE POrderId IN (SELECT POrderId FROM rework_product WHERE Id IN ($reworkProductIds) )";
        
        if($this->conn->query($sql)){
            $dsql = "delete from rework_product WHERE Id in ($reworkProductIds)";    
            return $this->conn->query($dsql);
        }
        return false;
    }


    #region 返修查询

    /**
     * 获取返修公司名
     * @return array|null
     */
    public function getReworkCompany()
    {
        $sql = "SELECT R.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort
                FROM rework_product R 
                LEFT JOIN  trade_object C ON C.CompanyId=R.CompanyId GROUP BY R.CompanyId";
        return $this->format($sql);
    }

    /**
     * 获取楼栋号
     * @param $companyId
     * @return array|null
     */
    public function getReworkBuildNo($companyId)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND CompanyId = $companyId ";
        }

        $sql = "SELECT DISTINCT BuildNo FROM rework_product WHERE 1 $searchRows";
        return $this->format($sql);
    }

    /**
     * 获取返修楼层
     * @param $companyId
     * @param $buildNo
     * @return array
     */
    public function getReworkFloorNo($companyId, $buildNo)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND CompanyId = $companyId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND BuildNo = $buildNo ";
        }
        $floorList = array();
        $sql = "SELECT DISTINCT OrderPO from rework_product WHERE 1 $searchRows";
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
     * 获取返修类型
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @return array|null
     */
    public function getReworkType($companyId, $buildNo, $orderPO)
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
        $sql = "SELECT R.PTypeId TypeId,P.TypeName from rework_product R
                LEFT JOIN producttype P ON R.PTypeId = P.TypeId WHERE 1 $searchRows";

        return $this->format($sql);
    }

    /**
     * 获取返修工艺页面构件
     * @param $companyId
     * @param $buildNo
     * @param $orderPO
     * @param $typeId
     * @param $reworkDate
     * @return array|null
     */
    public function getReworkPlan($companyId, $buildNo, $orderPO, $typeId, $reworkDate)
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
        if (!is_null($typeId)) {
            $searchRows .= " AND R.PTypeId = $typeId";
        }
        if (!is_null($reworkDate)) {
            $searchRows .= " AND R.ReworkDate ='$reworkDate'";
        }
        $sql = "SELECT R.Id, IFNULL(C.Forshort,'未知项目')  AS Forshort,T.TypeName,P.cName,R.ReworkDate,R.Estate,R.ReworkAnalysis,RT.ReworkPlan,R.Operator
FROM rework_product R 
LEFT JOIN  trade_object C ON C.CompanyId=R.CompanyId
LEFT JOIN producttype T ON T.TypeId = R.PTypeId
LEFT JOIN productdata P On P.ProductId = R.ProductId
LEFT JOIN rework_technology RT ON R.Id = RT.ReworkProductId WHERE 1 $searchRows";

        return $this->format($sql);
    }


    /**
     * 创建工艺方案
     * @param $workPlan
     * @param $reworkProductId
     * @return bool|mysqli_result
     */
    public function InsertWorkPlan($workPlan, $reworkProductId)
    {
        $sql = "INSERT INTO rework_technology (ReworkPlan,ReworkProductId) VALUES('$workPlan',$reworkProductId);";
        return $this->conn->query($sql);
    }

    /**
     * 获取审核产品
     * @param $reworkDate
     * @param $companyId
     * @param $typeId
     * @return array|null
     */
    public function getReworkCheckProduct($reworkDate, $companyId, $typeId)
    {
        $searchRows = "";
        if (!is_null($companyId)) {
            $searchRows .= " AND R.CompanyId = $companyId ";
        }
        if (!is_null($typeId)) {
            $searchRows .= " AND R.PTypeId = $typeId";
        }
        if (!is_null($reworkDate)) {
            $searchRows .= " AND R.ReworkDate ='$reworkDate'";
        }

        $sql = "SELECT R.Id, R.ReworkDate,IFNULL(C.Forshort,'未知项目')  AS Forshort,T.TypeName,P.cName,R.InvoicePath,RC.Status,RC.RejectAnalysis,RC.Estate,R.Operator
FROM rework_product R 
LEFT JOIN  trade_object C ON C.CompanyId=R.CompanyId
LEFT JOIN producttype T ON T.TypeId = R.PTypeId
LEFT JOIN productdata P On P.ProductId = R.ProductId
LEFT JOIN rework_check RC ON R.Id = RC.ReworkProductId WHERE 1 $searchRows";
        return $this->format($sql);
    }

    /**
     * 提交审核
     * @param $rejectReason
     * @param $reworkProductId
     * @param $status 退货状态：0：返修；1：报废
     * @return bool|mysqli_result
     */
    public function insertReworkCheck($reworkProductId, $status)
    {
        $sql = "INSERT INTO rework_check (ReworkProductId,Status) VALUES($reworkProductId,$status);";
        return $this->conn->query($sql);
    }

    /**
     * 审核
     * @param $reworkProductId
     * @param $eState
     * @param $rejectAnalysis
     * @return bool|mysqli_result
     */
    public function updateReworkCheck($reworkProductId, $eState, $rejectAnalysis)
    {
        $sql = "UPDATE rework_check SET Estate=$eState ,RejectAnalysis='$rejectAnalysis' WHERE ReworkProductId=$reworkProductId";
        return $this->conn->query($sql);
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
