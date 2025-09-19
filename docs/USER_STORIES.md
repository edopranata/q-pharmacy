# User Stories Q-Pharmacy

## Overview

Dokumen ini berisi kumpulan user stories lengkap untuk sistem Q-Pharmacy, mencakup semua fitur utama dengan kriteria penerimaan yang jelas dan skenario pengujian yang terperinci. Setiap user story mengikuti format standar: **As a [user type], I want [functionality] so that [benefit]**.

**Target Users:**
- **Admin Apotek**: Pemilik atau manajer apotek
- **Kasir**: Staff yang melayani penjualan
- **Supervisor**: Staff dengan akses terbatas untuk monitoring

## Epic 1: Authentication & User Management

### US-001: User Registration
**As an** Admin Apotek  
**I want** to register new user accounts  
**So that** I can manage staff access to the system

#### Acceptance Criteria
- [ ] Admin can create new user accounts with email and password
- [ ] System validates email format and password strength
- [ ] Admin can assign roles (Admin, Kasir, Supervisor) during registration
- [ ] New users receive email notification with login credentials
- [ ] System prevents duplicate email addresses
- [ ] User account is created with default inactive status

#### Test Scenarios

**Scenario 1: Successful User Registration**
```gherkin
Given I am logged in as an Admin
When I navigate to the user management page
And I click "Add New User" button
And I fill in valid user details:
  | Field    | Value              |
  | Name     | John Doe           |
  | Email    | john@example.com   |
  | Password | SecurePass123!     |
  | Role     | Kasir              |
And I click "Save" button
Then the user should be created successfully
And I should see a success message
And the new user should appear in the user list
And an email should be sent to john@example.com
```

**Scenario 2: Duplicate Email Validation**
```gherkin
Given I am logged in as an Admin
And a user with email "existing@example.com" already exists
When I try to create a new user with email "existing@example.com"
Then I should see an error message "Email already exists"
And the user should not be created
```

**Scenario 3: Password Strength Validation**
```gherkin
Given I am logged in as an Admin
When I try to create a user with password "123"
Then I should see an error message "Password must be at least 8 characters"
And the user should not be created
```

---

### US-002: User Login
**As a** registered user  
**I want** to log into the system  
**So that** I can access my assigned features

#### Acceptance Criteria
- [ ] Users can log in with email and password
- [ ] System validates credentials against database
- [ ] Successful login redirects to appropriate dashboard based on role
- [ ] Failed login shows appropriate error message
- [ ] System locks account after 5 failed attempts
- [ ] Login session expires after 8 hours of inactivity

#### Test Scenarios

**Scenario 1: Successful Login**
```gherkin
Given I have a valid user account with email "kasir@apotek.com"
When I navigate to the login page
And I enter email "kasir@apotek.com"
And I enter the correct password
And I click "Login" button
Then I should be redirected to the kasir dashboard
And I should see my name in the header
```

**Scenario 2: Invalid Credentials**
```gherkin
Given I am on the login page
When I enter email "kasir@apotek.com"
And I enter an incorrect password
And I click "Login" button
Then I should see an error message "Invalid credentials"
And I should remain on the login page
```

**Scenario 3: Account Lockout**
```gherkin
Given I have failed to login 4 times
When I enter incorrect credentials for the 5th time
Then my account should be locked
And I should see a message "Account locked due to multiple failed attempts"
```

---

### US-003: Password Reset
**As a** user who forgot their password  
**I want** to reset my password  
**So that** I can regain access to my account

#### Acceptance Criteria
- [ ] Users can request password reset via email
- [ ] System sends reset link to registered email
- [ ] Reset link expires after 1 hour
- [ ] Users can set new password using valid reset link
- [ ] Old password becomes invalid after reset
- [ ] System logs password reset activities

#### Test Scenarios

**Scenario 1: Successful Password Reset Request**
```gherkin
Given I am on the login page
When I click "Forgot Password" link
And I enter my registered email "user@apotek.com"
And I click "Send Reset Link" button
Then I should see a message "Reset link sent to your email"
And I should receive an email with reset instructions
```

