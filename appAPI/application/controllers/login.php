<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MC_Controller
{
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $bundleIdapp = element('BundleId', $this->input->post(), '');

        if (strpos($bundleIdapp, 'ClientAppFor') !== false || strpos($bundleIdapp, 'NewProducts') !== false) {

            $this->clientLogin();
            return;
        }
        $CompanyId = $CompanyName = '';


        $status = 0;
        $message = "";
        $user = array();
        $this->load->model('LoginUser');
        $query = $this->LoginUser->check_user($this->LoginUser->DataIn, $username, $password);//查询主系统
        $loginSign = 0;
        if ($query->num_rows() > 0) {
            $loginSign = 1;
        }

        //登记设备信息
        $UUID = element('UUID', $this->input->post(), '');
        $Device = element('DeviceModel', $this->input->post(), '');
        if ($UUID != '' && $Device != 'x86_64') {
            $deviceEstate = $this->LoginUser->check_user_uuid($username, $bundleIdapp, $Device, $UUID);
        }

        if ($loginSign > 0) {
            $status = 1;
            $row = $query->row_array();
            $user_number = $row['Number'];
            $user_id = $row['Id'];
            $user_type = $row['uType'];
            $estate = $row['Estate'];

            if ($user_number == '50019' || $user_type == '4') {//陈忆甬
                $query = $this->LoginUser->get_outuser_info($this->LoginUser->DataIn, $user_number);//获取外部人员信息
            } else {
                $dbname = $loginSign == 1 ? $this->LoginUser->DataIn : $this->LoginUser->DataSub;
                $query = $this->LoginUser->get_user_info($dbname, $user_number);//获取内部员工信息
            }

            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $estate = $user_number == '50019' ? $row['Estate'] : $estate;
                if ($estate == 1) {
                    //生成KEY
                    $publickey = $this->config->item('public_key');
                    $this->load->helper('security');
                    $new_key = do_hash($publickey . $user_number . date('Y') . date('W'), 'md5');
                    if ($user_number == '10868') {
                        $new_key = do_hash($publickey . $password . date('Y') . date('W'), 'md5');
                    }

                    $user = array(
                        'user_id' => $user_id,
                        'number' => $user_number,
                        'name' => $row['Name'],
                        'csign' => $row['cSign'] == 5 ? 7 : $row['cSign'],
                        'groupname' => $row['GroupName'],
                        'key' => $new_key,
                        'CompanyName' => $CompanyName,
                        'CompanyId' => $CompanyId
                    );
                }
            }
        } else {
            $message = "帐号或密码错误";
        }

        $data['jsondata'] = array('status' => $status, 'message' => $message, 'user' => $user);

        $this->load->view('output_json', $data);
    }


    public function clientLogin()
    {

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $bundleIdapp = element('BundleId', $this->input->post(), '');
        $CompanyId = $CompanyName = '';
        switch ($bundleIdapp) {

            case "ClientAppForGHC":
                $CompanyId = "100167";
                $CompanyName = "White Diamonds";
                break;
            case "ClientAppForKey":
                $CompanyId = "100103";
                $CompanyName = "Key";
                break;
            case "ClientAppFor4Smarts":
                $CompanyId = "100170";
                $CompanyName = "4smarts";
                break;
            case "ClientAppForInnov8":
                $CompanyId = "100185";
                $CompanyName = "Innov8";
                break;
            default:
                break;
        }

        $status = 0;
        $message = "";
        $user = array();
        $this->load->model('LoginUser');
        $query = $this->LoginUser->check_user($this->LoginUser->DataIn, $username, $password);//查询主系统
        $loginSign = 0;
        if ($query->num_rows() > 0) {
            $loginSign = 1;
        }
        /*

        */

        if ($loginSign > 0) {
            $status = 1;
            $row = $query->row_array();
            $user_number = $row['Number'];
            $user_id = $row['Id'];
            $user_type = $row['uType'];
            $estate = $row['Estate'];

            $publickey = $this->config->item('public_key');
            $this->load->helper('security');
            $new_key = do_hash($publickey . $user_number . date('Y') . date('W'), 'md5');
            $user = array(
                'user_id' => $user_id,
                'number' => $user_number,
                'name' => '',
                'csign' => '',
                'groupname' => '',
                'key' => $new_key,
                'CompanyName' => $CompanyName,
                'CompanyId' => $CompanyId
            );

            if ($user_number == '50019' || $user_type == '4') {//陈忆甬
                $query = $this->LoginUser->get_outuser_info($this->LoginUser->DataIn, $user_number);//获取外部人员信息
            } else {
                $dbname = $loginSign == 1 ? $this->LoginUser->DataIn : $this->LoginUser->DataSub;
                $query = $this->LoginUser->get_user_info($dbname, $user_number);//获取内部员工信息
            }

            if ($query->num_rows() > 0) {
                $row = $query->row_array();
                $estate = $user_number == '50019' ? $row['Estate'] : $estate;
                if ($estate == 1) {
                    //生成KEY
                    $publickey = $this->config->item('public_key');
                    $this->load->helper('security');
                    $new_key = do_hash($publickey . $user_number . date('Y') . date('W'), 'md5');

                    $user = array(
                        'user_id' => $user_id,
                        'number' => $user_number,
                        'name' => $row['Name'],
                        'csign' => $row['cSign'],
                        'groupname' => $row['GroupName'],
                        'key' => $new_key,
                        'CompanyName' => $CompanyName,
                        'CompanyId' => $CompanyId
                    );
                }
            }
        } else {
            $message = "帐号或密码错误";
        }

        $data['jsondata'] = array('status' => $status, 'message' => $message, 'user' => $user);

        $this->load->view('output_json', $data);
    }

    //保存devicetoken
    public function devicetoken()
    {
        $params = $this->input->post();
        $bundleId = element('bundleId', $params, '');
        $userId = element('userId', $params, '');
        $token = element('token', $params, '');

        if ($bundleId != '') {
            $this->load->model('AppPushModel');
            if ($bundleId == "AshCloudApp") {
                $this->AppPushModel->save_push_mainapp($bundleId, $userId, $token);
            } else {
                $this->AppPushModel->save_push_clientapp($bundleId, $userId, $token);
            }
        }
    }
}