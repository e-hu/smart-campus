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
class MealType extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_meal_type';

    /**
     * 套餐列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '套餐列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['company_id'=>session('user.company_id')]);
        // 应用搜索条件
        foreach (['meal_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 套餐添加
     */
    public function add() {
        LogService::write('订餐管理', '执行套餐添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'cookbook_meal_type', 'CMT', 0]);
            $extendData['meal_id'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
        }
        return $this->_form($this->table, 'form','meal_id','',$extendData);
    }

    /**
     * 套餐编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行套餐编辑操作');
        return $this->_form($this->table, 'form','meal_id');
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if(!isset($data['meal_flag'])){
                $this->error('计费模式必选！');
            }
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('meal_id', $data['meal_id'])->find()) {
                unset($data['meal_name']);
            } elseif (isset($data['meal_name']) and Db::name($this->table)->where('meal_name', $data['meal_name'])->where('company_id', session('user.company_id'))->find()) {
                $this->error('套餐名称已经存在，请使用其它名称！');
            }
        }
    }

    /**
     * 删除套餐
     */
    public function del() {
        LogService::write('订餐管理', '执行删除套餐操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'meal_id')) {
            $this->success("套餐删除成功！", '');
        }
        $this->error("套餐删除失败，请稍候再试！");
    }

}
