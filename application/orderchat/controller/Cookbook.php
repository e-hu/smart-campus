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
class CookBook extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_base_info';

    /**
     * 菜品套餐列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '菜单列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->field('cookbook_base_info.*,cookbook_typename,meal_name')
            ->join('cookbook_type', 'cookbook_base_info.cookbook_type = cookbook_type.cookbook_typeid','left')
            ->join('cookbook_meal_type', 'cookbook_base_info.meal_id = cookbook_meal_type.meal_id','left')
            ->where('cookbook_base_info.company_id',session('user.company_id'))->order('cookbook_no desc');
        // 应用搜索条件
        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            $db->where('cookbook_typeid',$get['tag']);
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 列表数据处理
     * @param type $list
     */
    protected function _data_filter(&$list) {
        $tags = Db::name('cookbook_type')->where('company_id', session('company_id'))->column('cookbook_typeid,cookbook_typename');
        $this->assign('tags', $tags);
    }

    /**
     * 菜品套餐添加
     */
    public function add() {
        LogService::write('订餐管理', '执行菜品套餐添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'cookbook', 'COBOK', 0]);
            $extendData['cookbook_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
        }
        return $this->_form($this->table, 'form','cookbook_no',['company_id'=> session('user.company_id')],$extendData);
    }

    /**
     * 菜品套餐编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行菜品套餐编辑操作');
        return $this->_form($this->table, 'form','cookbook_no',['company_id'=> session('user.company_id')]);
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('cookbook_no', $data['cookbook_no'])->find()) {
//                unset($data['cookbook_name']);
            } elseif (Db::name($this->table)->where('company_id', session('user.company_id'))->where('cookbook_name', $data['cookbook_name'])->find()) {
                $this->error('菜品套餐名称已经存在，请使用其它名称！');
            }
        }else{
            $this->assign('cookbook_types', Db::name('cookbook_type')->where('company_id', session('user.company_id'))->select());  //菜品套餐类别列表
            $this->assign('cookbook_meal_types', Db::name('cookbook_meal_type')->where('company_id', session('user.company_id'))->select());  //套餐列表
        }
    }

    /**
     * 删除菜品套餐
     */
    public function del() {
        LogService::write('订餐管理', '执行删除菜品套餐操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'cookbook_no')) {
            $this->success("菜品套餐删除成功！", '');
        }
        $this->error("菜品套餐删除失败，请稍候再试！");
    }

    /**
     * 菜品套餐禁用
     */
    public function forbid() {
        LogService::write('订餐管理', '执行菜品套餐禁用操作');
        if (DataService::update($this->table)) {
            $this->success("菜品套餐禁用成功！", '');
        }
        $this->error("菜品套餐禁用失败，请稍候再试！");
    }

    /**
     * 菜品套餐启用
     */
    public function resume() {
        LogService::write('订餐管理', '执行菜品套餐启用操作');
        if (DataService::update($this->table)) {
            $this->success("菜品套餐启用成功！", '');
        }
        $this->error("菜品套餐启用失败，请稍候再试！");
    }


}
