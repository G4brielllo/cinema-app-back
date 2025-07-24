<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Potwierdzenie nowego adresu e-mail</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f6f9fc;
      padding: 30px;
      color: #333;
    }
    .container {
      max-width: 600px;
      background-color: #ffffff;
      margin: auto;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    h2 {
      color: #2c3e50;
    }
    p {
      font-size: 16px;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 24px;
      background-color: #1976d2;
      color: #ffffff;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }
    .footer {
      margin-top: 40px;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Cześć {{ $name }},</h2>

    <p>Otrzymaliśmy prośbę o zmianę Twojego adresu e-mail. Aby potwierdzić nowy adres, kliknij poniższy przycisk:</p>

    <a href="{{ $url }}" class="btn">Potwierdź adres e-mail</a>

    <p style="margin-top: 30px;">
      Jeśli nie prosiłeś o zmianę adresu, po prostu zignoruj tę wiadomość – nic nie zostanie zmienione.
    </p>

    <p class="footer">Link do potwierdzenia wygaśnie po 15 minutach.</p>
  </div>
</body>
</html>
