<h1>📌 Human Resource Management System</h1>

<p>Sistem HRIS ini membantu mengelola data karyawan, absensi, cuti, dan payroll.</p>

<h2>🚀 Instalasi</h2>
<ol>
    <li>Clone repository: <code>git clone https://github.com/rachmatsumo/hris-attendance.git hris</code></li>
    <li>Buka directory: <code>cd hris</code></li>
    <li>Jalankan: <code>composer install</code></li>
    <li>Salin file <code>.env.example</code> menjadi <code>.env</code></li>
    <li>Set konfigurasi environment di file <code>.env</code></li>
    <li>Generate app key: <code>php artisan key:generate</code></li>
    <li>Buat symbolic link: <code>php artisan storage:link</code></li>
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
        Email: Periksa daftar karyawan<br>
        Password: <code>password123</code>
    </li>
</ul>

<h2>💻 Environment (Running Well on My Machine)</h2>
<ul>
    <li><strong>OS:</strong> <code>Ubuntu 24.04 LTS</code></li>
    <li><strong>Web Server:</strong> <code>Apache2</code></li>
    <li><strong>PHP:</strong> <code>8.3-FPM</code></li>
    <li><strong>DB:</strong> <code>Mysql 8^</code></li>
</ul>

<h2>🔔 Push Notification</h2>
<p>Untuk menggunakan <strong>Push Notification</strong>, anda memerlukan akun <a href="https://firebase.google.com/" target="_blank">Firebase</a> dan konfigurasi di <em>Google API Console</em>. Berikut langkah singkatnya:</p>

<ol>
  <li>Buka <a href="https://console.firebase.google.com/" target="_blank">Firebase Console</a> dan buat <strong>Project baru</strong>.</li>
  <li>Tambahkan aplikasi (Web, Android, atau iOS) ke project tersebut.</li>
  <li>Masuk ke menu <strong>Project Settings &gt; General &gt; Your apps</strong>, lalu salin konfigurasi Firebase (apiKey, authDomain, projectId, dsb).</li>
  <li>Untuk notifikasi, buka menu <strong>Cloud Messaging</strong> dan catat <code>Server Key</code> serta <code>Sender ID</code>.</li>
  <li>Buka <a href="https://console.cloud.google.com/apis/dashboard" target="_blank">Google Cloud Console</a>, pilih project Firebase tadi.</li>
  <li>Aktifkan API yang dibutuhkan (misalnya: <strong>Firebase Cloud Messaging API</strong>).</li>
  <li>Di menu <strong>Credentials</strong>, buat <em>Service Account Key</em> dalam format JSON, simpan di server anda.</li>
</ol>

<p>File JSON service account ini dibutuhkan agar server dapat mengirim <strong>push notification</strong> ke client melalui Firebase Cloud Messaging (FCM). Tempatkan pada directory <code>storage/app/privete/secret-kamu.json</code> dan atur .env GOOGLE_SERVICE_ACCOUNT_FILE</p>

<h2>✨ Fitur</h2> 

<ol>
  <li>📍 Absensi berbasis lokasi & foto (opsional dapat diaktifkan/dinonaktifkan)</li>
  <li>🗓️ Cuti & izin karyawan</li> 
  <li>📅 Schedule editor dengan tampilan kalender</li>
  <li>💰 Penggajian dengan komponen income & deduction fleksibel</li>
  <li>⏰ Lembur weekend & libur nasional yang bisa disesuaikan</li> 
  <li>⚙️ Pengaturan aplikasi yang fleksibel</li> 
  <li>🔔 Realtime notifikasi saat user absen</li> 
  <li>📑 Download slip gaji, rekap kehadiran, & jadwal kerja</li> 
</ol>
 

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
