<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .row {
            margin-top: 300px;
        }

        .caution {
            margin-top: 20px;
        }

        .btn {
            margin: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="d-flex align-items-center justify-content-center">
                    <a class="btn btn-primary" role="button" href="https://dev.plate.id/api/auth/signup/openid-connect?redirectUrl=https://dev.plate.id/api/auth/test/callback&projectId=99999&type=minapitadev">
                        新規登録
                    </a>
                    <a class="btn btn-secondary" role="button" href="https://dev.plate.id/api/auth/signin/openid-connect?redirectUrl=https://dev.plate.id/api/auth/test/callback&projectId=99999&type=minapitadev">
                        サインイン
                    </a>
                </div>
                <p class="caution text-center">
                    
                </p>
            </div>
        </div>

    </div>
</body>
</html>