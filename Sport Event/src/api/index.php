<?php namespace Core;

require_once __DIR__ . '/init.php';

$app = new \Slim\Slim([
    'mode' => isset($env['common']['production']) && ((boolean) $env['common']['production']) ? 'production' : 'test',
    'debug' =>  isset($env['common']['production']) ? (! (boolean) $env['common']['production']) : true,
]);

Registry::set('env', $env);
Registry::set('app', $app);

require __DIR__ . '/routers.php';

$app->hook('slim.after', function () use ($app) {
    $request = array(
        'headers' => $app->request->headers(),
        'body' => json_decode($app->request->getBody()),
        'params' => $app->request->params(),
        'method' => $app->request->getMethod(),
        'url' => $app->request->getUrl(),
        'pathInfo' => $app->request->getPathInfo(),
        'path' => $app->request->getPath(),
        'userAgent' => $app->request->getUserAgent(),
        'ip' => $app->request->getIp(),
        'host' => $app->request->getHost(),
    );

    $response = array(
        'headers' => $app->response->headers(),
        'body' => json_decode($app->response->getBody()),
        'status' => $app->response->getStatus(),
        'isRedirect' => $app->response->isRedirect(),
        'isServerError' => $app->response->isServerError()
    );

    Registry::get('logger')->api('Request: ' . json_encode($request));
    Registry::get('logger')->api('Response: ' . json_encode($response));

});


$app->error(function (\Exception $e) use ($app) {
    \Core\Registry::get('logger')->api(json_encode($e->getMessage()));
    $app->halt(404, json_encode($e->getMessage()));
});
$app->run();