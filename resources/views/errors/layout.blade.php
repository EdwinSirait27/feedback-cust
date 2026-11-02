<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Bali in Bali</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=yeseva-one:400|archivo:400,500|archivo-narrow:600&display=swap">
    <style>
        :root {
            --color-dusk: oklch(27% 0.05 40);
            --color-paper: oklch(94% 0.03 80);
            --color-dusk-ink: oklch(82% 0.04 80);
            --color-gold: oklch(74% 0.14 75);
            --color-gold-2: oklch(84% 0.11 80);
            --color-signal: oklch(54% 0.2 24);
            --color-signal-ink: oklch(97% 0.02 85);
            --font-display: "Yeseva One", ui-serif, Georgia, serif;
            --font-body: "Archivo", ui-sans-serif, system-ui, sans-serif;
            --font-outlier: "Archivo Narrow", "Archivo", ui-sans-serif, system-ui, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; }
        html, body { overflow-x: clip; }
        body { min-height: 100svh; display: flex; flex-direction: column; background: var(--color-dusk); color: var(--color-paper); font: 400 1rem/1.55 var(--font-body); }
        .frame { width: 100%; max-width: 72rem; margin-inline: auto; padding-inline: clamp(1rem, 5vw, 4rem); }
        .bar { padding-block: 1rem; border-bottom: 2px solid var(--color-gold); }
        .logo { display: block; width: auto; height: 3.5rem; }
        main { flex: 1; display: flex; flex-direction: column; justify-content: center; padding-block: 2.5rem 4rem; }
        .error-code { font: 400 clamp(5.5rem, 22vw, 11rem)/1 var(--font-display); color: var(--color-paper); }
        .title { margin-top: 1rem; font: 400 clamp(1.75rem, 5vw, 3rem)/1.05 var(--font-display); overflow-wrap: break-word; min-width: 0; }
        .subtitle { margin-top: 0.75rem; max-width: 38ch; color: var(--color-dusk-ink); }
        .detail { margin-top: 1rem; max-width: 38ch; color: var(--color-paper); font-weight: 500; font-variant-numeric: tabular-nums; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem 1.5rem; margin-top: 2.5rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 3.25rem; padding: 0.75rem 1.5rem; border-radius: 999px; background: var(--color-signal); color: var(--color-signal-ink); font: 600 1rem/1.2 var(--font-outlier); text-decoration: none; transition: background-color 220ms ease, transform 120ms ease; }
        .btn:hover { background: color-mix(in oklab, var(--color-signal) 86%, black); }
        .btn:active { transform: translateY(1px); }
        .plain { display: inline-flex; align-items: center; min-height: 2.75rem; border-bottom: 2px solid var(--color-gold-2); color: var(--color-paper); font: 600 1rem/1.2 var(--font-outlier); text-decoration: none; transition: color 120ms ease; }
        .plain:hover { color: var(--color-gold-2); }
        a:focus-visible { outline: 2px solid var(--color-gold-2); outline-offset: 3px; }
        .footer-note { padding-block: 1.25rem; font: 600 0.8125rem/1.4 var(--font-outlier); color: var(--color-dusk-ink); }
        @media (prefers-reduced-motion: reduce) { .btn, .plain { transition: none; } }
    </style>
</head>
<body>
    <header class="frame">
        <div class="bar">
            <img class="logo" src="/images/bali-in-bali-logo.png" width="480" height="431" alt="Bali in Bali">
        </div>
    </header>
    <main class="frame">
        <div class="error-code">@yield('code')</div>
        <h1 class="title">@yield('heading')</h1>
        <p class="subtitle">@yield('message')</p>
        @yield('detail')
        <div class="actions">
            <a href="{{ route('home') }}" class="btn btn-primary">Back To Home</a>
            <a href="javascript:location.reload()" class="plain btn-secondary">Try again</a>
        </div>
    </main>
    <footer class="frame">
        <p class="footer-note">© Bali in Bali</p>
    </footer>
    @yield('scripts')
</body>
</html>
