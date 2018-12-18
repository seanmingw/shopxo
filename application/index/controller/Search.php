<?php
namespace app\index\controller;

use app\service\SearchService;
use app\service\BrandService;

/**
 * 搜索
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Search extends Common
{
    private $params;

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

        // 品牌id
        $this->params['brand_id'] = intval(input('brand_id', 0));

        // 分类id
        $this->params['category_id'] = intval(input('category_id', 0));

        // 筛选价格id
        $this->params['screening_price_id'] = intval(input('screening_price_id', 0));

        // 搜索关键字
        $this->params['keywords'] = trim(input('keywords'));

        // 排序方式
        $this->params['order_by_field'] = input('order_by_field', 'default');
        $this->params['order_by_type'] = input('order_by_type', 'desc');

        // 用户信息
        $this->params['user_id'] = isset($this->user['id']) ? $this->user['id'] : 0;
    }
    
    /**
     * 首页
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2017-02-22T16:50:32+0800
     */
    public function Index()
    {
        if(input('post.'))
        {
            return redirect('index/search/index', ['keywords'=>$this->params['keywords']]);
        } else {
            // 品牌列表
            $this->assign('brand_list', BrandService::CategoryBrandList(['category_id'=>$this->params['category_id']]));

            // 商品分类
            $this->assign('category_list', SearchService::GoodsCategoryList(['category_id'=>$this->params['category_id']]));

            // 筛选价格区间
            $this->assign('screening_price_list', SearchService::ScreeningPriceList(['field'=>'id,name']));

            // 参数
            $this->assign('params', $this->params);

            return $this->fetch();
        }
    }

    /**
     * 获取商品列表
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2018-09-07
     * @desc    description
     */
    public function GoodsList()
    {        
        // 获取商品列表
        $data = SearchService::GoodsList($this->params);
        if(empty($data['data']))
        {
            $msg = '没有相关数据';
            $code = -100;
        } else {
            $msg = '操作成功';
            $code = 0;
        }

        // 搜索记录
        SearchService::SearchAdd($this->params);

        // 返回
        return json(DataReturn($msg, $code, $data));
    }
}
?>