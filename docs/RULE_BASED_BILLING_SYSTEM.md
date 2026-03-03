# Rule-Based Billing System Documentation

## Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Database Schema Changes](#database-schema-changes)
4. [Business Rules](#business-rules)
5. [Category Configurations](#category-configurations)
6. [Student Status Tracking](#student-status-tracking)
7. [Usage Guide](#usage-guide)
8. [API Reference](#api-reference)
9. [Migration Guide](#migration-guide)
10. [Testing](#testing)
11. [Troubleshooting](#troubleshooting)
12. [Future Enhancements](#future-enhancements)

---

## Overview

### What is the Rule-Based Billing System?

The rule-based billing system is a configurable and scalable billing infrastructure that allows administrators to define business rules for fee categories. Instead of hard-coding billing logic throughout the application, this system centralizes billing behavior through configurable rules on fee categories.

### Why Was It Implemented?

The rule-based billing system was implemented to address several challenges:

1. **Flexibility**: Different fee categories have different requirements (e.g., registration fees vs. monthly tuition)
2. **Maintainability**: Centralized business rules reduce code duplication and make updates easier
3. **Scalability**: New fee categories can be added without modifying core billing logic
4. **Consistency**: Ensures uniform behavior across all billing operations
5. **Auditability**: Clear rule definitions make system behavior transparent

### Key Benefits and Advantages

- **Configurable Behavior**: Fee categories can be customized through database configuration
- **Automatic Rule Enforcement**: System automatically applies rules during billing generation
- **Status-Based Control**: Billing generation respects student acceptance status
- **Flexible Activation**: Multiple activation modes for different billing scenarios
- **Lock Protection**: Prevents accidental modification of critical categories
- **Soft Deletion Support**: Maintains data integrity while allowing cleanup

---

## Architecture

### System Design Principles

The rule-based billing system follows these core design principles:

1. **Separation of Concerns**: Business rules are separated from billing logic
2. **Single Responsibility**: Each component has a clear, focused purpose
3. **Open/Closed Principle**: Open for extension (new categories), closed for modification
4. **Dependency Injection**: Services are injected for testability
5. **Observer Pattern**: Automatic reactions to state changes

### How the Rule-Based Approach Works

```
┌─────────────────────────────────────────────────────────────┐
│                     Billing Request                          │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  BillingService                              │
│  - Validates category rules                                  │
│  - Checks student status                                     │
│  - Applies activation logic                                  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              FeeCategory (Rules Engine)                      │
│  - is_locked: Prevents modification                         │
│  - activation_mode: Controls billing behavior               │
│  - can_generate_before_acceptance: Status requirement       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              Student Status Check                             │
│  - menunggu (Pending)                                        │
│  - diterima (Accepted)                                       │
│  - ditolak (Rejected)                                        │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              Billing Generation                              │
│  - Apply discounts                                           │
│  - Calculate amounts                                          │
│  - Create billing record                                      │
└─────────────────────────────────────────────────────────────┘
```

### Components and Their Responsibilities

| Component | Responsibility |
|-----------|---------------|
| [`FeeCategory`](../app/Models/FeeCategory.php) | Defines billing rules and behavior for fee types |
| [`Student`](../app/Models/Student.php) | Tracks student status and manages billing relationships |
| [`BillingService`](../app/Services/BillingService.php) | Core billing logic with rule enforcement |
| [`StudentObserver`](../app/Observers/StudentObserver.php) | Reacts to student status changes |
| [`ActivationMode`](../app/Enums/ActivationMode.php) | Defines activation behavior options |
| [`StudentStatus`](../app/Enums/StudentStatus.php) | Defines student status values |

---

## Database Schema Changes

### New Columns Added to fee_categories Table

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `is_locked` | boolean | `false` | Prevents modification of critical categories |
| `activation_mode` | enum | `'multi_active'` | Controls how billings are activated |
| `can_generate_before_acceptance` | boolean | `true` | Allows billing before student acceptance |

**Migration File**: [`2026_03_03_055000_add_business_rule_fields_to_fee_categories_table.php`](../database/migrations/2026_03_03_055000_add_business_rule_fields_to_fee_categories_table.php)

```php
Schema::table('fee_categories', function (Blueprint $table) {
    $table->boolean('is_locked')->default(false)->after('code');
    $table->enum('activation_mode', ['single_active_per_key', 'multi_active', 'manual_only'])
          ->default('multi_active')->after('is_locked');
    $table->boolean('can_generate_before_acceptance')->default(true)->after('activation_mode');
});
```

### New Columns Added to students Table

| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `status` | enum | `'menunggu'` | Student acceptance status |

**Migration File**: [`2026_03_03_060000_add_status_to_students_table.php`](../database/migrations/2026_03_03_060000_add_status_to_students_table.php)

```php
Schema::table('students', function (Blueprint $table) {
    $table->enum('status', ['menunggu', 'diterima', 'ditolak'])
          ->default('menunggu')->after('special_status');
});
```

### Migration Files Created

1. **Business Rule Fields** (2026_03_03_055000)
   - Adds rule columns to [`fee_categories`](../database/migrations/2026_03_03_055000_add_business_rule_fields_to_fee_categories_table.php) table
   - Enables configurable billing behavior

2. **Student Status** (2026_03_03_060000)
   - Adds status tracking to [`students`](../database/migrations/2026_03_03_060000_add_status_to_students_table.php) table
   - Enables status-based billing control

### Default Values and Constraints

#### FeeCategory Defaults

```php
[
    'is_locked' => false,
    'activation_mode' => 'multi_active',
    'can_generate_before_acceptance' => true,
]
```

#### Student Status Defaults

```php
[
    'status' => 'menunggu', // Pending
]
```

#### Constraints

- `activation_mode` must be one of: `'single_active_per_key'`, `'multi_active'`, `'manual_only'`
- `status` must be one of: `'menunggu'`, `'diterima'`, `'ditolak'`
- Boolean columns are cast to proper types in models

---

## Business Rules

### Rule Properties Explained

#### 1. is_locked

**Purpose**: Prevents modification of critical fee categories

**Behavior**:
- When `true`: Category cannot be edited or deleted
- When `false`: Category can be modified normally

**Use Cases**:
- Protect registration fee categories
- Prevent accidental changes to core billing structures

**Example**:
```php
// SPMB (Biaya Pendaftaran) is locked to prevent changes
$spmbCategory = FeeCategory::create([
    'name' => 'Biaya Pendaftaran',
    'code' => 'SPMB',
    'is_locked' => true,
]);
```

#### 2. activation_mode

**Purpose**: Controls how billings are generated and managed for a category

**Values**:

| Mode | Description | Use Case |
|------|-------------|----------|
| `single_active_per_key` | Only one active billing per unique key | Monthly tuition (SPP) |
| `multi_active` | Multiple billings can coexist | One-time fees (registration, pocket money) |
| `manual_only` | Billings created manually only | Special fees requiring admin approval |

**Behavior Details**:

**single_active_per_key**:
- Automatically deactivates old billings when new ones are created
- Key includes: student_id, fee_category_id, billing_month, unit_target, residence_target
- Prevents duplicate billings for the same period

**multi_active**:
- Allows multiple billings for the same category
- No automatic deactivation
- Suitable for one-time fees

**manual_only**:
- Prevents automatic billing generation
- Requires manual creation through admin interface
- Throws exception if automatic generation is attempted

**Example**:
```php
// SPP uses single_active_per_key for monthly billing
$sppCategory = FeeCategory::create([
    'name' => 'Sumbangan Pembinaan Pendidikan',
    'code' => 'SPP',
    'activation_mode' => ActivationMode::SINGLE_ACTIVE_PER_KEY->value,
]);

// Registration uses multi_active for one-time fees
$regCategory = FeeCategory::create([
    'name' => 'Pendaftaran',
    'code' => 'REG',
    'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
]);
```

#### 3. can_generate_before_acceptance

**Purpose**: Controls whether billings can be created before student acceptance

**Behavior**:
- When `true`: Billings can be created for students with any status
- When `false`: Billings can only be created for accepted students

**Use Cases**:
- Registration fees: Often require acceptance first (`false`)
- Tuition fees: Can be generated upfront (`true`)
- Pocket money: Can be generated anytime (`true`)

**Example**:
```php
// SPMB requires acceptance before billing
$spmbCategory = FeeCategory::create([
    'name' => 'Biaya Pendaftaran',
    'can_generate_before_acceptance' => false,
]);

// SPP can be generated before acceptance
$sppCategory = FeeCategory::create([
    'name' => 'Sumbangan Pembinaan Pendidikan',
    'can_generate_before_acceptance' => true,
]);
```

### How Rules Work Together

The three rule properties interact to create flexible billing behavior:

```
┌─────────────────────────────────────────────────────────────┐
│                    Rule Interaction Matrix                    │
├─────────────────────────────────────────────────────────────┤
│ is_locked    | activation_mode          | can_generate_before │
│              |                          | acceptance          │
├─────────────────────────────────────────────────────────────┤
│ true         | manual_only              | false              │
│ (SPMB)       | (Admin only)             | (Requires accept)  │
├─────────────────────────────────────────────────────────────┤
│ false        | single_active_per_key    | true               │
│ (SPP)        | (One per month)         | (Any status)       │
├─────────────────────────────────────────────────────────────┤
│ false        | multi_active             | true               │
│ (REG, POCKET)| (Multiple allowed)       | (Any status)       │
└─────────────────────────────────────────────────────────────┘
```

### Rule Combinations and Their Effects

| Combination | Effect |
|-------------|--------|
| `is_locked: true` + `manual_only` + `can_generate_before_acceptance: false` | Protected category, admin-only creation, requires acceptance |
| `is_locked: false` + `single_active_per_key` + `can_generate_before_acceptance: true` | Editable, one billing per key, any status |
| `is_locked: false` + `multi_active` + `can_generate_before_acceptance: true` | Editable, multiple billings, any status |
| `is_locked: false` + `manual_only` + `can_generate_before_acceptance: true` | Editable, admin-only creation, any status |

---

## Category Configurations

### SPMB Category Rules and Behavior

**Configuration**:
```php
[
    'name' => 'Biaya Pendaftaran',
    'code' => 'SPMB',
    'is_locked' => true,
    'activation_mode' => 'manual_only',
    'can_generate_before_acceptance' => false,
]
```

**Behavior**:
- **Locked**: Cannot be edited or deleted
- **Manual Only**: Billings created only through admin interface
- **Requires Acceptance**: Students must be accepted before billing

**Use Case**: Registration fees that require approval and manual creation

**Workflow**:
1. Student applies (status: menunggu)
2. Admin reviews application
3. Admin accepts student (status: diterima)
4. Admin manually creates SPMB billing
5. Student receives billing notification

### SPP Category Rules and Behavior

**Configuration**:
```php
[
    'name' => 'Sumbangan Pembinaan Pendidikan',
    'code' => 'SPP',
    'is_locked' => false,
    'activation_mode' => 'single_active_per_key',
    'can_generate_before_acceptance' => true,
]
```

**Behavior**:
- **Not Locked**: Can be edited
- **Single Active Per Key**: Only one billing per month/student/category
- **Any Status**: Can be generated before acceptance

**Use Case**: Monthly tuition fees with automatic billing

**Workflow**:
1. System generates SPP billing for month
2. If billing already exists for month, old one is deactivated
3. New billing becomes active
4. Student receives notification

**Billing Key Components**:
- student_id
- fee_category_id
- billing_month (e.g., "2026-03")
- unit_target (if applicable)
- residence_target (if applicable)

### Other Categories (Default Rules)

#### REG (Pendaftaran)

```php
[
    'name' => 'Pendaftaran',
    'code' => 'REG',
    'is_locked' => false,
    'activation_mode' => 'multi_active',
    'can_generate_before_acceptance' => true,
]
```

**Behavior**: Multiple registration fees allowed, any status

#### RE_REG (Daftar Ulang)

```php
[
    'name' => 'Daftar Ulang',
    'code' => 'RE_REG',
    'is_locked' => false,
    'activation_mode' => 'multi_active',
    'can_generate_before_acceptance' => true,
]
```

**Behavior**: Multiple re-registration fees allowed, any status

#### POCKET (Uang Saku)

```php
[
    'name' => 'Uang Saku',
    'code' => 'POCKET',
    'is_locked' => false,
    'activation_mode' => 'multi_active',
    'can_generate_before_acceptance' => true,
]
```

**Behavior**: Multiple pocket money fees allowed, any status

#### OTHER (Lain-lain)

```php
[
    'name' => 'Lain-lain',
    'code' => 'OTHER',
    'is_locked' => false,
    'activation_mode' => 'multi_active',
    'can_generate_before_acceptance' => true,
]
```

**Behavior**: Miscellaneous fees, multiple allowed, any status

### How to Configure New Categories

**Step 1**: Create the category via admin interface or seeder

```php
use App\Enums\ActivationMode;
use App\Models\FeeCategory;

$category = FeeCategory::create([
    'name' => 'New Fee Category',
    'code' => 'NEW_CAT',
    'is_locked' => false,
    'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
    'can_generate_before_acceptance' => true,
]);
```

**Step 2**: Define activation mode based on requirements

- Use `manual_only` for fees requiring admin approval
- Use `single_active_per_key` for recurring fees (monthly, yearly)
- Use `multi_active` for one-time fees

**Step 3**: Set acceptance requirement

- Use `false` for fees that require student acceptance
- Use `true` for fees that can be generated anytime

**Step 4**: Configure lock status

- Use `true` for critical categories that shouldn't be modified
- Use `false` for editable categories

**Step 5**: Run seeder to populate database

```bash
php artisan db:seed --class=FeeCategorySeeder
```

---

## Student Status Tracking

### Student Status Values

| Status | Value | Label | Description |
|--------|-------|-------|-------------|
| Pending | `menunggu` | Menunggu | Student application is under review |
| Accepted | `diterima` | Diterima | Student has been accepted |
| Rejected | `ditolak` | Ditolak | Student application has been rejected |

**Enum Definition**: [`StudentStatus`](../app/Enums/StudentStatus.php)

```php
enum StudentStatus: string
{
    case PENDING = 'menunggu';
    case ACCEPTED = 'diterima';
    case REJECTED = 'ditolak';
}
```

### How Status Affects Billing Generation

The student status interacts with the `can_generate_before_acceptance` rule:

```
┌─────────────────────────────────────────────────────────────┐
│              Status × Acceptance Rule Matrix                 │
├─────────────────────────────────────────────────────────────┤
│ Student Status    | can_generate_before_acceptance: true    │
│ menunggu          | ✓ Billing allowed                       │
│ diterima          | ✓ Billing allowed                       │
│ ditolak           | ✓ Billing allowed                       │
├─────────────────────────────────────────────────────────────┤
│ Student Status    | can_generate_before_acceptance: false   │
│ menunggu          | ✗ Billing blocked                       │
│ diterima          | ✓ Billing allowed                       │
│ ditolak           | ✗ Billing blocked                       │
└─────────────────────────────────────────────────────────────┘
```

**Validation Logic** ([`BillingService::validateBillingCreation`](../app/Services/BillingService.php:135)):

```php
private function validateBillingCreation(FeeCategory $category, Student $student): void
{
    if ($category->requiresAcceptance()) {
        if (!$student->isAccepted()) {
            throw new Exception(
                "Tagihan kategori {$category->name} hanya dapat dibuat " .
                "untuk siswa dengan status 'diterima'. " .
                "Status siswa saat ini: {$student->status}"
            );
        }
    }

    if ($category->isManualOnly()) {
        throw new Exception(
            "Tagihan kategori {$category->name} hanya dapat dibuat " .
            "secara manual melalui antarmuka admin."
        );
    }
}
```

### Status Change Workflow

**Observer**: [`StudentObserver`](../app/Observers/StudentObserver.php)

```
┌─────────────────────────────────────────────────────────────┐
│              Student Status Change Workflow                  │
└─────────────────────────────────────────────────────────────┘

1. Admin updates student status
   │
   ▼
2. StudentObserver::updated() triggered
   │
   ├─► Special status changed?
   │   └─► Recalculate all billings for student
   │
   └─► Status changed?
       └─► handleStatusChange()
           │
           ├─► Pending → Accepted?
           │   └─► Recalculate billings
           │
           ├─► Accepted → Other?
           │   └─► Log unpaid billings
           │
           └─► Other changes?
               └─► Log status change
```

**Status Change Examples**:

```php
// Accept a student
$student->markAsAccepted();
// Triggers: recalculateStudentBillings()

// Reject a student
$student->markAsRejected();
// Logs: Student has unpaid billings (if any)

// Reset to pending
$student->markAsPending();
// Logs: Status change
```

---

## Usage Guide

### How to Create a New Fee Category with Rules

**Via Admin Interface**:

1. Navigate to "Kategori Biaya" (Fee Categories)
2. Click "Tambah Kategori" (Add Category)
3. Fill in the form:
   - **Nama Kategori** (Category Name): e.g., "Biaya Buku"
   - **Kode** (Code): e.g., "BOOK"
   - **Terkunci** (Locked): Check if category should be protected
   - **Mode Aktivasi** (Activation Mode): Select from dropdown
   - **Buat Sebelum Diterima** (Generate Before Acceptance): Check if allowed
4. Click "Simpan" (Save)

**Via Seeder**:

```php
// In FeeCategorySeeder.php
FeeCategory::updateOrCreate(
    ['code' => 'BOOK'],
    [
        'name' => 'Biaya Buku',
        'code' => 'BOOK',
        'is_locked' => false,
        'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
        'can_generate_before_acceptance' => true,
    ]
);
```

### How to Edit Existing Category Rules

**Via Admin Interface**:

1. Navigate to "Kategori Biaya" (Fee Categories)
2. Find the category to edit
3. Click "Edit" button
4. Modify the fields:
   - Update activation mode if needed
   - Toggle acceptance requirement
   - Lock/unlock category (if not already locked)
5. Click "Simpan" (Save)

**Important Notes**:
- Locked categories cannot be edited
- Changes affect all future billings
- Existing billings are not retroactively affected

### How Rules Affect Billing Creation

**Scenario 1: Automatic Billing Generation**

```php
use App\Services\BillingService;
use App\Models\Student;

$billingService = new BillingService();
$student = Student::find(1);

// Attempt to create billing
try {
    $billing = $billingService->generateBill(
        $student,
        $feeCategoryId,
        'SPP Maret 2026'
    );
    // Success: Billing created
} catch (Exception $e) {
    // Failed: Rules blocked creation
    // Common reasons:
    // - Student not accepted (if can_generate_before_acceptance = false)
    // - Category is manual_only
}
```

**Scenario 2: Manual Billing Creation**

```php
// For manual_only categories, use admin interface
// or create directly (bypassing automatic checks)
$billing = Billing::create([
    'student_id' => $student->id,
    'fee_master_id' => $feeMasterId,
    'title' => 'Biaya Pendaftaran',
    'original_amount' => 500000,
    'final_amount' => 500000,
    'status' => 'UNPAID',
]);
```

**Scenario 3: Single Active Per Key Behavior**

```php
// First billing for March 2026
$billing1 = $billingService->generateBill($student, $sppCategoryId, 'SPP Maret 2026');
// Billing 1 is active (visible_to_wali = true)

// Second billing for March 2026
$billing2 = $billingService->generateBill($student, $sppCategoryId, 'SPP Maret 2026');
// Billing 1 is deactivated (visible_to_wali = false)
// Billing 2 is active (visible_to_wali = true)
```

### Common Use Cases and Examples

#### Use Case 1: Monthly Tuition Billing

```php
// Configuration
$sppCategory = FeeCategory::where('code', 'SPP')->first();

// Generate billing for all students
$students = Student::where('status', 'diterima')->get();
foreach ($students as $student) {
    $billingService->generateBill(
        $student,
        $sppCategory->id,
        'SPP Maret 2026'
    );
}
```

#### Use Case 2: Registration Fee After Acceptance

```php
// Configuration
$spmbCategory = FeeCategory::where('code', 'SPMB')->first();

// Student accepted
$student->markAsAccepted();

// Create billing manually (manual_only category)
$billing = Billing::create([
    'student_id' => $student->id,
    'title' => 'Biaya Pendaftaran',
    'original_amount' => 1000000,
    'final_amount' => 1000000,
    'status' => 'UNPAID',
]);
```

#### Use Case 3: Multiple One-Time Fees

```php
// Configuration
$pocketCategory = FeeCategory::where('code', 'POCKET')->first();

// Generate multiple pocket money billings
$billingService->generateBill($student, $pocketCategory->id, 'Uang Saku Januari');
$billingService->generateBill($student, $pocketCategory->id, 'Uang Saku Februari');
$billingService->generateBill($student, $pocketCategory->id, 'Uang Saku Maret');
// All three billings remain active (multi_active)
```

#### Use Case 4: Status-Based Billing Control

```php
// Category requires acceptance
$spmbCategory = FeeCategory::where('code', 'SPMB')->first();

// Pending student
$student = Student::where('status', 'menunggu')->first();

try {
    $billingService->generateBill($student, $spmbCategory->id, 'Biaya Pendaftaran');
} catch (Exception $e) {
    // Exception: "Tagihan kategori Biaya Pendaftaran hanya dapat dibuat
    // untuk siswa dengan status 'diterima'. Status siswa saat ini: menunggu"
}

// Accept student
$student->markAsAccepted();

// Now billing can be created
$billing = $billingService->generateBill($student, $spmbCategory->id, 'Biaya Pendaftaran');
```

---

## API Reference

### FeeCategory Model Methods

**Location**: [`app/Models/FeeCategory.php`](../app/Models/FeeCategory.php)

#### Relationships

```php
public function fees(): HasMany
```
- Returns all fee masters in this category
- Usage: `$category->fees`

#### Rule Check Methods

```php
public function isLocked(): bool
```
- Returns `true` if category is locked
- Usage: `if ($category->isLocked()) { ... }`

```php
public function isManualOnly(): bool
```
- Returns `true` if activation mode is `manual_only`
- Usage: `if ($category->isManualOnly()) { ... }`

```php
public function isSingleActivePerKey(): bool
```
- Returns `true` if activation mode is `single_active_per_key`
- Usage: `if ($category->isSingleActivePerKey()) { ... }`

```php
public function isMultiActive(): bool
```
- Returns `true` if activation mode is `multi_active`
- Usage: `if ($category->isMultiActive()) { ... }`

```php
public function canGenerateBeforeAcceptance(): bool
```
- Returns `true` if billing can be created before acceptance
- Usage: `if ($category->canGenerateBeforeAcceptance()) { ... }`

```php
public function requiresAcceptance(): bool
```
- Returns `true` if billing requires student acceptance
- Usage: `if ($category->requiresAcceptance()) { ... }`

#### Validation Rules

```php
public static function rules(): array
```
- Returns validation rules for fee category creation/update
- Usage: `$rules = FeeCategory::rules();`

### Student Model Methods

**Location**: [`app/Models/Student.php`](../app/Models/Student.php)

#### Relationships

```php
public function guardian(): BelongsTo
```
- Returns the student's guardian
- Usage: `$student->guardian`

```php
public function billings(): HasMany
```
- Returns all billings for the student
- Usage: `$student->billings`

#### Status Methods

```php
public function getStatus(): string
```
- Returns the student's status value
- Usage: `$status = $student->getStatus()`

```php
public function getStatusEnum(): ?StudentStatus
```
- Returns the student's status as enum
- Usage: `$statusEnum = $student->getStatusEnum()`

```php
public function isPending(): bool
```
- Returns `true` if student status is `menunggu`
- Usage: `if ($student->isPending()) { ... }`

```php
public function isAccepted(): bool
```
- Returns `true` if student status is `diterima`
- Usage: `if ($student->isAccepted()) { ... }`

```php
public function isRejected(): bool
```
- Returns `true` if student status is `ditolak`
- Usage: `if ($student->isRejected()) { ... }`

```php
public function markAsAccepted(): void
```
- Updates student status to `diterima`
- Usage: `$student->markAsAccepted()`

```php
public function markAsPending(): void
```
- Updates student status to `menunggu`
- Usage: `$student->markAsPending()`

```php
public function markAsRejected(): void
```
- Updates student status to `ditolak`
- Usage: `$student->markAsRejected()`

```php
public function setStatus(string $status): void
```
- Updates student status to specified value
- Throws `InvalidArgumentException` for invalid status
- Usage: `$student->setStatus('diterima')`

### BillingService Methods

**Location**: [`app/Services/BillingService.php`](../app/Services/BillingService.php)

#### Billing Generation

```php
public function generateBill(Student $student, int $feeCategoryId, string $title, ?string $feeItemName = null)
```
- Generates a billing record for a student
- Parameters:
  - `$student`: Student model instance
  - `$feeCategoryId`: ID of fee category
  - `$title`: Billing title
  - `$feeItemName`: Optional specific fee item name
- Returns: Billing model instance or `null`
- Throws: Exception if rules block creation

**Example**:
```php
$billing = $billingService->generateBill(
    $student,
    $sppCategoryId,
    'SPP Maret 2026'
);
```

#### Billing Recalculation

```php
public function recalculateBillingsForFeeMaster(FeeMaster $feeMaster): int
```
- Recalculates all unpaid billings for a specific fee master
- Parameters:
  - `$feeMaster`: FeeMaster model instance
- Returns: Number of billings updated

```php
public function recalculateBilling(Billing $billing, ?FeeMaster $feeMaster = null): void
```
- Recalculates a single billing based on current fee master and discounts
- Parameters:
  - `$billing`: Billing model instance
  - `$feeMaster`: Optional FeeMaster instance (auto-detected if null)

```php
public function recalculateStudentBillings(Student $student): int
```
- Recalculates all unpaid billings for a student
- Parameters:
  - `$student`: Student model instance
- Returns: Number of billings updated

#### Batch Billing Generation

```php
public function generateOnceBillsForSelectedFees(Student $student, array $feeMasterIds): int
```
- Generates one-time billings for selected fee masters
- Parameters:
  - `$student`: Student model instance
  - `$feeMasterIds`: Array of fee master IDs
- Returns: Number of billings created

**Example**:
```php
$count = $billingService->generateOnceBillsForSelectedFees(
    $student,
    [1, 2, 3] // Fee master IDs
);
```

### Enum Classes

#### ActivationMode

**Location**: [`app/Enums/ActivationMode.php`](../app/Enums/ActivationMode.php)

**Values**:
- `SINGLE_ACTIVE_PER_KEY` = `'single_active_per_key'`
- `MULTI_ACTIVE` = `'multi_active'`
- `MANUAL_ONLY` = `'manual_only'`

**Methods**:
```php
public function value(): string
```
- Returns the string value of the enum

```php
public static function values(): array
```
- Returns all enum values as array

**Usage**:
```php
use App\Enums\ActivationMode;

$mode = ActivationMode::SINGLE_ACTIVE_PER_KEY;
echo $mode->value(); // 'single_active_per_key'

$values = ActivationMode::values();
// ['single_active_per_key', 'multi_active', 'manual_only']
```

#### StudentStatus

**Location**: [`app/Enums/StudentStatus.php`](../app/Enums/StudentStatus.php)

**Values**:
- `PENDING` = `'menunggu'`
- `ACCEPTED` = `'diterima'`
- `REJECTED` = `'ditolak'`

**Methods**:
```php
public function getLabel(): string
```
- Returns the Indonesian label for the status

```php
public function isPending(): bool
```
- Returns `true` if status is pending

```php
public function isAccepted(): bool
```
- Returns `true` if status is accepted

```php
public function isRejected(): bool
```
- Returns `true` if status is rejected

**Usage**:
```php
use App\Enums\StudentStatus;

$status = StudentStatus::ACCEPTED;
echo $status->getLabel(); // 'Diterima'

if ($status->isAccepted()) {
    // Handle accepted student
}
```

---

## Migration Guide

### Steps to Upgrade from Old System

**Prerequisites**:
- Laravel 10.x or higher
- PHP 8.1 or higher
- Database backup recommended

**Step 1: Backup Database**

```bash
# Create database backup
mysqldump -u username -p database_name > backup_before_upgrade.sql
```

**Step 2: Pull Latest Code**

```bash
git pull origin main
composer install
npm install
npm run build
```

**Step 3: Run Migrations**

```bash
# Run all pending migrations
php artisan migrate
```

**Expected Migrations**:
1. `2026_03_03_055000_add_business_rule_fields_to_fee_categories_table.php`
2. `2026_03_03_060000_add_status_to_students_table.php`

**Step 4: Run Seeders**

```bash
# Seed fee categories with default rules
php artisan db:seed --class=FeeCategorySeeder

# Seed other data if needed
php artisan db:seed
```

**Step 5: Clear Cache**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Step 6: Verify Installation**

```bash
# Check fee categories have new columns
php artisan tinker
>>> \App\Models\FeeCategory::first()->getAttributes();
// Should include: is_locked, activation_mode, can_generate_before_acceptance

# Check students have status column
>>> \App\Models\Student::first()->getAttributes();
// Should include: status
```

### Running Migrations

**Single Migration**:
```bash
php artisan migrate --path=database/migrations/2026_03_03_055000_add_business_rule_fields_to_fee_categories_table.php
```

**All Migrations**:
```bash
php artisan migrate
```

**Rollback Last Migration**:
```bash
php artisan migrate:rollback
```

**Rollback Specific Migration**:
```bash
php artisan migrate:rollback --step=1
```

### Running Seeders

**Single Seeder**:
```bash
php artisan db:seed --class=FeeCategorySeeder
```

**All Seeders**:
```bash
php artisan db:seed
```

**Force Re-seed**:
```bash
php artisan db:seed --class=FeeCategorySeeder --force
```

### Verifying Installation

**Check Fee Categories**:

```php
// Via Tinker
php artisan tinker

// Check all categories
$categories = \App\Models\FeeCategory::all();
foreach ($categories as $cat) {
    echo "{$cat->code}: {$cat->activation_mode}, locked: {$cat->is_locked}\n";
}

// Expected output:
// SPMB: manual_only, locked: 1
// SPP: single_active_per_key, locked: 0
// REG: multi_active, locked: 0
// RE_REG: multi_active, locked: 0
// POCKET: multi_active, locked: 0
// OTHER: multi_active, locked: 0
```

**Check Student Status**:

```php
// Via Tinker
php artisan tinker

// Check students have status
$students = \App\Models\Student::all();
foreach ($students as $student) {
    echo "{$student->full_name}: {$student->status}\n";
}

// Expected output:
// Student Name: menunggu
```

**Test Billing Generation**:

```php
// Via Tinker
php artisan tinker

use App\Services\BillingService;
use App\Models\Student;

$billingService = new BillingService();
$student = Student::first();

// Test SPP billing (should work)
$billing = $billingService->generateBill($student, 2, 'Test SPP');
echo "Billing created: " . ($billing ? 'Yes' : 'No') . "\n";

// Test SPMB billing (should fail for pending student)
try {
    $billing = $billingService->generateBill($student, 1, 'Test SPMB');
    echo "SPMB billing created: Yes\n";
} catch (Exception $e) {
    echo "SPMB billing blocked: " . $e->getMessage() . "\n";
}
```

---

## Testing

### How to Test the Implementation

**Manual Testing Steps**:

1. **Test Fee Category Rules**:
   - Create new category with different activation modes
   - Verify locked categories cannot be edited
   - Test manual_only categories block automatic generation

2. **Test Student Status**:
   - Create student with pending status
   - Accept student and verify status change
   - Test billing generation with different statuses

3. **Test Billing Generation**:
   - Generate billing for multi_active category
   - Generate billing for single_active_per_key category
   - Verify old billings are deactivated
   - Test manual_only category behavior

4. **Test Status Changes**:
   - Change student status and verify observer triggers
   - Check billing recalculation on status change
   - Verify logs are created

**Automated Testing**:

```bash
// Run all tests
php artisan test

// Run specific test file
php artisan test tests/Feature/BillingServiceTest.php

// Run with coverage
php artisan test --coverage
```

### Test Scenarios Covered

#### Scenario 1: Multi-Active Billing

```php
public function test_multi_active_category_allows_multiple_billings()
{
    $category = FeeCategory::factory()->create([
        'activation_mode' => ActivationMode::MULTI_ACTIVE->value,
    ]);

    $student = Student::factory()->create();

    $billing1 = $this->billingService->generateBill($student, $category->id, 'Billing 1');
    $billing2 = $this->billingService->generateBill($student, $category->id, 'Billing 2');

    $this->assertNotNull($billing1);
    $this->assertNotNull($billing2);
    $this->assertTrue($billing1->visible_to_wali);
    $this->assertTrue($billing2->visible_to_wali);
}
```

#### Scenario 2: Single Active Per Key

```php
public function test_single_active_per_key_deactivates_old_billing()
{
    $category = FeeCategory::factory()->create([
        'activation_mode' => ActivationMode::SINGLE_ACTIVE_PER_KEY->value,
    ]);

    $student = Student::factory()->create();

    $billing1 = $this->billingService->generateBill($student, $category->id, 'Billing 1');
    $billing2 = $this->billingService->generateBill($student, $category->id, 'Billing 2');

    $this->assertTrue($billing1->fresh()->visible_to_wali === false);
    $this->assertTrue($billing2->visible_to_wali === true);
}
```

#### Scenario 3: Requires Acceptance

```php
public function test_requires_acceptance_blocks_pending_students()
{
    $category = FeeCategory::factory()->create([
        'can_generate_before_acceptance' => false,
    ]);

    $student = Student::factory()->create([
        'status' => StudentStatus::PENDING->value,
    ]);

    $this->expectException(Exception::class);
    $this->billingService->generateBill($student, $category->id, 'Test Billing');
}
```

#### Scenario 4: Manual Only

```php
public function test_manual_only_blocks_automatic_generation()
{
    $category = FeeCategory::factory()->create([
        'activation_mode' => ActivationMode::MANUAL_ONLY->value,
    ]);

    $student = Student::factory()->create();

    $this->expectException(Exception::class);
    $this->billingService->generateBill($student, $category->id, 'Test Billing');
}
```

### Known Limitations

1. **No Bulk Status Update**: Student status must be updated individually
2. **No Rule Versioning**: Rule changes affect all future billings immediately
3. **No Retroactive Billing**: Cannot apply new rules to existing billings
4. **Manual Only Categories**: Cannot be generated through service, must use admin interface
5. **Single Active Per Key**: Key is fixed and cannot be customized per category

---

## Troubleshooting

### Common Issues and Solutions

#### Issue 1: Billing Not Created for Accepted Student

**Symptoms**:
- Student status is `diterima`
- Billing generation throws exception
- Error message mentions category rules

**Possible Causes**:
1. Category is `manual_only`
2. Category has `can_generate_before_acceptance: false` but student was not accepted at time of generation

**Solutions**:
```php
// Check category configuration
$category = FeeCategory::find($categoryId);
echo "Activation Mode: " . $category->activation_mode . "\n";
echo "Can Generate Before Acceptance: " . $category->can_generate_before_acceptance . "\n";

// If manual_only, create billing manually
if ($category->isManualOnly()) {
    $billing = Billing::create([
        'student_id' => $student->id,
        'fee_master_id' => $feeMasterId,
        'title' => 'Manual Billing',
        'original_amount' => $amount,
        'final_amount' => $amount,
        'status' => 'UNPAID',
    ]);
}
```

#### Issue 2: Old Billing Still Visible After New One Created

**Symptoms**:
- Category is `single_active_per_key`
- Old billing remains visible after new one created
- Multiple billings shown to guardian

**Possible Causes**:
1. Billing key mismatch
2. Deactivation logic not triggered
3. Database transaction not committed

**Solutions**:
```php
// Check billing keys
$billing1 = Billing::find(1);
$billing2 = Billing::find(2);

echo "Billing 1 Key: " . json_encode([
    'student_id' => $billing1->student_id,
    'fee_category_id' => $billing1->fee_master->category->id,
    'billing_month' => $billing1->fee_master->billing_month,
]) . "\n";

echo "Billing 2 Key: " . json_encode([
    'student_id' => $billing2->student_id,
    'fee_category_id' => $billing2->fee_master->category->id,
    'billing_month' => $billing2->fee_master->billing_month,
]) . "\n";

// Manually deactivate old billing
$billing1->update(['visible_to_wali' => false]);
```

#### Issue 3: Student Status Change Not Triggering Billing Recalculation

**Symptoms**:
- Student status changed
- Billings not recalculated
- Discounts not applied

**Possible Causes**:
1. Observer not registered
2. Special status not changed
3. Billings already paid

**Solutions**:
```php
// Check observer is registered
php artisan tinker
>>> app('events')->getListeners('eloquent.updated: App\Models\Student');

// Manually recalculate billings
$student = Student::find($studentId);
$billingService->recalculateStudentBillings($student);

// Check logs
tail -f storage/logs/laravel.log
```

#### Issue 4: Cannot Edit Fee Category

**Symptoms**:
- Category edit button disabled
- Error message when trying to update
- Changes not saved

**Possible Causes**:
1. Category is locked (`is_locked: true`)
2. User lacks permissions
3. Database constraint violation

**Solutions**:
```php
// Check if category is locked
$category = FeeCategory::find($categoryId);
if ($category->isLocked()) {
    echo "Category is locked and cannot be edited\n";
}

// Unlock category (use with caution)
$category->update(['is_locked' => false]);
```

### Error Messages and Their Meanings

| Error Message | Meaning | Solution |
|---------------|---------|----------|
| `Tagihan kategori X hanya dapat dibuat untuk siswa dengan status 'diterima'` | Category requires acceptance but student is not accepted | Accept student first or change category rule |
| `Tagihan kategori X hanya dapat dibuat secara manual melalui antarmuka admin` | Category is manual_only | Create billing through admin interface |
| `Tagihan yang sudah dibayar tidak dapat diubah` | Attempting to modify paid billing | Create new billing instead |
| `Tidak dapat menghapus tagihan yang sudah dibayar` | Attempting to delete paid billing | Archive billing instead |
| `Invalid status: X` | Invalid student status value | Use valid status: menunggu, diterima, ditolak |

### Debugging Tips

**Enable Query Logging**:
```php
// In app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\DB;

public function boot()
{
    DB::listen(function ($query) {
        logger($query->sql, $query->bindings);
    });
}
```

**Check Observer Events**:
```php
// Add logging to StudentObserver
public function updated(Student $student): void
{
    Log::info('Student updated', [
        'id' => $student->id,
        'changes' => $student->getDirty(),
    ]);
}
```

**Trace Billing Generation**:
```php
// Add logging to BillingService
public function generateBill(Student $student, int $feeCategoryId, string $title, ?string $feeItemName = null)
{
    Log::info('Generating bill', [
        'student_id' => $student->id,
        'fee_category_id' => $feeCategoryId,
        'title' => $title,
    ]);

    // ... rest of method
}
```

**Check Database State**:
```php
// Via Tinker
php artisan tinker

// Check fee categories
>>> \App\Models\FeeCategory::with('fees')->get()->toArray()

// Check student status
>>> \App\Models\Student::with('billings')->find(1)->toArray()

// Check billing visibility
>>> \App\Models\Billing::where('visible_to_wali', true)->get()->toArray()
```

---

## Future Enhancements

### Potential Improvements

1. **Rule Versioning**:
   - Track rule changes over time
   - Apply different rules to different billing periods
   - Audit trail of rule modifications

2. **Custom Billing Keys**:
   - Allow per-category customization of billing keys
   - Support complex key combinations
   - Enable more granular control over billing uniqueness

3. **Rule Conditions**:
   - Add conditional rules based on time, location, or other factors
   - Support rule chaining and priority
   - Enable dynamic rule evaluation

4. **Bulk Operations**:
   - Bulk student status updates
   - Bulk billing generation with progress tracking
   - Batch rule updates

5. **Advanced Notifications**:
   - Configurable notification templates per category
   - Multi-channel notifications (email, SMS, push)
   - Notification scheduling

### Extensibility Options

1. **Custom Activation Modes**:
   ```php
   // Add new activation mode to enum
   enum ActivationMode: string
   {
       // ... existing modes
       case SCHEDULED = 'scheduled'; // New mode
   }
   ```

2. **Custom Status Values**:
   ```php
   // Add new student status
   enum StudentStatus: string
   {
       // ... existing statuses
       case ON_HOLD = 'ditangguhkan'; // New status
   }
   ```

3. **Custom Rule Validators**:
   ```php
   // Add custom validation logic
   class CustomBillingValidator
   {
       public function validate(FeeCategory $category, Student $student): bool
       {
           // Custom validation logic
           return true;
       }
   }
   ```

4. **Event-Driven Architecture**:
   ```php
   // Dispatch events for billing lifecycle
   event(new BillingCreated($billing));
   event(new BillingDeactivated($billing));
   event(new StudentStatusChanged($student));
   ```

5. **Plugin System**:
   ```php
   // Allow third-party extensions
   interface BillingPluginInterface
   {
       public function beforeGenerate(BillingContext $context): void;
       public function afterGenerate(Billing $billing): void;
   }
   ```

---

## Conclusion

The rule-based billing system provides a flexible, scalable foundation for managing fee categories and billing operations. By centralizing business rules in the database and enforcing them through the service layer, the system ensures consistency and maintainability while allowing for easy customization.

For questions or issues, refer to the troubleshooting section or consult the development team.

---

**Document Version**: 1.0.0
**Last Updated**: 2026-03-03
**System Version**: SIM Santri An-Nawawiy
