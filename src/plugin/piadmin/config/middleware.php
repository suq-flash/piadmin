<?php

return [
    '' => [
        \plugin\piadmin\app\middleware\CrossDomain::class,
        \plugin\piadmin\app\middleware\CheckLogin::class
    ]
];
