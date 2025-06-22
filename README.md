# 🌸 Flower Shop - Hướng Dẫn Cài Đặt Backend

## 🚀 Bắt Đầu

### 1. **Clone Repository**

```bash
git clone <repository-link>
cd <project-folder>
```

### 2. **Cài Đặt Thư Viện**

```bash
composer install
```

### 3. **Cấu Hình Môi Trường**

-   Đổi tên file `.env.example` thành `.env`:
    ```bash
    cp .env.example .env
    ```
-   Tạo khóa ứng dụng:
    ```bash
    php artisan key:generate
    php artisan jwt:secret
    ```

### 4. **Cấu Hình Database**

-   Mở file `.env` và chỉnh sửa:
    ```
    DB_DATABASE=ten_database_cua_ban
    ```
-   **Cấu hình email để gửi mail:**
    ```
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME="gmail của bạn"
    MAIL_PASSWORD="mật khẩu"
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="gmail của bạn"
    MAIL_FROM_NAME="Flower Shop"
    ```

### 5. **Xóa Cache & Chạy Migration**

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan migrate
```

### 6. **Chạy Dự Án**

```bash
php artisan serve
```

-   Truy cập: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📚 **Cập Nhật Swagger**

```bash
php artisan l5-swagger:generate
```

---

## 🐳 **Sử Dụng Docker**

1. **Khởi động container:**

    ```bash
    docker-compose up -d
    ```

    - Copy file CSDL trong `database/database` vào MySQL, đặt tên database là `flower_shop`.

2. **Truy cập vào container:**

    ```bash
    docker exec -it laravel-app bash
    ```

3. **Chạy server Laravel:**
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

---

> **Chúc bạn cài đặt thành công! 🌷**
