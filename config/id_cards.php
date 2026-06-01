<?php

/**
 * Student ID cards: one config block per template key (grade_school, high_school, college).
 *
 * Coordinates are in pixels relative to each level’s own front.png / back.png size.
 * Tune front, back, and photo independently when template dimensions differ.
 */
return [

    'student_levels' => [

        'grade_school' => [
            'label' => 'GRADE SCHOOL',
            'accent' => '#29abe2',
            'accent_bar' => true,
            'show_level_label' => true,

            'front' => [
                'accent_bar' => ['x' => 0, 'y' => 0, 'width' => 1013, 'height' => 48],
                'level' => ['x' => 506, 'y' => 420, 'size' => 28, 'align' => 'center'],
                'name' => ['x' => 506, 'y' => 460, 'size' => 36, 'align' => 'center'],
                'program_line' => ['x' => 506, 'y' => 510, 'size' => 26, 'align' => 'center'],
                'student_id' => ['x' => 200, 'y' => 240, 'size' => 32, 'align' => 'left'],
                'qrcode_label' => ['x' => 320, 'y' => 620, 'size' => 40, 'align' => 'center'],
                'barcode' => [
                    'x' => 220,
                    'y' => 660,
                    'scale' => 4,
                    'height' => 120,
                ],
            ],

            'back' => [
                'birth_date' => ['x' => 850, 'y' => 220, 'size' => 120],
                'blood_type' => ['x' => 850, 'y' => 420, 'size' => 120],
                'emergency_name' => ['x' => 620, 'y' => 720, 'size' => 100],
                'emergency_relationship' => ['x' => 640, 'y' => 820, 'size' => 100],
                'emergency_number' => ['x' => 630, 'y' => 920, 'size' => 100],
                'emergency_address' => ['x' => 630, 'y' => 1020, 'size' => 100, 'max_chars' => 40],
                'qr' => ['x' => 180, 'y' => 140, 'size' => 520],
                'signature' => ['width' => 700, 'height' => 350, 'x' => 0, 'y' => 880],
            ],

            'photo' => [
                'x' => 40,
                'y' => 120,
                'width' => 180,
                'height' => 180,
            ],
        ],

        'high_school' => [
            'label' => 'HIGH SCHOOL',
            'accent' => '#1a8fc4',
            'accent_bar' => true,
            'show_level_label' => true,

            'front' => [
                'accent_bar' => ['x' => 0, 'y' => 0, 'width' => 1013, 'height' => 56],
                'level' => ['x' => 506, 'y' => 400, 'size' => 30, 'align' => 'center'],
                'name' => ['x' => 506, 'y' => 445, 'size' => 40, 'align' => 'center'],
                'program_line' => ['x' => 506, 'y' => 500, 'size' => 28, 'align' => 'center'],
                'student_id' => ['x' => 220, 'y' => 220, 'size' => 36, 'align' => 'left'],
                'qrcode_label' => ['x' => 350, 'y' => 640, 'size' => 44, 'align' => 'center'],
                'barcode' => [
                    'x' => 240,
                    'y' => 680,
                    'scale' => 5,
                    'height' => 140,
                ],
            ],

            'back' => [
                'birth_date' => ['x' => 900, 'y' => 240, 'size' => 140],
                'blood_type' => ['x' => 900, 'y' => 460, 'size' => 140],
                'emergency_name' => ['x' => 680, 'y' => 780, 'size' => 110],
                'emergency_relationship' => ['x' => 700, 'y' => 890, 'size' => 110],
                'emergency_number' => ['x' => 690, 'y' => 1000, 'size' => 110],
                'emergency_address' => ['x' => 690, 'y' => 1110, 'size' => 110, 'max_chars' => 50],
                'qr' => ['x' => 200, 'y' => 150, 'size' => 600],
                'signature' => ['width' => 800, 'height' => 400, 'x' => 0, 'y' => 950],
            ],

            'photo' => [
                'x' => 48,
                'y' => 130,
                'width' => 200,
                'height' => 200,
            ],
        ],

        'college' => [
            'label' => 'COLLEGE',
            'accent' => '#125a82',
            'accent_bar' => false,
            'show_level_label' => false,

            'front' => [
                'accent_bar' => ['x' => 0, 'y' => 0, 'width' => 3600, 'height' => 120],
                'level' => ['x' => 2470, 'y' => 1080, 'size' => 48, 'align' => 'center'],
                'name' => ['x' => 2470, 'y' => 1180, 'size' => 70, 'align' => 'center'],
                'program_line' => ['x' => 2470, 'y' => 1285, 'size' => 52, 'align' => 'center'],
                'student_id' => ['x' => 1000, 'y' => 610, 'size' => 65, 'align' => 'left'],
                'qrcode_label' => ['x' => 1555, 'y' => 1540, 'size' => 80, 'align' => 'center'],
                'barcode' => [
                    'x' => 1100,
                    'y' => 1650,
                    'scale' => 8,
                    'height' => 300,
                ],
            ],

            'back' => [
                'birth_date' => ['x' => 3000, 'y' => 800, 'size' => 300],
                'blood_type' => ['x' => 3000, 'y' => 1550, 'size' => 300],
                'emergency_name' => ['x' => 2190, 'y' => 2650, 'size' => 250],
                'emergency_relationship' => ['x' => 2250, 'y' => 2900, 'size' => 250],
                'emergency_number' => ['x' => 2230, 'y' => 3200, 'size' => 250],
                'emergency_address' => ['x' => 2230, 'y' => 3500, 'size' => 250, 'max_chars' => 60],
                'qr' => ['x' => 620, 'y' => 510, 'size' => 1300],
                'signature' => ['width' => 2000, 'height' => 1000, 'x' => 50, 'y' => 2875],
            ],

            'photo' => null,
        ],

    ],

];