**Scenario 2: Password Reset with Valid Link**
```gherkin
Given I have received a valid password reset email
When I click the reset link in the email
And I enter a new password "NewSecurePass123!"
And I confirm the password
And I click "Reset Password" button
Then my password should be updated
And I should be redirected to login page
And I should be able to login with the new password
```

---

## Epic 2: Master Data Management

### US-004: Category Management
**As an** Admin Apotek  
**I want** to manage product categories  
**So that** I can organize products systematically

#### Acceptance Criteria
- [ ] Admin can create new categories with name and description
- [ ] Admin can edit existing category details
- [ ] Admin can deactivate categories (soft delete)
- [ ] System prevents deletion of categories with associated products
- [ ] Categories are displayed in alphabetical order
- [ ] System validates category name uniqueness

#### Test Scenarios

**Scenario 1: Create New Category**
```gherkin
Given I am logged in as an Admin
When I navigate to Categories page
And I click "Add Category" button
And I enter category name "Antibiotik"
And I enter description "Obat untuk infeksi bakteri"
And I click "Save" button
Then the category should be created successfully
And I should see "Antibiotik" in the categories list
```

**Scenario 2: Edit Existing Category**
```gherkin
Given I am logged in as an Admin
And a category "Vitamin" exists
When I click edit button for "Vitamin" category
And I change the description to "Suplemen vitamin dan mineral"
And I click "Update" button
Then the category should be updated successfully
And the new description should be displayed
```

**Scenario 3: Prevent Deletion of Category with Products**
```gherkin
Given I am logged in as an Admin
And a category "Analgesik" has 5 associated products
When I try to delete the "Analgesik" category
Then I should see an error message "Cannot delete category with existing products"
And the category should not be deleted
```

---

### US-005: Supplier Management
**As an** Admin Apotek  
**I want** to manage supplier information  
**So that** I can track product sources and maintain supplier relationships

#### Acceptance Criteria
- [ ] Admin can add suppliers with complete contact information
- [ ] Admin can edit supplier details
- [ ] Admin can deactivate suppliers
- [ ] System stores supplier contact person, phone, email, and address
- [ ] System validates email format and phone number format
- [ ] Suppliers can be searched by name or contact information

#### Test Scenarios

**Scenario 1: Add New Supplier**
```gherkin
Given I am logged in as an Admin
When I navigate to Suppliers page
And I click "Add Supplier" button
And I fill in supplier details:
  | Field           | Value                    |
  | Company Name    | PT Kimia Farma          |
  | Contact Person  | Budi Santoso            |
  | Phone           | 021-12345678            |
  | Email           | budi@kimiafarma.com     |
  | Address         | Jl. Sudirman No. 123    |
And I click "Save" button
Then the supplier should be created successfully
And I should see "PT Kimia Farma" in the suppliers list
```

**Scenario 2: Search Supplier**
```gherkin
Given I am on the Suppliers page
And there are multiple suppliers in the system
When I enter "Kimia" in the search box
Then I should see only suppliers containing "Kimia" in their name
```

---

## Epic 3: Product Management

### US-006: Product Registration
**As an** Admin Apotek  
**I want** to register new products  
**So that** I can maintain an accurate product catalog

#### Acceptance Criteria
- [ ] Admin can add products with all required information
- [ ] System generates or accepts barcode for products
- [ ] Products must be assigned to category, supplier, and unit
- [ ] System validates barcode uniqueness
- [ ] Product images can be uploaded and displayed
- [ ] Products can be marked as prescription or non-prescription

#### Test Scenarios

**Scenario 1: Register New Product**
```gherkin
Given I am logged in as an Admin
And categories, suppliers, and units exist in the system
When I navigate to Products page
And I click "Add Product" button
And I fill in product details:
  | Field           | Value                    |
  | Name            | Paracetamol 500mg       |
  | Description     | Obat penurun demam      |
  | Category        | Analgesik               |
  | Supplier        | PT Kimia Farma          |
  | Unit            | Strip                   |
  | Barcode         | 8992761123456           |
  | Prescription    | No                      |
And I upload a product image
And I click "Save" button
Then the product should be created successfully
And I should see "Paracetamol 500mg" in the products list
```

