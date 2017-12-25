<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\DataService;
use service\LogService;
use think\Db;

/**
 * 部门信息管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Department extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'dept_info';

    /**
     * 部门列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '部门信息管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->where('company_id', session('user.company_id'))->where('parent_dept_no', 'Null');
        // 应用搜索条件ss
        foreach (['dept_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 部门添加
     */
    public function add()
    {
        LogService::write('订餐管理', '执行部门添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'department', 'DEPT', 0]);
            $extendData['dept_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
            $get = $this->request->get();
            if (!empty($get['parent_dept_no'])) {
                $extendData['parent_dept_no'] = $get['parent_dept_no'];
            }
        }
        return $this->_form($this->table, 'form', 'dept_no', '', $extendData);
    }

    /**
     * 部门编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行部门编辑操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $data = $this->request->get();
        }
        return $this->_form($this->table, 'form', 'dept_no');
    }

    /**
     * 下级部门查看
     */
    public function view()
    {
        LogService::write('订餐管理', '执行下级部门查看操作');
        if ($this->request->isGet()) {
            $data = [];
            $get = $this->request->get();
            $db = Db::name($this->table)
                ->where('parent_dept_no',$get['parent_dept_no'])
                ->select();
            $data['list'] = $db;
            $this->assign('list', $data['list']);
            return $this->_form($this->table, 'viewform');
        }
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('dept_no', $data['dept_no'])->find()) {
                unset($data['dept_name']);
            } elseif (isset($data['dept_name']) and Db::name($this->table)->where('dept_name', $data['dept_name'])->where('company_id', session('user.company_id'))->find()) {
                $this->error('部门名称已经存在，请使用其它部门名称！');
            }
        }
    }

    /**
     * 删除部门
     */
    public function del()
    {
        LogService::write('订餐管理', '执行删除部门操作');
        if (DataService::update($this->table,'','dept_no')) {
            $this->success("部门删除成功！", '');
        }
        $this->error("部门删除失败，请稍候再试！");
    }

}
