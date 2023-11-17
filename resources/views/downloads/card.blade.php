<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content={{ now() }}>
    <title>Membership Card</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=DM+Sans:wght@400;500;700&family=DM+Serif+Text&display=swap"
        rel="stylesheet">


    <style>
        #content {
            width: 50%;
        }

        #card {
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background-image: url('/images/bg-card.png');
            background-size: 100% 100%;
            padding: 10px;
            width: 380px;
            height: 182px;
        }

        #member-id {
            margin-left: auto;
            font-weight: 600;
            font-size: 25px;
            line-height: normal;
            color: #0C377A;
            font-family: DM Serif Text;
        }

        #bottom-card {
            border-radius: 0px 0px 7.04px 7.04px;
            background-image: url('/images/bg-bottom-card.png');
            background-size: 100% 100%;
            padding: 15px;
            padding-top: 35px;
            position: relative;
        }

        .exp-text {
            color: #FFF;
            font-family: DM Sans;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .date-text {
            margin-left: 5px;
            color: #FFF;
            font-family: DM Sans;
            font-size: 12px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .link-url {
            color: #FFF;
            font-family: DM Sans;
            font-size: 13px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
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
    <div id="card">
        <div style="display: flex; width: 100%; margin-bottom: 40px">
            <img src="/images/ieia.png" alt="IEIA" width="80">
            <p id="member-id">
                {{ $memberId }}</p>
        </div>
        <div id="bottom-card">
            <div style="position: absolute; top:10px; right: 15px; ">
                <span class="exp-text">Exp Date </span><span class="date-text"> {{ $expired_at }}</span>
            </div>
            <div style="display: flex;">
                <div style="flex-basis: 50%; color: white">
                    <h2>{{ $name ?? 'Name' }}</h2>
                    <p>{{ $status ?? '-' }}</p>
                </div>
                <div style="margin-left: auto; color: white">
                    <p class="link-url">{{ $linkScopus ?? '-' }}</p>
                    <p class="link-url">{{ $linkSchooler ?? '-' }}</p>
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
