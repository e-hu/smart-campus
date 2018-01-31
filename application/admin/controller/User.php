<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class User extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'system_user';

    /**
     * 用户列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '系统用户管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        if (session('user.company_id') == '0') {  //超级管理员显示自己创建的账户 ,不显示自己的账户
            $db = Db::name($this->table)->alias('s')
                ->field('system_user.*,Company_Name')
                ->join('Company_List c', 's.company_id = c.company_id', 'left')
                ->where('system_user.is_deleted', '0')->where('system_user.create_by', session('user.id'))->where('system_user.id', 'neq', session('user.id'));

        } else {  //公司账户显示自己公司ID的账户，不显示自己的账户
            $db = Db::name($this->table)->alias('s')
                ->field('system_user.*,Company_Name')
                ->join('Company_List c', 's.company_id = c.company_id', 'left')
                ->where('system_user.is_deleted', '0')
                ->where(['system_user.company_id' => session('user.company_id'),'system_user.create_by'=>session('user.id')])
                ->where('system_user.id', 'neq', session('user.id'));
        }

        // 应用搜索条件
        foreach (['username', 'phone'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 授权管理
     * @return array|string
     */
    public function auth()
    {
        return $this->_form($this->table, 'auth');
    }

    /**
     * 用户添加
     */
    public function add()
    {
        $extendData = [];
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if ($data['password']) {
                $extendData['password'] = md5($data['password']);
                $extendData['create_by'] = session('user.id');
                if (!empty($data['company_id'])) {
                    $extendData['company_id'] = $data['company_id'];
                } else {
                    $extendData['company_id'] = session('user.company_id');
                }
            }
        }
        return $this->_form($this->table, 'form', '', '', $extendData);
    }

    /**
     * 用户编辑
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 用户密码修改
     */
    public function pass()
    {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止操作！');
        }
        if ($this->request->isGet()) {
            $this->assign('verify', false);
            return $this->_form($this->table, 'pass');
        }
        $data = $this->request->post();
        if ($data['password'] !== $data['repassword']) {
            $this->error('两次输入的密码不一致！');
        }
        if (DataService::save($this->table, ['id' => $data['id'], 'password' => md5($data['password'])], 'id')) {
            $this->success('密码修改成功，下次请使用新密码登录！', '');
        }
        $this->error('密码修改失败，请稍候再试！');
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            if (isset($data['authorize']) && is_array($data['authorize'])) {
                $data['authorize'] = join(',', $data['authorize']);
            }else{
                $data['authorize'] = '';
            }
            if (isset($data['id'])) {
                unset($data['username']);
            } elseif (Db::name($this->table)->where(['username'=>$data['username'],'company_id'=>session('company_id')])->find()) {
                $this->error('用户账号已经存在，请使用其它账号！');
            }
            if (isset($data['canteen']) && is_array($data['canteen']) && isset($data['id'])) {
                Db::name('t_user_manager_dept_id')->where(['company_id' => session('user.company_id'), 'u_id' => $data['id'], 'dept_type' => 'canteen'])->delete();
                foreach ($data['canteen'] as $key => $val) {
                    Db::name('t_user_manager_dept_id')->insert(['u_id' => $data['id'], 'dept_id' => $data['canteen'][$key], 'company_id' => session('user.company_id'), 'dept_type' => 'canteen']);
                }
                unset($data['canteen']);
            }elseif(!isset($data['canteen'])&& isset($data['id'])){
                Db::name('t_user_manager_dept_id')->where(['company_id' => session('user.company_id'), 'u_id' => $data['id'], 'dept_type' => 'canteen'])->delete();
            }
        } else {
            $data['authorize'] = explode(',', isset($data['authorize']) ? $data['authorize'] : '');
            $this->assign('authorizes', Db::name('SystemAuth')->where(['company_id' => session('user.company_id')])->select());
            if (session('user.company_id') == '0') {     //只有超级管理员才能选择公司列表
                $this->assign('companys', Db::name('Company_list')->where('status', 1)->select());
            }
            if (session('user.company_id') != '0' && isset($_GET['id'])) {     //不是超级管理员才能选择数据权限列表
                $list = Db::name('t_user_manager_dept_id')->where(['company_id'=>session('user.company_id'),'u_id'=>$_GET['id'],'dept_type'=>'canteen'] )->select();
                $dept_ids = array_column($list, 'dept_id');
                $this->assign('manager', $dept_ids);
                $db = Db::name('canteen_base_info')->where('company_id', session('user.company_id'))->select();
                if(session('user.create_by') != '10001'){
                    $db ->where('exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
                }
                $this->assign('canteens',$db);
            }
        }
    }

    /**
     * 删除用户
     */
    public function del()
    {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止删除！');
        }
        if (DataService::update($this->table)) {
            $this->success("用户删除成功！", '');
        }
        $this->error("用户删除失败，请稍候再试！");
    }

    /**
     * 用户禁用
     */
    public function forbid()
    {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止操作！');
        }
        if (DataService::update($this->table)) {
            $this->success("用户禁用成功！", '');
        }
        $this->error("用户禁用失败，请稍候再试！");
    }

    /**
     * 用户禁用
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            $this->success("用户启用成功！", '');
        }
        $this->error("用户启用失败，请稍候再试！");
    }

}