**Scenario 2: Barcode Validation**
```gherkin
Given I am adding a new product
And a product with barcode "8992761123456" already exists
When I enter barcode "8992761123456"
And I try to save the product
Then I should see an error message "Barcode already exists"
And the product should not be saved
```

---

### US-007: Product Search
**As a** Kasir  
**I want** to search for products quickly  
**So that** I can find products efficiently during sales

#### Acceptance Criteria
- [ ] Users can search products by name, barcode, or category
- [ ] Search results appear in real-time as user types
- [ ] Search results show product name, current stock, and price
- [ ] Users can scan barcode to search products
- [ ] Search results are sorted by relevance
- [ ] Out-of-stock products are clearly marked

#### Test Scenarios

**Scenario 1: Search by Product Name**
```gherkin
Given I am logged in as a Kasir
And there are products in the system
When I navigate to the POS page
And I type "paracetamol" in the search box
Then I should see all products containing "paracetamol" in the name
And the results should show current stock levels
```

**Scenario 2: Barcode Scanning**
```gherkin
Given I am on the POS page
When I click the barcode scanner button
And I scan a product barcode "8992761123456"
Then the product should be automatically found and selected
And it should be added to the current transaction
```

---

## Epic 4: Inventory Management

### US-008: Batch Management
**As an** Admin Apotek  
**I want** to manage product batches  
**So that** I can track expiry dates and implement FIFO

#### Acceptance Criteria
- [ ] Admin can add new batches with expiry dates
- [ ] System tracks batch quantities separately
- [ ] Batches are automatically sorted by expiry date (FIFO)
- [ ] System alerts when batches are near expiry
- [ ] Expired batches are clearly marked and cannot be sold
- [ ] Batch information is displayed during product selection

#### Test Scenarios

**Scenario 1: Add New Batch**
```gherkin
Given I am logged in as an Admin
And a product "Paracetamol 500mg" exists
When I navigate to the product details page
And I click "Add Batch" button
And I fill in batch details:
  | Field           | Value        |
  | Batch Number    | BATCH001     |
  | Quantity        | 100          |
  | Purchase Price  | 5000         |
  | Selling Price   | 7500         |
  | Expiry Date     | 2026-12-31   |
And I click "Save" button
Then the batch should be created successfully
And the product stock should increase by 100
```

**Scenario 2: Expiry Date Alert**
```gherkin
Given there is a batch expiring in 30 days
When I log into the system
Then I should see an alert notification
And the notification should show "Batch BATCH001 expires in 30 days"
```

---

### US-009: Stock Monitoring
**As an** Admin Apotek  
**I want** to monitor stock levels  
**So that** I can maintain adequate inventory

#### Acceptance Criteria
- [ ] System displays current stock for all products
- [ ] Low stock alerts are generated automatically
- [ ] Stock movements are tracked and logged
- [ ] Stock reports can be generated by date range
- [ ] System shows stock value calculations
- [ ] Out-of-stock products are highlighted

#### Test Scenarios

**Scenario 1: Low Stock Alert**
```gherkin
Given a product "Vitamin C" has minimum stock level set to 20
And the current stock is 15
When I view the inventory dashboard
Then I should see a low stock alert for "Vitamin C"
And the product should be highlighted in red
```

**Scenario 2: Stock Movement Tracking**
```gherkin
Given I am viewing a product's stock history
When stock movements have occurred
Then I should see a log of all movements including:
  - Date and time
  - Movement type (Sale, Purchase, Adjustment)
  - Quantity changed
  - User who made the change
  - Remaining stock
```

---

## Epic 5: Point of Sale (POS)

### US-010: Sales Transaction
**As a** Kasir  
**I want** to process sales transactions  
**So that** I can serve customers efficiently

