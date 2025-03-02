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
    'text_set_default' => 'MAKE DEFAULT',
    'text_default_address' => 'My Default Address',
    'text_no_default_address' => 'You don\'t have a default address',
    'text_no_orders' => 'There are no orders available to show.',
    'text_no_reservations' => 'There are no reservations available to show.',
    'text_no_inbox' => 'There are no messages available to show',
    'text_no_cart_items' => 'There are no menus added in your cart.',

    'text_mail_admin_password_reset' => 'Password reset email to admin',
    'text_mail_admin_password_reset_request' => 'Password reset request email to admin',
    'text_mail_password_reset' => 'Password reset email to customer',
    'text_mail_password_reset_request' => 'Password reset request email to customer',
    'text_mail_registration' => 'Registration email to customer',
    'text_mail_registration_alert' => 'Registration alert email to admin',
    'text_mail_activation' => 'Registration activation email to customer',
    'text_mail_invite' => 'Invite email to staff to access the TastyIgniter Admin',
    'text_mail_invite_customer' => 'Invite email to customer to place an order',

    'text_permission_group' => 'User',
    'text_permission_customer_groups' => 'Manage customer groups',
    'text_permission_customers' => 'Create and manage customers',
    'text_permission_impersonate_staff' => 'Ability to impersonate staff members',
    'text_permission_impersonate_customers' => 'Ability to impersonate customers',
    'text_permission_user_groups' => 'Manage user groups',
    'text_permission_staffs' => 'Create and manage staff members',
    'text_permission_delete_staffs' => 'Ability to delete staff members',
    'text_permission_delete_customers' => 'Ability to delete customers',

    'text_side_menu_user' => 'Staff members',
    'text_side_menu_customer' => 'Customers',
    'text_side_menu_customer_group' => 'Groups',
    'text_side_menu_user_group' => 'Groups',
    'text_side_menu_user_role' => 'Roles',

    'text_logout' => 'Logout',
    'text_impersonating_user' => 'Impersonating user',
    'text_leave' => 'Leave',

    'label_heading' => 'Heading:',
    'label_template' => 'Mail template',
    'label_send_to' => 'Send To',
    'label_send_to_staff_group' => 'Send To User Group',
    'label_send_to_custom' => 'Send To Email Address',
    'label_allow_registration' => 'Allow customer registration',
    'label_registration_email' => 'Send Registration Email',

    'column_date' => 'Date/Time',
    'column_subject' => 'Subject',

    'alert_logout_success' => 'You have been logged out successfully.',

    'text_send_to_restaurant' => 'Restaurant email address',
    'text_send_to_location' => 'Location email address (if available)',
    'text_send_to_staff_email' => 'User email address (if available)',
    'text_send_to_customer_email' => 'Customer email address (if available)',
    'text_send_to_custom' => 'Specific email address',
    'text_send_to_staff_group' => 'User Group',
    'text_send_to_customer_group' => 'Customer Group',
    'text_send_to_all_staff' => 'All Staff members',
    'text_send_to_all_customer' => 'All Customers',
    'text_tab_user' => 'Customer registration',
    'text_tab_desc_user' => 'Configure registration email confirmation ...',

    'help_allow_registration' => 'If this is disabled customers can only be created by administrators.',
    'help_registration_email' => 'Send a confirmation mail to the customer and/or admin email after successfully account registration',

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

        'error_email_exist' => 'The Email address already has an account, please log in',

        'alert_logout_success' => 'You have been logged out successfully.',
        'alert_expired_login' => 'Session expired, please login',
        'alert_invalid_login' => 'Username and password not found!',
        'alert_account_created' => 'Account created successfully, login below!',
        'alert_account_activation' => 'An activation email has been sent to your email address.',
        'alert_registration_disabled' => 'Registration is currently disabled by the site administrator.',

        'notify_registered_account_title' => 'Customer registered',
        'notify_registered_account' => '<b>%s</b> created an account.',
    ],

    'account' => [
        'text_heading' => 'Address Book',
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
    'settings' => [
        'text_heading' => 'My Details',
        'text_details' => 'Edit your details',
        'text_password_heading' => 'Change Password',

        'button_subscribe' => 'Subscribe',
        'button_back' => 'Back',
        'button_delete' => 'Delete account',
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
        'alert_deleted_success' => 'Account deleted successfully.',
        'alert_delete_confirm' => 'Are you sure you want to delete your account? This cannot be undone.',
    ],

    'user_groups' => [
        'text_title' => 'User Groups',
        'text_form_name' => 'User Group',
        'text_empty' => 'There are no user groups available.',
        'text_round_robin' => 'Round Robin',
        'text_load_balanced' => 'Load Balanced',

        'label_auto_assign' => 'Automatic Order Assignment',
        'label_assignment_mode' => 'Assignment Mode',
        'label_assignment_availability' => 'Assignment Availability',
        'label_load_balanced_limit' => 'Load Balanced Limit',

        'column_users' => '# Users',

        'alert_no_available_assignee' => 'No available assignee.',

        'help_auto_assign' => 'Allocate and control the number of orders assigned to user in this group.',
        'help_round_robin' => 'Assign orders to the user who are online in a circular fashion.',
        'help_load_balanced' => 'Limit the number of orders a user can handle simultaneously.',
        'help_load_balanced_limit' => 'Maximum number of orders per staff.',
        'help_assignment_availability' => 'Allow user to control their availability for automatic order assignment',
    ],

    'user_roles' => [
        'text_title' => 'User Roles',
        'text_form_name' => 'User Roles',
        'text_tab_permission' => 'Permissions',
        'text_empty' => 'There are no user roles available.',

        'label_permissions' => 'Permissions',
    ],

    'staff' => [
        'text_title' => 'Staff members',
        'text_form_name' => 'Staff member',
        'text_filter_search' => 'Search by location, name or email.',
        'text_filter_role' => 'View all roles',
        'text_filter_group' => 'View all groups',
        'text_empty' => 'There are no staff members available.',
        'text_roles_scope_groups' => 'Scope, Roles and Groups',
        'text_sale_permission_global_access' => 'Global Access',
        'text_sale_permission_groups' => 'Groups',
        'text_sale_permission_restricted' => 'Restricted Access',
        'text_impersonate' => 'Impersonate User',

        'column_group' => 'User Groups',
        'column_role' => 'User Roles',
        'column_location' => 'Locations',
        'column_last_login' => 'Last Login',

        'label_super_staff' => 'Super Admin',
        'label_username' => 'Username',
        'label_send_invite' => 'Send Invitation Email',
        'label_password' => 'Password',
        'label_confirm_password' => 'Password Confirm',
        'label_role' => 'Role',
        'label_group' => 'Groups',
        'label_language' => 'Language',
        'label_location' => 'Locations',
        'label_sale_permission' => 'Order and Reservation Scope',

        'help_send_invite' => 'Sends an invitation message containing a link to set a password on their account.',
        'help_super_staff' => 'Grants this user unlimited access to all areas of the system. Super user can add and manage other staff.',
        'help_role' => 'Roles define user permissions.',
        'help_groups' => 'Specify which groups the user should belong to. Segmenting agents into groups lets you easily assign orders.',
        'help_location' => 'Specify which locations the user should belong to. The user can ONLY view menus, categories, orders, and reservations attached to the selected location(s). Does not apply to super admins',
        'help_sale_permission_global_access' => 'Can view all Orders and Reservations in the Admin Panel',
        'help_sale_permission_groups' => 'Can view Orders and Reservations in their Group(s) and Orders and Reservations assigned to them',
        'help_sale_permission_restricted' => 'Can only view Orders and Reservations assigned to them',

        'alert_login_restricted' => 'Warning: You do not have the right permission to <b>access a user account</b>, please contact system administrator.',
        'alert_impersonate_confirm' => 'Are you sure you want to impersonate this staff? You can revert to your original state by logging out.',
        'alert_impersonate_success' => 'You are now impersonating staff: %s',
    ],

    'staff_status' => [
        'text_set_status' => 'Set a status',
        'text_online' => 'Online',
        'text_back_soon' => 'Back Soon',
        'text_away' => 'Away',
        'text_lunch_break' => 'On my lunch break...',
        'text_custom_status' => 'Set a custom away status',
        'text_clear_tomorrow' => 'Clear tomorrow',
        'text_clear_hours' => 'Clear in 4 hours',
        'text_clear_minutes' => 'Clear in 30 minutes',
        'text_dont_clear' => 'Don\'t Clear',
    ],

    'customer_groups' => [
        'text_title' => 'Customer Groups',
        'text_form_name' => 'Customer Group',
        'text_empty' => 'There are no customer groups available.',

        'column_customers' => '# Customers',

        'label_approval' => 'Requires Approval',

        'alert_set_default' => 'Customer group set as default',

        'help_approval' => 'New customers must be approved before they can login.',
    ],

    'customers' => [
        'text_title' => 'Customers',
        'text_form_name' => 'Customer',
        'text_tab_general' => 'Customer',
        'text_tab_address' => 'Addresses',
        'text_filter_search' => 'Search by name or email.',
        'text_empty' => 'There are no customers available.',
        'text_subscribe' => 'Subscribe',
        'text_un_subscribe' => 'Un-subscribe',
        'text_title_edit_address' => 'Address',
        'text_impersonate' => 'Impersonate Customer',

        'column_full_name' => 'Full Name',
        'column_telephone' => 'Telephone',
        'column_date_added' => 'Date Registered',

        'button_activate' => 'Activate',

        'label_first_name' => 'First Name',
        'label_last_name' => 'Last Name',
        'label_password' => 'Password',
        'label_confirm_password' => 'Confirm Password',
        'label_telephone' => 'Telephone',
        'label_newsletter' => 'Newsletter',
        'label_send_invite' => 'Send Invitation Email',
        'label_customer_group' => 'Customer Group',
        'label_address_1' => 'Address 1',
        'label_address_2' => 'Address 2',
        'label_city' => 'City',
        'label_state' => 'State',
        'label_postcode' => 'Postcode',
        'label_country' => 'Country',

        'help_send_invite' => 'Sends an invitation message containing a link to set a password on their account.',
        'help_password' => 'Leave blank to leave password unchanged',

        'alert_login_restricted' => 'Warning: You do not have the right permission to <b>access a customer account</b>, please contact system administrator.',
        'alert_impersonate_confirm' => 'Are you sure you want to impersonate this customer? You can revert to your original state by logging out.',
        'alert_impersonate_success' => 'You are now impersonating customer: %s',
        'alert_activation_success' => 'Customer activated successfully.',
        'alert_customer_not_active' => "Cannot login user '%s' until activated.",
        'alert_customer_payment_profile_not_found' => 'Customer payment profile not found!',
    ],

    'notifications' => [
        'text_title' => 'Notifications',
        'text_filter_search' => 'Search notifications...',
        'text_empty' => 'There are no notifications available.',

        'button_mark_as_read' => 'Mark all as read',
    ],
];
