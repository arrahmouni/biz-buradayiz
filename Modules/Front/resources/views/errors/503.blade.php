<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('maintenance.title')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #1F2937 0%, #111827 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .maintenance-card {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            text-align: center;
            padding: 2rem 2rem 2.5rem;
            transition: transform 0.2s ease;
        }
        .maintenance-card:hover { transform: translateY(-4px); }
        .icon-wrapper {
            background: #FEE2E2;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrapper i { font-size: 3rem; color: #DC2626; }
        h1 { font-size: 1.8rem; font-weight: 800; color: #1F2937; margin-bottom: 0.75rem; }
        .status-code {
            font-size: 0.875rem;
            font-weight: 600;
            color: #DC2626;
            background: #FEE2E2;
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }
        p { color: #4B5563; line-height: 1.6; margin: 1rem 0; }
        .gears {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0;
            font-size: 2rem;
            color: #9CA3AF;
        }
        .gears i { animation: spin 4s linear infinite; }
        .gears i:first-child { animation-duration: 3s; animation-direction: reverse; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @media (max-width: 640px) {
            .maintenance-card { padding: 1.5rem; }
            h1 { font-size: 1.5rem; }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="maintenance-card">
        <div class="icon-wrapper">
            <i class="fas fa-tools"></i>
        </div>
        <div class="status-code">503</div>
        <h1>@lang('maintenance.heading')</h1>
        <p>@lang('maintenance.message')</p>
        <div class="gears">
            <i class="fas fa-cog"></i>
            <i class="fas fa-cog"></i>
            <i class="fas fa-cog"></i>
        </div>
    </div>
</body>
</html>
