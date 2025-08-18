<h1>📌 Human Resource Management System</h1>

<p>Sistem HRIS ini membantu mengelola data karyawan, absensi, cuti, dan payroll.</p>

<h2>🚀 Instalasi</h2>
<ol>
    <li>Clone repository: <code>git clone https://github.com/rachmatsumo/attendance-system.git hris</code></li>
    <li>Buka directory: <code>cd hris</code></li>
    <li>Salin file <code>.env.example</code> menjadi <code>.env</code></li>
    <li>Set konfigurasi environment di file <code>.env</code></li>
    <li>Generate app key: <code>php artisan key:generate</code></li>
    <li>Jalankan: <code>composer install</code></li>
    <li>Jalankan: <code>npm install</code></li>
    <li>Jalankan: <code>npm run build</code></li>
    <li>Jalankan migrasi database: <code>php artisan migrate</code></li>
    <li>Isi data awal: <code>php artisan db:seed</code></li>
    <li>Jalankan server: <code>php artisan serve</code></li>
</ol>

<h2>🔑 Informasi Login</h2>
<ul>
    <li><strong>Admin</strong><br>
        Email: <code>admin@hris.com</code><br>
        Password: <code>password123</code>
    </li>
    <li><strong>User</strong><br>
        Email: <code>david.brown@hris.com</code><br>
        Password: <code>password123</code>
    </li>
</ul>

<h2>📄 Lisensi</h2>
<p>Proyek ini dilisensikan di bawah <a href="LICENSE">MIT License</a>.</p>

![Admin Menu](screenshots/pic1.png)
![Schedule Editor](screenshots/pic2.png)
![Absensi](screenshots/pic3.png)
![Konfirmasi Absen](screenshots/pic4.png)
![Area Kerja](screenshots/pic5.png)
![Cuti dan Izin](screenshots/pic6.png)
![Pengaturan App](screenshots/pic7.png)
![Manage Cuti/Izin](screenshots/pic8.png)