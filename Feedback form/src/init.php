<?php
/**
 * Init script
 * @author alexandrshumilow
 */

require_once __DIR__ . '/controllers/FeedbackController.php';
require_once __DIR__ . '/helpers/Validator.php';
require_once __DIR__ . '/core/Mail.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/App.php';
require_once __DIR__ . '/core/DB.php';
require_once __DIR__ . '/lib/sendgrid-php/sendgrid-php.php';

function readEnvelopmentFile() {
    $env = parse_ini_file(__DIR__ . '/.env', true);
    if (false === $env) {
        die("Could not read environment configuration");
    }
    return $env;
}
$env = readEnvelopmentFile();
