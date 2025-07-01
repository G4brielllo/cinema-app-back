<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Potwierdzenie rezerwacji</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #222;
            padding: 40px 20px;
            line-height: 1.6;
            font-size: 18px;
        }
        .container {
            max-width: 650px;
            background: #ffffff;
            margin: 0 auto;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        h1 {
            font-size: 2.4rem;
            font-weight: 700;
            color: #0d47a1;
            margin-bottom: 25px;
            text-align: center;
        }
        .info {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #555;
        }
        ul {
            list-style: none;
            padding: 0;
            margin-bottom: 40px;
        }
        ul li {
            margin-bottom: 18px;
            font-size: 1.1rem;
        }
        ul li strong {
            color: #0d47a1;
            font-weight: 600;
            width: 140px;
            display: inline-block;
        }
        .code {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1976d2;
            background: #e3f2fd;
            padding: 6px 14px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-style: italic;
        }
        @media (max-width: 480px) {
            body {
                font-size: 16px;
                padding: 20px 10px;
            }
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 1.8rem;
            }
            ul li strong {
                width: 120px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Dziękujemy za rezerwację!</h1>

    <p class="info">Poniżej znajdują się szczegóły Twojej rezerwacji:</p>

    <ul>
        <li><strong>Tytuł filmu:</strong> {{ $reservation->screening->movie->title }}</li>
        <li><strong>Data seansu:</strong> {{ \Carbon\Carbon::parse($reservation->screening->screening_date)->format('d.m.Y') }}</li>
        <li><strong>Godzina:</strong> {{ \Carbon\Carbon::parse($reservation->screening->screening_time)->format('H:i') }}</li>
        <li><strong>Miejsca:</strong> 
            {{ $reservation->seats->map(fn($seat) => 'Rząd '.$seat->row.' Miejsce '.$seat->number)->join(', ') }}
        </li>
        <li><strong>Kod rezerwacji:</strong> <span class="code">{{ $reservation->reservation_code }}</span></li>
    </ul>

    <p class="info">Prosimy o okazanie kodu rezerwacji przy wejściu na salę.</p>

    <div class="footer">
        © {{ date('Y') }} CineManager – Miłego Seansu!
    </div>
</div>
</body>
</html>
