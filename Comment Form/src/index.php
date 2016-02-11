<?php

/**
 * Simple handmade router
 * @author alexandrshumilow
 */

require_once __DIR__ . '/init.php';

try {
    $app = new \Core\App();

    $app->container->env = $env;
    $app->container->db = new \Core\DB($env['db']);
    $app->container->db->connect();

    $app->container->mail = new \Core\Mail(
        array_merge($env['mail'], $env['sendgrid'])
    );

    if ($app->request->uri == "/feedback" && $app->request->method == "POST") {

        $feedbackController = new \Controller\FeedbackController($app);
        $feedbackController->handleFeedbackPosting();

    } else if ($app->request->uri == "/" && $app->request->method == "GET") {

        $app->response->response('200', file_get_contents('views/feedbackPage.php'));

    } else {
        $app->response->response('404');
    }

    $app->respond($app->response);

} catch (\Exception $e) {
    // Logger is not implemented
    die($e->getMessage());
}

