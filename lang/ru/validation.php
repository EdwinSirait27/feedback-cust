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
     'required'    => 'Поле :attribute обязательно для заполнения.',
    'string'      => 'Поле :attribute должно быть строкой.',
    'integer'     => 'Поле :attribute должно быть числом.',
    'email'       => 'Поле :attribute должно быть действительным адресом электронной почты.',
    'between'     => ['numeric' => 'Поле :attribute должно быть между :min и :max.'],
    'max'         => ['string' => 'Поле :attribute не должно превышать :max символов.'],
    'boolean'     => 'Поле :attribute должно быть да или нет.',
    'required_if' => 'Поле :attribute обязательно для заполнения.',
    'unique'      => 'Поле :attribute уже используется.',

    'receipt_not_found'  => 'Номер чека не найден. Пожалуйста, проверьте еще раз.',
    'receipt_expired'    => 'Этот чек больше недействителен. Можно использовать только чеки за месяц: месяц.',
    'receipt_used'       => 'Этот чек уже был использован для другого отзыва.',
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
        'country'                => 'Страна проживания',
        'overall_rating'         => 'Общая оценка',
        'receipt_number'         => 'Номер чека',
        'total_purchase'         => 'Общая сумма покупки',
        'staff_friendliness'     => 'Дружелюбие персонала',
        'staff_helpfulness'      => 'Готовность помочь',
        'checkout_process'       => 'Процесс оплаты',
        'service_speed'          => 'Скорость обслуживания',
        'store_cleanliness'      => 'Чистота магазина',
        'store_ambience'         => 'Атмосфера магазина',
        'product_display'        => 'Выкладка товаров',
        'product_variety'        => 'Ассортимент товаров',
        'product_quality'        => 'Качество товаров',
        'product_design'         => 'Дизайн товаров',
        'price_appropriateness'  => 'Соответствие цены',
        'value_for_money'        => 'Соотношение цена/качество',
        'like_most'              => 'Что понравилось больше всего',
        'improvement_suggestion' => 'Предложения по улучшению',
        'recommend_likelihood'   => 'Вероятность рекомендации',
        'may_contact'            => 'Разрешение на контакт',
        'full_name'              => 'Полное имя',
        'email'                  => 'Электронная почта',
        'whatsapp'               => 'WhatsApp',
    ],

];
