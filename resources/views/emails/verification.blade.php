<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Email</title>
</head>
<body>
    <h1>メールアドレスの確認</h1>
    <p>以下のリンクをクリックしてメールアドレスの確認を行ってください。</p>
    <p>
        <a href="{{ url('verify-email/' . $token) }}">メールアドレスを確認する</a>
    </p>
    <p>このリンクは24時間有効です。</p>
</body>
</html>
