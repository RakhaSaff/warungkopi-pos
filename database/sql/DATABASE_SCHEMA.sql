-- ============================================================
-- Warung Kopi Nusantara — PostgreSQL Schema
-- Generated for Laravel 12
-- ============================================================

-- Enable UUID extension (opsional, kita pakai BIGSERIAL)
-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- 1. USERS
-- ============================================================
CREATE TABLE users (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password        VARCHAR(255) NOT NULL,
    role            VARCHAR(20) NOT NULL DEFAULT 'kasir' CHECK (role IN ('owner', 'kasir')),
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    phone           VARCHAR(20) NULL,
    remember_token  VARCHAR(100) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);

-- ============================================================
-- 2. PRODUCT CATEGORIES
-- ============================================================
CREATE TABLE product_categories (
    id          BIGSERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    icon        VARCHAR(10) NULL,
    sort_order  INTEGER NOT NULL DEFAULT 0,
    is_active   BOOLEAN NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);

-- ============================================================
-- 3. CONSIGNMENT SUPPLIERS
-- ============================================================
CREATE TABLE consignment_suppliers (
    id            BIGSERIAL PRIMARY KEY,
    name          VARCHAR(255) NOT NULL,
    phone         VARCHAR(20) NULL,
    address       TEXT NULL,
    balance_owed  NUMERIC(15,2) NOT NULL DEFAULT 0,
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL
);

-- ============================================================
-- 4. PRODUCTS
-- ============================================================
CREATE TABLE products (
    id              BIGSERIAL PRIMARY KEY,
    category_id     BIGINT NOT NULL REFERENCES product_categories(id),
    supplier_id     BIGINT NULL REFERENCES consignment_suppliers(id),
    name            VARCHAR(255) NOT NULL,
    sku             VARCHAR(50) UNIQUE NULL,
    description     TEXT NULL,
    price           NUMERIC(12,2) NOT NULL,
    cost_price      NUMERIC(12,2) NOT NULL DEFAULT 0,
    stock           INTEGER NOT NULL DEFAULT 0,
    stock_alert     INTEGER NOT NULL DEFAULT 5,
    is_consignment  BOOLEAN NOT NULL DEFAULT FALSE,
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    has_variants    BOOLEAN NOT NULL DEFAULT FALSE,
    image           VARCHAR(255) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL  -- soft delete
);

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_active   ON products(is_active) WHERE deleted_at IS NULL;

-- ============================================================
-- 5. PRODUCT ADD-ONS / VARIAN
-- ============================================================
CREATE TABLE product_addons (
    id          BIGSERIAL PRIMARY KEY,
    product_id  BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    name        VARCHAR(100) NOT NULL,
    price       NUMERIC(10,2) NOT NULL DEFAULT 0,
    is_active   BOOLEAN NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);

-- ============================================================
-- 6. INGREDIENTS (BAHAN BAKU)
-- ============================================================
CREATE TABLE ingredients (
    id           BIGSERIAL PRIMARY KEY,
    name         VARCHAR(255) NOT NULL,
    unit         VARCHAR(20) NOT NULL,
    stock        NUMERIC(12,3) NOT NULL DEFAULT 0,
    stock_alert  NUMERIC(12,3) NOT NULL DEFAULT 0,
    cost_per_unit NUMERIC(12,4) NOT NULL DEFAULT 0,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL
);

-- ============================================================
-- 7. PRODUCT INGREDIENTS (RESEP / BOM)
-- ============================================================
CREATE TABLE product_ingredients (
    id             BIGSERIAL PRIMARY KEY,
    product_id     BIGINT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    ingredient_id  BIGINT NOT NULL REFERENCES ingredients(id) ON DELETE CASCADE,
    quantity       NUMERIC(10,3) NOT NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,
    UNIQUE (product_id, ingredient_id)
);

-- ============================================================
-- 8. SHIFTS
-- ============================================================
CREATE TABLE shifts (
    id                          BIGSERIAL PRIMARY KEY,
    user_id                     BIGINT NOT NULL REFERENCES users(id),
    shift_name                  VARCHAR(50) NOT NULL,
    started_at                  TIMESTAMP NOT NULL,
    ended_at                    TIMESTAMP NULL,
    opening_balance             NUMERIC(12,2) NOT NULL DEFAULT 0,
    closing_balance_expected    NUMERIC(12,2) NULL,
    closing_balance_actual      NUMERIC(12,2) NULL,
    closing_balance_difference  NUMERIC(12,2) NULL,
    notes                       TEXT NULL,
    status                      VARCHAR(10) NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'closed')),
    created_at                  TIMESTAMP NULL,
    updated_at                  TIMESTAMP NULL
);

CREATE INDEX idx_shifts_user_status ON shifts(user_id, status);

