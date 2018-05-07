<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\DataService;
use service\LogService;
use think\Db;

/**
 * 推荐菜谱管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Cbpushed extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'cookbook_recommend_detail';

    /**
     * 推荐菜谱列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '推荐菜谱列表管理';
        $this->assign('title', $this->title);
        $tags = Db::name('dinner_base_info')->where('company_id', session('company_id'))->column('dinner_flag,dinner_name');
        $weeks = Db::name('week_day_list')->column('id,week_num,start_datetime,end_datetime');
        $this->assign('tags', $tags);
        $this->assign('weeks', $weeks);

        $list = [];
        // 应用搜索条件
        $get = $this->request->get();
        if ($this->request->get() && isset($_GET['dinner_flag']) && isset($_GET['week_id'])) {
            if ( $get['dinner_flag'] == '' || $get['week_id'] == '') {
                $this->error('条件不正确');
            }
            $weekinfo = Db::name('week_day_list')->where(['id'=>$get['week_id']])->find();
            $sqlstr = "exec [up_create_recommend_detail] ?,?,?,?,?";
            $return = Db::query($sqlstr, [$weekinfo['year'], $weekinfo['week_num'], session('company_id'), $_GET['dinner_flag'],'2']);
            if(empty($return[0])){
                $this->error('数据不存在');
            }
            $list = $return[0];
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 推荐菜谱禁用
     */
    public function forbid()
    {
        LogService::write('订餐管理', '执行推荐菜谱禁用操作');
        if (empty($_GET['recommend_index'])||empty($_GET['start_datetime']))
            $this->error("条件错误，请稍候再试！");
        if (Db::name('cookbook_recommend_detail')->where(['company_id' => session('company_id'), 'recommend_index' => $_GET['recommend_index'],'start_datetime'=> $_GET['start_datetime']])->update(['choose_flag'=>'0'])) {
            $this->success("推荐菜谱禁用成功！", '');
        }
        $this->error("推荐菜谱禁用失败，请稍候再试！");
    }

    /**
     * 推荐菜谱启用
     */
    public function resume()
    {
        LogService::write('订餐管理', '执行推荐菜谱启用操作');
        if (empty($_GET['recommend_index'])||empty($_GET['start_datetime']))
            $this->error("条件错误，请稍候再试！");
        if (Db::name('cookbook_recommend_detail')->where(['company_id' => session('company_id'), 'recommend_index' => $_GET['recommend_index'],'start_datetime'=> $_GET['start_datetime']])->update(['choose_flag'=>'1'])) {
            $this->success("推荐菜谱启用成功！", '');
        }
        $this->error("推荐菜谱启用失败，请稍候再试！");
    }

}
