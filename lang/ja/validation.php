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
      'required'    => ':Attributeは必須です。',
    'string'      => ':Attributeは文字列である必要があります。',
    'integer'     => ':Attributeは数値である必要があります。',
    'email'       => ':Attributeは有効なメールアドレスである必要があります。',
    'between'     => ['numeric' => ':Attributeは:minから:maxの間である必要があります。'],
    'max'         => ['string' => ':Attributeは:max文字以内である必要があります。'],
    'boolean'     => ':Attributeははいまたはいいえである必要があります。',
    'required_if' => ':Attributeは必須です。',
    'unique'      => ':Attributeはすでに使用されています。',

    'receipt_not_found'  => '領収書番号が見つかりません。もう一度ご確認ください.',
    'receipt_expired'    => 'この領収書は無効です。〇月〇日以降の領収書のみご利用いただけます.',
    'receipt_used'       => 'この領収書は既に別のフィードバックに使用されています.',
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
        'country'                => '居住国',
        'overall_rating'         => '総合評価',
        'receipt_number'         => 'レシート番号',
        'total_purchase'         => '購入合計',
        'staff_friendliness'     => 'スタッフの親切さ',
        'staff_helpfulness'      => 'スタッフの助けになる度',
        'checkout_process'       => 'チェックアウトプロセス',
        'service_speed'          => 'サービス速度',
        'store_cleanliness'      => '店舗の清潔さ',
        'store_ambience'         => '店舗の雰囲気',
        'product_display'        => '商品の陳列',
        'product_variety'        => '商品の種類',
        'product_quality'        => '商品の品質',
        'product_design'         => '商品のデザイン',
        'price_appropriateness'  => '価格の適切さ',
        'value_for_money'        => 'コストパフォーマンス',
        'like_most'              => '最も気に入った点',
        'improvement_suggestion' => '改善提案',
        'recommend_likelihood'   => '推薦の可能性',
        'may_contact'            => '連絡の許可',
        'full_name'              => '氏名',
        'email'                  => 'メール',
        'whatsapp'               => 'WhatsApp',
    ],

];
