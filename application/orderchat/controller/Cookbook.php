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
class CookBook extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_base_info';

    /**
     * 菜品套餐列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '菜单列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->field('cookbook_base_info.*,cookbook_typename,meal_name')
            ->join('cookbook_type', 'cookbook_base_info.cookbook_type = cookbook_type.cookbook_typeid', 'left')
            ->join('cookbook_meal_type', 'cookbook_base_info.meal_id = cookbook_meal_type.meal_id', 'left')
            ->where('cookbook_base_info.company_id', session('user.company_id'))->order('cookbook_no desc');
        // 应用搜索条件
        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        if (isset($get['tag']) && $get['tag'] !== '') {
            $db->where('cookbook_typeid', $get['tag']);
        }
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 列表数据处理
     * @param type $list
     */
    protected function _data_filter(&$list)
    {
//        $tags = Db::name('cookbook_type')->where('company_id', session('user.company_id'))->column('cookbook_typeid,cookbook_typename');
//        $this->assign('tags', $tags);
    }

    /**
     * 菜品套餐添加
     */
    public function add()
    {
        LogService::write('订餐管理', '执行菜品套餐添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'cookbook', 'COBOK', 0]);
            $extendData['cookbook_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
            $cookbook = [];
            foreach ($_POST['cookbook'] as $key => $val) {
                if (!empty($val)) {
                    Db::table('cookbook_base_info_detail')->insert(['cookbook_no' => $extendData['cookbook_no'], 'company_id' => session('user.company_id'), 'cookbook_list_no' => $val]);
                    $info = Db::table('cookbook_base_info_list')->where('cookbook_no', $val)->find();
                    array_push($cookbook, $info['cookbook_name']);
                }
            }
            $extendData['cookbook_info'] = implode("+", $cookbook);
        }
        return $this->_form($this->table, 'form', 'cookbook_no', ['company_id' => session('user.company_id')], $extendData);
    }

    /**
     * 菜品套餐编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行菜品套餐编辑操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $cookbook = [];
            Db::table('cookbook_base_info_detail')->where(['cookbook_no' => $_POST['cookbook_no'], 'company_id' => session('user.company_id')])->delete();
            Db::table('cookbook_base_info')->where(['cookbook_no' => $_POST['cookbook_no'], 'company_id' => session('user.company_id')])->update(['cookbook_info' => '']);
            foreach ($_POST['cookbook'] as $key => $val) {
                if (!empty($val)) {
                    Db::table('cookbook_base_info_detail')->insert(['cookbook_no' => $_POST['cookbook_no'], 'company_id' => session('user.company_id'), 'cookbook_list_no' => $val]);
                    $info = Db::table('cookbook_base_info_list')->where('cookbook_no', $val)->find();
                    array_push($cookbook, $info['cookbook_name']);
                }
            }
            $extendData['cookbook_info'] = implode("+", $cookbook);
        }
        return $this->_form($this->table, 'form', 'cookbook_no', ['company_id' => session('user.company_id')], $extendData);
    }


    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('cookbook_no', $data['cookbook_no'])->find()) {
                unset($data['cookbook_name']);
            }
            unset($data['cookbook']);
        } else {
            $this->assign('cookbook_types', Db::name('cookbook_type')->where('company_id', session('user.company_id'))->select());  //菜品套餐类别列表
            $this->assign('cookbook_meal_types', Db::name('cookbook_meal_type')->where('company_id', session('user.company_id'))->select());  //套餐列表
            $this->assign('cookbook_list', Db::name('cookbook_base_info_list')->where('company_id', session('user.company_id'))->select());  //菜品列表
            if (!empty($_GET['cookbook_no'])) {
                $list = Db::name('cookbook_base_info_detail')
                    ->alias('a')
                    ->join('cookbook_base_info_list b', 'b.cookbook_no = a.cookbook_no', 'left')
                    ->where('a.cookbook_no', $_GET['cookbook_no'])
                    ->where('a.company_id', session('user.company_id'))
                    ->field('a.cookbook_list_no,b.cookbook_name')
                    ->select();
                $this->assign('cookbook_list_choice', $list);  //被选中菜品列表
            }
        }
    }

    /**
     * 删除菜品套餐
     */
    public function del()
    {
        LogService::write('订餐管理', '执行删除菜品套餐操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table, $where, 'cookbook_no')) {
            $this->success("菜品套餐删除成功！", '');
        }
        $this->error("菜品套餐删除失败，请稍候再试！");
    }


    /**
     * 菜品套餐启用
     */
    public function copy()
    {
        LogService::write('订餐管理', '执行菜品套餐复制操作');
        if (!empty($_POST['id'])) {
            $cookbook_info = Db::table($this->table)->where(['cookbook_no' => $_POST['id'], 'company_id' => session('user.company_id')])->find();
            $cookbook_detail = Db::table('cookbook_base_info_detail')->where(['cookbook_no' => $_POST['id'], 'company_id' => session('user.company_id')])->select();
            unset($cookbook_info['cookbook_no']);
            unset($cookbook_info['ROW_NUMBER']);
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'cookbook', 'COBOK', 0]);
            $cookbook_info['cookbook_no'] = $data[0][0]['id'];
            foreach ($cookbook_detail as $val){
                unset($val['ROW_NUMBER']);
                unset($val['id']);
                $val['cookbook_no'] = $cookbook_info['cookbook_no'];
                Db::table('cookbook_base_info_detail')->insert($val);
            }
            Db::table($this->table)->insert($cookbook_info);
            $this->success("菜品套餐复制成功！", '');
        }
        $this->error("菜品套餐复制失败，请稍候再试！");
    }


}
