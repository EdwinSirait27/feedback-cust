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
     'required'    => 'The :attribute field is required.',
    'string'      => 'The :attribute must be a string.',
    'integer'     => 'The :attribute must be a number.',
    'email'       => 'The :attribute must be a valid email address.',
    'between'     => ['numeric' => 'The :attribute must be between :min and :max.'],
    'max'         => ['string' => 'The :attribute must not exceed :max characters.'],
    'boolean'     => 'The :attribute must be yes or no.',
    'required_if' => 'The :attribute field is required.',
    'unique'      => 'The :attribute has already been taken.',

    'receipt_not_found'  => 'Receipt number not found. Please check again.',
    'receipt_expired'    => 'This receipt is no longer valid. Only receipts from month :month can be used.',
    'receipt_used'       => 'This receipt has already been used for another feedback.',
    

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
        'country'                => 'Country of residence',
        'overall_rating'         => 'Overall rating',
        'receipt_number'         => 'Receipt number',
        'total_purchase'         => 'Total purchase',
        'staff_friendliness'     => 'Staff friendliness',
        'staff_helpfulness'      => 'Staff helpfulness',
        'checkout_process'       => 'Checkout process',
        'service_speed'          => 'Service speed',
        'store_cleanliness'      => 'Store cleanliness',
        'store_ambience'         => 'Store ambience',
        'product_display'        => 'Product display',
        'product_variety'        => 'Product variety',
        'product_quality'        => 'Product quality',
        'product_design'         => 'Product design',
        'price_appropriateness'  => 'Price appropriateness',
        'value_for_money'        => 'Value for money',
        'like_most'              => 'What you liked most',
        'improvement_suggestion' => 'Improvement suggestion',
        'recommend_likelihood'   => 'Likelihood to recommend',
        'may_contact'            => 'Contact permission',
        'full_name'              => 'Full name',
        'email'                  => 'Email',
        'whatsapp'               => 'WhatsApp',
    ],

];
