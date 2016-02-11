<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Config;
use App\Helpers;

class WelcomeController extends Controller {

    /**
     * Returns welcome page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex() {

        $defaultCurrencyList = Config::get('exrates.currency', []);

        $weekDay = date('w');
        if ($weekDay == 6 || $weekDay == 0 || $weekDay == 1)
            $ratesLists = $this->getExchangeRates(strtotime("last Friday"));
        else
            $ratesLists = $this->getExchangeRates(strtotime("yesterday"));

        $parsedRates = [];
        $currencies = [];
        $i = 0;
        $date = false;
        foreach ($ratesLists as $bank) {
            $latestRates['banks'][] = $bank['bank_full_name'];
            foreach ($bank['rates'] as $symbol => $rate) {
                $parsedRates[$symbol][$i] = $rate;
                $currencies[$symbol] = isset($defaultCurrencyList[$symbol]) ? $defaultCurrencyList[$symbol] : '';
            }
            $date = $bank['date'];
            $i++;
        }
        foreach ($currencies as $currency => $fullName) {
            $latestRates['rates'][$currency] = [
                'full_name' => $fullName,
                'rates' => $parsedRates[$currency]
            ];
        }


        $latestRates['date'] = date('d.m.Y', $date);

        $currencies['EUR'] = 'European Euro';
        ksort($currencies);
        return view('welcome', [
            'latest_rates' => $latestRates,
            'currency_list' => $currencies
        ]);
    }

    /**
     * Returns exchange results
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResult(Request $request) {

        $messages = [
            'date.date_format' => 'The Date param format is invalid(dd-mm-yyyy).',
            'date.required' => 'The Date param is required.',

            'price.required' => 'The Price param is required.',
            'price.regex' => 'The Price param format is invalid.',

            'to-currency.required' => 'The "To Currency" param is invalid',
            'to-currency.size' => 'The "To Currency" param is invalid',
            'to-currency.alpha' => 'The "To Currency" param is invalid',

            'from-currency.required' => 'The "From Currency" param is invalid',
            'from-currency.size' => 'The "From Currency" param is invalid',
            'from-currency.alpha' => 'The "From Currency" param is invalid',
        ];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:d-m-Y',
            'price' => array('required','regex:/^([0-9]+)$|^([0-9]+[\.\,][0-9]+)$/'),
            'to-currency' => 'required|size:3|alpha',
            'from-currency' => 'required|size:3|alpha'
        ], $messages);


        if ($validator->fails()) {
            $responseMsg = [];
            foreach ($validator->messages()->all() as $message) {
                $responseMsg[] = $message;
            }
            return response()->json($responseMsg, 400);
        }
        $response = $this->calculate($request->all());
        if (! $response)
            return response()->json(['Could not convert'], 400);
        return response()->json($response);
    }

    /**
     * Calculates exchange data
     *
     * @param $data
     * @return array
     */
    private function calculate($data) {
        $date = $data['date'];
        $weekDay = date('w', strtotime($date));
        if ($weekDay == 6 || $weekDay == 0 || $weekDay == 1)
            $rates = $this->getExchangeRates(strtotime("$date last Friday"));
        else
            $rates = $this->getExchangeRates(strtotime("$date yesterday"));

        $bankRates = [];
        foreach ($rates as $rate) {
            $result = $this->exchangeRateConvert(strtoupper($data['from-currency']), strtoupper($data['to-currency']), $data['price'], $rate['rates']);
            if ($result)
                $bankRates[$rate['bank_full_name']] = $result;
        }

        if (! empty($bankRates))
            return [
                'from' => strtoupper($data['from-currency']),
                'to' => strtoupper($data['to-currency']),
                'price' => $data['price'],
                'date' => date('d-m-Y', strtotime($date)),
                'result' => $bankRates,
            ];
        return false;
    }

    /**
     * Returns list of rates for specified day time and from different feeds(banks)
     *
     * @param $timestamp
     * @return array|bool
     */
    private function getExchangeRates($timestamp) {
        $ratesFeeds = Config::get('exrates.sources', false);
        $results = [];
        if ($ratesFeeds) {
            foreach ($ratesFeeds as $ratesFeed) {
                $cacheKey = $ratesFeed['short_unique_name']
                    . '_' . $ratesFeed['currency'] . '_' . date('d-m-Y', $timestamp);

                $cacheResult = Cache::get($cacheKey);
                if (! $cacheResult) {
                    Log::info('updating for ' . $cacheKey);
                    $ratesFeed['timestamp'] = $timestamp;
                    $radeResult = $this->updateExchangeRates($ratesFeed);
                    if ($radeResult) {
                        $results[] = $radeResult;
                    }
                } else {
                    Log::info('getting from cache ' . $cacheKey);
                    $results[] = $cacheResult;
                }
            }
        }
        if (! empty($results))
            return $results;
        return false;
    }

    /**
     * Updates our exchange rates database
     *
     * Exchange rates data should be stored in next format:
     * [
     *  date => timestamp,
     *  bank_short_name => '',
     *  bank_full_name => '',
     *  currency => '',
     *  rates => [
     *      AUD => rate,
     *      ...
     *  ]
     * ]
     *
     * @param $ratesFeed
     * @return array|bool
     */
    private function updateExchangeRates($ratesFeed) {
        $result = [];
        $timestamp = $ratesFeed['timestamp'];
        try {
            $className = '\\App\\Helpers\\'.$ratesFeed['short_unique_name'].'Helper';
            $result = $className::getExchangeRates($ratesFeed);
            if ($result)
                $timestamp = $result['date'];

        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        if (! empty($result)) {
            $cacheKey = $ratesFeed['short_unique_name']
                . '_' . $ratesFeed['currency'] . '_' . date('d-m-Y', $timestamp);
            Cache::forever($cacheKey, $result);
            return $result;
        }
        return false;
    }

    /**
     * Converting currency
     *
     * @param $from
     * @param $to
     * @param $amount
     * @param $rates
     * @return bool|float
     */
    private function exchangeRateConvert($from, $to, $amount, $rates) {
        if ($from == $to)
            return $amount;
        else if ($to == "EUR") {
            if (! empty($rates[$from]))
            {
                return $amount / $rates[$from];
            }
        } else if ($from == "EUR") {
            if (! empty($rates[$to]))
            {
                return $amount * $rates[$to];
            }
        } else {
            if (! empty($rates[$from]) && ! empty($rates[$to]))
                return $amount / $rates[$from] * $rates[$to];
        }
        return false;
    }

}
