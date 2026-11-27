<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2D4235">
    <title>@yield('code') — @yield('title') | BASOKU</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-color: #F4EFE6;
            color: #26221E;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
        }
        .card {
            width: 100%;
            max-width: 34rem;
            background: #FAF7F2;
            border: 1px solid rgba(38, 34, 30, 0.12);
            border-radius: 1.75rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 20px 45px -20px rgba(38, 34, 30, 0.18);
        }
        .monogram {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background: #2D4235;
            color: #F4EFE6;
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 700;
            font-size: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .code {
            display: inline-block;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #B84E26;
            background: rgba(184, 78, 38, 0.1);
            border: 1px solid rgba(184, 78, 38, 0.25);
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }
        h1 {
            margin: 0 0 0.75rem;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(1.65rem, 4vw, 2.25rem);
            line-height: 1.2;
            color: #26221E;
        }
        p {
            margin: 0 0 1.75rem;
            font-size: 0.92rem;
            color: #5A534A;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            background: #2D4235;
            color: #FAF7F2;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover { background: #1F3025; }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.35rem;
            border-radius: 9999px;
            background: #FFFFFF;
            color: #26221E;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(38, 34, 30, 0.18);
            cursor: pointer;
        }
        .btn-secondary:hover { background: #EAE3D5; }
        .foot {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px dashed rgba(38, 34, 30, 0.12);
            font-size: 0.75rem;
            color: #5A534A;
        }
    </style>
</head>
<body>
    <main class="card" role="main">
        <div class="monogram" aria-hidden="true">B.</div>
        <div>
            <span class="code">Kode @yield('code')</span>
        </div>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        <div class="actions">
            @yield('actions')
        </div>
        <div class="foot">
            BASOKU &bull; Spesialis Bahan Baku &amp; Selepan Bakso Pasar Tradisional
        </div>
    </main>
</body>
</html>
