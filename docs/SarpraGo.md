# Dokumentasi UKK - Sarprago (Sistem Peminjaman Sarpras)

Dokumen ini menyajikan metode pengembangan, struktur data, diagram, serta dokumentasi modul berdasarkan implementasi pada proyek **SarpraGo** menggunakan **Laravel 12** dan **Filament v5**.

---

## 1) Metode Pengembangan: Waterfall (Prototype)

### a. Analisis Kebutuhan

#### Tujuan Sistem
- Digitalisasi pengelolaan inventaris sarana dan prasarana.
- Automasi alur peminjaman dari pengajuan hingga pengembalian.
- Membuat laporan dalam format pdf.

#### Aktor & Hak Akses
- **User**
    - Login dan logout
    - Melihat daftar alat
    - Mengajukan peminjaman
- **Staff**
    - Login dan logout
    - Menyetujui peminjaman 
    - Mengkonfirmasi pengembalian
    - Mencetak laporan
- **Admin**
    - Login dan logout
    - CRUD user
    - CRUD alat
    - CRUD kategori
    - Melihat log aktifitas
    - Menyetujui peminjaman 
    - Mengkonfirmasi pengembalian
    - Mencetak laporan

---

## 2) Struktur Data & Tipe Data

### 2.1 Struktur Data (Database Schema)

#### Tabel: `users` (Model: `User`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Utama User |
| `name` | VARCHAR | NOT NULL | Nama Lengkap |
| `email` | VARCHAR | UNIQUE, NOT NULL | Alamat Email |
| `password` | VARCHAR | NOT NULL | Password (Hashed) |
| `role` | ENUM | `admin`, `staff`, `user` | Level Akses (Default: user) |
| `is_active` | BOOLEAN | Default: true | Status Akif Akun |
| `deleted_at` | TIMESTAMP | Nullable | Soft Delete |

#### Tabel: `categories` (Model: `Category`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Kategori |
| `name` | VARCHAR | UNIQUE, NOT NULL | Nama Kategori |
| `slug` | VARCHAR | UNIQUE, NOT NULL | URL Friendly Name |

#### Tabel: `items` (Model: `Item`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Barang |
| `category_id` | FK | `categories` (Restrict) | Relasi Kategori |
| `name` | VARCHAR | UNIQUE, NOT NULL | Nama Barang |
| `slug` | VARCHAR | UNIQUE, NOT NULL | URL Friendly Name |
| `brand` | VARCHAR | Nullable | Merk Barang |
| `code_prefix` | VARCHAR | NOT NULL | Awalan Kode Asset |
| `image_path` | VARCHAR | Nullable | Path Foto Barang |

#### Tabel: `item_units` (Model: `ItemUnit`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Unit Spesifik |
| `item_id` | FK | `items` (Cascade) | Induk Barang |
| `unit_code` | VARCHAR | UNIQUE, NOT NULL | Serial Number / Kode Unik |
| `condition` | ENUM | `good`, `damaged`, `broken` | Kondisi Fisik |
| `status` | ENUM | `available`, `reserved`, `borrowed`, etc. | Status Ketersediaan |
| `attributes` | JSON | Nullable | Spek tambahan (Warna, dll) |

#### Tabel: `loans` (Model: `Loan`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Transaksi |
| `loan_code` | VARCHAR | UNIQUE, NOT NULL | No. Referensi Pinjam |
| `user_id` | FK | `users` (Cascade) | ID Peminjam |
| `approved_by` | FK | `users` (Set Null) | ID Petugas Penyetuju |
| `status` | ENUM | `pending`, `approved`, `on_going`, etc. | Status Transaksi |
| `borrowed_at` | DATETIME | Nullable | Waktu Serah Terima |
| `due_at` | DATETIME | NOT NULL | Tenggat Pengembalian |
| `finished_at` | DATETIME | Nullable | Waktu Selesai |

