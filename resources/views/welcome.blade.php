<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome_title') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #fbbf24;
            --primary-glow: rgba(251, 191, 36, 0.4);
            --bg-base: #020617;
            --glass: rgba(255, 255, 255, 0.02);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        h1, h2, h3, .heading-font {
            font-family: 'Space Grotesk', sans-serif;
        }

        /* --- MESH GRADIENT BACKGROUND --- */
        .mesh-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -2;
            background-color: var(--bg-base);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%), 
                radial-gradient(at 0% 50%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 50% 50%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 100% 50%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 0% 100%, hsla(339,49%,30%,1) 0, transparent 50%), 
                radial-gradient(at 50% 100%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(253,16%,7%,1) 0, transparent 50%);
            opacity: 0.6;
            filter: blur(100px);
            animation: backgroundFlow 20s infinite alternate linear;
        }

        @keyframes backgroundFlow {
            0% { transform: scale(1); }
            100% { transform: scale(1.2) rotate(3deg); }
        }

        /* --- NOISE TEXTURE --- */
        .noise {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.15;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3%3Cfilter id='noiseFilter'%3%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3%3C/filter%3%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3%3C/svg%3");
        }

        /* --- GLASS COMPONENTS --- */
        .glass-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
        }

        header {
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: var(--primary);
        }

        .lang-switcher {
            display: flex;
            background: rgba(255,255,255,0.05);
            padding: 4px;
            border-radius: 99px;
            border: 1px solid var(--glass-border);
        }

        .lang-link {
            padding: 6px 16px;
            border-radius: 99px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .lang-link.active {
            background: var(--primary);
            color: #000;
        }

        /* --- MAIN SECTIONS --- */
        main {
            padding: 6rem 0;
            display: flex;
            flex-direction: column;
            gap: 10rem;
        }

        .hero-section {
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            animation: fadeIn 1.2s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .category-badge {
            display: inline-block;
            background: rgba(251, 191, 36, 0.1);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 99px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 1px solid rgba(251, 191, 36, 0.2);
            margin-bottom: 2rem;
        }

        h1 {
            font-size: clamp(3rem, 10vw, 6rem);
            font-weight: 700;
            line-height: 0.95;
            margin-bottom: 2rem;
            letter-spacing: -3px;
        }

        h1 span {
            display: block;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 3rem auto;
            font-weight: 300;
        }

        .main-cta {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 20px 48px;
            background: var(--primary);
            color: #000;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.125rem;
            border-radius: 100px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 40px var(--primary-glow);
        }

        .main-cta:hover {
            transform: scale(1.05);
            box-shadow: 0 0 60px var(--primary-glow);
            letter-spacing: 0.5px;
        }

        /* --- FEATURE CARDS --- */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 3rem;
            border-radius: 40px;
            backdrop-filter: blur(20px);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.05), transparent);
            pointer-events: none;
        }

        .card:hover {
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-10px);
        }

        .icon-box {
            width: 64px;
            height: 64px;
            background: rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            color: var(--primary);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .card p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* --- INSTRUCTIONS SECTION --- */
        .how-it-works {
            text-align: center;
        }

        .section-headline {
            font-size: 3rem;
            margin-bottom: 4rem;
            font-weight: 700;
        }

        .steps-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 3rem;
            justify-content: center;
        }

        .step-pill {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            padding: 2rem;
            background: rgba(255,255,255,0.02);
            border-radius: 30px;
            border: 1px solid var(--glass-border);
        }

        .step-num {
            width: 50px;
            height: 50px;
            background: var(--text-main);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 800;
            font-size: 1.25rem;
        }

        .step-pill h4 {
            font-size: 1.2rem;
        }

        .step-pill p {
            color: var(--text-muted);
            font-weight: 300;
        }

        /* --- ABOUT BOT --- */
        .about-bot {
            background: linear-gradient(rgba(255,255,255,0.02), transparent);
            padding: 8rem 4rem;
            border-radius: 60px;
            border: 1px solid var(--glass-border);
            text-align: center;
        }

        .about-bot h2 {
            font-size: 3.5rem;
            line-height: 1;
            margin-bottom: 2rem;
        }

        .about-bot p {
            max-width: 800px;
            margin: 0 auto 3rem auto;
            font-size: 1.25rem;
            color: var(--text-muted);
            font-weight: 300;
        }

        .bot-link-btn {
            display: inline-flex;
            align-items: center;
            padding: 18px 40px;
            border: 1px solid var(--glass-border);
            border-radius: 100px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            background: rgba(255,255,255,0.05);
        }

        .bot-link-btn:hover {
            border-color: var(--primary);
            background: var(--primary);
            color: #000;
        }

        /* --- FLOATING ORBS --- */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -2;
            opacity: 0.4;
            pointer-events: none;
            animation: orbFloat 20s infinite alternate cubic-bezier(0.45, 0.05, 0.55, 0.95);
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(251, 191, 36, 0.15);
            top: -100px;
            left: -100px;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: rgba(59, 130, 246, 0.1);
            bottom: -150px;
            right: -100px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: rgba(236, 72, 153, 0.08);
            top: 40%;
            left: 60%;
            animation-delay: -12s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        /* --- PARTICLES --- */
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: #fff;
            border-radius: 50%;
            opacity: 0;
            filter: blur(2px);
            animation: particleFade 4s infinite alternate ease-in-out, 
                       particleTeeter 8s infinite ease-in-out;
        }

        @keyframes particleFade {
            0% { opacity: 0.05; }
            50% { opacity: 0.3; }
            100% { opacity: 0.1; }
        }

        @keyframes particleTeeter {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(15px, 10px); }
            50% { transform: translate(-10px, 15px); }
            75% { transform: translate(10px, -10px); }
        }

        /* --- REVEAL CLASSES --- */
        .reveal-init {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .reveal-active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- SPOTLIGHT EFFECT --- */
        .card.spotlight {
            position: relative;
        }

        .card.spotlight::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(
                600px circle at var(--mouse-x) var(--mouse-y),
                rgba(255, 255, 255, 0.06),
                transparent 40%
            );
            border-radius: 40px;
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
            z-index: 1;
        }

        .card.spotlight:hover::after {
            opacity: 1;
        }

        /* --- BUTTON ENHANCEMENTS --- */
        .main-cta, .bot-link-btn {
            transition: transform 0.15s ease-out, box-shadow 0.3s ease;
        }

        .main-cta span, .bot-link-btn span {
            display: inline-block;
            transition: transform 0.1s ease-out;
        }

        /* --- IMAGE INTEGRATION --- */
        .hero-visual {
            position: relative;
            margin-top: 4rem;
            width: 100%;
            max-width: 900px;
            aspect-ratio: 16/9;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5), 
                        0 0 40px rgba(251, 191, 36, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: perspective(1000px) rotateX(5deg);
            transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .hero-visual:hover {
            transform: perspective(1000px) rotateX(0deg) scale(1.02);
        }

        .hero-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.9);
        }

        .hero-visual::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10,11,14,0.6), transparent);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-image {
            width: 100%;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .about-image img {
            width: 100%;
            display: block;
            filter: grayscale(0.2) contrast(1.1);
            transition: filter 0.5s;
        }

        .about-image:hover img {
            filter: grayscale(0) contrast(1);
        }

        /* --- MOBILE RESPONSIVE --- */
        @media (max-width: 968px) {
            .about-grid { grid-template-columns: 1fr; text-align: center; }
        }
            main { gap: 6rem; padding: 4rem 0; }
            h1 { font-size: 4rem; letter-spacing: -2px; }
            .section-headline { font-size: 2.25rem; }
            .about-bot { padding: 4rem 1.5rem; border-radius: 30px; }
            .about-bot h2 { font-size: 2.5rem; }
            .card { padding: 2rem; }
            header { height: 80px; }
            .logo { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>
    <div class="noise"></div>
    <div id="particles-container" class="particles-container"></div>

    <!-- Floating Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="glass-container">
        <header>
            <a href="/" class="logo">SAHOVAT<span>.</span></a>
            <div class="lang-switcher">
                <a href="/?lang=uz" class="lang-link {{ app()->getLocale() == 'uz' ? 'active' : '' }}">O'ZBEK</a>
                <a href="/?lang=ru" class="lang-link {{ app()->getLocale() == 'ru' ? 'active' : '' }}">РУССКИЙ</a>
                <a href="/?lang=en" class="lang-link {{ app()->getLocale() == 'en' ? 'active' : '' }}">ENGLISH</a>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="hero-section">
                <div class="category-badge">{{ __('features') }}</div>
                <h1><span>{{ __('welcome_title') }}</span></h1>
                <p class="hero-subtitle">{{ __('welcome_subtitle') }}</p>
                <a href="https://t.me/Saxovat24_Bot" target="_blank" class="main-cta ripple-btn">
                    <span>{{ __('get_started') }}</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </a>
                
                <div class="hero-visual reveal-init" style="transition-delay: 0.4s">
                    <img src="{{ asset('images/hero.png') }}" alt="Sahovat Hero">
                </div>
            </section>

            <!-- Grid Features -->
            <div class="features-grid">
                <div class="card spotlight">
                    <div class="icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"></path></svg>
                    </div>
                    <h3>{{ __('feature_1_title') }}</h3>
                    <p>{{ __('feature_1_desc') }}</p>
                </div>
                <div class="card spotlight">
                    <div class="icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    </div>
                    <h3>{{ __('feature_2_title') }}</h3>
                    <p>{{ __('feature_2_desc') }}</p>
                </div>
                <div class="card spotlight">
                    <div class="icon-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h3>{{ __('feature_3_title') }}</h3>
                    <p>{{ __('feature_3_desc') }}</p>
                </div>
            </div>

            <!-- How it works -->
            <section class="how-it-works">
                <h2 class="section-headline">{{ __('how_it_works_title') }}</h2>
                <div class="steps-flex">
                    <div class="step-pill">
                        <div class="step-num">01</div>
                        <h4>{{ __('step_1_title') }}</h4>
                        <p>{{ __('step_1_desc') }}</p>
                    </div>
                    <div class="step-pill">
                        <div class="step-num">02</div>
                        <h4>{{ __('step_2_title') }}</h4>
                        <p>{{ __('step_2_desc') }}</p>
                    </div>
                    <div class="step-pill">
                        <div class="step-num">03</div>
                        <h4>{{ __('step_3_title') }}</h4>
                        <p>{{ __('step_3_desc') }}</p>
                    </div>
                </div>
            </section>

            <!-- About -->
            <section class="about-bot">
                <div class="about-grid">
                    <div class="about-image reveal-init">
                        <img src="{{ asset('images/about.png') }}" alt="Community Support">
                    </div>
                    <div class="about-content">
                        <h2>{{ __('about_bot_title') }}</h2>
                        <p>{{ __('about_bot_text') }}</p>
                        <a href="https://t.me/Saxovat24_Bot" target="_blank" class="bot-link-btn ripple-btn">
                            <span>@Saxovat24_Bot</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Advanced Interaction Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Reveal Animations on Scroll
            const observerOptions = {
                threshold: 0.15,
                rootMargin: '0px 0px -50px 0px'
            };

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('reveal-active');
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.card, .step-pill, .about-bot, h1, .hero-subtitle, .main-cta, .hero-visual, .about-image');
            revealElements.forEach((el, i) => {
                if (!el.classList.contains('reveal-init')) {
                    el.classList.add('reveal-init');
                }
                if (el.tagName === 'H1' || el.classList.contains('hero-subtitle')) {
                    el.style.transitionDelay = `${i * 0.1}s`;
                }
                revealObserver.observe(el);
            });

            // 2. Spotlight Effect for Cards
            const spotCards = document.querySelectorAll('.spotlight');
            spotCards.forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--mouse-x', `${x}px`);
                    card.style.setProperty('--mouse-y', `${y}px`);
                });
            });

            // 3. Magnetic Button Interaction
            const magneticBtns = document.querySelectorAll('.main-cta, .bot-link-btn');
            magneticBtns.forEach(btn => {
                btn.addEventListener('mousemove', e => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    
                    btn.style.transform = `translate(${x * 0.2}px, ${y * 0.3}px) scale(1.05)`;
                    if(btn.querySelector('span')) {
                        btn.querySelector('span').style.transform = `translate(${x * 0.1}px, ${y * 0.1}px)`;
                    }
                });

                btn.addEventListener('mouseleave', () => {
                    btn.style.transform = '';
                    if(btn.querySelector('span')) {
                        btn.querySelector('span').style.transform = '';
                    }
                });
            });

            // 4. Particle Generation
            const createParticles = () => {
                const container = document.getElementById('particles-container');
                const count = 25;
                const colors = ['#fbbf24', '#3b82f6', '#ec4899', '#ffffff'];

                for (let i = 0; i < count; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    
                    const size = Math.random() * 4 + 2;
                    const x = Math.random() * 100;
                    const y = Math.random() * 100;
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    const delay = Math.random() * 5;
                    const duration = 6 + Math.random() * 10;

                    particle.style.width = `${size}px`;
                    particle.style.height = `${size}px`;
                    particle.style.left = `${x}%`;
                    particle.style.top = `${y}%`;
                    particle.style.backgroundColor = color;
                    particle.style.animationDelay = `${delay}s`;
                    particle.style.animationDuration = `${duration}s`;

                    container.appendChild(particle);
                }
            };
            createParticles();
        });
    </script>
</body>
</html>
