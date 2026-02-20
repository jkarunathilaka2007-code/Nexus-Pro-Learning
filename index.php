<?php
session_start();
// දැනටමත් Login වී ඇත්නම් Dashboard එකට යොමු කරයි
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Exam Pro | The Future of A/L Preparation</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary: #6366f1;
            --accent: #22d3ee;
            --dark-bg: #020617;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --success: #10b981;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark-bg);
            color: #f8fafc;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Ambient Background Shadows */
        .ambient-light {
            position: fixed;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            z-index: -1; filter: blur(80px);
        }

        /* Glass Navbar */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(2, 6, 23, 0.7);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 0;
            z-index: 1000;
        }

        .navbar-brand { font-weight: 800; letter-spacing: -1px; }

        /* Hero Styling */
        .hero-section { padding: 180px 0 80px; text-align: center; position: relative; }
        
        .hero-badge {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid var(--primary);
            color: var(--accent);
            padding: 8px 24px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center; gap: 10px;
            margin-bottom: 2rem;
        }

        .hero-title {
            font-size: clamp(3rem, 9vw, 6rem);
            font-weight: 800;
            line-height: 0.9;
            letter-spacing: -4px;
            margin-bottom: 1.5rem;
        }

        .text-gradient {
            background: linear-gradient(135deg, #fff 30%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Bento Grid */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto;
            gap: 20px; margin-top: 50px;
        }

        .bento-item {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 35px;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
        }

        .bento-item:hover {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-8px);
        }

        .bento-lg { grid-column: span 2; }
        .bento-md { grid-column: span 1; }

        /* Comparison Table */
        .comparison-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            overflow: hidden;
            margin-top: 100px;
        }

        .table-dark { background: transparent !important; margin-bottom: 0; }
        .table-dark th { background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 0; padding: 25px; }
        .table-dark td { padding: 25px; border-color: var(--glass-border); vertical-align: middle; }

        /* Marquee Testimonials */
        .marquee-container {
            display: flex; gap: 20px;
            width: max-content;
            animation: marquee 30s linear infinite;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .testimonial-card {
            width: 350px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 25px;
        }

        /* Countdown Grid */
        .countdown-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 15px; border-radius: 20px;
            min-width: 100px;
        }

        .countdown-val { font-size: 2.5rem; font-weight: 800; color: var(--accent); display: block; }

        /* Buttons */
        .btn-nexus {
            background: var(--primary);
            color: white; border: none;
            padding: 16px 45px; border-radius: 20px;
            font-weight: 700; transition: 0.3s;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
            text-decoration: none; display: inline-block;
        }

        .btn-nexus:hover {
            transform: scale(1.05); color: white;
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
        }

        /* Floating Stats Pulse */
        .pulse-stat {
            width: 12px; height: 12px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @media (max-width: 992px) {
            .bento-grid { grid-template-columns: 1fr 1fr; }
            .bento-lg, .bento-md { grid-column: span 2; }
        }
    </style>
</head>
<body>

    <div class="ambient-light" style="top: -100px; right: -100px;"></div>
    <div class="ambient-light" style="bottom: 100px; left: -100px;"></div>

    <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
        <div class="container">
            <a class="navbar-brand fs-3" href="#">
                <i class="fa fa-braille text-primary me-2"></i>NEXUS<span class="text-primary">PRO</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-4">
                <a href="login.php" class="text-white text-decoration-none fw-600 d-none d-sm-block">Sign In</a>
                <a href="login.php" class="btn btn-nexus py-2 px-4">Join Now</a>
            </div>
        </div>
    </nav>

    <section class="hero-section container">
        <div class="animate__animated animate__fadeIn">
            <div class="hero-badge">
                <span class="pulse-stat"></span> 1,240+ Students Tracking Live
            </div>
            <h1 class="hero-title">
                Revolutionize Your<br><span class="text-gradient">A/L Performance.</span>
            </h1>
            <p class="text-white-50 fs-5 mx-auto mb-5" style="max-width: 750px;">
                The ultimate AI-driven ecosystem for Sri Lankan A/L students. Track past papers, analyze question speed, and predict your island rank with surgical precision.
            </p>
            
            <div class="d-flex justify-content-center gap-3 mb-5">
                <a href="register.php" class="btn btn-nexus">Start Free Journey</a>
                <a href="#features" class="btn btn-outline-light py-3 px-5 rounded-4 border-opacity-25 fw-bold">Explore Features</a>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-5">
                <div class="countdown-item">
                    <span class="countdown-val" id="days">--</span>
                    <span class="small fw-bold opacity-50">DAYS</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-val" id="hours">--</span>
                    <span class="small fw-bold opacity-50">HOURS</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-val" id="mins">--</span>
                    <span class="small fw-bold opacity-50">MINS</span>
                </div>
            </div>
            <p class="mt-3 text-white-50 small fw-bold uppercase">UNTIL A/L 2026 EXAMINATION</p>
        </div>

        <div class="bento-grid text-start" id="features">
            <div class="bento-item bento-lg">
                <i class="fa fa-chart-line fs-2 text-primary mb-4"></i>
                <h3 class="fw-800">Advanced Heatmaps</h3>
                <p class="text-white-50">සෑම විෂයකටම අදාළව ඔයාගේ දුර්වල ඒකක ස්වයංක්‍රීයව හඳුනාගෙන heatmap එකක් ලෙස පෙන්වයි.</p>
                <div class="mt-4 p-3 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10">
                    <div class="d-flex gap-2">
                        <div style="width:20px; height:20px; background:var(--primary); opacity:0.3; border-radius:4px;"></div>
                        <div style="width:20px; height:20px; background:var(--primary); opacity:0.6; border-radius:4px;"></div>
                        <div style="width:20px; height:20px; background:var(--primary); opacity:1.0; border-radius:4px;"></div>
                    </div>
                </div>
            </div>

            <div class="bento-item bento-md">
                <i class="fa fa-stopwatch fs-2 text-accent mb-4"></i>
                <h4 class="fw-bold">Speed Master</h4>
                <p class="small text-white-50">Find average time per MCQ question.</p>
                <h2 class="fw-800 mt-3 text-accent">1.4m/q</h2>
            </div>

            <div class="bento-item bento-md">
                <i class="fa fa-layer-group fs-2 text-warning mb-4"></i>
                <h4 class="fw-bold">Paper Bank</h4>
                <p class="small text-white-50">1990 - 2025 Past Paper Archive.</p>
                <div class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mt-2">All Streams</div>
            </div>

            <div class="bento-item bento-md">
                <i class="fa fa-robot fs-2 text-danger mb-4"></i>
                <h4 class="fw-bold">AI Planner</h4>
                <p class="small text-white-50">Daily schedule generated by AI.</p>
            </div>

            <div class="bento-item bento-lg">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="fw-800">Island Rank Prediction</h3>
                        <p class="text-white-50 mb-0">සමස්ත ලංකා මට්ටමින් වෙනත් සිසුන්ගේ දත්ත සමඟ සසඳා ඔබේ දළ ශ්‍රේණිගත කිරීම පෙන්වයි.</p>
                    </div>
                    <div class="col-4 text-end">
                        <div class="p-4 bg-primary bg-opacity-10 rounded-circle d-inline-block">
                            <i class="fa fa-trophy text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bento-item bento-md text-center">
                <h2 class="fw-800 text-success mb-0">A+</h2>
                <p class="small text-white-50">Target Grade</p>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-800 fs-1">Nexus Pro vs <span class="text-white-50">Traditional Study</span></h2>
        </div>
        <div class="comparison-card">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th class="text-center opacity-50">Manual Logs</th>
                            <th class="text-center">Nexus Pro Engine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Data Security</td>
                            <td class="text-center opacity-50">Can be lost easily</td>
                            <td class="text-center fw-bold text-success"><i class="fa fa-cloud me-2"></i>Cloud Encrypted</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Performance Analytics</td>
                            <td class="text-center opacity-50">Non-existent</td>
                            <td class="text-center fw-bold text-primary"><i class="fa fa-brain me-2"></i>AI Insights</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Speed Tracking</td>
                            <td class="text-center opacity-50">Manual stopwatch</td>
                            <td class="text-center fw-bold text-accent"><i class="fa fa-bolt me-2"></i>Auto Calculation</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="py-100 overflow-hidden mt-5">
        <div class="text-center mb-5">
            <h2 class="fw-800">Loved by <span class="text-primary">Rankers</span></h2>
        </div>
        <div class="marquee-container">
            <div class="testimonial-card">
                <p class="text-white-50 small">"MCQ speed එක මැනගන්න මම පාවිච්චි කරපු හොඳම tool එක. Physics ලකුණු ගොඩක් වැඩි වුණා."</p>
                <div class="d-flex align-items-center gap-2 mt-4">
                    <div class="bg-primary rounded-circle" style="width:35px; height:35px;"></div>
                    <div><h6 class="mb-0 fw-bold">Saman Kumara</h6><small class="opacity-50">Colombo District #4</small></div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="text-white-50 small">"පේපර්ස් කරලා මාර්ක්ස් දාලා තියන්න දැන් පොත් ඕන නෑ. Nexus එකේ ඔක්කොම තියෙනවා."</p>
                <div class="d-flex align-items-center gap-2 mt-4">
                    <div class="bg-accent rounded-circle" style="width:35px; height:35px;"></div>
                    <div><h6 class="mb-0 fw-bold">Dilini Perera</h6><small class="opacity-50">Kandy District #12</small></div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="text-white-50 small">"AI planner එක නිසා මම කලින් පාඩම් නොකරපු unit පවා පිළිවෙළකට cover කරගත්තා."</p>
                <div class="d-flex align-items-center gap-2 mt-4">
                    <div class="bg-warning rounded-circle" style="width:35px; height:35px;"></div>
                    <div><h6 class="mb-0 fw-bold">Naveen Silva</h6><small class="opacity-50">Galle District #2</small></div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="text-white-50 small">"MCQ speed එක මැනගන්න මම පාවිච්චි කරපු හොඳම tool එක. Physics ලකුණු ගොඩක් වැඩි වුණා."</p>
                <div class="d-flex align-items-center gap-2 mt-4">
                    <div class="bg-primary rounded-circle" style="width:35px; height:35px;"></div>
                    <div><h6 class="mb-0 fw-bold">Saman Kumara</h6><small class="opacity-50">Colombo District #4</small></div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5 mt-5">
        <div class="p-5 rounded-5 text-center overflow-hidden position-relative" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
            <h2 class="fw-800 fs-1 mb-4">Ready to Change Your Life?</h2>
            <p class="mb-5 opacity-75 fs-5">Join the elite community of students who use technology to win.</p>
            <a href="register.php" class="btn btn-dark py-3 px-5 rounded-4 fw-bold shadow-lg">Get Started Now - It's Free</a>
        </div>
    </section>

    <footer class="py-5 border-top border-white border-opacity-5 mt-5">
        <div class="container text-center">
            <div class="fs-4 fw-800 mb-2">NEXUS<span class="text-primary">PRO</span></div>
            <p class="text-white-50 small mb-0">&copy; 2026 Nexus Exam Pro Engine. Build for champions.</p>
        </div>
    </footer>

    <a href="https://wa.me/yournumber" class="btn btn-nexus rounded-circle d-flex align-items-center justify-content-center" style="position:fixed; bottom:30px; right:30px; width:60px; height:60px; z-index:1000; padding:0;">
        <i class="fab fa-whatsapp fa-2x"></i>
    </a>

    <script>
        function updateCountdown() {
            const examDate = new Date("November 25, 2026 08:30:00").getTime();
            const now = new Date().getTime();
            const diff = examDate - now;

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

            document.getElementById("days").innerText = d;
            document.getElementById("hours").innerText = h;
            document.getElementById("mins").innerText = m;
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>