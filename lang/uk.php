<?php

return [
    'header' => [
        'title' => 'Юлія Рудоманенко',
        'subtitle' => 'Психолог, психотерапевт, Одеса',
        'menu' => [
            'education' => 'Освіта',
            'contacts' => 'Контакти',
            'login' => 'Вхід',
            'home' => 'Головна',
            'services' => 'Послуги',
            'about' => 'Про мене',
            'blog' => 'Блог'
        ]
    ],
    'pages' => [
        'home' => [
            'meta_title' => 'Психологічна допомога від Юлії Рудоманенко'
        ],
        'about' => [
            'meta_title' => 'Про Юлію Рудоманенко',
            'about' => 'Про мене',
            'education' => 'Освіта',
        ],
        'blog' => [
            'meta_title' => 'Блог Юлії Рудоманенко',
            'title' => 'Блог',
        ],
        'blog_post' => [],
        'education' => [
            'certificates' => 'Сертифікати',
            'meta_title' => 'Освіта Юлії Рудоманенко'
        ],
        'service' => [
            'description' => 'Опис'
        ],
        'services' => [
            'meta_title' => 'Послуги'
        ],
        'thankyou' => [
            'meta_title' => 'Дякуємо',
            'title' => 'Дякуємо',
            'subtitle' => 'Ваша заявка обробляється!',
        ],
        '404' => [
            'meta_title' => 'Сторінка не знайдена',
            'title' => 'Сторінка не знайдена',
            'subtitle' => 'Вибачте, цієї сторінки немає.',
        ],
        '500' => [
            'meta_title' => 'Помилка',
            'title' => 'Виникла помилка',
            'subtitle' => 'Вибачте, виникла помилка.',
        ],
    ],
    'actions' => [
        'show_more' => 'Показати ще',
        'find_out_more' => 'Дізнатись більше',
        'submit' => 'Відправити',
        'read' => 'Читати',
        'go_to_other_posts' => 'До інших постів',
        'make_appointment' => 'Записатися на прийом',
        'go_to_other_services' => 'До інших послуг'
    ],
    'blocks' => [
        'services' => [
            'title' => 'Послуги',
            'communication_options' => 'Онлайн чи офлайн'
        ],
        'facebook_posts' => [
            'title' => 'Публікації'
        ]
    ],

    'forms' => [
        'feedback' => [
            'title' => 'Залиште повідомлення',
            'fields' => [
                'name' => [
                    'label' => "Ім'я",
                    'placeholder' => "Введіть Ваше ім'я",
                    'error' => [
                        'required' => "Будь ласка, заповніть Ваше ім'я"
                    ],
                    'valid' => 'Добре'
                ],
                'phone' => [
                    'label' => 'Телефон',
                    'placeholder' => 'Телефон',
                    'error' => [
                        'phoneUa' => 'Будь ласка, вкажіть вірний номер телефону'
                    ],
                    'valid' => 'Добре'
                ],
                'message' => [
                    'label' => 'Повідомлення',
                    'placeholder' => 'Коротко опишіть Вашу проблему'
                ]
            ],
            'successMessage' => 'Дякую! Ваше повідомлення відправлено!'
        ]
    ],
    'footer' => [
        'copyright' => 'Юлія Рудоманенко'
    ]
];