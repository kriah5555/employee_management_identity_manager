<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>We have received your request to reset your account password.</p>
    <p>You can use the following code to recover your account:</p>
    <div style="background-color: #f0f0f0; padding: 10px;">
        {{ $code }}
    </div>
    <p>The code is valid for 15 minutes from the time this message was sent.</p>
</body>
</html>
