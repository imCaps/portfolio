<?php
/**
 * Mail handler class
 * @author alexandrshumilow
 */

namespace Core;


class Mail
{
    /**
     * @var string
     */
    private $driver = 'mail';
    private $apikey;



    public function __construct($params)
    {
        if (isset($params['driver'])) {
            $this->driver = $params['driver'];
        }
        if ($this->driver == 'sendgrid' && empty($params['apikey'])) {
            throw new \Exception('Sendgrid api key is missing');
        } else {
            $this->apikey = $params['apikey'];
        }
    }

    public function send($to, $subject, $message, $from) {
        if ($this->driver == 'mail') {
            return mail($to, $subject, $message);
        }
        if ($this->driver == 'sendgrid') {
            $sendgrid = new \SendGrid($this->apikey);

            $email = new \SendGrid\Email();
            $email
                ->addTo($to)
                ->setFrom($from)
                ->setSubject($subject)
                ->setHtml($message)
            ;

            return $sendgrid->send($email);
        }
        return false;
    }
}