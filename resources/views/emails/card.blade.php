<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content={{ now() }}>
    <title>Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #content {
            width: 50%;
        }

        p,
        h2 {
            margin: 0px;
            padding: 0px;
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
                <img src="images/ieia.png" alt="IEIA" width="100">
                <hr style="border:1px solid #f6f6f6">
                <h2>Halo, {{ $name ?? 'name' }}!</h2>
                <p style="margin-bottom: 20px">Membership anda telah
                    {{ $verified ?? true ? 'diaktifkan. Berikut merupakan kartu membership anda' : 'dinonaktifkan' }}.
                </p>
                @if ($verified ?? true)
                    <div id="card"
                        style="display: flex; flex-direction: column;  border-radius: 16px; background: #9695951A; padding: 10px; width: 500px">
                        <div style="display: flex; width: 100%; margin-bottom: 40px">
                            <img src="images/ieia.png" alt="IEIA" width="100">
                            <p
                                style="margin-left: auto; font-weight: 600; font-size: 30px; line-height: normal; color: #0C377A; font-family: DM Serif Text;">
                                12/IEIA/11/2023</p>
                        </div>
                        <div
                            style="border-radius: 0px 0px 7.04px 7.04px;
                        background: linear-gradient(270deg, #0C377A 0.28%, #5288DD 105.66%); padding: 15px; padding-top: 50px">
                            <div style="display: flex;">
                                <div style="flex-basis: 50%; color: white">
                                    <h2>Ricki Rinaldi Concepto</h2>
                                    <p>Mahasiswa</p>
                                </div>
                                <div style="margin-left: auto; color: white">
                                    <p>https://linkScoopus.com</p>
                                    <p>https://linkSchooler.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <p><br></p>
                <p>Terimakasih,<br><b>Team IAIE</b></p>
            </div>
        </div>
        <canvas id="canvas"></canvas>

        <div id="footer" style="text-align: center; padding-bottom: 3px">
            <p style="color: #8f8f8f">Copyright © {{ now()->year }} iarn.or.id</p>
            </p>
        </div>
    </div>
</body>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    var contentToConvert = document.getElementById("card");

    // Get the canvas element
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");

    // Set the canvas size to match the content
    canvas.width = contentToConvert.offsetWidth;
    canvas.height = contentToConvert.offsetHeight;

    // Draw the HTML content onto the canvas
    html2canvas(contentToConvert).then(function(newCanvas) {
        // Remove the existing canvas
        var existingCanvas = document.getElementById("canvas");
        existingCanvas.parentNode.removeChild(existingCanvas);

        // Append the new canvas to replace the existing one
        document.body.appendChild(newCanvas);
    });
</script>

</html>
