# Contacts Module

This module handles the contact form functionality of the UPHG Events system.

## Description

The Contacts module allows viewers to send inquiries to administrators directly through the homepage. Messages submitted via the contact form are stored in the database under the `contact` table.

## Database Entity

**Contact**
- `c_id` – Contact ID (primary key)
- `name` – Name of the sender
- `email` – Email address of the sender
- `message` – Message content

## Unit Tests

Unit tests for this module are located in `tests/ContactValidationTest.php`.

### Test Coverage
- Valid contact form submission
- Empty field validation
- Email format validation
- Message length validation

## Running the Tests

```bash
composer install
./vendor/bin/phpunit tests/ContactValidationTest.php
```

## Assigned To

Garcia, Aubrey Ray T.
