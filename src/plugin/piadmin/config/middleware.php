<?php

return [
    '' => [
        \plugin\piadmin\app\middleware\CrossDomain::class,
        \plugin\piadmin\app\middleware\Language::class,
        \plugin\piadmin\app\middleware\CheckLogin::class
    ]
];