#### Tabel: `loan_items` (Model: `LoanItem`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Baris Item |
| `loan_id` | FK | `loans` (Cascade) | Parent Transaksi |
| `item_id` | FK | `items` (Cascade) | Barang yang Dipilih |
| `qty_request` | INT | NOT NULL | Jumlah yang Diminta |
| `qty_approved` | INT | Default: 0 | Jumlah Disetujui Petugas |

#### Tabel: `loan_details` (Model: `LoanDetail`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Detail Unit |
| `loan_item_id` | FK | `loan_items` (Cascade) | Hubungan ke Item Pinjam |
| `item_unit_id` | FK | `item_units` (Cascade) | Unit Fisik yang Dipakai |
| `condition_out` | ENUM | `good`, `damaged` | Kondisi Awal |
| `condition_in` | ENUM | `good`, `damaged`, `broken` | Kondisi Akhir |
| `returned_at` | DATETIME | Nullable | Waktu Unit Kembali |

#### Tabel: `fines` (Model: `Fine`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Denda |
| `loan_id` | FK | `loans` (Cascade) | Transaksi Terkait |
| `user_id` | FK | `users` (Cascade) | Target Denda |
| `amount` | DECIMAL | 12,2 | Nominal Rupiah |
| `reason` | VARCHAR | NOT NULL | Alasan (Terlambat/Rusak) |
| `status` | ENUM | `unpaid`, `paid` | Status Pelunasan |

#### Tabel: `activity_logs` (Model: `ActivityLog`)
| Field | Tipe | Aturan/Pembatasan | Keterangan |
| --- | --- | --- | --- |
| `id` | BIGINT | PK, AI | ID Log |
| `description` | TEXT | NOT NULL | Detail Aktivitas |
| `user_id` | BIGINT | Nullable | ID Aktor |
| `subject_id/type`| MORPH | NOT NULL | Objek yang Berubah |
| `properties` | JSON | Nullable | Data Lama & Data Baru |

---

## 3) Diagram Sistem

### 3.1 ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ LOANS : "mengajukan / menyetujui"
    USERS ||--o{ FINES : "menerima denda"
    USERS ||--o{ ACTIVITY_LOGS : "melakukan aksi"
    CATEGORIES ||--o{ ITEMS : "mengelompokkan"
    ITEMS ||--o{ ITEM_UNITS : "memiliki fisik"
    ITEMS ||--o{ LOAN_ITEMS : "dipesan dalam"
    LOANS ||--|{ LOAN_ITEMS : "berisi daftar"
    LOANS ||--o| FINES : "memiliki denda"
    LOAN_ITEMS ||--|{ LOAN_DETAILS : "memiliki detail unit"
    ITEM_UNITS ||--o{ LOAN_DETAILS : "dipakai pada"

    USERS {
        bigint id PK
        string name
        string email UK
        enum role "admin, staff, user"
        boolean is_active
    }

    CATEGORIES {
        bigint id PK
        string name UK
        string slug UK
    }

    ITEMS {
        bigint id PK
        bigint category_id FK
        string name UK
        string code_prefix
        string brand
    }

    ITEM_UNITS {
        bigint id PK
        bigint item_id FK
        string unit_code UK
        enum condition "good, damaged, broken"
        enum status "available, reserved, borrowed, etc"
    }

    LOANS {
        bigint id PK
        string loan_code UK
        bigint user_id FK
        bigint approved_by FK
        enum status "pending, approved, on_going, etc"
        datetime due_at
        datetime borrowed_at
        datetime finished_at
    }

    LOAN_ITEMS {
        bigint id PK
        bigint loan_id FK
        bigint item_id FK
        int qty_request
        int qty_approved
    }

    LOAN_DETAILS {
        bigint id PK
        bigint loan_item_id FK
        bigint item_unit_id FK
        enum condition_out
        enum condition_in
        datetime returned_at
    }

    FINES {
        bigint id PK
        bigint loan_id FK
        bigint user_id FK
        decimal amount
        string reason
        enum status "unpaid, paid"
    }

    ACTIVITY_LOGS {
        bigint id PK
        string log_name
        text description
        bigint user_id FK
        string subject_type
        bigint subject_id
        json properties
    }