# Credentia Verifiable Credential (VC) Prototype Documentation

## Project Overview

The Credentia VC Prototype is a basic web application designed to demonstrate the core principles of a Verifiable Credential ecosystem: **Issuance, Storage, and Revocation**.. It uses PHP and a flat-file SQLite database to manage the records. The system separates the **Issuer Console** (admin interface) from the **Holder View** (student ID card). I have never made a documenttion before so please be patient with the quality.

***

## 1. File Structure and Dependincies

This project requires a local environment capable of running **PHP with the SQLite extension enabled**.

| Filename | Description | Role in System |
| :--- | :--- | :--- |
| `index.php` | Main administrative file. | **Issuer Console** & **Authentication Gate**. Handles all database interactions (CRUD). |
| `student-card.php` | Digital student credential display. | **Holder View**. Renders student data and the static VC ID passed via URL. |
| `credentials.sqlite` | Automatically created upon first run. | **Data Store**. Stores all issued student records and their unique VC IDs. |

***

## 2. Setup and Execution

To get the system running locally, follow these steps:

1.  **Server Setup:** Ensure you have a web server running (like Apache or Nginx) and that **PHP 7.4 or higher** is installed and configured.
2.  **SQLite Check:** Verify that the **`pdo_sqlite`** extension is active in your PHP configuration (`php.ini`). This is essential for the database to function!
3.  **File Placement:** Place both `index.php` and `student-card.php` in your web server's root directory (e.g., `htdocs` or `www`).
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

    * The system inserts the data into the database and generates a unique, pseudo-random **VC ID** (e.g., `VC-C4E7D0A2`). A success message with the new VC ID will display at the top of the screen.

### C. Viewing the Credential (Holder View)

The issued VC is viewable via the `student-card.php` interface, simulating how a student might access their digital wallet.

1.  In the **"Issued Credentials (Verification List)"** table, find the student record.
2.  Click the **"Card"** button next to their name.
3.  This opens `student-card.php` in a new tab, passing the necessary student information and the **VC ID** (verifiable credential ID) through the URL query string parameters.

> **Note on VC ID:** The card clearly displays the VC ID. We decided to temporarily skip the implementation of the live QR code scanner, which would normally encrypt this data, as it required external cryptographic libraries. This is marked as a **Future Feature** in the card's display.

### D. Revoking a Credential

To invalidate a credential (for instance, if a student drops out), the Issuer uses the revocation feature.

1.  In the **"Issued Credentials (Verification List)"**, find the credential you wish to revoke.
2.  Click the red **"Revoke"** button.
3.  Confirm the revocation when prompted.

    * This action permanently **deletes the record** from the `credentials.sqlite` database. Any attempt to use that student's details would fail a verification check, demonstrating the principle of credential management.

***

## 4. Developer Notes

This section provides detailed technical insights into the implementation of `index.php` and `student-card.php`, including code structure, design decisions, and key functionalities. These notes are intended for developers looking to understand or extend the codebase.

### A. index.php (Main Administrative Interface)

**Purpose and Role:**
- Acts as the central hub for the Issuer Console, handling user authentication, credential issuance, revoking, and listing issued credentials.
- Integrates PHP backend for session management and SQLite database operations, with responsive HTML/CSS frontend powered by Font Awesome icons and Inter font.

**Code Structure:**
- **Authentication Block:** Uses PHP sessions to enforce access control. If not authenticated, renders a modern login form with glassmorphism effects (backdrop-blur and gradient backgrounds) and transitions. The hardcoded password is intentional for prototype simplicity.
- **Database Setup:** Initializes SQLite connection and creates the `students` table with UNIQUE constraints on `enrollment_id` and `vc_id` to ensure data integrity and prevent duplicates.
- **CRUD Operations:**
  - **Create/Issue:** Processes POST requests from the "Issue Credential" form, generates a unique VC ID using `generateVCID()`, and inserts data.
  - **Read:** Fetches all students ordered by issue date for display in a grid layout.
  - **Delete/Revoke:** Handles GET requests to remove records, with confirmation prompts.