-- ============================================================
-- 9. TRANSACTIONS
-- ============================================================
CREATE TABLE transactions (
    id                  BIGSERIAL PRIMARY KEY,
    invoice_number      VARCHAR(50) UNIQUE NOT NULL,
    shift_id            BIGINT NOT NULL REFERENCES shifts(id),
    user_id             BIGINT NOT NULL REFERENCES users(id),
    customer_name       VARCHAR(100) NULL,
    payment_method      VARCHAR(20) NOT NULL CHECK (payment_method IN ('tunai', 'qris', 'transfer')),
    payment_reference   VARCHAR(100) NULL,
    subtotal            NUMERIC(12,2) NOT NULL,
    discount            NUMERIC(12,2) NOT NULL DEFAULT 0,
    total               NUMERIC(12,2) NOT NULL,
    amount_paid         NUMERIC(12,2) NOT NULL DEFAULT 0,
    change_amount       NUMERIC(12,2) NOT NULL DEFAULT 0,
    consignment_amount  NUMERIC(12,2) NOT NULL DEFAULT 0,
    status              VARCHAR(20) NOT NULL DEFAULT 'completed' CHECK (status IN ('completed', 'voided', 'pending')),
    void_reason         TEXT NULL,
    voided_at           TIMESTAMP NULL,
    voided_by           BIGINT NULL REFERENCES users(id),
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL
);

-- Index penting untuk performa query laporan
CREATE INDEX idx_transactions_date_status   ON transactions(created_at, status);
CREATE INDEX idx_transactions_shift         ON transactions(shift_id, status);
CREATE INDEX idx_transactions_payment       ON transactions(payment_method);
CREATE INDEX idx_transactions_date          ON transactions(DATE(created_at));

-- ============================================================
-- 10. TRANSACTION ITEMS
-- ============================================================
CREATE TABLE transaction_items (
    id              BIGSERIAL PRIMARY KEY,
    transaction_id  BIGINT NOT NULL REFERENCES transactions(id) ON DELETE CASCADE,
    product_id      BIGINT NOT NULL REFERENCES products(id),
    product_name    VARCHAR(255) NOT NULL,   -- snapshot
    product_price   NUMERIC(12,2) NOT NULL,  -- snapshot harga saat beli
    is_consignment  BOOLEAN NOT NULL DEFAULT FALSE,
    quantity        INTEGER NOT NULL,
    addon_price     NUMERIC(10,2) NOT NULL DEFAULT 0,
    addons          JSONB NULL,              -- [{name, price}]
    notes           TEXT NULL,
    subtotal        NUMERIC(12,2) NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);

CREATE INDEX idx_tx_items_transaction ON transaction_items(transaction_id);
CREATE INDEX idx_tx_items_product     ON transaction_items(product_id);

-- ============================================================
-- 11. EXPENSES
-- ============================================================
CREATE TABLE expenses (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id),
    title           VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    category        VARCHAR(30) NOT NULL CHECK (category IN ('cogs', 'operational', 'payroll', 'consignment', 'other')),
    amount          NUMERIC(12,2) NOT NULL,
    payment_method  VARCHAR(20) NOT NULL DEFAULT 'tunai' CHECK (payment_method IN ('tunai', 'transfer', 'other')),
    receipt_number  VARCHAR(100) NULL,
    expense_date    DATE NOT NULL,
    supplier_id     BIGINT NULL REFERENCES consignment_suppliers(id),
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL   -- soft delete
);

CREATE INDEX idx_expenses_date_category ON expenses(expense_date, category) WHERE deleted_at IS NULL;

-- ============================================================
-- 12. STOCK MOVEMENTS (AUDIT TRAIL)
-- ============================================================
CREATE TABLE stock_movements (
    id               BIGSERIAL PRIMARY KEY,
    product_id       BIGINT NULL REFERENCES products(id),
    ingredient_id    BIGINT NULL REFERENCES ingredients(id),
    type             VARCHAR(15) NOT NULL CHECK (type IN ('in', 'out', 'adjustment')),
    quantity         NUMERIC(12,3) NOT NULL,
    quantity_before  NUMERIC(12,3) NOT NULL,
    quantity_after   NUMERIC(12,3) NOT NULL,
    reference_type   VARCHAR(100) NULL,     -- 'App\Models\Transaction'
    reference_id     BIGINT NULL,
    notes            TEXT NULL,
    user_id          BIGINT NOT NULL REFERENCES users(id),
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL
);

CREATE INDEX idx_stock_mov_product  ON stock_movements(product_id, created_at);
CREATE INDEX idx_stock_mov_ref      ON stock_movements(reference_type, reference_id);

