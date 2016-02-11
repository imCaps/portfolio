<?php

$app->post('/point', function (){
    $controller = new \Core\Controller\PointEvent();
    $controller->processPost();
});
$app->get('/point', function (){
    $controller = new \Core\Controller\PointEvent();
    $controller->processGet();
});
$app->get('/athletes', function (){
    $controller = new \Core\Controller\Athletes();
    $controller->processGet();
});