- **Frontend Layout:** Header with gradient background and responsive flexbox; forms using CSS Grid; student list in a card-based design with toggleable action buttons (click ellipsis to reveal Card/Revoke options). Alerts display success/error messages.
- **Styling Features:** CSS variables for consistency, grid for alignment, hover effects, and mobile responsiveness. The header has a gradient and enhanced shadows for visual appeal.

**Key Functions:**
- `generateVCID()`: Generates a random 8-character hex VC ID prefixed with "VC-". Future-ready for cryptographic enhancements like hashes tied to DIDs.
- `toggleOptions()` JS: Toggles visibility of action buttons per student item, reducing UI clutter.

**Design Choices and Improvements:**
- Session-based auth for security prototype; not production-ready.
- Embedded CSS for portability; separated login CSS to avoid conflicts.
- Grid layout ensures perfect alignment of credentials despite varying text lengths.
- Action buttons hidden by default (via JS toggle) to declutter the list.
- No redundant comments or variables; code is cleaned and streamlined.
- Mobile-first with media queries for header stacking.

**Potential Enhancements:**
- Replace hardcoded auth with a full user system.
- Add pagination or search for large lists.
- Implement AJAX for smoother UX during issuance/revoking.
- Integrate real DID/VC standards using libraries like did-pep or veramo.

### B. student-card.php (Digital Credential Display)

**Purpose and Role:**
- Simulates the Holder/Holder View, displaying a student's digital ID card with photo, details, and VC ID.
- Pure HTML/JS for simplicity; reads data from URL parameters to render the card, mimicking a digital wallet display.

**Code Structure:**
- **HTML Layout:** Card-based design with logo strip, photo circular container, details section, and VC box. Includes a print button for simple offline use.
- **Styling Features:** Custom CSS with variables; linear gradients for background and logo; shadow effects on photo and card; responsive-free design fitting 320px width. Photo has gradient border for a fashionable, premium look. Glow shadow adds depth.
- **JavaScript Logic:** Parses URL query parameters (name, enrollment, course, email, vc_id) and populates the DOM elements. Fallback placeholder image for demo purposes.

**Key Features:**
- **Photo Handling:** Uses URL parameter or placeholder; positioned with relative/absolute for the gradient border effect.
- **VC Display:** Prominently shows the VC ID in monospace font within a styled box, with helper text noting future QR implementation.
- **Print Functionality:** `@media print` hides buttons and adjusts positioning for physical printing; includes `window.print()` on click.
- **Fashionable Design:** Subtle gradients, enhanced shadows, and a clean aesthetic without changing text—focus on visual polish.

**Design Choices and Improvements:**
- Static PHP naming for consistency, though no server-side processing.
- Front-end only for speed; data passed via URL as in real-world deep linking to wallets.
- Shadow light and glow effects for 3D appearance.
- No JS redundancy; cleaned comments and streamlined object parsing.
- Centered, card-like layout for easy viewing/printing.

**Potential Enhancements:**
- Integrate real QR code generation using libraries like qrcodejs.
- Add cryptographic verification by fetching from a BlockChain/API.
- Enable PDF export or email sharing from the card.
- Support multiple photos or dynamic backgrounds.

### General Technical Notes

* **Security Flaw:** Hardcoded password and session-only auth are demonstrative; enhance with bcrypt/hashed passwords and secure sessions in production.
* **Data Integrity:** SQLite UNIQUE constraints prevent dupe issues; consider foreign keys for multi-table structures.
* **VC ID Generation:** Simple randomness suffices for prototype; production needs deterministic, secure hashes (e.g., keyed HMAC).
* **Performance:** Flat-file DB is fine for small-scale; scale to MySQL/PostgreSQL for growth.
* **Accessibility:** Basic alt tags and color contrast; add ARIA labels for screen readers.
* **Dependencies:** PHP 7.4+, SQLite; Font Awesome via CDN for icons.
* **File Naming:** Consistent `.php` extension despite static content for project conventions.
