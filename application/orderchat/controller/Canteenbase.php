<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class CanteenBase extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'canteen_base_info';

    /**
     * 餐厅列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '餐厅列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['company_id' => session('user.company_id')]);
        // 应用搜索条件
        foreach (['canteen_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 餐厅添加
     */
    public function add()
    {
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'canteen', 'CANT', 0]);
            $extendData['canteen_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
        }
        LogService::write('订餐管理', '执行餐厅添加操作');
        return $this->_form($this->table, 'form', 'canteen_no', '', $extendData);
    }

    /**
     * 餐厅编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行餐厅编辑操作');
        return $this->_form($this->table, 'form', 'canteen_no');
    }


    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('canteen_no', $data['canteen_no'])->find()) {
                unset($data['canteen_name']);
            } elseif (Db::name($this->table)->where('company_id', session('user.company_id'))->where('canteen_name', $data['canteen_name'])->find()) {
                $this->error('餐厅名称已经存在，请使用其它名称！');
            }
        }
    }

    /**
     * 删除餐厅
     */
    public function del()
    {
        LogService::write('订餐管理', '执行餐厅删除操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table, $where, 'canteen_no')) {
            $this->success("餐厅删除成功！", '');
        }
        $this->error("餐厅删除失败，请稍候再试！");
    }


}
