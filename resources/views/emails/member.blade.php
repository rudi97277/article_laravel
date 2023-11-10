<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content={{now()}}>
    <title>Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #content {
          width: 50%;
        }
        
        @media only screen and (max-width: 768px) {
            #content {
                width: 90%;
            }
        }
        </style>
</head>
<body style="font-family: 'Roboto', sans-serif; font-size:14px">
    <div id="background" style="text-align: left; background-color:#f6f6f6;">
        <div id="content" style=" background-color:white; margin:auto">
            <hr style="padding: 0; margin:0;  border: 3px solid #0C377A; border-radius:5px">
            <div id="inner-content" style="padding: 20px;">
                <img src="{{url('images/ieia.png')}}" alt="IEIA" width="100" >
                <hr style="border:1px solid #f6f6f6">
                <h2>Halo, {{$name ?? 'name'}}!</h2>
                <p>Terimakasih telah mendaftar membership di website kami. Silahkan melakukan pembayaran pada rekening berikut.</p>
                <p><b>PT TIMBUL TENGGELAM </b><br>BRI 106-112-2232-1123</p>
                <p>Kirimkan bukti pembayaran anda pada nomor Whatsapp berikut.</p>
                <p><b>Admin Whatsapp</b><br>0853-1123-4354</p>
                <p><br></p>
                <p>Terimakasih,<br><b>Team IAIE</b></p>
            </div>
        </div>
        <div id="footer" style="text-align: center; padding-bottom: 3px">
            <p style="color: #8f8f8f">Copyright © {{now()->year}} iarn.or.id</p>
            </p>
        </div>
    </div>
</body>
</html>
