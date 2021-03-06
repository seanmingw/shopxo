// 规格弹窗PC显示
function poptit_pc_show()
{
    $(document.body).css("position", "static");
    $('.theme-signin-left').scrollTop(0);
    $('.theme-popover-mask').hide();
    $('.theme-popover').slideDown(0);
}
// 规格弹窗关闭
function poptit_close()
{
    if($(window).width() < 1025)
    {
        $(document.body).css("position", "static");
        $('.theme-signin-left').scrollTop(0);
        $('.theme-popover-mask').hide();
        $('.theme-popover').slideUp(100);
    }
}

/**
 * 购买/加入购物车
 * @author   Devil
 * @blog    http://gong.gg/
 * @version 1.0.0
 * @date    2018-09-13
 * @desc    description
 * @param   {[object]}        e [当前标签对象]
 */
function CartAdd(e)
{
    // 参数
    var type = e.attr('data-type');
    var stock = $('#text_box').val();
    if((stock || 0) <= 0)
    {
        PromptCenter('购买数量有误');
        return false;
    }

    // 属性
    var attr = {};
    var sku_count = $('.sku-items').length;
    if(sku_count > 0)
    {
        var attr_count = $('.sku-line.selected').length;
        if(attr_count < sku_count)
        {
            $('.sku-items').each(function(k, v)
            {
                if($(this).find('.sku-line.selected').length == 0)
                {
                    $(this).addClass('attr-not-active');
                }
            });
            PromptCenter('请选择属性');
            return false;
        } else {
            $('.iteminfo_parameter .sku-items').removeClass('attr-not-active');
            $('.sku-line.selected').each(function(k, v)
            {
                attr[$(this).data('parent-id')] = $(this).data('id');
            });
        }
    }

    // 操作类型
    switch(type)
    {
        // 立即购买
        case 'buy' :
            var $form_buy = $('form.buy-form');
            $form_buy.find('input[name="attr"]').val(JSON.stringify(attr));
            $form_buy.find('input[name="stock"]').val(stock);
            $form_buy.find('button[type="submit"]').trigger('click');
            break;

        // 加入购物车
        case 'cart' :
            // 开启进度条
            $.AMUI.progress.start();

            var $button = e;
            $button.attr('disabled', true);

            // ajax请求
            $.ajax({
                url: e.data('ajax-url'),
                type: 'post',
                dataType: "json",
                timeout: 10000,
                data: {"goods_id": $('.goods-detail').data('id'), "stock": stock, "attr": attr},
                success: function(result)
                {
                    poptit_close();
                    $.AMUI.progress.done();
                    $button.attr('disabled', false);

                    if(result.code == 0)
                    {
                        HomeCartNumberTotalUpdate(parseInt(result.data));
                        PromptCenter(result.msg, 'success');
                    } else {
                        PromptCenter(result.msg);
                    }
                },
                error: function(xhr, type)
                {
                    poptit_close();
                    $.AMUI.progress.done();
                    $button.attr('disabled', false);
                    PromptCenter('网络异常错误');
                }
            });
            break;

        // 默认
        default :
            PromptCenter('操作参数配置有误');
    }
    return true;
}

