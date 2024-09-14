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
