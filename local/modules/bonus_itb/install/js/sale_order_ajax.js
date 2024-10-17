BX.ready(function(){
    itbLogicBonusOrder();
});

function updateBonusField() {
    if(document.getElementById('paybonus_input').value == '0')
        document.getElementById('paybonus_input').value = '00';
    // BX.Sale.OrderAjaxComponent.sendRequest();
}

function useBonus()
{
    BX('paybonus_input_hidden').value = BX('paybonus_input').value;
    updateBonusField();
}


BX.addCustomEvent('onAjaxSuccess', function() {itbLogicBonusOrder('ajax');});

function itbLogicBonusOrder(type) {

    if(!BX.Sale || BX.Sale.OrderAjaxComponent == undefined)
        return;

    var result = BX.Sale.OrderAjaxComponent.result.ITB_BONUS;

    if(BX('ORDER_PROP_'+result.ORDER_PROP_PAYMENT_BONUS_ID))
        return;

    BX.remove(BX.findParent(BX('soa-property-'+result.ORDER_PROP_PAYMENT_BONUS_ID), {'tag': 'div', 'class': 'bx-soa-customer-field'}));
    BX.remove(BX.findParent(BX('soa-property-'+result.ORDER_PROP_ADD_BONUS_ID), {'tag': 'div', 'class': 'bx-soa-customer-field'}));

    var total_block = BX.findChild(BX('bx-soa-total'), {'tag': 'div', 'class': 'bx-soa-cart-total'});
    var itog_block = BX.findChild(BX('bx-soa-total'), {'tag': 'div', 'class': 'bx-soa-cart-total-line-total'}, true);

    if(result.ADD_BONUS > 0) {
        BX.remove(BX('bonus_add_block'));
        var addBonusAll_block = BX.create('DIV', {attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_add_block'},
            html: '<div id="bonus_add_sum"><span class="bx-soa-cart-t">'+result.TEXT_BONUS_BALLS+'</span><span class="bx-soa-cart-d">'+result.ADD_BONUS_FORMAT+'</span></div>'
        });
        BX.insertAfter(addBonusAll_block, itog_block);
    }


    if(BX('bx-soa-total-mobile'))
    {
        var total_block_mobile = BX.findChild(BX('bx-soa-total-mobile'), {'tag': 'div', 'class': 'bx-soa-cart-total'});
        var itog_block_mobile = BX.findChild(BX('bx-soa-total-mobile'), {'tag': 'div', 'class': 'bx-soa-cart-total-line-total'}, true);

        if(result.ADD_BONUS > 0) {
            BX.remove(BX('bonus_add_block_mobile'));
            var addBonusAll_block_mobile = BX.create('DIV', {attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_add_block_mobile'},
                html: '<div id="bonus_add_sum_mobile"><span class="bx-soa-cart-t">'+result.TEXT_BONUS_BALLS+'</span><span class="bx-soa-cart-d">'+result.ADD_BONUS+'</span></div>'
            });
            BX.insertAfter(addBonusAll_block_mobile, itog_block_mobile);
        }
    }

    if(parseFloat(result.USER_BONUS) > 0){

        var payment_block = BX.create('DIV', {props: {id: 'bonus_payment_block', className: 'bx-soa-section'}});
        var payment_title = BX.create('DIV', {attrs: {className: 'bx-soa-section-title-container'},
            html: '<h2 class="bx-soa-section-title col-sm-9"><span class="bx-soa-section-title-count"></span>'+result.MODULE_LANG.TEXT_BONUS_FOR_PAYMENT+'</h2>'
        });
        payment_block.appendChild(payment_title);
        var payment_block_content = BX.create('DIV', {props: {className: 'bx-soa-section-content lt_bonus_cont_success'}});
        payment_block.appendChild(payment_block_content);



        var comment_block = BX.create('DIV', {
            props: {className: 'bonus_comment'},
            children: [
                BX.create('strong', {html: result.MODULE_LANG.HAVE_BONUS_TEXT_FORMAT})
            ]
        });
        comment_block.appendChild(BX.create('DIV', {props: {className: 'bonus_comment_min_max'}, html: result.MODULE_LANG.CAN_USE_BONUS_TEXT_FORMAT}));
        if(parseFloat(result.USER_BONUS) > 0)
            payment_block_content.appendChild(comment_block);


        var pay_field_block = BX.create('DIV', {props: {id: 'bonus_payfield_block'}, children: [BX.create('strong', {text: result.MODULE_LANG.PAY_BONUS_TEXT})]});


        if(type == 'ajax')
        {
            var logictimPayBonus = result.PAY_BONUS;
            if(result.INPUT_BONUS == '-' || result.INPUT_BONUS == '00')
                var logictimPayBonus = '0';
        }
        else
            var logictimPayBonus = result.PAY_BONUS;


        if(result.ORDER_PAY_BONUS_AUTO == 'Y')
        {
            var input_field_block = BX.create('DIV', {props: {className: 'bx-soa-coupon-input'}});
            var input_field = BX.create('input', {
                attrs: {
                    type: 'text',
                    id: 'paybonus_input',
                    className: 'form-control bx-ios-fix',
                    onchange: 'updateBonusField();',
                    value: logictimPayBonus,
                    name: 'ORDER_PROP_'+result.ORDER_PROP_PAYMENT_BONUS_ID
                },
            });
            input_field_block.appendChild(input_field);
            pay_field_block.appendChild(input_field_block);
            payment_block_content.appendChild(pay_field_block);
        }
        else
        {
            if(type != 'ajax')
                logictimPayBonusField = result.MAX_BONUS;
            if(type == 'ajax')
            {
                if(logictimPayBonus == '0')
                    logictimPayBonusField = result.MAX_BONUS;
                else
                    logictimPayBonusField = logictimPayBonus;
            }
            var input_field_block = BX.create('DIV', {props: {className: 'bx-soa-coupon-input lt_no_arrow'}});
            var input_field_hide = BX.create('input', {
                attrs: {
                    type: 'hidden',
                    id: 'paybonus_input_hidden',
                    onchange: 'updateBonusField();',
                    value: logictimPayBonus,
                    name: 'ORDER_PROP_'+result.ORDER_PROP_PAYMENT_BONUS_ID
                },
            });
            var input_field = BX.create('input', {
                attrs: {
                    type: 'text',
                    id: 'paybonus_input',
                    //onchange: 'useBonus();',
                    className: 'form-control bx-ios-fix',
                    value: logictimPayBonusField,
                },
            });
            var button = BX.create('a', {
                attrs: {
                    onclick: 'useBonus();',
                    className: 'btn btn-default',
                },
                text: result.MODULE_LANG.TEXT_BONUS_USE_BONUS_BUTTON,
            });
            input_field_block.appendChild(input_field);
            pay_field_block.appendChild(input_field_block);
            pay_field_block.appendChild(input_field_hide);
            pay_field_block.appendChild(button);
            payment_block_content.appendChild(pay_field_block);
        }

        if(logictimPayBonus > 0 && result.MODULE_LANG.TEXT_BONUS_PAYMENT_COMMENT != '')
        {
            var payComment = BX.create('DIV', {attrs: {className: 'bonus_payment_comment'}, text: result.MODULE_LANG.TEXT_BONUS_PAYMENT_COMMENT});
            payment_block_content.appendChild(payComment);
        }

        BX.remove(BX('bonus_payment_block'));


        var last_block = document.querySelectorAll('.bx-soa-section.bx-active');
        last_block = last_block[last_block.length -1];
        BX.insertAfter(payment_block, last_block);
    }
    else {
        BX.remove(BX('bonus_payment_block'));
    }

    if(parseFloat(result.USER_BONUS) > 0 && parseFloat(result.MIN_BONUS) > parseFloat(result.USER_BONUS) || parseFloat(result.USER_BONUS) > 0 && parseFloat(result.MIN_BONUS) > parseFloat(result.MAX_BONUS))
    {
        var payment_block = BX.create('DIV', {props: {id: 'bonus_payment_block', className: 'bx-soa-section'}});
        var payment_title = BX.create('DIV', {attrs: {className: 'bx-soa-section-title-container'},
            html: '<h2 class="bx-soa-section-title col-sm-9"><span class="bx-soa-section-title-count"></span>'+result.MODULE_LANG.TEXT_BONUS_FOR_PAYMENT+'</h2>'
        });
        payment_block.appendChild(payment_title);
        var payment_block_content = BX.create('DIV', {props: {className: 'bx-soa-section-content lt_bonus_cont_error'}});
        payment_block.appendChild(payment_block_content);

        var comment_block = BX.create('DIV', {
            props: {className: 'bonus_comment'},
            children: [
                BX.create('strong', {html: result.MODULE_LANG.HAVE_BONUS_TEXT_FORMAT})
            ]
        });

        if(parseFloat(result.MIN_BONUS) > parseFloat(result.MAX_BONUS))
            result.MODULE_LANG.TEXT_BONUS_ERROR_MIN_BONUS_FORMAT =  result.MODULE_LANG.TEXT_BONUS_ERROR_MIN_BONUS_FORMAT + ' ' +  result.MODULE_LANG.MAX_BONUS_TEXT;
        comment_block.appendChild(BX.create('span', {html: result.MODULE_LANG.TEXT_BONUS_ERROR_MIN_BONUS_FORMAT}));
        payment_block_content.appendChild(comment_block);

        BX.remove(BX('bonus_payment_block'));
        var last_block = document.querySelectorAll('.bx-soa-section.bx-active');
        last_block = last_block[last_block.length -1];
        BX.insertAfter(payment_block, last_block);
    }

    if(logictimPayBonus > 0 && result.DISCOUNT_TO_PRODUCTS != 'B') {
        BX.remove(BX('bonus_pay_block'));
        var addPay_info_block = BX.create('DIV', {attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_pay_block'},
            html: '<div id="bonus_pay_sum"><span class="bx-soa-cart-t">'+result.TEXT_BONUS_PAY+'</span><span class="bx-soa-cart-d">'+logictimPayBonus+'</span></div>'
        });
        var before_itog_block = BX.findPreviousSibling(itog_block, {'tag': 'div', 'class': 'bx-soa-cart-total-line'});
        BX.insertAfter(addPay_info_block, before_itog_block);

        //for mobile
        BX.remove(BX('bonus_pay_block_mobile'));
        var addPay_info_block_mobile = BX.create('DIV', {attrs: {className: 'bx-soa-cart-total-line', id: 'bonus_pay_block_mobile'},
            html: '<div id="bonus_pay_sum_mobile"><span class="bx-soa-cart-t">'+result.TEXT_BONUS_PAY+'</span><span class="bx-soa-cart-d">'+logictimPayBonus+'</span></div>'
        });
        var before_itog_block_mobile = BX.findPreviousSibling(itog_block_mobile, {'tag': 'div', 'class': 'bx-soa-cart-total-line'});
        BX.insertAfter(addPay_info_block_mobile, before_itog_block_mobile);
    }
}

