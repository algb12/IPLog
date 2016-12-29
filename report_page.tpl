<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{title}}</title>
    <link rel="stylesheet" href=" https://necolas.github.io/normalize.css/latest/normalize.css">
    <style media="screen">
        body {
            font-family: Arial, Helvetica, Verdana, sans-serif;
            margin: 5px;
        }
        .reportTbl {
            border: 2px solid black;
            border-collapse: collapse;
            margin: 20px 0 20px 0;
        }
        .reportTbl td {
            border: 1px solid black;
            padding: 2px;
        }
        .reportTbl td:first-child {
            font-weight: bold;
        }
        .report, .disclaimer {
            border-top: 1px solid #888;
        }
    </style>
</head>
<body>
    <h1>{{title}}</h1>
    {{entries}}
    <div class="disclaimer">
        <h3>Disclaimer:</h3>
        <p>Please respect the guidelines in place for data protection – that is, use the data only with the consent of the data subject – otherwise delete it!</p>
        <p>More information on data protection can be found on the websites of <a href="https://www.privacyinternational.org/node/44">Privacy International</a> and the <a href="http://oecdprivacy.org">OECD Privacy Principles</a>.</p>
    </div>
</body>
</html>
