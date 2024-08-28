<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rates</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .forex-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .forex-table th,
        .forex-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .forex-table th {
            background-color: #007bff;
            color: white;
        }

        .conversion-form {
            padding: 20px;
            max-width: 400px;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin: 20px;
            items-align: center;
            max-width: 1200px;
            margin: auto;
        }

        .conversion-form label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
        }

        .conversion-form select,
        .conversion-form input,
        .conversion-form button {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .conversion-form select {
            background-color: #fff;
        }

        .conversion-form input {
            background-color: #fff;
        }

        .conversion-form button {
            background-color: #4a90e2;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .conversion-form button:hover {
            background-color: #357ABD;
        }

        #conversion-result {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>

<body>
    <h1>Forex Rates</h1>
    <div class="conversion-form">
        <div>
            <select id="currency"></select>
        </div>
        <div>
            <input type="number" id="amount" placeholder="Amount" min="0">
        </div>

        <div>
            <button id="convert">Convert</button>
        </div>
    </div>
    <p id="conversion-result"></p>
    <table class="forex-table">
        <thead>
            <tr>
                <th>Currency</th>
                <th>Rate</th>
            </tr>
        </thead>
        <tbody id="forex-rates">
        </tbody>
    </table>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('fetch-rates') }}',
                method: 'GET',
                dataType: 'json',
                success: function(forexRates) {
                    let ratesHtml = '';
                    let optionsHtml = '<option value="">Select Currency</option>';

                    $.each(forexRates, function(currency, rate) {
                        ratesHtml += `<tr><td>${currency}</td><td>${rate}</td></tr>`;
                        optionsHtml += `<option value="${currency}">${currency}</option>`;
                    });

                    $('#currency').html(optionsHtml).select2({
                        placeholder: 'Select a Currency',
                    });

                    $('#forex-rates').html(ratesHtml);
                },
                error: function() {
                    alert('Failed to fetch Forex rates.');
                }
            });

            $('#convert').on('click', function() {
                const amount = $('#amount').val();
                const currency = $('#currency').val();

                if (amount && currency) {
                    $.ajax({
                        url: '{{ route('convert-currency') }}',
                        method: 'POST',
                        data: {
                            amount: amount,
                            currency: currency,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#conversion-result').text(
                                `Converted Amount: ${response.convertedAmount} ${currency.replace(/^USD_/, '')}`
                                );
                        },
                        error: function(jqXHR) {
                            if (jqXHR.status === 422) {
                                const errors = jqXHR.responseJSON.errors;
                                let errorMessages = 'Validation errors:\n';

                                $.each(errors, function(field, messages) {
                                    messages.forEach(function(message) {
                                        errorMessages += `${message}\n`;
                                    });
                                });

                                alert(errorMessages);
                            } else {
                                alert('Failed to convert currency.');
                            }
                        }
                    });
                } else {
                    if (!amount || amount <= 0) {
                        alert('Please enter a valid amount.');
                    }

                    if (!currency) {
                        alert('Please select a currency.');
                    }

                }
            });
        });
    </script>
</body>

</html>
