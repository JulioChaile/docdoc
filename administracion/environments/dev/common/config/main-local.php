<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=docdev',
            'username' => 'root',
            'password' => 'icui4cu2',
            'charset' => 'utf8',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'contacto@docdoc.com.ar',
                'password' => 'docdoc2019',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'bucket' => [
            'class' => 'common\components\CloudStorage',
            'key' => '{
                "type": "service_account",
                "project_id": "primervm-182021",
                "private_key_id": "2ae5df8154a9edee368524a884234c516a7388cd",
                "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCw1x2Rw7rUcjv/\nJGWBbfFv97yPzP6eScaRxSWrL8HAXIJHi0w6GC0SxGE3fz6/2iuLyBa7+lBY2CXg\nTPmLsJNQmReAuLLKoMzMRxZX72fxZQOIwGYSYEVNhZa/PHRL8hlvO+M+74Vulv6M\nDFatXFTMeAmZZGVt8kLH4wM4fIoRccu+FwCfmgptHsnaE8XCAjPIieEl4x7tGf77\nV5MDOgvuKH3j08AXKjfVG39bKuoArp6yeh4tdoV0jJD9HaZQlUquBdD59Hi1Dc1Q\nKYCrr30jG/w5Orkn8yjF1PpMlN/gqhHYUCopLOQYE03tFq7mlacnJxcTq5jinCeb\n65p0+WJdAgMBAAECggEAB8qaj2sPxr93vECWxlKZQiUopi9Sgpfei1qj07yJbVfG\nF6/f/mlFRx/m5qKRGbPbmk+JDOQ0+Q0g8haCIjD39KOiIPZCTmIyU8fICLzQGBcq\nxjIv7RxXwVgi8a8Qsq3qh/ecBhBYkSxO7821XfME+3NIfP0q6OK1JYhvgmQeUzFc\n9orDrR7KXT1KosfL+DWzcGG/ubWd2hKSTcF+nH0Et1oOlXVJzT2XfiQAnTziim62\nEoWvlW4vqK+kbQcD7xW/8bHxEOr/Jq3yEu/i7b9MaP/Zaoi6V4N9PRN7/aqUMFxN\nMx8uvFMQfQUl2C0v7jVR5nu+8WY9zAKoG6n3EL1u4QKBgQDvmBxIrT+/J6JU9EPj\n9V/adk+/F7JcPGbnShTvWf0x8I0+wB0JtezVvHvSOw1VTmGiOH5pJC2huWTd70eZ\n60+/OE2R5h9E02qNTq9Ao9G5/S7bMS1DzEAMf4yB+O65Wo+tLDqcolev/AR4x9Yv\nKlmAaeuk/QusAl9/ORwTKbXebQKBgQC88vsZIEfPad3QbkM/jftv4Jhd8pZmx4kr\nFFqABab0/58QjGLGSpjbiXxeJ3ln5WPFWtdkb5J3wccYutAn9ZcyOv5wvQf0CDIC\n4vA4tF/l9t7GH31mcTbVea9aCWRKWNgSXCtgFr4UlY06AnNsasfHLuxTLZ14RLDK\nDC9vpgFdsQKBgQCE1QIpUNPcuK6JBvt0sJ850QoZWZvrAxnR9QJySKPVQhYsyfnP\nXms1fE/xmAemWdRLSaLby9D4rn47hONfdFQ8IYzv7HM9hzC1sv/6iFhIExQdNcBw\nrBZ+V2Cg1lATtx+c1L6SBHc0/DXGeJRIheQlC5Q3XotQC+jB8fmcri1EaQKBgH3A\ndQ9e5YwMWF0c1VLaCuqFnsJ/7ktPJ2JtdGHZ4SkTa2jXFfIEucD6urPrL6jPCVzS\nvaGRg/iCFiDGTD0b/Vmn26lqlX17atVy9g+3NE/XwqI3WP0yAh2HlAWnVIEKqhtm\nDlQwLqeCdPCTqwAP/Q/6kbw2EqR1ivedroBvCl+xAoGAPnI4iJRpdT6OARi1i3a4\nk0zeJSpz4Vv21L4bCo7EZCS3ccfnAiswNEdXdnwo/XbI7Nk3hK5fUBkQGxmMZw9V\n9TwQTVfD6sbDVlac8ePoum5NlAV1vqQ55IHwYhk3RMXIBgJlljEgvSAoMsmE5iTQ\nI6SxwG+dLKasvN0afWXfol8=\n-----END PRIVATE KEY-----\n",
                "client_email": "io-docdoc@primervm-182021.iam.gserviceaccount.com",
                "client_id": "100609410226962448506",
                "auth_uri": "https://accounts.google.com/o/oauth2/auth",
                "token_uri": "https://oauth2.googleapis.com/token",
                "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
                "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/io-docdoc%40primervm-182021.iam.gserviceaccount.com"
              }',
            'bucketName' => 'docdoc-bucket'
        ],
        'chatapi' => [
            'class' => 'common\components\ChatApi',
            'InstanceId' => '153725',
            'Token' => '67aqw7sghzkatk34',
            'ServerId' => 'eu155'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
