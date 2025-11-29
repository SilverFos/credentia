# Credentia Prototype Documentation

## Project Overview

The Credentia Prototype is a basic web app designed to manage credentials and  demonstrate the core principles of a Verifiable Credential ecosystem: **Issuance, Storage, and Revocation**.. It uses PHP and a SQLite database to manage the records. The system separates the Issuer Console (admin interface) from the Holder View (student ID card). We hope this documentation gives you an approximation of what is currently in this project.

***

## 1. File Structure and Dependincies

This project requires a local environment capable of running **PHP with the SQLite extension enabled**. We emphasize in enabling extension because `php.ini` by default has it disabled.

| Filename | Description | Role in System |
| :--- | :--- | :--- |
| `index.php` | Main administrative file. | *Issuer Console* & *Authentication*. Handles all database functions. |
| `student-card.php` | Digital student ID display. | *Holder View*. Presents student data and the static ID passed. |
| `credentials.sqlite` | Automatically created upon first run. | *Data Store*. Stores all issued student records and their unique VC IDs. |

***

## 2. Setup and Execution

To get the system running locally, follow these steps:

1.  **Server Setup:** Ensure you have **PHP 7.4 or higher** installed and configured.
2.  **SQLite Check:** Verify that the **`pdo_sqlite`** extension is active in your PHP configuration (`php.ini`). This is essential for the database to function!
3.  **File Placement:** Place both `index.php` and `student-card.php` in your web root directory.
4.  **First Run:** Navigate to `http://localhost/index.php` in your browser.

    * The first time you access the page, the database file, `credentials.sqlite`, will be automatically generated.

***

## 3. Usage Guide

### A. Logging In

The administrative panel is protected by a basic authentication layer.

* **Access URL:** `index.php`
* **Default Password:** `admin`

### B. Issuing a Credential

1.  Log into the Admin Console via `index.php`.
2.  Locate the section titled **"Issue New Student Credential"**.
3.  Fill out the required fields: Full Name, Enrollment ID, Course/Program, and Email.
4.  Click the **"Issue Credential"** button.

    * The system inserts the data into the database and generates a unique, random **VC ID** (e.g., `VC-C4E7D0A2`). A success message with the new VC ID will display at the top of the screen.

### C. Viewing the Credential (Holder View)

The issued VC is viewable via the `student-card.php` interface, simulating how a student might access their digital wallet.

1.  In the **"Issued Credentials (Verification List)"** table, find the student record.
2.  Click the **"Card"** button next to their name.
3.  This opens `student-card.php` in a new tab, passing the necessary student information and the **VC ID** (verifiable credential ID) through the URL query string parameters.

> **Note on VC ID:** The card clearly displays the VC ID. We decided to temporarily skip the implementation of the live QR code scanner, which would normally encrypt this data, as it required external cryptographic libraries. This is marked as a *Future Feature* in the card's display.

### D. Revoking a Credential

To invalidate a credential (for instance, if a student drops out), the Issuer uses the revocation feature.

1.  In the **"Issued Credentials (Verification List)"**, find the ID you wish to revke.
2.  Click the red **"Revoke"** button.
3.  Confirm the revocation when prompted.

    * This action permanently **deletes the record** from the `credentials.sqlite` database. Any attempt to use that student's details would fail a verification check, demonstrating the principle of credential management.

***

## 4. Technical Notes (Dev Notes)
~to be filled~
* **Security Flaw:** The password is hardcoded (`$ADMIN_PASSWORD = "admin"`) and there's no proper user management system. This is acceptable for a simple **demonstration prototype**.
* **Data Integrity:** The Enrollment ID and VC ID fields are enforced as **UNIQUE** in the SQLite table schema. This prevents accidental duplicate credential issuance.
* **VC ID Generation:** The `generateVCID()` PHP function currently uses a simple `bin2hex` random byte generation. In a production system, this would be a cryptographic hash tied to a Decentralized Identifier (DID) system.
* **File Naming:** We used `student-card.php` despite it containing only static HTML/JS code (no PHP required) for **naming convention consistency**.
