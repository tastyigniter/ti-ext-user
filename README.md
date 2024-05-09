## Introduction

The TastyIgniter user extension is a fundamental part of the TastyIgniter platform, providing comprehensive user management capabilities. It allows administrators to manage front-end users (customers) and staff members, handle authentication, registration, password reset, email verification, and user impersonation.

## Features

- **User Management:** Manage front-end users (customers) and staff members.
- **Authentication:** Handle user authentication.
- **Registration:** Handle customer registration with optional email verification.
- **Password Reset:** Provide users with the ability to reset their passwords.
- **User Impersonation:** Allow administrators to impersonate customers or other staff members for troubleshooting.
- **Automation Events & Conditions:** Provide automation events for customer registration and conditions for customer attributes.

## Installation

You can install the extension via composer using the following command:

```bash
composer require tastyigniter/ti-ext-user:"^4.0" -W
```

Run the database migrations to create the required tables:
  
```bash
php artisan igniter:up
```

## Getting started

### Registration settings

You can configure the registration settings in the admin area. Navigate to the _System > Settings > Customer registration_ admin settings page. Here you can enable/disable customer registration, and where to send registration emails to the customer email and/or location email.

### Managing customers

### Managing staff members

## Usage

### Authentication

### Customer registration

### Password reset

#### Requesting a password reset link
#### Resetting the password

### Email verification

#### Resending email verification

### Impersonating users

### Auth Manager

### Automation Events

When setting up automation rules through the Admin Panel, you can use the following events registered by this extension:

#### Customer Registered Event

An automation event class used to capture the `igniter.user.register` system event when a customer registers. The event class is also used to prepare the customer parameters for automation rules. The following parameters are available:

- `customer`: The customer model instance.
- `data`: The customer registration form data.

### Automation Conditions

When setting up automation rules through the Admin Panel, you can use the following automation conditions registered by this extension:

#### Customer Attribute Condition

A condition class used to check if an customer attribute match the specified value or rule. The following attributes are available:

- `first_name`: The customer's first name.
- `last_name`: The customer's last name.
- `telephone`: The customer's telephone number.
- `email`: The customer's email address.

### Events

This extension will fire some global events that can be useful for interacting with other extensions.

| Event | Description | Parameters |
| ----- | ----------- | ---------- |
| `igniter.user.beforeAuthenticate` | Before the user is attempting to authenticate    |      [ $component, $credentials ]    |
| `igniter.user.beforeRegister` | Before the user is attempting to register  |        [ &$postData ]  |
| `igniter.user.login` | The user has logged in successfully  |         [ $component ]    |
| `igniter.user.logout` | The user has logged out sucessfully  |        [ $customer ] |
| `igniter.user.register` | The user has registered successfully |        [ $customer, $postData ]    |

**Example of hooking an event**

```
Event::listen('igniter.user.logout', function($customer) {
    // ...
});
```

## Changelog

Please see [CHANGELOG](https://github.com/tastyigniter/ti-ext-user/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Reporting issues

If you encounter a bug in this extension, please report it using the [Issue Tracker](https://github.com/tastyigniter/ti-ext-user/issues) on GitHub.

## Contributing

Contributions are welcome! Please read [TastyIgniter's contributing guide](https://tastyigniter.com/docs/contribution-guide).

## Security vulnerabilities

For reporting security vulnerabilities, please see our [our security policy](https://github.com/tastyigniter/ti-ext-user/security/policy).

## License

TastyIgniter User extension is open-source software licensed under the [MIT license](https://github.com/tastyigniter/ti-ext-user/blob/master/LICENSE.md).
