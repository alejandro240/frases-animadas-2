<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $frase->texto }} - Animación {{ ucfirst($frase->animacion) }}</title>
    {{--
        Vista: Mostrar una animación (show)
        - Contiene estilos inline para las diferentes animaciones (matrix, quantum, nebula, hologram, particle)
        - Incluye un <canvas> que se usa para los efectos de fondo y un bloque de JS con las funciones de animación.
        Notas:
        - Los comentarios en los bloques CSS usan /* ... */ para no romper el CSS.
        - Los comentarios en JS usan //.
    --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .container {
            text-align: center;
            z-index: 10;
            position: relative;
        }

        .animation-container {
            min-height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
            position: relative;
            perspective: 1000px;
        }

        .letra {
            display: inline-block;
            font-size: 64px;
            font-weight: bold;
            margin: 0 4px;
            position: relative;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        /* Matrix Style */
        .matrix-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.3;
        }

        .matrix-letter {
            color: #00ff41;
            text-shadow: 0 0 10px #00ff41, 0 0 20px #00ff41, 0 0 30px #00ff41;
        }

        /* Quantum Style */
        .quantum-letter {
            color: #00ffff;
            filter: blur(0px);
        }

        .quantum-glitch {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            mix-blend-mode: screen;
        }

        /* Nebula Style */
        .nebula-letter {
            background: linear-gradient(45deg, #ff00ff, #00ffff, #ffff00, #ff00ff);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 20px rgba(255, 0, 255, 0.8));
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            pointer-events: none;
        }

        /* Hologram Style */
        .hologram-letter {
            color: #00d4ff;
            text-shadow: 0 0 5px #00d4ff, 0 0 10px #00d4ff;
            position: relative;
        }

        .hologram-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00d4ff, transparent);
            opacity: 0.7;
        }

        /* Particle Style */
        .particle-letter {
            color: #ffd700;
            filter: drop-shadow(0 0 10px #ffd700);
        }

        .controls {
            margin-top: 3rem;
            position: relative;
            z-index: 100;
        }

        .btn {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            font-size: 16px;
            font-weight: bold;
            margin: 0 0.5rem;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
        }

        .animation-title {
            font-size: 24px;
            margin-bottom: 2rem;
            opacity: 0.8;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Canvas para efectos de fondo */
        #bgCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
    </style>
    {{-- Librería GSAP usada para animaciones de texto/dom (timeline, tweening) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>
    <canvas id="bgCanvas"></canvas>
    
    <div class="container">
        {{-- Título de la animación (nombre del tipo) --}}
        <div class="animation-title">{{ $frase->animacion }} Animation</div>

        {{-- Contenedor donde se colocan las letras individuales. Cada letra es un <span> con clase según el tipo de animación. --}}
        <div class="animation-container" id="animationContainer">
            @php
                // Dividir el texto en caracteres individuales incluyendo caracteres multibyte
                // Se usa preg_split con PREG_SPLIT_NO_EMPTY para obtener arrays de caracteres Unicode
                $letras = preg_split('//u', $frase->texto, -1, PREG_SPLIT_NO_EMPTY);
            @endphp
            @foreach($letras as $index => $letra)
                <span class="letra {{ $frase->animacion }}-letter" data-index="{{ $index }}">{!! $letra === ' ' ? '&nbsp;' : $letra !!}</span>
            @endforeach
        </div>
        
        <div class="controls">
            <a href="{{ route('frases.index') }}" class="btn">📋 Mis Animaciones</a>
            <a href="{{ route('frases.create') }}" class="btn">✨ Nueva Animación</a>
        </div>
    </div>

    <script>
        // Variables globales para las animaciones
        // animationType: slug del tipo de animación (ej. 'matrix', 'quantum', ...)
        const animationType = '{{ $frase->animacion }}';
        // Seleccionar todos los elementos que representan letras individuales
        const letras = document.querySelectorAll('.letra');
        // Contenedor DOM donde están las letras
        const container = document.getElementById('animationContainer');
        // Canvas usado para efectos de fondo (lluvia, partículas, escaneo...)
        const canvas = document.getElementById('bgCanvas');
        const ctx = canvas.getContext('2d');

        // Ajustar tamaño inicial del canvas al viewport
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        // ==================== MATRIX ANIMATION ====================
        function matrixAnimation() {
            // Matrix background
            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
            const columns = Math.floor(canvas.width / 20);
            const drops = Array(columns).fill(1);

            function drawMatrix() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = '#0F0';
                ctx.font = '15px monospace';
                
                for (let i = 0; i < drops.length; i++) {
                    const text = chars[Math.floor(Math.random() * chars.length)];
                    ctx.fillText(text, i * 20, drops[i] * 20);
                    
                    if (drops[i] * 20 > canvas.height && Math.random() > 0.975) {
                        drops[i] = 0;
                    }
                    drops[i]++;
                }
            }
            
            setInterval(drawMatrix, 50);

            // Animación de letras
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 2 });
            
            letras.forEach((letra, index) => {
                gsap.set(letra, { opacity: 0, y: -100, rotationX: -90 });
            });

            tl.to(letras, {
                opacity: 1,
                y: 0,
                rotationX: 0,
                duration: 0.5,
                stagger: 0.1,
                ease: "power2.out"
            });

            tl.to(letras, {
                y: -20,
                duration: 0.3,
                stagger: { each: 0.05, repeat: 5, yoyo: true },
                ease: "power1.inOut"
            });

            tl.to(letras, {
                scale: 1.2,
                duration: 0.2,
                stagger: 0.05,
                ease: "back.out(2)"
            });

            tl.to(letras, {
                scale: 1,
                duration: 0.2,
                stagger: 0.05,
                ease: "power1.inOut"
            });

            tl.to(letras, {
                opacity: 0,
                y: 100,
                rotationX: 90,
                duration: 0.5,
                stagger: 0.05,
                ease: "power2.in"
            });
        }

        // ==================== QUANTUM GLITCH ANIMATION ====================
        function quantumAnimation() {
            // Fondo con ondas cuánticas
            let time = 0;
            function drawQuantum() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.strokeStyle = '#00ffff';
                ctx.lineWidth = 2;
                
                for (let i = 0; i < 5; i++) {
                    ctx.beginPath();
                    for (let x = 0; x < canvas.width; x += 5) {
                        const y = canvas.height / 2 + Math.sin((x + time * 2 + i * 100) / 50) * 50;
                        if (x === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.stroke();
                }
                time++;
                requestAnimationFrame(drawQuantum);
            }
            drawQuantum();

            // Animación glitch
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 1.5 });
            
            gsap.set(letras, { opacity: 0, x: -200, filter: 'blur(20px)' });

            tl.to(letras, {
                opacity: 1,
                x: 0,
                filter: 'blur(0px)',
                duration: 0.6,
                stagger: 0.08,
                ease: "power4.out"
            });

            // Efecto glitch
            for (let i = 0; i < 3; i++) {
                tl.to(letras, {
                    x: () => Math.random() * 20 - 10,
                    y: () => Math.random() * 20 - 10,
                    duration: 0.05,
                    stagger: 0.02,
                    ease: "none"
                });
                
                tl.to(letras, {
                    x: 0,
                    y: 0,
                    duration: 0.05,
                    stagger: 0.02,
                    ease: "none"
                });
            }

            tl.to(letras, {
                rotationY: 180,
                duration: 0.8,
                stagger: 0.05,
                ease: "power2.inOut"
            });

            tl.to(letras, {
                rotationY: 360,
                duration: 0.8,
                stagger: 0.05,
                ease: "power2.inOut"
            });

            tl.to(letras, {
                opacity: 0,
                filter: 'blur(20px)',
                scale: 0,
                duration: 0.6,
                stagger: 0.05,
                ease: "power4.in"
            });
        }

        // ==================== NEBULA COSMIC ANIMATION ====================
        function nebulaAnimation() {
            // Partículas de fondo
            const particles = [];
            for (let i = 0; i < 100; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 3,
                    speedX: (Math.random() - 0.5) * 2,
                    speedY: (Math.random() - 0.5) * 2,
                    color: `hsl(${Math.random() * 360}, 100%, 50%)`
                });
            }

            function drawNebula() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                particles.forEach(p => {
                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();
                    
                    p.x += p.speedX;
                    p.y += p.speedY;
                    
                    if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                    if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
                });
                
                requestAnimationFrame(drawNebula);
            }
            drawNebula();

            // Animación de letras
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 1 });
            
            gsap.set(letras, { opacity: 0, scale: 0, rotation: 0 });

            tl.to(letras, {
                opacity: 1,
                scale: 2,
                rotation: 720,
                duration: 1,
                stagger: 0.1,
                ease: "elastic.out(1, 0.5)"
            });

            tl.to(letras, {
                scale: 1,
                duration: 0.5,
                stagger: 0.05,
                ease: "power2.inOut"
            });

            // Efecto de onda
            tl.to(letras, {
                y: -30,
                duration: 0.4,
                stagger: { each: 0.05, repeat: 3, yoyo: true },
                ease: "sine.inOut"
            });

            // Explosión de partículas
            tl.call(() => {
                letras.forEach(letra => {
                    const rect = letra.getBoundingClientRect();
                    for (let i = 0; i < 20; i++) {
                        const particle = document.createElement('div');
                        particle.className = 'particle';
                        particle.style.background = `hsl(${Math.random() * 360}, 100%, 50%)`;
                        particle.style.left = rect.left + rect.width / 2 + 'px';
                        particle.style.top = rect.top + rect.height / 2 + 'px';
                        document.body.appendChild(particle);
                        
                        gsap.to(particle, {
                            x: (Math.random() - 0.5) * 300,
                            y: (Math.random() - 0.5) * 300,
                            opacity: 0,
                            duration: 1.5,
                            ease: "power2.out",
                            onComplete: () => particle.remove()
                        });
                    }
                });
            });

            tl.to(letras, {
                opacity: 0,
                scale: 0,
                rotation: -360,
                duration: 0.8,
                stagger: 0.05,
                ease: "back.in(2)"
            });
        }

        // ==================== HOLOGRAM SCAN ANIMATION ====================
        function hologramAnimation() {
            // Líneas de escaneo
            let scanY = 0;
            function drawHologram() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Grid holográfico
                ctx.strokeStyle = 'rgba(0, 212, 255, 0.1)';
                ctx.lineWidth = 1;
                
                for (let i = 0; i < canvas.width; i += 50) {
                    ctx.beginPath();
                    ctx.moveTo(i, 0);
                    ctx.lineTo(i, canvas.height);
                    ctx.stroke();
                }
                
                for (let i = 0; i < canvas.height; i += 50) {
                    ctx.beginPath();
                    ctx.moveTo(0, i);
                    ctx.lineTo(canvas.width, i);
                    ctx.stroke();
                }
                
                // Línea de escaneo
                ctx.strokeStyle = '#00d4ff';
                ctx.lineWidth = 3;
                ctx.shadowBlur = 20;
                ctx.shadowColor = '#00d4ff';
                ctx.beginPath();
                ctx.moveTo(0, scanY);
                ctx.lineTo(canvas.width, scanY);
                ctx.stroke();
                ctx.shadowBlur = 0;
                
                scanY = (scanY + 5) % canvas.height;
                requestAnimationFrame(drawHologram);
            }
            drawHologram();

            // Animación de letras
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 1.5 });
            
            gsap.set(letras, { opacity: 0, z: -500, rotationY: -90 });

            tl.to(letras, {
                opacity: 1,
                z: 0,
                rotationY: 0,
                duration: 0.8,
                stagger: 0.08,
                ease: "power3.out"
            });

            // Scan effect
            letras.forEach((letra, index) => {
                const line = document.createElement('div');
                line.className = 'hologram-line';
                letra.appendChild(line);
                
                tl.to(line, {
                    top: '100%',
                    duration: 0.5,
                    delay: index * 0.1,
                    ease: "none"
                }, 1);
            });

            tl.to(letras, {
                rotationX: 360,
                duration: 1.2,
                stagger: 0.06,
                ease: "power2.inOut"
            });

            tl.to(letras, {
                opacity: 0.3,
                duration: 0.2,
                stagger: { each: 0.05, repeat: 4, yoyo: true },
                ease: "none"
            });

            tl.to(letras, {
                opacity: 0,
                z: 500,
                rotationY: 90,
                duration: 0.8,
                stagger: 0.06,
                ease: "power3.in"
            });
        }

        // ==================== PARTICLE EXPLOSION ANIMATION ====================
        function particleAnimation() {
            // Fondo estrellado
            const stars = [];
            for (let i = 0; i < 200; i++) {
                stars.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 2,
                    speed: Math.random() * 3 + 1
                });
            }

            function drawParticles() {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = '#ffd700';
                stars.forEach(star => {
                    ctx.beginPath();
                    ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                    ctx.fill();
                    
                    star.y += star.speed;
                    if (star.y > canvas.height) {
                        star.y = 0;
                        star.x = Math.random() * canvas.width;
                    }
                });
                
                requestAnimationFrame(drawParticles);
            }
            drawParticles();

            // Animación de letras
            const tl = gsap.timeline({ repeat: -1, repeatDelay: 1 });
            
            gsap.set(letras, { opacity: 0, scale: 0 });

            // Formación inicial
            tl.to(letras, {
                opacity: 1,
                scale: 1,
                duration: 0.01,
                stagger: 0.15,
                ease: "none",
                onStart: function() {
                    const index = letras.length - 1 - Math.floor(this.progress() * letras.length);
                    if (letras[index]) {
                        const letra = letras[index];
                        const rect = letra.getBoundingClientRect();
                        
                        // Explotar partículas al aparecer
                        for (let i = 0; i < 30; i++) {
                            const particle = document.createElement('div');
                            particle.className = 'particle';
                            particle.style.background = '#ffd700';
                            particle.style.boxShadow = '0 0 10px #ffd700';
                            particle.style.left = rect.left + rect.width / 2 + 'px';
                            particle.style.top = rect.top + rect.height / 2 + 'px';
                            document.body.appendChild(particle);
                            
                            const angle = (Math.PI * 2 * i) / 30;
                            const distance = 150;
                            
                            gsap.to(particle, {
                                x: Math.cos(angle) * distance,
                                y: Math.sin(angle) * distance,
                                opacity: 0,
                                duration: 1,
                                ease: "power2.out",
                                onComplete: () => particle.remove()
                            });
                        }
                    }
                }
            });

            tl.to(letras, {
                y: -50,
                duration: 0.6,
                stagger: 0.05,
                ease: "power2.out"
            });

            tl.to(letras, {
                y: 0,
                duration: 0.6,
                stagger: 0.05,
                ease: "bounce.out"
            });

            tl.to(letras, {
                rotation: 360,
                scale: 1.5,
                duration: 1,
                stagger: 0.05,
                ease: "power2.inOut"
            });

            tl.to(letras, {
                scale: 1,
                duration: 0.3,
                stagger: 0.03,
                ease: "back.out(3)"
            });

            // Dispersión final
            tl.to(letras, {
                opacity: 0,
                scale: 0,
                rotation: 720,
                x: () => (Math.random() - 0.5) * 400,
                y: () => (Math.random() - 0.5) * 400,
                duration: 0.8,
                stagger: 0.05,
                ease: "power4.in"
            });
        }

        // Ejecutar animación según el tipo
        if (animationType === 'matrix') {
            matrixAnimation();
        } else if (animationType === 'quantum') {
            quantumAnimation();
        } else if (animationType === 'nebula') {
            nebulaAnimation();
        } else if (animationType === 'hologram') {
            hologramAnimation();
        } else if (animationType === 'particle') {
            particleAnimation();
        }

        // Ajustar canvas al redimensionar
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>