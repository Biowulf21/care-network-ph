<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- Apply saved appearance as early as possible to avoid flashes and ensure toggling works reliably -->
<script>
	(function(){
		try {
			var appearance = window.localStorage.getItem('flux.appearance') || 'system';
			var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

			if (appearance === 'dark' || (appearance === 'system' && prefersDark)) {
				document.documentElement.classList.add('dark');
			} else {
				document.documentElement.classList.remove('dark');
			}
		} catch (e) {
			// ignore
		}
	})();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
