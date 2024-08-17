<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
       
    </style>
</head>

<body>
    <div class="container">
        @if ($success === 'true')
        <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">データ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">ユーザー名</th>
                <td>{{ $userData['userName'] }}</td>
            </tr>
            <tr>
                <th scope="row">有効期限</th>
                <td>{{ $userData['expireDate'] }}</td>
            </tr>
            <tr>
                <th scope="row">ログイン日時</th>
                <td colspan="2">{{ $userData['lastLogin'] }} </td>
            </tr>
        </tbody>
        </table>
        @else
        <div class="row">認証失敗</div>
        <div class="row">エラーメッセージ {{ $message }}</div>
        @endif
    </div>
</body>
</html>