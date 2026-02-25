# Quotes API

API sederhana untuk mengelola dan mendapatkan kutipan (quotes) inspiratif berdasarkan kategori. Proyek ini dibangun menggunakan Laravel 12.

## Fitur Utama

- **Quotes Management**: CRUD kutipan (tambah, lihat, hapus).
- **Random Quote**: Mendapatkan satu kutipan secara acak.
- **Categorization**: Pengelompokan kutipan berdasarkan kategori.
- **Filter by Category**: Mencari kutipan berdasarkan ID kategori atau nama kategori.
- **Seeding from JSON**: Mengisi database secara otomatis dari file JSON yang ada di folder `database/data`.

## Teknologi

- **Framework**: [Laravel 12](https://laravel.com)
- **Language**: PHP ^8.2
- **Database**: SQLite (default) / MySQL

## Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di lokal:

1.  **Clone repository**:
    ```bash
    git clone <repository-url>
    cd quotes-api
    ```

2.  **Instal dependensi**:
    ```bash
    composer install
    npm install
    ```

3.  **Setup lingkungan (.env)**:
    Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database.
    ```bash
    cp .env.example .env
    ```

4.  **Generate app key**:
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi dan Seed Database**:
    Menjalankan migrasi tabel dan mengisi data awal dari file JSON.
    ```bash
    php artisan migrate --seed
    ```

6.  **Jalankan server**:
    ```bash
    php artisan serve
    ```

---

## API Endpoints

### Quotes

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/quotes` | Mengambil semua kutipan |
| `GET` | `/api/quotes/random` | Mengambil satu kutipan acak |
| `POST` | `/api/quotes` | Menambahkan kutipan baru |
| `DELETE` | `/api/quotes/{id}` | Menghapus kutipan berdasarkan ID |

### Categories

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/categories` | Mengambil semua kategori beserta kutipannya |
| `POST` | `/api/categories` | Menambahkan kategori baru |

### Filter & Search

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/quotes/category/{id}` | Mencari kutipan berdasarkan ID kategori |
| `GET` | `/api/quotes/category/name/{name}` | Mencari kutipan berdasarkan nama kategori |

---

## Contoh Request & Response

### Mendapatkan Kutipan Acak
**Request:**
`GET /api/quotes/random`

**Response:**
```json
{
  "id": 1,
  "quote": "The only way to do great work is to love what you do.",
  "author": "Steve Jobs",
  "category_id": 5,
  "created_at": "2026-02-26T00:00:00.000000Z",
  "updated_at": "2026-02-26T00:00:00.000000Z",
  "category": {
    "id": 5,
    "name": "Success",
    "created_at": "2026-02-26T00:00:00.000000Z",
    "updated_at": "2026-02-26T00:00:00.000000Z"
  }
}
```

### Menambahkan Kutipan Baru
**Request:**
`POST /api/quotes`
```json
{
  "quote": "Believe you can and you're halfway there.",
  "author": "Theodore Roosevelt",
  "category_id": 1
}
```

---

## Lisensi
Proyek ini bersifat open-source dan berada di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
