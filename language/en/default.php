<?php

return [
    'text_title' => 'My Account',

    'text_heading' => 'Account',
    'text_account' => 'My Account',
    'text_edit_details' => 'Edit My Details',
    'text_address' => 'Address Book',
    'text_orders' => 'Recent Orders',
    'text_reservations' => 'Recent Reservations',
    'text_inbox' => 'My Inbox',
    'text_welcome' => 'Welcome %s',
    'text_cart_summary' => 'You have %s items: %s',
    'text_change_password' => 'Change Password',
    'text_order' => 'ORDER NOW',
    'text_checkout' => 'CHECKOUT NOW',
    'text_edit' => 'EDIT',
    'text_default_address' => 'My Default Address',
    'text_no_default_address' => 'You don\'t have a default address',
    'text_no_orders' => 'There are no orders available to show.',
    'text_no_reservations' => 'There are no reservations available to show.',
    'text_no_inbox' => 'There are no messages available to show',
    'text_no_cart_items' => 'There are no menus added in your cart.',

    'text_logout' => 'Logout',
    'text_logged_in' => 'Already have an account? <a href="%s">Login Here</a>',
    'text_logged_out' => 'Welcome back <b>%s</b>, Not You? <a href="javascript:;" data-request="%s">Logout</a>',

    'label_heading' => 'Heading:',
    'label_template' => 'Mail template',
    'label_send_to' => 'Send To',
    'label_send_to_staff_group' => 'Send To Staff Group',
    'label_send_to_custom' => 'Send To Email Address',

    'column_date' => 'Date/Time',
    'column_subject' => 'Subject',

    'alert_logout_success' => 'You have been logged out successfully.',

    'text_send_to_restaurant' => 'Restaurant email address',
    'text_send_to_location' => 'Location email address (if available)',
    'text_send_to_staff_email' => 'Staff email address (if available)',
    'text_send_to_customer_email' => 'Customer email address (if available)',
    'text_send_to_custom' => 'Specific email address',
    'text_send_to_staff_group' => 'Staff Group',

    'login' => [
        'label_password' => 'Password',
        'label_password_confirm' => 'Password Confirm',
        'label_remember' => 'Remember me',
        'label_activation' => 'Activation Code',
        'label_newsletter' => 'Keep me up-to-date with offers by email.',
        'label_terms' => 'By clicking Register, you agree to the <a target="_blank" href="%s">Terms and Conditions</a> set out by this site, including our Cookie Use.',
        'label_i_agree' => 'I Agree',
        'label_subscribe' => 'Subscribe',

        'button_terms_agree' => 'I Agree',
        'button_subscribe' => 'Subscribe',
        'button_login' => 'Login',
        'button_register' => 'Register',

        'error_email_exist' => 'The Email address already has an account, please log in',

        'alert_logout_success' => 'You have been logged out successfully.',
        'alert_expired_login' => 'Session expired, please login',
        'alert_invalid_login' => 'Username and password not found!',
        'alert_account_created' => 'Account created successfully, login below!',
        'alert_account_activation' => 'An activation email has been sent to your email address.',
        'alert_registration_disabled' => 'Registration is currently disabled by the site administrator.',

        'activity_registered_account' => ' <b>created</b> an account.',
    ],

    'session' => [
        'component_title' => 'Session Component',
        'component_desc' => 'Adds auth session to a page and restricts page access.',
    ],

    'account' => [
        'component_title' => 'Account Component',
        'component_desc' => 'Displays account dashboard',
        'text_heading' => 'Address Book',
        'text_my_account' => 'My Account',
        'text_edit_heading' => 'Address Book Edit',
        'text_no_address' => 'You don\'t have any stored address(s)',
        'text_edit' => 'EDIT',
        'text_delete' => 'DELETE',

        'button_back' => 'Back',
        'button_add' => 'Add New Address',
        'button_update' => 'Update Address',

        'label_address_1' => 'Address 1',
        'label_address_2' => 'Address 2',
        'label_city' => 'City',
        'label_state' => 'State',
        'label_postcode' => 'Postcode',
        'label_country' => 'Country',

        'alert_updated_success' => 'Address added/updated successfully.',
        'alert_deleted_success' => 'Address deleted successfully.',
    ],
    'reset' => [
        'component_title' => 'Password Reset Component',
        'component_desc' => 'Displays password reset form',

        'text_heading' => 'Account Password Reset',
        'text_summary' => 'Email address you use to log in to your account We\'ll send you an email with a new password.',

        'label_email' => 'Email Address',
        'label_password' => 'Password',
        'label_password_confirm' => 'Confirm Password',
        'label_code' => 'Reset Code',

        'button_login' => 'Login',
        'button_reset' => 'Reset Password',

        'alert_reset_success' => 'Password reset successfully.',
        'alert_reset_request_success' => 'Password reset request successfully, please check your email on how to proceed.',
        'alert_reset_error' => 'Password reset unsuccessful, email not found or incorrect details entered.',
        'alert_reset_failed' => 'Password reset failed, reset code is either invalid or expired.',
        'alert_activation_failed' => 'Account activation failed, please try again.',
        'alert_no_email_match' => 'No matching email address',
    ],
    'addressbook' => [
        'component_title' => 'Account Address Book Component',
        'component_desc' => 'Displays and manages account address book',
    ],
    'settings' => [
        'component_title' => 'Account Settings Component',
        'component_desc' => 'Manages account settings',
        'text_heading' => 'My Details',
        'text_details' => 'Edit your details',
        'text_password_heading' => 'Change Password',

        'button_subscribe' => 'Subscribe',
        'button_back' => 'Back',
        'button_save' => 'Save Details',

        'label_first_name' => 'First Name',
        'label_last_name' => 'Last Name',
        'label_email' => 'Email Address',
        'label_password' => 'New Password',
        'label_password_confirm' => 'New Password Confirm',
        'label_old_password' => 'Old Password',
        'label_telephone' => 'Telephone',
        'label_s_question' => 'Security Question',
        'label_s_answer' => 'Security Answer',
        'label_newsletter' => 'Keep me up-to-date with offers by email.',

        'error_password' => 'The %s you entered does not match.',

        'alert_updated_success' => 'Details updated successfully.',
    ],
];