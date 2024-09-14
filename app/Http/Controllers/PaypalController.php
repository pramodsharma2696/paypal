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
