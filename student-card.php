<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentia Student Credential</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #b05e00;
            --primary-mid: #d67719;
            --accent: #FFDAB9;
            --bg-2: #FFFBF5;
            --white: #ffffff;
            --text: #2b2b2b;
            --muted: #666666;
            --shadow-light: 0 12px 25px rgba(0, 0, 0, 0.15);
            --shadow-glow: 0 0 20px rgba(176, 94, 0, 0.3);
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f9f5e8 0%, #ffffff 50%, #fff8f0 100%);
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text);
        }

        .id-card {
            width: 320px;
            height: 480px; /* Reduced height */
            background: var(--white);
            border: 1px solid #e0e0e0;
            border-radius: 18px;
            box-shadow: var(--shadow-light), var(--shadow-glow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            padding: 24px;
            background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(255,250,245,0.8) 100%);
        }

        /* Logo Strip */
        .logo-strip {
            width: 100%;
            height: 50px; /* Reduced height */
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary-mid) 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            border-radius: 15px 15px 0 0;
            margin: -24px -24px 24px -24px;
            padding: 0 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .photo {
            width: 100px; /* Smaller photo */
            height: 100px;
            background: #e0e0e0;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            background-size: cover;
            background-position: center;
            border: 4px solid var(--accent);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
        }
        .photo::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--primary-dark));
            z-index: -1;
        }

        .details {
            text-align: center;
            padding: 0 10px;
        }
        
        .details p {
            margin: 8px 0;
            font-size: 0.95rem;
            color: var(--text);
        }
        
        .details strong {
            display: block;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 15px;
            text-align: center;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--accent);
        }

        /* VC Display area */
        .vc-box {
            background: var(--bg-2);
            border: 1px solid var(--accent);
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
            text-align: center;
        }

        .vc-box h4 {
            margin: 0 0 8px 0;
            color: var(--primary-dark);
            font-size: 1rem;
        }

        .vc-id-text {
            font-family: monospace;
            font-weight: bold;
            font-size: 1.1rem;
            color: #2e7d32; /* Success green for ID */
        }
        
        .vc-helper {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 10px;
        }
        
        /* --- BUTTONS --- */
        .btn-group {
            margin-top: 20px;
        }
        
        .action-btn {
            width: 100%;
            padding: 10px 18px;
            background: var(--accent);
            border: 1px solid var(--primary-dark);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s, transform 0.1s;
        }
        
        .action-btn:hover {
            background: #f8c99e;
            transform: translateY(-1px);
        }
        
        @media print {
            body * { visibility: hidden; }
            .id-card, .id-card * { visibility: visible; }
            .id-card { position: absolute; top: 0; left: 0; box-shadow: none; border: 1px solid #000; }
            .btn-group { display: none; }
        }
    </style>
</head>
<body>
    <div class="id-card" id="card">
        <div class="logo-strip">CREDENTIA DIGITAL ID</div>
        <div class="photo" id="photo"></div>
        <div class="details">
            <strong id="name">Student Name</strong>
            <p id="enrollment">Enrollment ID: ENR-XXXX</p>
            <p id="course">Course: Program</p>
            <p id="email">Email: student@example.com</p>

            <div class="vc-box">
                <h4>VERIFIABLE CREDENTIAL ID</h4>
                <div class="vc-id-text" id="vcIdDisplay">VC-0000-0000</div>
                <p class="vc-helper">**Under Development:** Live verification token and QR code system for real-time validation.</p>
            </div>
        </div>
    </div>

    <div class="btn-group">
        <button class="action-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Card
        </button>
    </div>


    <script>
        const $ = (id) => document.getElementById(id);
        const params = new URLSearchParams(window.location.search);

        const student = {
            name: params.get('name') || 'Student Name',
            enrollment: params.get('enrollment') || 'ENR-XXXX',
            course: params.get('course') || 'Program',
            email: params.get('email') || 'student@example.com',
            vcId: params.get('vc_id') || 'VC-DEMO-2025',
            photoUrl: params.get('photo'),
        };

        $('name').textContent = student.name;
        $('enrollment').textContent = 'Enrollment ID: ' + student.enrollment;
        $('course').textContent = 'Course: ' + student.course;
        $('email').textContent = 'Email: ' + student.email;
        $('vcIdDisplay').textContent = student.vcId;

        if (student.photoUrl) {
            $('photo').style.backgroundImage = `url(${student.photoUrl})`;
        } else {
            $('photo').style.backgroundImage = `url('https://via.placeholder.com/100?text=Photo')`;
        }
    </script>
</body>
</html>
