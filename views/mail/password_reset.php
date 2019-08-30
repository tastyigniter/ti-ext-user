subject = "Password reset at {site_name}"
==
Hi {first_name} {last_name},

Your password was changed successfully!

Please login using your new password:
{account_login_link}

If you think this password update was a mistake, reset your password immediately.
==
Hi {first_name} {last_name},

## Your password was changed successfully!

@partial('button', ['url' => '{account_login_link}', 'type' => 'primary'])
Login using your new password
@endpartial

If you think this password update was a mistake, reset your password immediately.