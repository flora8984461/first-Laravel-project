<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SignUp confirm email</title>
</head>
<body>
<h1>Thank you for registering at Weibo Web App!</h1>

<p>
    Please use the following link to activate your account：
    <a href="{{ route('confirm_email', $user->activation_token) }}">
        {{ route('confirm_email', $user->activation_token) }}
    </a>
</p>

<p>
    If this is not your action, please ignore this message.
</p>
</body>
</html>