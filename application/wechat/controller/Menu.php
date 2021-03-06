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

namespace app\wechat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\ToolsService;
use service\WechatService;
use think\Db;

/**
 * 微信菜单管理
 * Class Menu
 * @package app\wechat\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/03/27 14:43
 */
class Menu extends BasicAdmin
{

    /**
     * 指定当前页面标题
     * @var string
     */
    public $title = '微信菜单定制';

    /**
     * 指定默认操作的数据表
     * @var string
     */
    public $table = 'WechatMenu';

    /**
     * 微信菜单的类型
     * @var array
     */
    protected $menu_type = array(
        'view' => '跳转URL',
        'click' => '点击推事件',
        'scancode_push' => '扫码推事件',
        'scancode_waitmsg' => '扫码推事件且弹出“消息接收中”提示框',
        'pic_sysphoto' => '弹出系统拍照发图',
        'pic_photo_or_album' => '弹出拍照或者相册发图',
        'pic_weixin' => '弹出微信相册发图器',
        'location_select' => '弹出地理位置选择器',
    );

    /**
     * 显示列表操作
     */
    public function index($type=0)
    {
        $wechat = &load_wechat("User");
        if (WechatService::syncFansTags()) {
            LogService::write('微信管理', '菜单管理列表同步全部微信粉丝标签成功');
        }
        $tag_list = Db::name('WechatFansTags')->where('appid', $wechat->appid)->order('id asc')->select();
        $this->assign('tag_list', $tag_list);
        $this->assign('appid', $wechat->appid);
        $this->assign('type', $type);
        return parent::_list(Db::name($this->table)->where('appid', $wechat->appid)->where('tag_id',$type), false, true);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        $data = ToolsService::arr2tree($data, 'index', 'pindex');
    }

    /**
     * 微信菜单编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $wechat = &load_wechat("User");
            $post = $this->request->post();
            if(!isset($post['data'])){
                $data = [];
            }else{
                $data = $post['data'];
            }
            $tag = $post['type'];
            if (empty($data)) {
                if($tag == '0'){
                    Db::name($this->table)->where('appid', $wechat->appid)->delete();
                    load_wechat('Menu')->deleteMenu();
                    $this->success('删除并取消全部微信菜单成功！', '');
                }else{
                    $menu = Db::name($this->table)->where('appid', $wechat->appid)->where('tag_id',$tag)->find();
                    load_wechat('Menu')->deleteCondMenu($menu['menuid']);
                    Db::name($this->table)->where('appid', $wechat->appid)->where('tag_id',$tag)->delete();
                    $this->success('删除并取消标签微信菜单成功！', '');
                }
            }
            foreach ($data as &$vo) {
                if (isset($vo['content'])) {
                    $vo['content'] = str_replace('"', "'", $vo['content']);
                }
            }
            if (Db::name($this->table)->where('appid', $wechat->appid)->where('tag_id',$tag)->delete() !== false && Db::name($this->table)->insertAll($data) !== false) {
                $result = $this->_push($tag);
                if ($result['status']) {
                    LogService::write('微信管理', '发布微信菜单成功');
                    $this->success('保存发布菜单成功！', '');
                }
                $this->error('菜单发布失败，' . $result['errmsg']);
            }
            $this->error('保存发布菜单失败！');
        }
    }

    /**
     * 取消菜单
     */
    public function cancel()
    {
        $wehcat = &load_wechat('Menu');
        if (false !== $wehcat->deleteMenu()) {
            $this->success('菜单取消成功，重新关注可立即生效！', '');
        }
        $this->error('菜单取消失败，' . $wehcat->errMsg);
    }

    /**
     * 菜单推送
     */
    protected function _push($tag = 0)
    {
        $wechat = &load_wechat("User");
        $result = Db::name($this->table)
            ->field('id,index,pindex,name,type,content')
            ->where('status', '1')
            ->where('appid', $wechat->appid)
            ->where('tag_id', $tag)
            ->order('convert(sort) ASC,id ASC')//mysql和sqlsever差异  sort
            ->select();
        foreach ($result as &$row) {
            empty($row['content']) && $row['content'] = uniqid();
            switch ($row['type']) {
                case 'miniprogram':
                    list($row['appid'], $row['url'], $row['pagepath']) = explode(',', $row['content'] . ',,');
                    break;
                case 'view':
                    $row['url'] = preg_match('#^(\w+:)?//#i', $row['content']) ? $row['content'] : url($row['content'], '', true, true);
                    break;
                case 'event':
                    if (isset($this->menu_type[$row['content']])) {
                        $row['type'] = $row['content'];
                        $row['key'] = "wechat_menu#id#{$row['id']}";
                    }
                    break;
                case 'media_id':
                    $row['media_id'] = $row['content'];
                    break;
                default :
                    (!in_array($row['type'], $this->menu_type)) && $row['type'] = 'click';
                    $row['key'] = "wechat_menu#id#{$row['id']}";
            }
            unset($row['content']);
        }
        $menus = ToolsService::arr2tree($result, 'index', 'pindex', 'sub_button');
        //去除无效的字段
        foreach ($menus as &$menu) {
            unset($menu['index'], $menu['pindex'], $menu['id']);
            if (empty($menu['sub_button'])) {
                continue;
            }
            foreach ($menu['sub_button'] as &$submenu) {
                unset($submenu['index'], $submenu['pindex'], $submenu['id']);
            }
            unset($menu['type']);
        }
        $wechat = &load_wechat('Menu');
        if($tag == '0'){
            if (false !== $wechat->createMenu(['button' => $menus])) {
                return array('status' => true, 'errmsg' => '');
            }
        }else{
            $res = $wechat->createCondMenu(['button' => $menus,'matchrule'=>['tag_id'=>$tag]]);
            if (false !== $res) {
                Db::name($this->table)->where('appid', $wechat->appid)->where('tag_id',$tag)->update(['menuid'=>$res]);
                return array('status' => true, 'errmsg' => '');
            }
        }
        return array('status' => false, 'errmsg' => $wechat->errMsg);
    }

}
