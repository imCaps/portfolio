<?php
/**
 * Validator class
 * @author alexandrshumilow
 */

namespace Helpers;


class Validator
{
    /**
     * Validates feedback params
     * @param $params
     * @return array|bool
     */
    public static function validateFeedbackFormParams($params) {
        $errors = [];
        if (isset($params, $params['email'], $params['name'], $params['comment'])) {
            if (! filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email format is invalid';
            }
            if (strlen($params['name']) == 0 || strlen($params['name']) > 50) {
                $errors[] = 'Name length is invalid max. 50';
            }
            if (strlen($params['email']) == 0 || strlen($params['email']) > 100) {
                $errors[] = 'Email length is invalid max. 100';
            }
            if (strlen($params['comment']) == 0 || strlen($params['comment']) > 200) {
                $errors[] = 'Comment length is invalid max. 200';
            }
        } else {
            $errors[] = 'Missing params';
        }

        return empty($errors) ? true : $errors;
    }
}