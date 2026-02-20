<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Guide | Master Prep</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        :root {
            --bg: #060910;
            --card-bg: rgba(17, 24, 39, 0.7);
            --accent: #6366f1;
            --neon: #22d3ee;
            --success: #10b981;
            --danger: #f43f5e;
        }

        body {
            background: var(--bg);
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .hero-section {
            padding: 80px 0 40px;
            text-align: center;
        }

        .guide-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, var(--neon));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }

        .step-pill {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid var(--accent);
            color: var(--neon);
            padding: 5px 20px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Modern Accordion */
        .accordion-item {
            background: var(--card-bg) !important;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 24px !important;
            margin-bottom: 15px;
            transition: 0.3s ease;
        }

        .accordion-item:hover {
            border-color: var(--accent) !important;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
        }

        .accordion-button {
            background: transparent !important;
            color: #fff !important;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 25px;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            color: var(--neon) !important;
        }

        .accordion-button::after {
            filter: invert(1);
        }

        .icon-box {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: var(--neon);
        }

        .accordion-body {
            padding: 0 25px 25px 85px;
            color: #94a3b8;
            line-height: 1.8;
        }

        .highlight {
            color: #fff;
            font-weight: 600;
        }

        /* Feature Cards inside FAQ */
        .feature-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            font-size: 0.85rem;
            margin-top: 10px;
            margin-right: 10px;
        }

        /* Video Section */
        .video-guide-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.1));
            border: 1px dashed var(--accent);
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            margin-top: 60px;
            cursor: pointer;
            transition: 0.4s;
            text-decoration: none;
            display: block;
        }

        .video-guide-card:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(34, 211, 238, 0.2));
            transform: translateY(-5px);
        }

        .play-btn {
            width: 70px;
            height: 70px;
            background: #ff0000;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.5rem;
            box-shadow: 0 0 30px rgba(255, 0, 0, 0.4);
        }
    </style>
</head>
<body>

<div class="container pb-5">
    <div class="hero-section">
        <span class="step-pill">Quick Help Center</span>
        <h1 class="guide-title">How Master Prep Works</h1>
        <p class="text-muted">Master your exams with our step-by-step past paper tracking system.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="accordion" id="faqAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                            <div class="icon-box"><i class="fa fa-user-plus"></i></div>
                            How do I start as a new user?
                        </button>
                    </h2>
                    <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Simply <span class="highlight">Register</span> with your email and name. Once logged in, you will be taken to your <span class="highlight">Dashboard</span>. This is your command center where all your subject progress bars are displayed globally.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                            <div class="icon-box"><i class="fa fa-book"></i></div>
                            How to add my Subjects & Trackers?
                        </button>
                    </h2>
                    <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Click on <span class="highlight">"Add New Subject"</span>. You need to provide:
                            <br>• <span class="highlight">Subject Name:</span> (e.g., Chemistry)
                            <br>• <span class="highlight">Year Range:</span> Select the start and end year of the papers you want to cover.
                            <br>• <span class="highlight">Paper Configuration:</span> Define how many questions are in MCQ, Essay, or Structured papers. This allows the system to generate your custom grid.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                            <div class="icon-box"><i class="fa fa-mouse-pointer"></i></div>
                            How to mark questions? (Interaction Rules)
                        </button>
                    </h2>
                    <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Our grid is highly interactive. Use these gestures on the question buttons:
                            <div class="d-block mt-3">
                                <span class="feature-tag"><i class="fa fa-hand-pointer text-success"></i> Single Click: Mark as <b>Done</b></span>
                                <span class="feature-tag"><i class="fa fa-bolt text-danger"></i> Double Click: Mark as <b>Hard</b></span>
                                <span class="feature-tag"><i class="fa fa-clock text-warning"></i> Long Press: Open <b>Notes</b></span>
                            </div>
                            <p class="mt-3">A <span class="highlight">White Dot</span> will appear under questions that have saved notes, helping you identify them at a glance.</p>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                            <div class="icon-box"><i class="fa fa-sticky-note"></i></div>
                            Can I add images or text notes?
                        </button>
                    </h2>
                    <div id="q4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! Every question can hold <span class="highlight">One Detailed Note</span>. Once you mark a question, a small <b>Edit Icon</b> appears on the top-right of the button. Click it to:
                            <br>• Write down why you got it wrong.
                            <br>• Use the <b>Bullet Point</b> tool for better organization.
                            <br>• <b>Upload an Image</b> (like a screenshot of a marking scheme or your own calculation).
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q5">
                            <div class="icon-box"><i class="fa fa-stopwatch"></i></div>
                            What is Focus Mode?
                        </button>
                    </h2>
                    <div id="q5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Use the <span class="highlight">Stopwatch Icon</span> to set a countdown timer (e.g., 2 hours for a full paper). Once started, the <b>Focus Layer</b> will cover your screen with a giant clock to keep you away from distractions. An alarm will ring when time is up!
                        </div>
                    </div>
                </div>

            </div>

            <a href="https://www.youtube.com" target="_blank" class="video-guide-card">
                <div class="play-btn">
                    <i class="fa fa-play"></i>
                </div>
                <h3 class="text-white fw-bold">Watch Video for More Guide</h3>
                <p class="text-muted mb-0">New to Master Prep? See our full walkthrough on YouTube to maximize your study efficiency.</p>
            </a>

            <div class="text-center mt-5">
                <a href="dashboard.php" class="btn btn-link text-decoration-none text-muted">
                    <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>