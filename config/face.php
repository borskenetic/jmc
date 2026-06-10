<?php

return [

    /*
    | Show face scanner nav + enrollment UI. Routes stay registered when false.
    */
    'enabled' => env('FACE_ATTENDANCE_ENABLED', false),

    /*
    | Euclidean distance threshold (face-api.js). Lower = stricter. Typical: 0.45–0.55.
    */
    'match_threshold' => (float) env('FACE_MATCH_THRESHOLD', 0.55),

    /*
    | CDN base for @vladmandic/face-api model weights (browser loads these).
    */
    'model_cdn' => env(
        'FACE_MODEL_CDN',
        'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model'
    ),

    'descriptor_length' => 128,

];
