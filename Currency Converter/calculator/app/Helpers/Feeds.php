<?php namespace App\Helpers;


/**
 * Class EstBHelper
 * Gets exchange rates from specified feed(check exrates config file)
 *
 * Exchange rates data should be returned in next format:
 * [
 *  date => 'timestamp',
 *  bank_short_name => '',
 *  bank_full_name => '',
 *  currency => '',
 *  rates => [
 *      AUD => rate,
 *      ...
 *  ]
 * ]
 * @package App\Helpers
 */
class EstBHelper {

    public static function getExchangeRates($ratesFeed) {

        $src = $ratesFeed['url'];
        $dateFormat = $ratesFeed['param_format'];
        $timestamp = $ratesFeed['timestamp'];
        $src = preg_replace('/{{date}}/', date($dateFormat, $timestamp), $src);

        $result = [];
        $xml = simplexml_load_file($src);

        if ($xml) {
            $date = $xml->Cube->Cube['time'];
            $rates = [];
            foreach ($xml->Cube->Cube->Cube as $rate) {
                $rates[trim($rate['currency'])] = trim($rate['rate']);
            }
            if (! empty($rates))
                $result = [
                    'date' => strtotime(trim($date)),
                    'bank_short_name' => $ratesFeed['short_unique_name'],
                    'bank_full_name' => $ratesFeed['full_name'],
                    'currency' => $ratesFeed['currency'],
                    'rates' => $rates
                ];
        }

        if (! empty($result))
            return $result;
        return false;
    }
}

/**
 * Class LithBHelper
 * Gets exchange rates from specified feed(check exrates config file)
 *
 * Check first class description
 *
 * @package App\Helpers
 */
class LithBHelper {
    public static function getExchangeRates($ratesFeed) {

        $src = $ratesFeed['url'];
        $dateFormat = $ratesFeed['param_format'];
        $timestamp = $ratesFeed['timestamp'];
        $src = preg_replace('/{{date}}/', date($dateFormat, $timestamp), $src);

        $result = [];
        $csv = file_get_contents($src);
        $csv = str_replace(array("\r\n","\r"),array("\\n","\\n"), $csv);

        if ($csv && $data = str_getcsv($csv, '\r\n')) {
            $rates = [];
            $date = trim(str_getcsv($data[0], ",")[3]);

            foreach($data as &$row) {
                $row = str_getcsv($row, ",");
                $rates[trim($row[1])] = trim($row[2]);
            }

            if (! empty($rates))
                $result = [
                    'date' => strtotime(trim($date)),
                    'bank_short_name' => $ratesFeed['short_unique_name'],
                    'bank_full_name' => $ratesFeed['full_name'],
                    'currency' => $ratesFeed['currency'],
                    'rates' => $rates
                ];
        }
        if (! empty($result))
            return $result;
        return false;
    }
}
