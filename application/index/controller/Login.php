<?php


namespace app\index\controller;

use controller\ApiBase;
use think\Db;
use think\Session;

/**
 * 网站入口控制器
 * Class Index
 * @package app\index\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/04/05 10:38
 */
class Login extends ApiBase
{

    protected $checkAuth = false;

    public function _initialize()
    {
        $client_user_info=$this->getClientUserInfo();
        if ($client_user_info) {
            $this->redirect('@index/index');
        }
    }

    /**
     * 登录首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 用户登录
     */
    public function login()
    {
        $username = $this->request->post('username', '', 'trim');
        $password = $this->request->post('password', '', 'trim');
        (empty($username) || strlen($username) < 4) && $this->error('登录账号长度不能少于4位有效字符!');
        (empty($password) || strlen($password) < 4) && $this->error('登录密码长度不能少于4位有效字符!');
        $employee = Db::name('Employee_list')->where(['Emp_MircoMsg_Uid'=>$username])->find();  //用户登录表
        if (empty($employee)) {
            $this->error("登录账号或密码不存在，请重新输入！", '@index/login');
        }
        if ($employee['Emp_Status'] == 0) {
            $this->error("登录账号被禁用，请联系管理员！", '@index/login');
        }
        if ($employee['Emp_MircoMsg_Upwd'] !== md5($password)) {
            $this->error("登录密码与账号不匹配，请重新输入!!", '@index/login');
        }
        Session::set('company_id', $employee['company_id']);
        Session::set('client_user_id', $employee['Emp_Id']);
        $this->success("登录成功！", '@index/index');
    }


}
