---
title: "User Extension"
section: "extensions"
sortOrder: 110
---

## Introduction

Management of front-end users (customers) on TastyIgniter.

## Installation

To install this extension, click on the **Add to Site** button on the TastyIgniter marketplace item page or search
for **Igniter.User** in **Admin System > Updates > Browse Extensions**

## Admin Panel

In the admin user interface you can manage customers and their groups.

## Events

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
