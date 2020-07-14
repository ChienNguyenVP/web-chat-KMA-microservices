<?php

return [

    'unauthorized' => [
        'status_code' => 401,
        'message' => 'Unauthorized.'
    ],
    'bad_request' => [
        'status_code' => 400,
        'message' => 'Bad request.' 
    ],
    'forbidden' => [
        'status_code' => 403,
        'message' => 'Forbidden.'
    ],
    'not_found' => [
        'status_code' => 404,
        'message' => 'Not Found.'
    ],
    'method_not_allow' => [
        'status_code' => 405,
        'message' => 'Method Not Allowed.'
    ],
    'unprocessable_entity' => [
        'status_code' => 422,
        'message' => 'Validation Errors.'
    ]
];
