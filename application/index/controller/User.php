<?php
namespace app\index\controller;

use app\service\OrderService;
use app\service\GoodsService;
use app\service\UserService;
use app\service\BuyService;

/**
 * 用户
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2017-03-02T22:48:35+0800
 */
class User extends Common
{
    /**
     * 构造方法
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-11-30
     * @desc    description
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [GetrefererUrl 获取上一个页面地址]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-09T15:46:16+0800
     */
    private function GetrefererUrl()
    {
        // 上一个页面, 空则用户中心
        $referer_url = empty($_SERVER['HTTP_REFERER']) ? url('index/user/index') : $_SERVER['HTTP_REFERER'];
        if(!empty($_SERVER['HTTP_REFERER']))
        {
            $all = ['logininfo', 'reginfo', 'smsreginfo', 'emailreginfo', 'forgetpwdinfo'];
            foreach($all as $v)
            {
                if(strpos($_SERVER['HTTP_REFERER'], $v) !== false)
                {
                    $referer_url = url('index/user/index');
                    break;
                }
            }
        }
        return $referer_url;
    }

    /**
     * [Index 用户中心]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-02T22:48:35+0800
     */
    public function Index()
    {
        // 登录校验
        $this->Is_Login();
        
        // 订单总数
        $where = ['user_id'=>$this->user['id'], 'is_delete_time'=>0, 'user_is_delete_time'=>0];
        $this->assign('user_order_count', OrderService::OrderTotal($where));

        // 商品收藏总数
        $where = ['user_id'=>$this->user['id']];
        $this->assign('user_goods_favor_count', GoodsService::GoodsFavorTotal($where));

        // 商品浏览总数
        $where = ['user_id'=>$this->user['id']];
        $this->assign('user_goods_browse_count', GoodsService::GoodsBrowseTotal($where));

        // 用户订单状态
        $user_order_status = OrderService::OrderStatusStepTotal(['user_type'=>'user', 'user'=>$this->user, 'is_comments'=>1]);
        $this->assign('user_order_status', $user_order_status['data']);

        // 获取进行中的订单列表
        $params = array_merge($_POST, $_GET);
        $params['user'] = $this->user;
        $params['is_more'] = 1;
        $params['status'] = [1,2,3,4];
        $params['is_comments'] = 0;
        $params['user_type'] = 'user';
        $where = OrderService::OrderListWhere($params);
        $order_params = array(
            'limit_start'   => 0,
            'limit_number'  => 3,
            'where'         => $where,
        );
        $order = OrderService::OrderList($order_params);
        $this->assign('order_list', $order['data']);

        // 获取购物车
        $cart_list = BuyService::CartList(['user'=>$this->user]);
        $this->assign('cart_list', $cart_list['data']);

        // 收藏商品
        $params = array_merge($_POST, $_GET);
        $params['user'] = $this->user;
        $where = GoodsService::UserGoodsFavorListWhere($params);
        $favor_params = array(
            'limit_start'   => 0,
            'limit_number'  => 8,
            'where'         => $where,
        );
        $favor = GoodsService::GoodsFavorList($favor_params);
        $this->assign('goods_favor_list', $favor['data']);

        // 我的足迹
        $params = array_merge($_POST, $_GET);
        $params['user'] = $this->user;
        $where = GoodsService::UserGoodsBrowseListWhere($params);
        $browse_params = array(
            'limit_start'   => 0,
            'limit_number'  => 6,
            'where'         => $where,
        );
        $data = GoodsService::GoodsBrowseList($browse_params);
        $this->assign('goods_browse_list', $data['data']);

        // 用户中心公告
        $this->assign('common_user_center_notice', MyC('common_user_center_notice'));

        return $this->fetch();
    }

