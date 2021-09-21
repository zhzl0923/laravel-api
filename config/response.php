<?php


return [
    'is_restful' => false,              // 是否 RESTful 规范(设置为 false, HTTP 状态码统一返回 200)
    'is_unified_return_json' => true,   // 是否统一返回 JSON
    'code' => [
        'success' => 'Success|200000',
        'fail' => 'Fail|400000',
        'error' => 'Error|500000',
        'unauthorized' => 'Unauthenticated|200001',
        'validation' => 'Unprocessable Entity|422001',
    ]
];
