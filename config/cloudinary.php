<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    |
    |
    */
//     'cloud_url' => env('CLOUDINARY_URL', null),

//    'cloudinary' => [
//         'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
//         'api_key'    => env('CLOUDINARY_API_KEY'),
//         'api_secret' => env('CLOUDINARY_API_SECRET'),
//     ],
 'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => true
   

];