#### Acceptance Criteria
- [ ] Kasir can add products to transaction by search or barcode
- [ ] System calculates total amount including tax
- [ ] Multiple payment methods are supported (cash, card, transfer)
- [ ] System generates receipt after payment
- [ ] Stock is automatically reduced after sale
- [ ] Transaction history is maintained

#### Test Scenarios

**Scenario 1: Complete Sales Transaction**
```gherkin
Given I am logged in as a Kasir
And I am on the POS page
When I search for "Paracetamol 500mg"
And I select the product
And I set quantity to 2
And I click "Add to Cart"
And I click "Checkout"
And I select payment method "Cash"
And I enter amount received "20000"
And I click "Complete Sale"
Then the transaction should be processed successfully
And a receipt should be generated
And the product stock should be reduced by 2
And change amount should be calculated correctly
```

**Scenario 2: Insufficient Stock**
```gherkin
Given a product "Vitamin D" has only 3 units in stock
When I try to add 5 units to the transaction
Then I should see an error message "Insufficient stock available"
And only 3 units should be added to the cart
```

---

### US-011: Receipt Generation
**As a** Kasir  
**I want** to generate receipts for customers  
**So that** customers have proof of purchase

#### Acceptance Criteria
- [ ] Receipt includes all transaction details
- [ ] Receipt shows pharmacy information
- [ ] Receipt can be printed or emailed
- [ ] Receipt includes transaction ID for reference
- [ ] Tax breakdown is clearly shown
- [ ] Receipt format is professional and readable

#### Test Scenarios

**Scenario 1: Print Receipt**
```gherkin
Given I have completed a sales transaction
When the transaction is finalized
Then a receipt should be automatically generated
And the receipt should include:
  - Pharmacy name and address
  - Transaction date and time
  - Transaction ID
  - List of purchased items with quantities and prices
  - Subtotal, tax, and total amount
  - Payment method and amount received
  - Change amount (if applicable)
And I should be able to print the receipt
```

---

## Epic 6: Reporting & Analytics

### US-012: Sales Reports
**As an** Admin Apotek  
**I want** to view sales reports  
**So that** I can analyze business performance

#### Acceptance Criteria
- [ ] Reports can be generated for different time periods
- [ ] Sales data can be filtered by product, category, or cashier
- [ ] Reports show revenue, profit, and transaction count
- [ ] Charts and graphs visualize sales trends
- [ ] Reports can be exported to PDF or Excel
- [ ] Real-time dashboard shows today's sales

#### Test Scenarios

**Scenario 1: Generate Monthly Sales Report**
```gherkin
Given I am logged in as an Admin
When I navigate to Reports page
And I select "Sales Report"
And I set date range to "Last Month"
And I click "Generate Report"
Then I should see a report showing:
  - Total revenue for the month
  - Number of transactions
  - Top-selling products
  - Sales by category
  - Daily sales trend chart
And I should be able to export the report to PDF
```

**Scenario 2: Filter Sales by Cashier**
```gherkin
Given I am viewing the sales report
When I select cashier "John Doe" from the filter
And I click "Apply Filter"
Then the report should show only sales made by John Doe
And the totals should be recalculated accordingly
```

---

### US-013: Inventory Reports
**As an** Admin Apotek  
**I want** to view inventory reports  
**So that** I can manage stock levels effectively

#### Acceptance Criteria
- [ ] Current stock levels for all products
- [ ] Low stock and out-of-stock reports
- [ ] Expiry date reports with upcoming expirations
- [ ] Stock movement history
- [ ] Inventory valuation reports
- [ ] Dead stock analysis

#### Test Scenarios

**Scenario 1: Low Stock Report**
```gherkin
Given I am logged in as an Admin
When I navigate to Reports page
And I select "Low Stock Report"
Then I should see a list of products with stock below minimum level
And each product should show:
  - Product name
  - Current stock
  - Minimum stock level
  - Recommended reorder quantity
And I should be able to export this report
```

