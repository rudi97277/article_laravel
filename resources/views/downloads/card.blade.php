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
    <div id="card"
        style="display: flex; flex-direction: column;  border-radius: 16px; background: #9695951A; padding: 10px; width: 500px">
        <div style="display: flex; width: 100%; margin-bottom: 40px">
            <img src="/images/ieia.png" alt="IEIA" width="100">
            <p
                style="margin-left: auto; font-weight: 600; font-size: 30px; line-height: normal; color: #0C377A; font-family: DM Serif Text;">
                {{ $memberId }}</p>
        </div>
        <div
            style="border-radius: 0px 0px 7.04px 7.04px;
                        background: linear-gradient(270deg, #0C377A 0.28%, #5288DD 105.66%); padding: 15px; padding-top: 50px">
            <div style="display: flex;">
                <div style="flex-basis: 50%; color: white">
                    <h2>{{ $name ?? 'Name' }}</h2>
                    <p>{{ $status ?? '-' }}</p>
                </div>
                <div style="margin-left: auto; color: white">
                    <p>{{ $linkScopus ?? '-' }}</p>
                    <p>{{ $linkSchooler ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    var element = document.getElementById("card");

    html2canvas(element, {
        scale: 2,
    }).then(function(canvas) {
        var link = document.createElement("a");
        link.download = "card.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
    });
</script>

</html>