$(function() {
    // 商品规格选择
    $(".theme-options").each(function()
    {
        $(this).find('ul>li').on('click', function()
        {
            if($(this).hasClass("selected"))
            {
                $(this).removeClass("selected");
            } else {
                $(this).addClass("selected").siblings("li").removeClass("selected");
                $(this).parents('.sku-items').removeClass('attr-not-active');
            }
        });
    });

    // 放大镜初始化
    $(".jqzoom").imagezoom();
    $("#thumblist li a").on('click', function() {
        $(this).parents("li").addClass("tb-selected").siblings().removeClass("tb-selected");
        $(".jqzoom").attr('src', $(this).find("img").attr("mid"));
        $(".jqzoom").attr('rel', $(this).find("img").attr("big"));
    });

    //弹出规格选择
    $('.buy-event').on('click', function() {
        if($(window).width() < 1025) {
            // 是否登录
            if(__user_id__ != 0)
            {
                $(document.body).css("position", "fixed");
                $('.theme-popover-mask').show();
                $('.theme-popover').slideDown(200);

                $('.theme-popover .confirm').attr('data-type', $(this).data('type'));
            }
        } else {
            poptit_pc_show();
        }
    });
    $('.theme-poptit .close, .btn-op .close').on('click', function() {
        poptit_close();
    });

    // 购买
    $('.buy-submit, .cart-submit').on('click', function()
    {
        // 是否登录
        if(__user_id__ != 0)
        {
            if($(window).width() >= 1025)
            {
                CartAdd($(this));
            }
        }
    });
    $('.theme-popover .confirm').on('click', function()
    {
        // 是否登录
        if(__user_id__ != 0)
        {
            if($(window).width() < 1025)
            {
                CartAdd($(this));
            }
        }
    });

    // 收藏
    $('.favor-submit').on('click', function()
    {
        // 是否登录
        if(__user_id__ != 0)
        {
            var $this = $(this);
            // 开启进度条
            $.AMUI.progress.start();

            // ajax请求
            $.ajax({
                url: $(this).data('ajax-url'),
                type: 'post',
                dataType: "json",
                timeout: 10000,
                data: {"id": $('.goods-detail').data('id')},
                success: function(result)
                {
                    poptit_close();
                    $.AMUI.progress.done();

                    if(result.code == 0)
                    {
                        $this.find('.goods-favor-text').text(result.data.text);
                        $this.find('.goods-favor-count').text('('+result.data.count+')');
                        if(result.data.status == 1)
                        {
                            $this.addClass('text-active');
                        } else {
                            $this.removeClass('text-active');
                        }

                        if($(window).width() < 640)
                        {
                            PromptBottom(result.msg, 'success', null, 50);
                        } else {
                            PromptCenter(result.msg, 'success');
                        }
                    } else {
                        if($(window).width() < 640)
                        {
                            PromptBottom(result.msg, null, null, 50);
                        } else {
                            PromptCenter(result.msg);
                        }
                    }
                },
                error: function(xhr, type)
                {
                    poptit_close();
                    $.AMUI.progress.done();
                    if($(window).width() < 640)
                    {
                        PromptBottom('网络异常错误', null, null, 50);
                    } else {
                        PromptCenter('网络异常错误');
                    }
                }
            });
        }
    });

    // 视频
    $('.goods-video-submit-start').on('click', function()
    {
        $('.goods-video-container').removeClass('none').trigger('play');
        $('.goods-video-submit-close').removeClass('none');
        $('.goods-video-submit-start').addClass('none');
    });
    $('.goods-video-submit-close').on('click', function()
    {
        $('.goods-video-container').addClass('none').trigger('pause');
        $('.goods-video-submit-close').addClass('none');
        $('.goods-video-submit-start').removeClass('none');
    });

});

// 购买导航动画显示/隐藏
var temp_scroll = 0;
var scroll_type = -1;
var location_scroll = 0;
var nav_status = 1;
var $buy_nav= $("div.buy-nav");
$(window).scroll(function()
{
    if($(window).width() <= 625)
    {
        var scroll = $(document).scrollTop();
        if(scroll != temp_scroll)
        {
            var temp_scroll_type = (scroll > temp_scroll) ? 1 : 0;
            if(temp_scroll_type != scroll_type)
            {
                scroll_type = temp_scroll_type;
                location_scroll = scroll;
            }

            if(scroll_type == 1)
            {
                if(nav_status == 1 && scroll > location_scroll+200)
                {
                    nav_status = 0;
                    if(!$buy_nav.is(":animated"))
                    {
                        $buy_nav.slideUp(500);
                    }
                }
            } else {
                if(nav_status == 0 && scroll < location_scroll-50)
                {
                    nav_status = 1;
                    if(!$buy_nav.is(":animated"))
                    {
                        $buy_nav.slideDown(500);
                    }
                }
            }
            temp_scroll = scroll;
        }
    }
});

// 浏览器窗口实时事件
$(window).resize(function()
{
    // 规格显示/隐藏处理
    if($(window).width() < 1025)
    {
        poptit_close();
    } else {
        poptit_pc_show();
    }
});

$(document).ready(function() {
    //获得文本框对象
    var t = $("#text_box");
    //初始化数量为1,并失效减
    $('#min').attr('disabled', true);
    //数量增加操作
    $("#add").on('click', function() {
        var stock = parseInt($('.stock-tips .stock').text());
        var number = parseInt(t.val());
        if(number < stock)
        {
            t.val(number + 1)
            if (parseInt(t.val()) > 1) {
                $('#min').attr('disabled', false);
            }
        } else {
            $('#add').attr('disabled', true);
        }
            

        });
    //数量减少操作
    $("#min").on('click', function() {
        t.val(parseInt(t.val()) - 1);
        if (parseInt(t.val()) == 1) {
            $('#min').attr('disabled', true);
        }
        $('#add').attr('disabled', false);
    })

});