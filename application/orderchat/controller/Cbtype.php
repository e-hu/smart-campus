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
class CbType extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_type';

    /**
     * 菜品分类列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '菜品分类列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['company_id'=>session('user.company_id')]);
        // 应用搜索条件
        foreach (['cookbook_typename'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 菜品分类添加
     */
    public function add() {
        LogService::write('订餐管理', '执行菜品分类添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'cookbook_type', 'TYPE', 0]);
            $extendData['cookbook_typeid'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
        }
        return $this->_form($this->table, 'form','cookbook_typeid','',$extendData);
    }

    /**
     * 菜品分类编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行菜品分类编辑操作');
        return $this->_form($this->table, 'form','cookbook_typeid');
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('cookbook_typeid', $data['cookbook_typeid'])->find()) {
                unset($data['sale_window_name']);
            } elseif (Db::name($this->table)->where('company_id', session('user.company_id'))->where('cookbook_typename', $data['cookbook_typename'])->find()) {
                $this->error('菜品分类已经存在，请使用其它名称！');
            }
        }
    }

    /**
     * 删除菜品分类
     */
    public function del() {
        LogService::write('订餐管理', '执行删除菜品分类操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'cookbook_typeid')) {
            $this->success("菜品分类删除成功！", '');
        }
        $this->error("菜品分类删除失败，请稍候再试！");
    }

}
