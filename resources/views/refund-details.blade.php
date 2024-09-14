<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
            padding: 50px;
        }
        .success-icon {
            font-size: 100px;
            color: #4CAF50;
        }
        .message {
            font-size: 24px;
            color: #333;
        }
        .card-header {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
             <h2>Refunded Payment Details</h2>
            </div>
            <div class="col-md-6">
            <a href="{{ route('home') }}" class="btn btn-primary p-3 mt-3 mb-3"> Go to Dashboard</a>
            </div>
        </div>
        </div>
        <div class="row">
            <!-- Payment Details Card -->
            
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Refunded Payment Details
                    </div>
                    <div class="card-body">
                        <p><strong>ID:</strong> {{$response['id']}}</p>
                        <p><strong>Status:</strong> {{$response['status']}}</p>
                        <p><strong>Create Time:</strong> {{$response['create_time']}}</p>
                    </div>
                </div>
            </div>
            <!-- Payee Details Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Payee Details
                    </div>
                    <div class="card-body">
                        <p><strong>Email Address:</strong>{{$response['payer']['email_address']}}</p>
                        <p><strong>Merchant ID:</strong> {{$response['payer']['merchant_id']}}</p>
                    </div>
                </div>
            </div>
            
            <!-- Amount Details Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                    Seller Payable breakdown
                    </div>
                    <div class="card-body">
                        <p><strong>Note :</strong>  {{$response['note_to_payer']}}</p>
                        <p><strong>Capture Amount:</strong> $ {{$response['amount']['value']}}</p>
                        <p><strong>Gross Amount:</strong> $ {{$response['seller_payable_breakdown']['gross_amount']['value']}}</p>
                        <p><strong>PayPal Fee:</strong> $ {{$response['seller_payable_breakdown']['paypal_fee']['value']}}</p>
                        <p><strong>Net Amount:</strong> $ {{$response['seller_payable_breakdown']['net_amount']['value']}}</p>
                        <p><strong>Total Refunded Amount:</strong> $ {{$response['seller_payable_breakdown']['total_refunded_amount']['value']}}</p>
                        <p><strong>Currency Code:</strong> {{$response['amount']['currency_code']}}</p>
                        <p><strong>invoice Id:</strong> {{$response['invoice_id']}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
