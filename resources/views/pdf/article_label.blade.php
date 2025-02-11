<!DOCTYPE html>
<html>
<head>
    <style>
        @font-face {
            font-family: 'Dosis';
        }

        body {
            margin: 0mm;
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
            font-size: 32px;
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
            <barcode code="{{ url('/qr/article/'  . $id) }}" type="QR" size="0.8" error="M" disableborder="1"/>
        </td>
        <td class="label">
            <span>{{ $articleName }}</span>
        </td>
    </tr>
</table>
</body>
</html>