    /**
     * [ForgetPwdInfo 密码找回]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-10T17:06:47+0800
     */
    public function ForgetPwdInfo()
    {
        if(empty($this->user))
        {
            return $this->fetch();
        } else {
            $this->assign('msg', '已经登录了，如要重置密码，请先退出当前账户');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * [RegInfo 用户注册页面]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-02T22:48:35+0800
     */
    public function RegInfo()
    {
        $reg_all = MyC('home_user_reg_state');
        if(!empty($reg_all))
        {
            if(empty($this->user))
            {
                return $this->fetch();
            } else {
                $this->assign('msg', '已经登录了，如要注册新账户，请先退出当前账户');
                return $this->fetch('public/tips_error');
            }
        } else {
            $this->assign('msg', '暂时关闭用户注册');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * [EmailRegInfo 用户注册页面-邮箱]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-10T12:18:17+0800
     */
    public function EmailRegInfo()
    {
        if(in_array('email', MyC('home_user_reg_state')))
        {
            if(empty($this->user))
            {
                $this->assign('referer_url', $this->GetrefererUrl());
                return $this->fetch();
            } else {
                $this->assign('msg', '已经登录了，如要注册新账户，请先退出当前账户');
                return $this->fetch('public/tips_error');
            }
        } else {
            $this->assign('msg', '暂时关闭邮箱注册');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * [SmsRegInfo 用户注册页面-短信]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-02T22:48:35+0800
     */
    public function SmsRegInfo()
    {
        if(in_array('sms', MyC('home_user_reg_state')))
        {
            if(empty($this->user))
            {
                $this->assign('referer_url', $this->GetrefererUrl());
                return $this->fetch();
            } else {
                $this->assign('msg', '已经登录了，如要注册新账户，请先退出当前账户');
                return $this->fetch('public/tips_error');
            }
        } else {
            $this->assign('msg', '暂时关闭短信注册');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * [LoginInfo 用户登录页面]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-02T22:48:35+0800
     */
    public function LoginInfo()
    {
        if(MyC('home_user_login_state') == 1)
        {
            if(empty($this->user))
            {
                $this->assign('referer_url', $this->GetrefererUrl());
                return $this->fetch();
            } else {
                $this->assign('msg', '已经登录了，请勿重复登录');
                return $this->fetch('public/tips_error');
            }
        } else {
            $this->assign('msg', '暂时关闭用户登录');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * modal弹窗登录
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-09-13
     * @desc    description
     */
    public function ModalLoginInfo()
    {
        $this->assign('is_header', 0);
        $this->assign('is_footer', 0);

        if(MyC('home_user_login_state') == 1)
        {
            if(empty($this->user))
            {
                $this->assign('referer_url', $this->GetrefererUrl());
                return $this->fetch();
            } else {
                $this->assign('msg', '已经登录了，请勿重复登录');
                return $this->fetch('public/tips_error');
            }
        } else {
            $this->assign('msg', '暂时关闭用户登录');
            return $this->fetch('public/tips_error');
        }
    }

    /**
     * [Reg 用户注册-数据添加]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-07T00:08:36+0800
     */
    public function Reg()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 调用服务层
        $ret = UserService::Reg(input('post.'));
        return json($ret);
    }

    /**
     * [Login 用户登录]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-09T10:57:31+0800
     */
    public function Login()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 调用服务层
        $ret = UserService::Login(input('post.'));
        return json($ret);
    }

    /**
     * [UserVerifyEntry 用户-验证码显示]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-05T15:10:21+0800
     */
    public function UserVerifyEntry()
    {
        $params = array(
                'width' => 100,
                'height' => 32,
                'key_prefix' => input('type', 'reg'),
            );
        $verify = new \base\Verify($params);
        $verify->Entry();
    }

    /**
     * [RegVerifySend 用户注册-验证码发送]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-05T19:17:10+0800
     */
    public function RegVerifySend()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 调用服务层
        $ret = UserService::RegVerifySend(input('post.'));
        return json($ret);
    }

    /**
     * [ForgetPwdVerifySend 密码找回验证码发送]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-10T17:35:03+0800
     */
    public function ForgetPwdVerifySend()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 调用服务层
        $ret = UserService::ForgetPwdVerifySend(input('post.'));
        return json($ret);
    }

    /**
     * [ForgetPwd 密码找回]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-03-10T17:55:42+0800
     */
    public function ForgetPwd()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 调用服务层
        $ret = UserService::ForgetPwd(input('post.'));
        return json($ret);
    }

    /**
     * [Logout 退出]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-05T14:31:23+0800
     */
    public function Logout()
    {
        session('user', null);
        return redirect(__MY_URL__);
    }

    /**
     * 用户头像上传
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-12-03
     * @desc    description
     */
    public function UserAvatarUpload()
    {
        // 是否ajax请求
        if(!IS_AJAX)
        {
            return $this->error('非法访问');
        }

        // 登录校验
        $this->Is_Login();

        $params = $_POST;
        $params['user'] = $this->user;
        $params['img_field'] = 'file';
        $ret = UserService::UserAvatarUpload($params);
        return json($ret);
    }
}
?>