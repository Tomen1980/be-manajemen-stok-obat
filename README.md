### INSTAL ###

```
1. Buka terminal tuliskan 'composer install'

2. Copy paste .env.example lalu ganti menjadi .env

3. Connectkan DB nya 

    DB_CONNECTION=pgsql (bisa disesuaikan mysql dll)
    DB_HOST=127.0.0.1   (sesuaikan)
    DB_PORT=5432        (sesuaikan)
    DB_DATABASE=manajemen_obat (sesuaikan)
    DB_USERNAME=postgres    (userDB tergantung biasanya kalau xampp pakai 'root')
    DB_PASSWORD=password    (sesuikan)

4. Buka terminal tuliskan 'php artisan migrate' kalau error 'php artisan migrate:fresh'

5. Buka terminal tuliskan 

    'php artisan db:seed'

6. Buka terminal tuliskan 'php artisan storage:link'

7. (jalankan) Buka terminal tuliskan 'php artisan serve'

```

## User
```
----ADMIN----

admin@mail.com
password

----USER----

user@mail.com
password

-------------