**Scenario 2: Expiry Report**
```gherkin
Given I am viewing inventory reports
When I select "Expiry Report"
And I set the time frame to "Next 3 months"
Then I should see all batches expiring in the next 3 months
And they should be sorted by expiry date
And expired items should be highlighted in red
```

---

## Epic 7: System Administration

### US-014: User Role Management
**As an** Admin Apotek  
**I want** to manage user roles and permissions  
**So that** I can control system access appropriately

#### Acceptance Criteria
- [ ] Admin can assign and modify user roles
- [ ] Different roles have different permission levels
- [ ] Role changes take effect immediately
- [ ] System logs all role changes
- [ ] Users are notified of role changes
- [ ] Role-based menu and feature access

#### Test Scenarios

**Scenario 1: Change User Role**
```gherkin
Given I am logged in as an Admin
And a user "jane@apotek.com" has role "Kasir"
When I navigate to User Management
And I edit user "jane@apotek.com"
And I change role to "Supervisor"
And I click "Save"
Then the user's role should be updated to "Supervisor"
And the user should have supervisor-level access
And the change should be logged in the audit trail
```

**Scenario 2: Role-Based Access Control**
```gherkin
Given I am logged in as a user with "Kasir" role
When I try to access the User Management page
Then I should see an "Access Denied" message
And I should be redirected to my dashboard
```

---

### US-015: System Backup
**As an** Admin Apotek  
**I want** to backup system data  
**So that** I can recover from data loss

#### Acceptance Criteria
- [ ] Admin can initiate manual backups
- [ ] Automatic daily backups are performed
- [ ] Backup files are stored securely
- [ ] Backup status and history are visible
- [ ] Restore functionality is available
- [ ] Backup integrity is verified

#### Test Scenarios

**Scenario 1: Manual Backup**
```gherkin
Given I am logged in as an Admin
When I navigate to System Settings
And I click "Create Backup"
Then a backup process should start
And I should see a progress indicator
And when complete, I should see a success message
And the backup should appear in the backup history
```

**Scenario 2: Automatic Backup Verification**
```gherkin
Given automatic backups are configured
When I check the backup history
Then I should see daily backups for the past week
And each backup should show:
  - Backup date and time
  - File size
  - Status (Success/Failed)
  - Verification status
```

---

## Epic 8: Mobile & Responsive Features

### US-016: Mobile POS
**As a** Kasir  
**I want** to use the POS system on mobile devices  
**So that** I can serve customers anywhere in the store

#### Acceptance Criteria
- [ ] POS interface is fully responsive on mobile
- [ ] Barcode scanning works on mobile camera
- [ ] Touch-friendly interface for product selection
- [ ] Mobile receipt printing capability
- [ ] Offline mode for basic transactions
- [ ] Sync data when connection is restored

#### Test Scenarios

**Scenario 1: Mobile Transaction**
```gherkin
Given I am using the system on a mobile device
And I am logged in as a Kasir
When I navigate to the POS page
Then the interface should be optimized for mobile
And I should be able to:
  - Search products easily
  - Add items to cart with touch gestures
  - Process payments
  - Generate receipts
```

**Scenario 2: Mobile Barcode Scanning**
```gherkin
Given I am on the mobile POS page
When I click the barcode scanner button
Then the device camera should activate
And when I scan a product barcode
Then the product should be automatically added to the transaction
```

---

## Epic 9: Integration & API

### US-017: External API Integration
**As an** Admin Apotek  
**I want** to integrate with external systems  
**So that** I can streamline business processes

#### Acceptance Criteria
- [ ] API endpoints for product synchronization
- [ ] Integration with payment gateways
- [ ] Supplier catalog integration
- [ ] Government reporting API integration
- [ ] Third-party analytics integration
- [ ] Webhook support for real-time updates

#### Test Scenarios

**Scenario 1: Product Sync API**
```gherkin
Given an external system wants to sync products
When it sends a POST request to /api/v1/products/sync
With valid authentication and product data
Then the products should be created or updated
And a success response should be returned
And the changes should be reflected in the system
```

---

## Epic 10: Advanced Features

