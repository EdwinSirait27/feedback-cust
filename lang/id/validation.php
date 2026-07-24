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
     'required'    => ':Attribute wajib diisi.',
    'string'      => ':Attribute harus berupa teks.',
    'integer'     => ':Attribute harus berupa angka.',
    'email'       => ':Attribute harus berupa alamat email yang valid.',
    'between'     => ['numeric' => ':Attribute harus antara :min dan :max.'],
    'max'         => ['string' => ':Attribute tidak boleh lebih dari :max karakter.'],
    'boolean'     => ':Attribute harus berupa pilihan ya atau tidak.',
    'required_if' => ':Attribute wajib diisi.',
    'unique'      => ':Attribute sudah digunakan.',
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
        'country'                => 'Negara asal',
        'overall_rating'         => 'Rating keseluruhan',
        'receipt_number'         => 'Nomor struk',
        'total_purchase'         => 'Total belanja',
        'staff_friendliness'     => 'Keramahan staf',
        'staff_helpfulness'      => 'Kesediaan membantu',
        'checkout_process'       => 'Proses di kasir',
        'service_speed'          => 'Kecepatan layanan',
        'store_cleanliness'      => 'Kebersihan toko',
        'store_ambience'         => 'Suasana toko',
        'product_display'        => 'Penataan produk',
        'product_variety'        => 'Kelengkapan produk',
        'product_quality'        => 'Kualitas produk',
        'product_design'         => 'Desain produk',
        'price_appropriateness'  => 'Kesesuaian harga',
        'value_for_money'        => 'Nilai yang didapat',
        'like_most'              => 'Yang paling disukai',
        'improvement_suggestion' => 'Saran perbaikan',
        'recommend_likelihood'   => 'Kemungkinan merekomendasikan',
        'may_contact'            => 'Persetujuan kontak',
        'full_name'              => 'Nama lengkap',
        'email'                  => 'Email',
        'whatsapp'               => 'WhatsApp',
    ],

];
