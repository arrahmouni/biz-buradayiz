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

    'accepted'          => ':attribute alanı kabul edilmelidir.',
    'accepted_if'       => ':other alanı :value olduğunda :attribute alanı kabul edilmelidir.',
    'active_url'        => ':attribute alanı geçerli bir URL olmalıdır.',
    'after'             => ':attribute alanı :date tarihinden sonra olmalıdır.',
    'after_or_equal'    => ':attribute alanı :date tarihinden sonra veya aynı tarihte olmalıdır.',
    'alpha'             => ':attribute alanı yalnızca harf içermelidir.',
    'alpha_dash'        => ':attribute alanı yalnızca harf, rakam, tire ve alt çizgi içermelidir.',
    'alpha_num'         => ':attribute alanı yalnızca harf ve rakam içermelidir.',
    'array'             => ':attribute alanı bir dizi olmalıdır.',
    'ascii'             => ':attribute alanı yalnızca tek baytlı alfasayısal karakterler ve semboller içermelidir.',
    'before'            => ':attribute alanı :date tarihinden önce olmalıdır.',
    'before_or_equal'   => ':attribute alanı :date tarihinden önce veya aynı tarihte olmalıdır.',
    'between'           => [
        'array'         => ':attribute alanı :min ile :max arasında öğe içermelidir.',
        'file'          => ':attribute alanı :min ile :max kilobayt arasında olmalıdır.',
        'numeric'       => ':attribute alanı :min ile :max arasında olmalıdır.',
        'string'        => ':attribute alanı :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean'           => ':attribute alanı doğru veya yanlış olmalıdır.',
    'can'               => ':attribute alanı yetkisiz bir değer içeriyor.',
    'confirmed'         => ':attribute alanı onayı eşleşmiyor.',
    'current_password'  => 'Şifre yanlış.',
    'date'              => ':attribute alanı geçerli bir tarih olmalıdır.',
    'date_equals'       => ':attribute alanı :date tarihine eşit olmalıdır.',
    'date_format'       => ':attribute alanı :format biçimiyle eşleşmelidir.',
    'decimal'           => ':attribute alanı :decimal ondalık basamağa sahip olmalıdır.',
    'declined'          => ':attribute alanı reddedilmelidir.',
    'declined_if'       => ':other alanı :value olduğunda :attribute alanı reddedilmelidir.',
    'different'         => ':attribute alanı ile :other farklı olmalıdır.',
    'digits'            => ':attribute alanı :digits basamaklı olmalıdır.',
    'digits_between'    => ':attribute alanı :min ile :max basamak arasında olmalıdır.',
    'dimensions'        => ':attribute alanı geçersiz görsel boyutlarına sahip.',
    'distinct'          => ':attribute alanı yinelenen bir değere sahip.',
    'doesnt_end_with'   => ':attribute alanı şunlardan biriyle bitmemelidir: :values.',
    'doesnt_start_with' => ':attribute alanı şunlardan biriyle başlamamalıdır: :values.',
    'email'             => ':attribute alanı geçerli bir e-posta adresi olmalıdır.',
    'ends_with'         => ':attribute alanı şunlardan biriyle bitmelidir: :values.',
    'enum'              => 'Seçilen :attribute geçersiz.',
    'exists'            => 'Seçilen :attribute geçersiz.',
    'file'              => ':attribute alanı bir dosya olmalıdır.',
    'filled'            => ':attribute alanı bir değere sahip olmalıdır.',
    'gt'                => [
        'array'         => ':attribute alanı :value öğeden fazla içermelidir.',
        'file'          => ':attribute alanı :value kilobayttan büyük olmalıdır.',
        'numeric'       => ':attribute alanı :value değerinden büyük olmalıdır.',
        'string'        => ':attribute alanı :value karakterden uzun olmalıdır.',
    ],
    'gte'               => [
        'array'         => ':attribute alanı en az :value öğe içermelidir.',
        'file'          => ':attribute alanı :value kilobayta eşit veya daha büyük olmalıdır.',
        'numeric'       => ':attribute alanı :value değerine eşit veya daha büyük olmalıdır.',
        'string'        => ':attribute alanı en az :value karakter olmalıdır.',
    ],
    'image'             => ':attribute alanı bir görsel olmalıdır.',
    'in'                => 'Seçilen :attribute geçersiz.',
    'in_array'          => ':attribute alanı :other içinde bulunmalıdır.',
    'integer'           => ':attribute alanı bir tam sayı olmalıdır.',
    'ip'                => ':attribute alanı geçerli bir IP adresi olmalıdır.',
    'ipv4'              => ':attribute alanı geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'              => ':attribute alanı geçerli bir IPv6 adresi olmalıdır.',
    'json'              => ':attribute alanı geçerli bir JSON metni olmalıdır.',
    'lowercase'         => ':attribute alanı küçük harf olmalıdır.',
    'lt'                => [
        'array'         => ':attribute alanı :value öğeden az içermelidir.',
        'file'          => ':attribute alanı :value kilobayttan küçük olmalıdır.',
        'numeric'       => ':attribute alanı :value değerinden küçük olmalıdır.',
        'string'        => ':attribute alanı :value karakterden kısa olmalıdır.',
    ],
    'lte'               => [
        'array'         => ':attribute alanı en fazla :value öğe içermelidir.',
        'file'          => ':attribute alanı :value kilobayta eşit veya daha küçük olmalıdır.',
        'numeric'       => ':attribute alanı :value değerine eşit veya daha küçük olmalıdır.',
        'string'        => ':attribute alanı en fazla :value karakter olmalıdır.',
    ],
    'mac_address'       => ':attribute alanı geçerli bir MAC adresi olmalıdır.',
    'max'               => [
        'array'         => ':attribute alanı en fazla :max öğe içerebilir.',
        'file'          => ':attribute alanı en fazla :max kilobayt olabilir.',
        'numeric'       => ':attribute alanı en fazla :max olabilir.',
        'string'        => ':attribute alanı en fazla :max karakter olabilir.',
    ],
    'max_digits'        => ':attribute alanı en fazla :max basamak içermelidir.',
    'mimes'             => ':attribute alanı şu türde bir dosya olmalıdır: :values.',
    'mimetypes'         => ':attribute alanı şu türde bir dosya olmalıdır: :values.',
    'min'               => [
        'array'         => ':attribute alanı en az :min öğe içermelidir.',
        'file'          => ':attribute alanı en az :min kilobayt olmalıdır.',
        'numeric'       => ':attribute alanı en az :min olmalıdır.',
        'string'        => ':attribute alanı en az :min karakter olmalıdır.',
    ],
    'min_digits'        => ':attribute alanı en az :min basamak içermelidir.',
    'missing'           => ':attribute alanı bulunmamalıdır.',
    'missing_if'        => ':other alanı :value olduğunda :attribute alanı bulunmamalıdır.',
    'missing_unless'    => ':other alanı :value değilse :attribute alanı bulunmamalıdır.',
    'missing_with'      => ':values mevcutken :attribute alanı bulunmamalıdır.',
    'missing_with_all'  => ':values mevcutken :attribute alanı bulunmamalıdır.',
    'multiple_of'       => ':attribute alanı :value katı olmalıdır.',
    'not_in'            => 'Seçilen :attribute geçersiz.',
    'not_regex'         => ':attribute alanı biçimi geçersiz.',
    'numeric'           => ':attribute alanı bir sayı olmalıdır.',
    'password'          => [
        'letters'       => ':attribute alanı en az bir harf içermelidir.',
        'mixed'         => ':attribute alanı en az bir büyük ve bir küçük harf içermelidir.',
        'numbers'       => ':attribute alanı en az bir rakam içermelidir.',
        'symbols'       => ':attribute alanı en az bir sembol içermelidir.',
        'uncompromised' => 'Verilen :attribute bir veri sızıntısında görünmüş. Lütfen farklı bir :attribute seçin.',
    ],
    'present'               => ':attribute alanı mevcut olmalıdır.',
    'present_if'            => ':other alanı :value olduğunda :attribute alanı mevcut olmalıdır.',
    'present_unless'        => ':other alanı :value değilse :attribute alanı mevcut olmalıdır.',
    'present_with'          => ':values mevcutken :attribute alanı mevcut olmalıdır.',
    'present_with_all'      => ':values mevcutken :attribute alanı mevcut olmalıdır.',
    'prohibited'            => ':attribute alanı yasaktır.',
    'prohibited_if'         => ':other alanı :value olduğunda :attribute alanı yasaktır.',
    'prohibited_unless'     => ':other alanı :values içinde değilse :attribute alanı yasaktır.',
    'prohibits'             => ':attribute alanı :other alanının mevcut olmasını engeller.',
    'regex'                 => ':attribute alanı biçimi geçersiz.',
    'required'              => ':attribute alanı zorunludur.',
    'required_array_keys'   => ':attribute alanı şu girişleri içermelidir: :values.',
    'required_if'           => ':other alanı :value olduğunda :attribute alanı zorunludur.',
    'required_if_accepted'  => ':other kabul edildiğinde :attribute alanı zorunludur.',
    'required_unless'       => ':other alanı :values içinde değilse :attribute alanı zorunludur.',
    'required_with'         => ':values mevcutken :attribute alanı zorunludur.',
    'required_with_all'     => ':values mevcutken :attribute alanı zorunludur.',
    'required_without'      => ':values mevcut değilken :attribute alanı zorunludur.',
    'required_without_all'  => ':values alanlarından hiçbiri mevcut değilken :attribute alanı zorunludur.',
    'same'                  => ':attribute alanı :other ile eşleşmelidir.',
    'size'                  => [
        'array'             => ':attribute alanı :size öğe içermelidir.',
        'file'              => ':attribute alanı :size kilobayt olmalıdır.',
        'numeric'           => ':attribute alanı :size olmalıdır.',
        'string'            => ':attribute alanı :size karakter olmalıdır.',
    ],
    'starts_with'           => ':attribute alanı şunlardan biriyle başlamalıdır: :values.',
    'string'                => ':attribute alanı bir metin olmalıdır.',
    'timezone'              => ':attribute alanı geçerli bir saat dilimi olmalıdır.',
    'unique'                => ':attribute zaten alınmış.',
    'uploaded'              => ':attribute yüklenemedi.',
    'uppercase'             => ':attribute alanı büyük harf olmalıdır.',
    'url'                   => ':attribute alanı geçerli bir URL olmalıdır.',
    'ulid'                  => ':attribute alanı geçerli bir ULID olmalıdır.',
    'uuid'                  => ':attribute alanı geçerli bir UUID olmalıdır.',

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
            'rule-name' => 'özel-mesaj',
        ],
        'ability_types.*'   => [
            'required'      => 'En az bir tür seçilmelidir.',
        ],
        'ability_code.*'    => [
            'unique'        => 'Bu kod adı zaten alınmış.',
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
        'ability_types'                                 => 'İzin türleri',
        'app_name.*'                                    => 'Uygulama adı',
        'image.en'                                      => 'İngilizce görsel',
        'image.ar'                                      => 'Arapça görsel',
    ],

];