### US-018: Prescription Management
**As a** Kasir  
**I want** to manage prescription orders  
**So that** I can handle prescription medications properly

#### Acceptance Criteria
- [ ] Prescription orders can be recorded
- [ ] Doctor and patient information is captured
- [ ] Prescription validation before dispensing
- [ ] Prescription history tracking
- [ ] Controlled substance logging
- [ ] Insurance claim processing

#### Test Scenarios

**Scenario 1: Process Prescription Order**
```gherkin
Given I am logged in as a Kasir
When a customer brings a prescription
And I navigate to Prescription Orders
And I click "New Prescription"
And I enter:
  - Patient name and details
  - Doctor name and license
  - Prescribed medications
  - Dosage and instructions
And I verify the prescription
And I dispense the medications
Then the prescription should be recorded
And the medications should be deducted from stock
And a prescription receipt should be generated
```

---

## Testing Guidelines

### Test Data Requirements

For comprehensive testing, the following test data should be available:

**Users:**
- 1 Admin user
- 2 Kasir users
- 1 Supervisor user

**Master Data:**
- 10 Categories
- 5 Suppliers
- 8 Units
- 50 Products with various stock levels
- 100 Batches with different expiry dates

**Transactions:**
- 200 completed sales transactions
- 50 prescription orders
- Various payment methods used

### Performance Criteria

**Response Times:**
- Product search: < 500ms
- Transaction processing: < 2 seconds
- Report generation: < 10 seconds
- Page load times: < 3 seconds

**Concurrent Users:**
- System should support 20 concurrent users
- POS should handle 5 simultaneous transactions

### Browser Compatibility

**Supported Browsers:**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Mobile Devices:**
- iOS 13+ (Safari)
- Android 8+ (Chrome)

### Accessibility Requirements

- WCAG 2.1 AA compliance
- Keyboard navigation support
- Screen reader compatibility
- High contrast mode support
- Minimum font size 14px

## Definition of Done

A user story is considered "Done" when:

1. **Development Complete**
   - [ ] All acceptance criteria implemented
   - [ ] Code reviewed and approved
   - [ ] Unit tests written and passing
   - [ ] Integration tests passing

2. **Testing Complete**
   - [ ] All test scenarios executed
   - [ ] Manual testing completed
   - [ ] Performance criteria met
   - [ ] Cross-browser testing done
   - [ ] Mobile testing completed

3. **Documentation Updated**
   - [ ] API documentation updated
   - [ ] User documentation updated
   - [ ] Technical documentation updated

4. **Deployment Ready**
   - [ ] Code deployed to staging
   - [ ] Stakeholder approval received
   - [ ] Ready for production deployment

## Traceability Matrix

| Epic | User Story | Priority | Sprint | Status |
|------|------------|----------|--------|---------|
| Authentication | US-001 | High | 1 | Planned |
| Authentication | US-002 | High | 1 | Planned |
| Authentication | US-003 | Medium | 1 | Planned |
| Master Data | US-004 | High | 2 | Planned |
| Master Data | US-005 | High | 2 | Planned |
| Product Mgmt | US-006 | High | 3 | Planned |
| Product Mgmt | US-007 | High | 3 | Planned |
| Inventory | US-008 | High | 4 | Planned |
| Inventory | US-009 | High | 4 | Planned |
| POS | US-010 | High | 5 | Planned |
| POS | US-011 | High | 5 | Planned |
| Reporting | US-012 | Medium | 6 | Planned |
| Reporting | US-013 | Medium | 6 | Planned |
| Admin | US-014 | Medium | 2 | Planned |
| Admin | US-015 | Low | 6 | Planned |
| Mobile | US-016 | Medium | 5 | Planned |
| Integration | US-017 | Low | Future | Planned |
| Advanced | US-018 | Low | Future | Planned |

---

**Document Information:**
- **Version**: 1.0
- **Created**: 15 September 2025
- **Last Updated**: 20 September 2025
- **Owner**: Product Team Q-Pharmacy
- **Reviewers**: Development Team, Stakeholders