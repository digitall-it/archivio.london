<!DOCTYPE html>
<html>
<head>
    <style>
        @font-face {
            font-family: 'Dosis';
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Dosis', sans-serif;
            transform: rotate(90deg);
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        td.code {
            width: 32mm;
            height: 29mm;
            text-align: center;
        }

        td.label {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
            text-align: left;
        }
    </style>
</head>
<body>
<table rotate="90">
    <tr>
        <td class="code">
            <barcode code="{{ url('/qr/' . $mode . '/' . $id) }}" type="QR" size="1" error="M" disableborder="1"/>
        </td>
        <td class="label">
            <span>{{ $containerName }} &middot; {{ $mode === 'load' ? 'carica' : 'scarica' }}</span>
        </td>
    </tr>
</table>
</body>
</html>
