<?php

/**
 * Input validation utilities.
 * Batavia Madrigal Singers — Backend API
 */

/**
 * Validate user registration input.
 *
 * @param  array $data  Associative array of input fields.
 * @return array        List of error strings (empty = valid).
 */
function validateRegistration(array $data): array
{
    $errors = [];

    // Name
    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors['name'] = 'Full name is required.';
    } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors['name'] = 'Name must be between 2 and 100 characters.';
    }

    // Email
    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Password
    $password = $data['password'] ?? '';
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must contain at least one number.';
    }

    // Confirm password
    $confirm = $data['password_confirmation'] ?? '';
    if ($confirm !== $password) {
        $errors['password_confirmation'] = 'Passwords do not match.';
    }

    return $errors;
}

/**
 * Validate login input.
 *
 * @param  array $data
 * @return array
 */
function validateLogin(array $data): array
{
    $errors = [];

    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (($data['password'] ?? '') === '') {
        $errors['password'] = 'Password is required.';
    }

    return $errors;
}
