<?php
/**
 * Feedback Controller
 * @author alexandrshumilow
 */

namespace Controller;

use Core\App;
use Helpers\Validator;

class FeedbackController
{
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handleFeedbackPosting()
    {
        $feedbackParams = json_decode($this->app->request->content, true);
        $validationResults = Validator::validateFeedbackFormParams($feedbackParams);
        if ($validationResults !== true) {
            $this->app->response->response('400', json_encode($validationResults));
        } else {
            $saveStatus = $this->app->container->db->exec("INSERT INTO feedback(name, email, comment)"
                . " VALUES (:name, :email, :comment);",
                array(
                    ':name' => $feedbackParams['name'],
                    ':email' => $feedbackParams['email'],
                    ':comment' => $feedbackParams['comment']
                ));

            $preparedMessage = "<p>You got the new comment:</p>"
                . "<p>Name: {$feedbackParams['name']}"
                . "<p>E-mail: {$feedbackParams['email']}"
                . "<p>Comment: {$feedbackParams['comment']}";
            $to = $this->app->container->env['mail']['feedback_email'];
            $from = $this->app->container->env['mail']['from_email'];
            $subject = 'You got the new comment';

            $emailingStatus = $this->app->container->mail->send($to, $subject, $preparedMessage, $from);

            if ($saveStatus && $emailingStatus)
                $this->app->response->response('200');
        }
    }
}