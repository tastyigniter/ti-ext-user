subject = "Password reset request at {site_name}"
==

Hi, {first_name} {last_name}

Someone requested a password reset for your {site_name} account.

If you did request a password reset, copy and paste the link below in a new browser window: {reset_link}

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
                            <p class="lead">Someone requested a password reset for your {site_name} account.</p>
                            <p>If you did request a password reset,
                                <a href="{reset_link}">click here</a> to reset password or alternatively, copy and paste
                                the link below in a new browser window: {reset_link}
                            </p>
                        </td>
                    </tr>
                </table>
            </div><!-- /content -->
        </td>
        <td></td>
    </tr>
</table><!-- /BODY -->