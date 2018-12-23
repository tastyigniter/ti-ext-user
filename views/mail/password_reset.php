subject = "Password reset at {site_name}"
==

Hi, {first_name} {last_name}

Your password was changed successfully!

Please login using your new password.

If you think this password update was a mistake, reset your password immediately.

==
<!-- HEADER -->
<table class="head-wrap" bgcolor="#D7D7DE">
    <tr>
        <td></td>
        <td class="header container">
            <div class="content">
                <table bgcolor="#D7D7DE">
                    <tr>
                        <td><img src="{site_logo}"/></td>
                        <td align="right"><h6 class="collapse">{site_name}</h6></td>
                    </tr>
                </table>
            </div>
        </td>
        <td></td>
    </tr>
</table><!-- /HEADER -->
<!-- BODY -->
<table class="body-wrap">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">
            <div class="content">
                <table>
                    <tr>
                        <td>
                            <h3>Hi, {first_name} {last_name}</h3>
                            <p class="lead">Your password was changed successfully!</p>
                            <p>Please
                                <a href="{account_login_link}">login</a> using your new password.
                            </p>
                            <p>If you think this password update was a mistake, reset your password immediately.</p>
                        </td>
                    </tr>
                </table>
            </div><!-- /content -->
        </td>
        <td></td>
    </tr>
</table><!-- /BODY -->