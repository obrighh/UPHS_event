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
# UPHG Events

A web-based event management and posting system for the University of Perpetual Help System GMA Campus.

## About

UPHG Events is a centralized platform for organizing, managing, and publishing all official school events of the university. It replaces the manual and fragmented approach of posting events through social media, printed materials, and group chats.

## Features

- **Event Listings** – View all upcoming school events with full details
- **Calendar of Events** – Interactive calendar for tracking scheduled activities
- **Countdown Timer** – Highlights the nearest upcoming event
- **Contact Section** – Users can send inquiries directly through the platform
- **AI Chatbot** – Answers frequently asked questions and retrieves event information
- **Admin Dashboard** – Manage events, approve or deny submissions, and update account settings
- **Role-Based Access** – Separate interfaces for admins, organizations/staff, and viewers
- **Real-Time Updates** – Changes are instantly reflected on the platform

## User Roles

- **Administrators** – Manage and approve event content
- **Organizations / Staff** – Submit event proposals for admin approval
- **Viewers** – Browse events and calendar without logging in

## Tech Stack

- PHP
- JavaScript
- MySQL
- HTML / CSS

## Modules

- `contacts` – Handles user inquiries submitted through the contact form
- `events` – Core event management and display
- `announcements` – Org and admin announcements
- `accounts` – User authentication and account management
- `chatbot` – AI-assisted event query responses

## Running the Project

1. Clone the repository
2. Import the SQL database
3. Configure `.env.local` with your database credentials
4. Run on a local server (e.g. XAMPP or Laragon)

## Contributors

- Lazaro, Elisha Kyle A.
- Garcia, Aubrey Ray T.
- Sabilla, Khaizen M.

*Capstone Project – Bachelor of Science in Information Technology*
*University of Perpetual Help System GMA Campus, 2026*
