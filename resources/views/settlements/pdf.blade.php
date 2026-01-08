<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Települések listája</title>
    <style>
        /* Speciális beállítások a dompdf-hez */
        @page {
            margin: 120px 50px 80px 50px; /* Margó a fejlécnek és láblécnek */
        }
        header {
            position: fixed;
            top: -90px;
            left: 0px;
            right: 0px;
            height: 80px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        .page-number:after {
            content: counter(page);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th { background-color: #f2f2f2; }
        .logo { height: 50px; }
    </style>
</head>
<body>
    <header>
        {{-- A logó beágyazása base64 formátumban a legbiztosabb dompdf alatt --}}
        <h3>Települések és Irányítószámok</h3>
    </header>

    <footer>
        <span>Készült: {{ date('Y.m.d H:i') }} | Oldal: </span>
        <span class="page-number"></span>
    </footer>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Irányítószám</th>
                    <th>Település</th>
                    <th>Megye</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settlements as $s)
                <tr>
                    <td>{{ $s['zip_code'] }}</td>
                    <td>{{ $s['name'] }}</td>
                    <td>{{ $s['county']['name'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>