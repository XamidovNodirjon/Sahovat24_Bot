<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome_title') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-dark: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Hero Background */
        .hero-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.9)), url('/bg/hero1.png');
            background-size: cover;
            background-position: center;
            filter: blur(5px);
            transform: scale(1.1);
            transition: background 1s ease-in-out;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        header {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(to right, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .lang-switcher {
            display: flex;
            gap: 1rem;
        }

        .lang-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            transition: color 0.3s;
            text-transform: uppercase;
        }

        .lang-link:hover, .lang-link.active {
            color: var(--primary);
        }

        /* Main Content */
        main {
            min-height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 4rem 0;
            gap: 6rem;
        }

        .hero-section {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out;
            padding: 4rem 0;
        }

        .category-tag {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: clamp(1rem, 4vw, 1.25rem);
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            font-weight: 300;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            padding: 1rem 2.5rem;
            background-color: var(--primary);
            color: #000;
            text-decoration: none;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
        }

        .cta-button:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.4);
        }

        .cta-button.secondary {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            color: var(--primary);
            box-shadow: none;
        }

        .cta-button.secondary:hover {
            background: rgba(255,255,255,0.05);
            border-color: var(--primary);
        }

        /* How It Works Section */
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .step-item {
            display: flex;
            gap: 1.5rem;
            background: var(--glass);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            transition: transform 0.3s ease;
        }

        .step-item:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .step-content h3 {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        /* About Bot Section */
        .about-bot {
            padding: 4rem 0;
            margin-bottom: 4rem;
        }

        .about-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
            border: 1px solid var(--glass-border);
            padding: 4rem;
            border-radius: 32px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            backdrop-filter: blur(20px);
        }

        .about-card h2 {
            font-size: 2.25rem;
            margin-bottom: 1.5rem;
        }

        .about-card p {
            font-size: 1.125rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 5rem;
        }

        .feature-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            border-radius: 24px;
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out both;
        }

        .feature-card:hover {
            border-color: var(--primary);
            background: rgba(255,255,255,0.05);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(245, 158, 11, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 0.9375rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.4s; }

        /* Mobile Adjustments */
        @media (max-width: 640px) {
            header { height: 80px; }
            main { padding: 2rem 0; }
            .features-grid { margin-top: 3rem; }
        }
    </style>
</head>
<body>
    <div class="hero-bg"></div>

    <div class="container">
        <header>
            <a href="/" class="logo">Sahovat</a>
            <div class="lang-switcher">
                <a href="/?lang=uz" class="lang-link {{ app()->getLocale() == 'uz' ? 'active' : '' }}">UZ</a>
                <a href="/?lang=ru" class="lang-link {{ app()->getLocale() == 'ru' ? 'active' : '' }}">RU</a>
                <a href="/?lang=en" class="lang-link {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
            </div>
        </header>

        <main>
            <div class="hero-section">
                <span class="category-tag">{{ __('features') }}</span>
                <h1>{{ __('welcome_title') }}</h1>
                <p class="subtitle">{{ __('welcome_subtitle') }}</p>
                <a href="https://t.me/Saxovat24_Bot" target="_blank" class="cta-button">
                    {{ __('get_started') }}
                    <svg style="margin-left: 10px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </a>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.8-2.8L13 15"/><path d="M14 11V9a2 2 0 1 0-4 0v2"/><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/></svg>
                    </div>
                    <h3>{{ __('feature_1_title') }}</h3>
                    <p>{{ __('feature_1_desc') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <h3>{{ __('feature_2_title') }}</h3>
                    <p>{{ __('feature_2_desc') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h3>{{ __('feature_3_title') }}</h3>
                    <p>{{ __('feature_3_desc') }}</p>
                </div>
            </div>

            <!-- How It Works Section -->
            <section class="how-it-works">
                <h2 class="section-title">{{ __('how_it_works_title') }}</h2>
                <div class="steps-container">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>{{ __('step_1_title') }}</h3>
                            <p>{{ __('step_1_desc') }}</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>{{ __('step_2_title') }}</h3>
                            <p>{{ __('step_2_desc') }}</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>{{ __('step_3_title') }}</h3>
                            <p>{{ __('step_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About Bot Section -->
            <section class="about-bot">
                <div class="about-card">
                    <h2>{{ __('about_bot_title') }}</h2>
                    <p>{{ __('about_bot_text') }}</p>
                    <a href="https://t.me/Saxovat24_Bot" target="_blank" class="cta-button secondary">
                        @Saxovat24_Bot
                    </a>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Smooth background image transition
        const bgs = ['/bg/hero1.png', '/bg/hero2.png'];
        let currentBg = 0;
        const heroBg = document.querySelector('.hero-bg');

        setInterval(() => {
            currentBg = (currentBg + 1) % bgs.length;
            heroBg.style.backgroundImage = `linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.9)), url('${bgs[currentBg]}')`;
        }, 10000);
    </script>
</body>
</html>
