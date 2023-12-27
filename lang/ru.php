<?php

return [
    'header' => [
        'title' => 'Юлия Рудоманенко',
        'subtitle' => 'Психолог, психотерапевт, Одесса',
        'menu' => [
            'education' => 'Образование',
            'contacts' => 'Контакты',
            'login' => 'Вход',
            'home' => 'Главная',
            'services' => 'Записаться',
            'about' => 'Обо мне',
            'blog' => 'Блог'
        ]
    ],
    'pages' => [
        'home' => [
            'meta_title' => 'Психологическая помощь от Юлии Рудоманенко',
        ],
        'about' => [
            'meta_title' => 'Про Юлию Рудоманенко',
            'about' => 'Обо мне',
            'education' => 'Образование',
        ],
        'blog' => [
            'meta_title' => 'Блог Юлии Рудоманенко',
            'title' => 'Блог',
        ],
        'education' => [
            'certificates' => 'Сертификаты',
            'meta_title' => 'Образование Юлии Рудоманенко'
        ],
        'service' => [
            'description' => 'Описание'
        ],
        'services' => [
            'meta_title' => 'Услуги'
        ],
        'thankyou' => [
            'meta_title' => 'Спасибо',
            'title' => 'Спасибо',
            'subtitle' => 'Ваша заявка обрабатывается!',
        ],
        '404' => [
            'meta_title' => 'Страница не найдена',
            'title' => 'Страница не найдена',
            'subtitle' => 'Извините, этой страницы не существует.',
        ],
        '500' => [
            'meta_title' => 'Ошибка',
            'title' => 'Возникла ошибка',
            'subtitle' => 'Извините возникла ошибка.',
        ],

    ],
    'actions' => [
        'show_more' => 'Показать еще',
        'find_out_more' => 'Узнать больше',
        'submit' => 'Отправить',
        'read' => 'Читать',
        'go_to_other_posts' => 'К другим постам',
        'make_appointment' => 'Записаться на прием',
        'go_to_other_services' => 'К другим услугам'
    ],
    'blocks' => [
        'services' => [
            'title' => 'Услуги',
            'communication_options' => 'Очно или онлайн'
        ],
        'facebook_posts' => [
            'title' => 'Публикации'
        ]
    ],
    'forms' => [
        'feedback' => [
            'title' => 'Оставьте сообщение',
            'fields' => [
                'name' => [
                    'label' => 'Имя',
                    'placeholder' => 'Введите Ваше имя',
                    'error' => [
                        'required' => 'Введите имя'
                    ],
                    'valid' => 'Выглядит хорошо'
                ],
                'phone' => [
                    'label' => 'Телефон',
                    'placeholder' => 'Телефон',
                    'error' => [
                        'phoneUa' => 'Укажите верный телефон'
                    ],
                    'valid' => 'Выглядит хорошо'
                ],
                'message' => [
                    'label' => 'Сообщение',
                    'placeholder' => 'Кратко опишите Вашу проблему'
                ]
            ],
            'success_message' => 'Спасибо! Ваше сообщение отправлено!'
        ]
    ],
    'footer' => [
        'copyright' => 'Юлия Рудоманенко'
    ]
];