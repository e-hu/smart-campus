<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\DataService;
use service\LogService;
use think\Db;

/**
 * 菜品推送管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Cookpush extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_base_info';

    /**
     * 菜品推送列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '菜品推送管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
//        $db = Db::name($this->table)
//            ->alias('a')
//            ->field('a.*,cookbook_typename,ISNULL(t.company_name,company_list.company_name) as company_name,CASE WHEN ISNULL(t.company_id,\'0\') = \'0\' THEN \'0\' ELSE t.company_id END AS adc')
//            ->join('cookbook_type', 'a.cookbook_type = cookbook_type.cookbook_typeid and a.company_id = cookbook_type.company_id','left')
//            ->join('company_list', 'a.company_id = company_list.company_id','left')
//            ->join('(select cookbook_base_info.*,company_list.company_name from cookbook_base_info ,company_list where  cookbook_base_info.company_id = company_list.company_id and cookbook_base_info.company_id = \'S2017112900005\') t','a.cookbook_no = t.cookbook_no','left')
//            ->where(['a.company_id'=>'0']);

        $db = Db::name($this->table)
            ->alias('a')
            ->field('a.*,cookbook_typename')
            ->join('cookbook_type', 'a.cookbook_type = cookbook_type.cookbook_typeid and a.company_id = cookbook_type.company_id', 'left')
            ->where(['a.company_id' => '0']);

        // 应用搜索条件
        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('a.cookbook_name', 'like', "%{$get[$key]}%");
            }
        }

        if (isset($get['company_id']) && $get['company_id'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->field('ISNULL(t.company_name,company_list.company_name) as company_name , CASE WHEN ISNULL(t.company_id,\'0\') = \'0\' THEN \'0\' ELSE t.company_id END AS status')
                ->join('company_list', 'a.company_id = company_list.company_id', 'left')
                ->join('(select cookbook_base_info.*,company_list.company_name from cookbook_base_info ,company_list where  cookbook_base_info.company_id = company_list.company_id and cookbook_base_info.company_id =  :company_id) t', 'a.cookbook_no = t.cookbook_no', 'left')
                ->bind(['company_id' => $get['company_id']]);
        }

        if (isset($get['tag']) && $get['tag'] !== '') {
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where("',' +''+a.cookbook_type+''+',' like :tag", ['tag' => "%,{$get['tag']},%"]);
        }

        if (isset($get['status']) && $get['status'] !== '') {
            if(empty($get['company_id'])){
                $this->error("请先选择目标公司，稍候再试！");
            }
            //$db->where("concat(',',tagid_list,',') like :tag", ['tag' => "%,{$get['tag']},%"]);   //mysql存在contcat内置函数
            $db->where(['CASE WHEN ISNULL(t.company_id,\'0\') = \'0\' THEN \'0\' ELSE t.company_id END' => $get['status']]);
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
        $tags = Db::name('cookbook_type')->where('company_id', session('company_id'))->column('cookbook_typeid,cookbook_typename');
        $company_lists = Db::name('company_list')->where('status', 1)->column('company_id,Company_Name');
        $this->assign('tags', $tags);
        $this->assign('company_lists', $company_lists);
    }

    /**
     * 菜品推送禁用
     */
    public function forbid()
    {
        LogService::write('订餐管理', '执行菜品推送禁用操作');
        if (empty($_GET['company_id']))
            $this->error("推送公司不存在，请稍候再试！");
        if (DataService::update($this->table, ['company_id' => $_GET['company_id'], 'source_flag' => '2'], 'cookbook_no')) {
            $this->success("菜品推送禁用成功！", '');
        }
        $this->error("菜品推送禁用失败，请稍候再试！");
    }

    /**
     * 菜品推送启用
     */
    public function resume()
    {
        LogService::write('订餐管理', '执行菜品推送启用操作');
        if (empty($_GET['company_id']))
            $this->error("推送公司不存在，请稍候再试！");
        $data = Db::name($this->table)->where(['company_id' => session('company_id'), 'cookbook_no' => $_GET['cookbook_no']])->find();
        $data['source_flag'] = '2';
        $data['company_id'] = $_GET['company_id'];
        unset($data['ROW_NUMBER']);
        if (DataService::save($this->table, $data, 'cookbook_no',['company_id' => $_GET['company_id']])) {
            $this->success("菜品推送启用成功！", '');
        }
        $this->error("菜品推送启用失败，请稍候再试！");
    }

}
