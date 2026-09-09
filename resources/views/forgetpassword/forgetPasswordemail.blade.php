

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; padding: 20px;">

    <table style="max-width: 600px; width: 100%; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="text-align: center; padding-bottom: 20px;">
                
            <h3 style="color: #333;">Tracker</h3>
                <h2 style="color: #333;">Forgot Your Password?</h2>
            </td>
        </tr>
        <tr>
            <td>
                <p style="font-size: 16px;">Hello!</p>
                <p style="font-size: 14px;">Email: {{ $email }}</p>
                <p style="font-size: 16px;">You recently requested to reset your password for your account. Click the button below to reset it.</p>
                <p style="font-size: 16px;"><strong style="font-size: 18px;">Reset Password:</strong></p>
                <p style="text-align: center;">
                    <a href="{{ route('reset.password.get', $token) }}" style="display: inline-block; background-color: #007bff; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">Reset Password</a>
                </p>
                <p style="font-size: 16px;">If you did not request a password reset, please ignore this email or reply to let us know. This link is valid for [1 hours].</p>
                <p style="font-size: 16px;">Thanks,<br>Wavy Informatics</p>
            </td>
        </tr>
    
    If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: https://tracker.wavyinformatics.com/reset-password/{{ $token }}
    </table>
</body>
</html>

