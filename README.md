# Sia Campsite Booking & Management System
**System Proposal & Documentation**

---

## 🏕️ System Overview
The **Sia Campsite Booking System** is a modern, mobile-first web application designed to digitize and streamline the process of reserving campsite locations (Tapak), tracking payments, and managing guest check-ins. Built securely with PHP, MySQL, and dynamic JavaScript, the system completely removes the need for physical logbooks and chaotic WhatsApp group tracking.

---

## 🚀 Core Capabilities (What It Can Do)

1. **Smart Booking Engine**
   * Intuitive front-end form for logging guest details, dates, and site preferences.
   * **Intelligent Double-Booking Prevention:** The system automatically cross-references dates. It is mathematically impossible for staff to double-book a site for the same overlapping dates, while still allowing a same-day checkout/check-in transition.

2. **Dynamic Admin Dashboard**
   * Real-time grid of all upcoming bookings, filterable by specific Site or Date Range.
   * **Payment & Proof Tracking:** Admins can instantly record payment amounts, set statuses (*Unpaid, Partial, Paid*), enter reference numbers, and securely upload PDF/Image transaction receipts.
   * Built-in safety logic automatically upgrades "Unpaid" bookings to "Partial" if evidence of a transaction is uploaded.

3. **Interactive Visual Calendar**
   * A beautiful, color-coded monthly calendar grid giving managers a high-level view of campsite occupancy.
   * Clicking any badge reveals deep details and allows instant editing directly from the calendar view.

4. **Secure Authentication & Management**
   * Fully protected backend requiring a Username and Password.
   * Dedicated Admin portal to add new Staff users, reset passwords, or revoke access.

5. **Progressive Web App (PWA) Ready**
   * The system acts as a native mobile app. It can be "Installed" directly to the Home Screen of any Android or iOS device, launching in full-screen without the need for the Google Play Store.

---

## 📈 Key Benefits

* **Zero Paper Waste & Data Loss:** All records, guest contacts, and transaction receipts are securely digitized and backed up in the database.
* **Anywhere Access:** Staff and management can view real-time availability from their phones while walking the campsite grounds.
* **Instant Dispute Resolution:** Because payment proofs (receipts) are attached directly to the booking ID, resolving payment disputes with guests takes seconds instead of hours.
* **Color-Coded Clarity:** Visual indicators immediately highlight unpaid guests (Red), partially paid guests (Yellow), and fully cleared guests (Green).

---

## 🎯 System Scope
**Currently In-Scope:**
* Internal staff booking creation (walk-ins or phone reservations).
* Comprehensive site management (CRUD operations for Campsites).
* User access control (Admin vs. Staff roles).
* Secure file storage for payment proofs.
* Static Info Setup (Address, Management Contacts).

**Currently Out-of-Scope:**
* Public-facing self-service booking (guests must still contact the campsite, and staff log the entry).
* Direct credit card processing (payments are handled via manual transfer, and the receipt is uploaded).

---

## 🔮 Future Upgrades & Roadmap

To evolve the platform further, the following modules are recommended for Phase 2 development:

1. **Automated WhatsApp/Email Notifications:**
   * Automatically ping guests with a beautifully formatted WhatsApp message or Email confirming their booking reference number (e.g., `SIA-20260510-42`) and check-in instructions.
2. **Customer Self-Service Portal:**
   * A public calendar where guests can check available dates themselves and submit a booking request for Admin approval.
3. **Payment Gateway Integration (ToyyibPay / Stripe):**
   * Allow guests to pay their deposit online instantly via FPX online banking. The system would auto-update the status to "Paid".
4. **Advanced Financial Reporting:**
   * Generate monthly PDF/Excel reports showing total revenue, occupancy rates, and outstanding balances to help management make better financial decisions.

---
### 🛠 System Specifications & Credits
- **Developer:** Kamal Harmoni & Hidayat
- **Version:** 1.1 May 2026
- **PHP Version:** 8.2.12
- **MySQL Version:** 10.4.32-MariaDB

*Developed & Structured for Sia Campsite Operations.*
