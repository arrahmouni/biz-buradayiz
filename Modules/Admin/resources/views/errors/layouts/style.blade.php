@if (isStaging())
    <link rel="stylesheet" href="{{ asset('css/staging-environment-banner.css') }}?v={{ config('admin.style_version') }}">
@endif
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        text-align: center;
    }

    .illustration {
        max-width: 50%;
        height: auto;
    }

    h1 {
        color: #343a40;
    }

    p {
        color: #6c757d;
    }

    a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }
</style>
