<!DOCTYPE html>
<html>
    <head>
        <title>Exchange calculator</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
              integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container">
            <div class="exchange-rates">
                <div class="row">
                    <h1>Exchange rates</h1>
                    <p>Valid as of: {{$latest_rates['date']}}</p>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Foreign currency</th>
                                <th>Symbol</th>
                                @foreach ($latest_rates['banks'] as $bank)
                                <th>{{$bank}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latest_rates['rates'] as $symbol => $rateDetails)
                            <tr>
                                <td>{{$rateDetails['full_name']}}</td>
                                <td>{{$symbol}}</td>
                                @foreach ($rateDetails['rates'] as $rate)
                                <td>{{$rate}}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p>Exchange rates reflect the rate in reference to one euro i.e. are shown in the form of 1 euro =
                        X units of foreign currency.</p>
                </div>
            </div>
            <div class="exchange-calculator">
                <div class="row">
                    <h1>Exchange calculator</h1>
                    <p class="text-danger form-error"></p>
                    <form class="form-horizontal" id="calculator-form">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Price</label>
                            <div class="col-sm-3 col-md-2">
                                <input type="text" class="form-control" id="price" name="price" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">From</label>
                            <div class="col-sm-4">
                                <select  class="form-control from-currency" id="from-currency" name="from-currency">
                                    @foreach ($currency_list as $symbol => $fullName)
                                        <option value="{{mb_strtolower($symbol)}}">{{$symbol}} - {{$fullName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-1 control-label">To</label>
                            <div class="col-sm-4">
                                <select class="form-control to-currency" id="to-currency" name="to-currency">
                                    @foreach ($currency_list as $symbol => $fullName)
                                        <option value="{{mb_strtolower($symbol)}}">{{$symbol}} - {{$fullName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Date</label>
                            <div class="col-sm-3 col-md-2">
                                <input data-provide="datepicker" id="date" name="date" class="datepicker" required>
                            </div>
                        </div>
                        <button class="btn btn-default calculate-btn">Convert</button>
                        <span class="converting-label">Converting...</span>
                    </form>
                    <div class="exchange-results">

                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.1/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.1/css/bootstrap-datepicker.min.css"
              rel="stylesheet" type="text/css">
        <script type="text/javascript">
            $(document).ready(function() {
                $('.datepicker').datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true
                });
                $(".calculate-btn").click(function() {
                    $('.form-error').hide();
                    $('.exchange-results').hide();
                    $('.calculate-btn').hide();
                    $('.converting-label').show();

                    if (validateForm()) {
                        $.ajax({
                            type: "POST",
                            url: "calculate",
                            data: $('#calculator-form').serialize(),
                            error: function(jqXHR) {
                                if (jqXHR.responseText) {
                                    var responce = JSON.parse(jqXHR.responseText);
                                    if (Object.prototype.toString.call(responce) === '[object Array]'
                                    && responce.length > 0) {
                                        var errors = responce.join('<br />');
                                        $('.form-error').html(errors);
                                    } else
                                        $('.form-error').text('An error has occurred');
                                } else {
                                    $('.form-error').text('An error has occurred');
                                }
                                $('.form-error').show().focus();


                                $('.converting-label').hide();
                                $('.calculate-btn').show();
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data && validateResponse(data)) {
                                    showExchangeResults(data);
                                } else {
                                    $('.form-error').text('An error has occurred');
                                    $('.form-error').show().focus();
                                }

                                $('.converting-label').hide();
                                $('.calculate-btn').show();
                            }
                        });
                        return false;
                    }
                });
                function validateForm() {
                    var price = $("input#price").val();
                    if (price == "") {
                        $("input#price").focus();
                        return false;
                    }
                    var date = $("input#date").val();
                    if (date == "") {
                        $("input#date").focus();
                        return false;
                    }
                    return true;
                }
                function validateResponse(data) {
                    console.log(data); // to debug
                    try {
                        if (data.hasOwnProperty('to')
                                && data.hasOwnProperty('price')
                                && data.hasOwnProperty('result')
                                && data.hasOwnProperty('date')
                                && data.hasOwnProperty('from')
                                && Object.keys(data.result).length > 0)
                            return true;
                    } catch(e) {
                        console.log(e); // to debug
                    }
                    return false;
                }
                function showExchangeResults(data) {
                    var result = '<ul class="banks-result">';
                    $.each(data.result, function(index, value) {
                        result += '<li><span class="bank-name">' + index + ':</span>' + parseFloat(value).toFixed(2) + ' ' + data.to + '</li>'
                    });
                    result += '</ul>';

                    result = '<h2>Exchange results</h2><span class="result-label">From:</span>' + data.from + '<br />' +
                            '<span class="result-label">To:</span>' + data.to + '<br />' +
                            '<span class="result-label">Price:</span>' + data.price + ' ' + data.from + '<br />' +
                            '<span class="result-label">Date:</span>' + data.date + '<br /><br />' +
                            result;

                    $('.exchange-results').html(result).show();
                    $('html, body').animate({
                        scrollTop: $('.exchange-results').offset().top
                    }, 1000);

                }
            });
        </script>

    </body>
</html>
