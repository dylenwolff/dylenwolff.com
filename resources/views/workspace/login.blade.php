<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Dylen Wolff Business Hub</title>
    <script>document.documentElement.dataset.theme=localStorage.getItem('theme')||((matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
<main class="grid min-h-screen place-items-center p-5">
    <div class="w-full max-w-md">
        <div class="mb-6 flex items-center justify-center gap-3">
            <span class="grid size-11 place-items-center rounded-2xl bg-primary text-primary-content"><i data-lucide="orbit" class="size-6"></i></span>
            <div><strong class="block text-lg">Dylen Wolff</strong><span class="text-sm text-base-content/55">Business Hub</span></div>
        </div>
        <section class="card border border-base-300 bg-base-100 shadow-xl">
            <div class="card-body gap-5 p-7">
                <div><p class="text-xs font-bold uppercase tracking-[.16em] text-primary">Secure access</p><h1 class="mt-2 text-2xl font-black">Sign in with an email code</h1><p class="mt-2 text-sm text-base-content/60">{{ $codeSent ? 'Enter the six-digit code. It expires in 10 minutes.' : 'Enter an approved email address. No password is required.' }}</p></div>
                @if(session('status'))<div class="alert alert-success text-sm"><i data-lucide="mail-check" class="size-5"></i><span>{{ session('status') }}</span></div>@endif
                @if($errors->any())<div class="alert alert-error text-sm"><i data-lucide="circle-alert" class="size-5"></i><span>{{ $errors->first() }}</span></div>@endif
                @if($codeSent)
                    <form method="POST" action="{{ route('workspace.login.verify') }}" class="space-y-4">@csrf
                        <fieldset class="fieldset"><legend class="fieldset-legend">Six-digit login code</legend><input name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" class="input input-bordered w-full text-center font-mono text-xl tracking-[.4em]" autofocus required></fieldset>
                        <button class="btn btn-primary w-full">Verify code<i data-lucide="arrow-right" class="size-4"></i></button>
                    </form>
                    <form method="POST" action="{{ route('workspace.login.code') }}" class="text-center">@csrf<input type="hidden" name="email" value="{{ session('workspace_otp_email') }}"><button class="btn btn-link btn-sm">Send a new code</button></form>
                @else
                    <form method="POST" action="{{ route('workspace.login.code') }}" class="space-y-4">@csrf
                        <fieldset class="fieldset"><legend class="fieldset-legend">Email address</legend><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="input input-bordered w-full" placeholder="you@example.com" autofocus required></fieldset>
                        <button class="btn btn-primary w-full">Send login code<i data-lucide="send" class="size-4"></i></button>
                    </form>
                @endif
            </div>
        </section>
        <p class="mt-5 text-center text-xs text-base-content/45">Passwordless access · Codes expire after 10 minutes</p>
    </div>
</main>
</body>
</html>
