step 0: form :
---------------
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel - Paypal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
   <div class="container py-5">
   
    <div class="row">
    <h1 class="py-4">PayPal Integration</h1>
    <div class="col-lg-3">
    <div class="card">
    <div class="card-header">Make Payment</div>
    <div class="card-body">
            
           
            <form action="{{ route('paypal') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="product_name" class="form-label">Product name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="$0" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                </div>
                <button type="submit" class="btn btn-primary">Pay with PayPal</button>
            </form>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
         <div class="card">
            <div class="card-header">Payment History</div>
            <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">#Transaction Id</th>
                    <th scope="col">Product</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                    <tr>
                    <th scope="row">{{$payment->id}}</th>
                    <td>{{$payment->capture_id}}</td>
                    <td>{{$payment->product_name}}</td>
                    <td>{{$payment->quantity}}</td>
                    <td>$ {{$payment->capture_amount}}</td>
                    @if ($payment->payment_status == 'COMPLETED')
                    <td><span class="badge text-bg-success">{{$payment->payment_status}}</span></td>
                    @else
                    <td><span class="badge text-bg-danger">{{$payment->payment_status}}</span></td>
                    @endif
                    
                    <td>
                       <a href="{{ route('details',$payment->capture_id) }}" class="btn btn-primary btn-sm"><i class="fa fa-credit-card payment-details-icon"></i> Details</a>
                        @if (is_null($payment->refund_id))
                        <a href="{{ route('refund',$payment->capture_id) }}" class="btn btn-danger btn-sm"><i class="fa fa-undo refund-icon"></i> Refund</a>
                        @else
                        <a href="{{ route('refunddetails',$payment->refund_id) }}" class="btn btn-info btn-sm"><i class="fa fa-credit-card payment-details-icon"></i> Refund Details</a>
                        @endif
                    </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
                </table>
            </div>
         </div>
    </div>
    </div>





   </div>
   </div>
</body>
</html>



route
-----------

Route::get('/',[PaypalController::class, 'index'])->name('home');
Route::post('/paypal',[PaypalController::class, 'paypal'])->name('paypal');
Route::get('/success',[PaypalController::class, 'success'])->name('success');
Route::get('/cancel',[PaypalController::class, 'cancel'])->name('cancel');
Route::get('/details/{capture_id}',[PaypalController::class, 'details'])->name('details');
Route::get('/refund/{capture_id}',[PaypalController::class, 'refund'])->name('refund');
Route::get('/refund-details/{refund_id}',[PaypalController::class, 'refundDetails'])->name('refunddetails');


Step 1 : composer require srmklive/PayPal
Step 2: php artisan vendor:publish --provider "Srmklive\PayPal\Providers\PayPalServiceProvider"
Step 3: check Installation on below link
https://srmklive.github.io/laravel-paypal/docs.html

step 4: configure paypal.php

step 5: Model & Migration
-----------
  public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->string('capture_id');
            $table->string('product_name');
            $table->integer('quantity');
            $table->double('capture_amount');
            $table->string('currency');
            $table->string('payer_name');
            $table->string('payer_email');
            $table->string('payment_status');
            $table->double('platform_fees');
            $table->string('payment_method');
            $table->double('seller_receivable_final_amount');
            $table->string('refund_id')->nullable();
            $table->timestamps();
        });
    }

