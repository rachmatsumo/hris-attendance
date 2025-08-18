<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kadaluwarsa</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .error-container {
            max-width: 400px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #dc3545;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            margin: 20px 0;
            color: #333;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">419</div>
        <div class="error-message">Oops! Halaman kadaluwasa.</div>
        <a href="{{ url()->previous() }}" class="btn-back">Kembali</a>
    </div>
</body>
</html>