-- ============================================================
-- 13. SESSIONS (Untuk SESSION_DRIVER=database)
-- ============================================================
CREATE TABLE sessions (
    id             VARCHAR(255) PRIMARY KEY,
    user_id        BIGINT NULL,
    ip_address     VARCHAR(45) NULL,
    user_agent     TEXT NULL,
    payload        TEXT NOT NULL,
    last_activity  INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user         ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- ============================================================
-- DATA AWAL (Seed)
-- ============================================================

-- Users
INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
VALUES
    ('Budi Santosa (Owner)', 'owner@warungkopi.com',  '$2y$12$...hashed...', 'owner',  TRUE, NOW(), NOW()),
    ('Siti Rahayu (Kasir)',  'kasir@warungkopi.com',  '$2y$12$...hashed...', 'kasir',  TRUE, NOW(), NOW());
-- NOTE: Generate hash dengan: php artisan tinker >>> bcrypt('password')

-- Kategori Produk
INSERT INTO product_categories (name, icon, sort_order, is_active, created_at, updated_at)
VALUES
    ('Semua',   '🏠', 0, TRUE, NOW(), NOW()),
    ('Kopi',    '☕', 1, TRUE, NOW(), NOW()),
    ('Minuman', '🥤', 2, TRUE, NOW(), NOW()),
    ('Makanan', '🍱', 3, TRUE, NOW(), NOW()),
    ('Titipan', '🛍️', 4, TRUE, NOW(), NOW());

-- Produk Contoh
INSERT INTO products (category_id, name, price, cost_price, stock, stock_alert, is_active, created_at, updated_at)
VALUES
    (2, 'Kopi Hitam',    8000,  3000, 100, 10, TRUE, NOW(), NOW()),
    (2, 'Kopi Susu',    15000,  6000, 80,  10, TRUE, NOW(), NOW()),
    (2, 'Cappuccino',   22000,  9000, 60,   5, TRUE, NOW(), NOW()),
    (2, 'Americano',    18000,  7000, 70,   5, TRUE, NOW(), NOW()),
    (2, 'Latte',        25000, 10000, 50,   5, TRUE, NOW(), NOW()),
    (2, 'Es Kopi Susu', 20000,  8000, 60,  10, TRUE, NOW(), NOW()),
    (3, 'Teh Tarik',    12000,  4000, 80,  10, TRUE, NOW(), NOW()),
    (3, 'Matcha Latte', 25000, 10000, 40,   5, TRUE, NOW(), NOW()),
    (3, 'Air Mineral',   5000,  2000,200,  20, TRUE, NOW(), NOW()),
    (4, 'Roti Bakar',   15000,  7000, 30,   5, TRUE, NOW(), NOW()),
    (4, 'Pisang Goreng',10000,  4000, 25,   5, TRUE, NOW(), NOW());

-- ============================================================
-- VIEW BERGUNA UNTUK LAPORAN
-- ============================================================

-- View: Ringkasan Harian
CREATE VIEW v_daily_summary AS
SELECT
    DATE(t.created_at)                                   AS tanggal,
    COUNT(*)                                             AS jumlah_transaksi,
    SUM(t.total)                                         AS total_pendapatan,
    SUM(CASE WHEN t.payment_method = 'tunai'    THEN t.total ELSE 0 END) AS pendapatan_tunai,
    SUM(CASE WHEN t.payment_method = 'qris'     THEN t.total ELSE 0 END) AS pendapatan_qris,
    SUM(CASE WHEN t.payment_method = 'transfer' THEN t.total ELSE 0 END) AS pendapatan_transfer,
    SUM(t.consignment_amount)                            AS total_konsinyasi
FROM transactions t
WHERE t.status = 'completed'
GROUP BY DATE(t.created_at)
ORDER BY tanggal DESC;

-- View: Top Produk Terlaris
CREATE VIEW v_top_products AS
SELECT
    p.name                  AS produk,
    SUM(ti.quantity)        AS total_terjual,
    SUM(ti.subtotal)        AS total_pendapatan,
    AVG(ti.product_price)   AS harga_rata_rata
FROM transaction_items ti
JOIN products p ON p.id = ti.product_id
JOIN transactions t ON t.id = ti.transaction_id
WHERE t.status = 'completed'
GROUP BY p.id, p.name
ORDER BY total_terjual DESC;

-- View: Laba Rugi Bulanan
CREATE VIEW v_monthly_pl AS
SELECT
    TO_CHAR(t.created_at, 'YYYY-MM') AS bulan,
    SUM(t.total)                      AS total_pemasukan,
    (SELECT COALESCE(SUM(e.amount), 0)
     FROM expenses e
     WHERE TO_CHAR(e.expense_date::TIMESTAMP, 'YYYY-MM') = TO_CHAR(t.created_at, 'YYYY-MM')
       AND e.deleted_at IS NULL
    )                                 AS total_pengeluaran,
    SUM(t.total) - (
     SELECT COALESCE(SUM(e.amount), 0)
     FROM expenses e
     WHERE TO_CHAR(e.expense_date::TIMESTAMP, 'YYYY-MM') = TO_CHAR(t.created_at, 'YYYY-MM')
       AND e.deleted_at IS NULL
    )                                 AS laba_bersih
FROM transactions t
WHERE t.status = 'completed'
GROUP BY TO_CHAR(t.created_at, 'YYYY-MM')
ORDER BY bulan DESC;
