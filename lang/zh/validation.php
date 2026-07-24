<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'required'    => ':Attribute 为必填项。',
    'string'      => ':Attribute 必须是字符串。',
    'integer'     => ':Attribute 必须是数字。',
    'email'       => ':Attribute 必须是有效的电子邮件地址。',
    'between'     => ['numeric' => ':Attribute 必须在 :min 和 :max 之间。'],
    'max'         => ['string' => ':Attribute 不能超过 :max 个字符。'],
    'boolean'     => ':Attribute 必须是是或否。',
    'required_if' => ':Attribute 为必填项。',
    'unique'      => ':Attribute 已被使用。',

    'receipt_not_found'  => '未找到收据号码。请回来查看.',
    'receipt_expired'    => '该收据不再有效。只能使用每月收据：月份.',
    'receipt_used'       => '此收据已用于其他反馈.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'country'                => '居住国家',
        'overall_rating'         => '整体评分',
        'receipt_number'         => '收据编号',
        'total_purchase'         => '购买总额',
        'staff_friendliness'     => '员工友好度',
        'staff_helpfulness'      => '员工帮助度',
        'checkout_process'       => '结账流程',
        'service_speed'          => '服务速度',
        'store_cleanliness'      => '店铺清洁度',
        'store_ambience'         => '店铺氛围',
        'product_display'        => '商品陈列',
        'product_variety'        => '商品种类',
        'product_quality'        => '产品质量',
        'product_design'         => '产品设计',
        'price_appropriateness'  => '价格合理性',
        'value_for_money'        => '性价比',
        'like_most'              => '最喜欢的地方',
        'improvement_suggestion' => '改进建议',
        'recommend_likelihood'   => '推荐可能性',
        'may_contact'            => '联系许可',
        'full_name'              => '全名',
        'email'                  => '电子邮件',
        'whatsapp'               => 'WhatsApp',
    ],
];