step 6:controller
---------------
<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\PaymentServices;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    public function __construct(PaymentServices $paymentServices)
    {
        $this->paymentServices = $paymentServices;
    }
    public function index()
    {
        $payments = Payment::all();
        return view('welcome', compact('payments'));
    }
    public function paypal(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD", // Ensure this matches your intended currency
                        "value" => $request->input('price') // Correct way to access request data
                    ]
                ]
            ]
        ]);

        // Debugging and redirect handling
        if (isset($response['id']) && $response['status'] === 'CREATED') {
            session()->put('product_name', $request->product_name);
            session()->put('quantity', $request->quantity);
            // Redirect to PayPal for approval
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        } else {
            // Handle errors or invalid responses
            return redirect()->route('cancel')->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }
    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        // Capture the order using the token received in the query parameters
        $response = $provider->capturePaymentOrder($request->query('token'));
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
          
            // save the payment information in your database
            $payment = new Payment;
            $payment->invoice_id = $response['id'];
            $payment->capture_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            $payment->product_name = session()->get('product_name');
            $payment->quantity = session()->get('quantity');
            $payment->capture_amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment->payer_name = $response['payer']['name']['given_name'];
            $payment->payer_email = $response['payer']['email_address'];
            $payment->payment_status = $response['status'];
            $payment->platform_fees = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value'];
            $payment->seller_receivable_final_amount = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['net_amount']['value'];
            $payment->payment_method = "PayPal";
            $payment->save();
            unset($_SESSION['product_name']);
            unset($_SESSION['quantity']);
            return view('success')->with('message', 'Payment completed successfully!');
        } else {
            // Handle errors or invalid responses
            return redirect()->route('cancel')->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function cancel()
    {
        return view('cancel')->with('message', 'Payment was cancelled.');
    }

    public function refund($capture_id)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $payment = Payment::where('capture_id', $capture_id)->first();

        try {
            // Ensure correct arguments are passed
            $note_to_payer = 'Defective product';
            $response = $provider->refundCapturedPayment($payment->capture_id, $payment->invoice_id, $payment->capture_amount, $note_to_payer); // Adjusted parameter order

            //dd($response);  // For debugging; remove this in production

            // Access the data directly from $response (not $response['data'])
            if (isset($response['id']) && $response['status'] === 'COMPLETED') {
                // Refund was successful, update the payment status in the database
                Payment::where('capture_id', $capture_id)->update(['payment_status' => 'REFUNDED', 'refund_id' => $response['id']]);
                return view('success')->with('message', 'Payment completed successfully!');
            } else {
                // Handle errors or invalid responses
                return redirect()->route('cancel')->with('error', $response['message'] ?? 'Refund failed.');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->route('cancel')->with('error', $e->getMessage());
        }
    }

    
    public function details($capture_id)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $payment = Payment::where('capture_id', $capture_id)->first();
        $response = $provider->showCapturedPaymentDetails($payment->capture_id);
        // dd($response);
        return view('payment-details', compact('response'));
    }
    public function refundDetails($refund_id)
    {   
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $payment = Payment::where('refund_id', $refund_id)->first();
        $response = $provider->showRefundDetails($payment->refund_id);
        // dd($response);
        return view('refund-details', compact('response'));
    }
}


step 7: success.blade.php
------------------------
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f0f8ff;
        }
        .success-icon {
            font-size: 100px;
            color: #4CAF50;
        }
        .message {
            font-size: 24px;
            color: #333;
        }
        .details {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <i class="fa fa-check-circle success-icon"></i>
    <h1 class="message">Payment Successful!</h1>
    <p class="details">Thank you for your purchase. Your transaction was completed successfully.</p>
</body>
</html>


Step 8 : cancel.blade.php
------------------------
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #fff8f0;
        }
        .cancel-icon {
            font-size: 100px;
            color: #f44336;
        }
        .message {
            font-size: 24px;
            color: #333;
        }
        .details {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <i class="fa fa-times-circle cancel-icon"></i>
    <h1 class="message">Payment Cancelled</h1>
    <p class="details">The payment was cancelled. If you have any questions, please contact our support.</p>
</body>
</html>

Step 9 : refund
-----------------
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f0f8ff;
        }
        .success-icon {
            font-size: 100px;
            color: #4CAF50;
        }
        .message {
            font-size: 24px;
            color: #333;
        }
        .details {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <i class="fa fa-check-circle success-icon"></i>
    <h1 class="message">Refund Successful!</h1>
    <p class="details">Thank you for your purchase. Your Refund was completed successfully.</p>
   
</body>
</html>

step 10 :payment-details.blade
-----------------------------
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
            <h2>Payment Details</h2>
            </div>
            <div class="col-md-6">
            <a href="{{ route('home') }}" class="btn btn-primary p-2 mt-3 mb-3"> Go to Dashboard</a>
            </div>
        </div>
        </div>
        <div class="row">
            <!-- Payment Details Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        Payment Details
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
                        <p><strong>Email Address:</strong>{{$response['payee']['email_address']}}</p>
                        <p><strong>Merchant ID:</strong> {{$response['payee']['merchant_id']}}</p>
                    </div>
                </div>
            </div>
            
            <!-- Amount Details Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                    Seller Receivable breakdown
                    </div>
                    <div class="card-body">
                        <p><strong>Capture Amount:</strong> $ {{$response['amount']['value']}}</p>
                        <p><strong>Gross Amount:</strong> $ {{$response['seller_receivable_breakdown']['gross_amount']['value']}}</p>
                        <p><strong>PayPal Fee:</strong> $ {{$response['seller_receivable_breakdown']['paypal_fee']['value']}}</p>
                        <p><strong>Net Amount:</strong> $ {{$response['seller_receivable_breakdown']['net_amount']['value']}}</p>
                        <p><strong>Currency Code:</strong> {{$response['amount']['currency_code']}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

step 11:refund-details.blade
-------------------------------
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


