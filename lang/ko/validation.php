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
    'required'    => ':Attribute 항목은 필수입니다.',
    'string'      => ':Attribute 항목은 문자열이어야 합니다.',
    'integer'     => ':Attribute 항목은 숫자여야 합니다.',
    'email'       => ':Attribute 항목은 유효한 이메일 주소여야 합니다.',
    'between'     => ['numeric' => ':Attribute 항목은 :min에서 :max 사이여야 합니다.'],
    'max'         => ['string' => ':Attribute 항목은 :max자를 초과할 수 없습니다.'],
    'boolean'     => ':Attribute 항목은 예 또는 아니오여야 합니다.',
    'required_if' => ':Attribute 항목은 필수입니다.',
    'unique'      => ':Attribute 항목은 이미 사용 중입니다.',
    'receipt_not_found'  => '영수증 번호를 찾을 수 없습니다. 다시 확인해 주세요.',
    'receipt_expired'    => '이 영수증은 더 이상 유효하지 않습니다. 다음 달(월:월)의 영수증만 사용 가능합니다.',
    'receipt_used'       => '이 영수증은 이미 다른 피드백에 사용되었습니다.',
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
        'country'                => '거주 국가',
        'overall_rating'         => '전체 평점',
        'receipt_number'         => '영수증 번호',
        'total_purchase'         => '총 구매액',
        'staff_friendliness'     => '직원 친절도',
        'staff_helpfulness'      => '직원 도움도',
        'checkout_process'       => '계산 과정',
        'service_speed'          => '서비스 속도',
        'store_cleanliness'      => '매장 청결도',
        'store_ambience'         => '매장 분위기',
        'product_display'        => '상품 진열',
        'product_variety'        => '상품 다양성',
        'product_quality'        => '상품 품질',
        'product_design'         => '상품 디자인',
        'price_appropriateness'  => '가격 적절성',
        'value_for_money'        => '가성비',
        'like_most'              => '가장 좋았던 점',
        'improvement_suggestion' => '개선 제안',
        'recommend_likelihood'   => '추천 가능성',
        'may_contact'            => '연락 동의',
        'full_name'              => '성명',
        'email'                  => '이메일',
        'whatsapp'               => 'WhatsApp',
    ],

];
