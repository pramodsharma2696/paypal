<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
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
    @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
    @endif
</body>
</html>
