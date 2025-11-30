<?php
session_start();

$ADMIN_PASSWORD = "admin";

// Handle Login Attempt
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $enteredPassword = $_POST['password'] ?? '';
    
    if ($enteredPassword === $ADMIN_PASSWORD) {
        $_SESSION['authenticated'] = true;
        // Redirect to clean URL after successful login
        header("Location: index.php");
        exit();
    } else {
        $loginError = "Incorrect password.";
    }
}

// 2. Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// 3. Check Authentication State
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // If not authenticated, display the login form and EXIT the script
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            :root {
                --primary-dark: #1e40af;
                --accent: #dbeafe;
                --bg-2: #f8fafc;
                --text: #0f172a;
                --shadow: 0 4px 12px rgba(30, 64, 175, 0.08);
            }
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--bg-2) 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                position: relative;
            }
            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.08)"/><circle cx="25" cy="75" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.06)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
                opacity: 0.5;
                pointer-events: none;
            }
            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                width: 350px;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.2);
                position: relative;
                z-index: 1;
                transform: translateY(0);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .login-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            }
            .login-card h2 {
                color: var(--primary-dark);
                margin-bottom: 30px;
                font-weight: 800;
            }
            .form-group {
                margin-bottom: 20px;
            }
            input[type="password"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                font-size: 1rem;
            }
            .btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                background: var(--primary-dark);
                color: white;
                transition: background-color 0.2s;
            }
            .btn:hover {
                background: #1e3a8a;
            }
            .error {
                color: #d9534f;
                margin-top: 15px;
            }
            .note {
                margin-top: 20px;
                font-size: 0.85rem;
                color: gray;
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h2><i class="fas fa-lock"></i> Admin Console Access</h2>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <input type="password" name="password" placeholder="Enter Admin Password" required autofocus>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <?php if (isset($loginError)) { echo "<p class='error'>$loginError</p>"; } ?>
            <p class="note">Default Password: **admin**</p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

try {
    $db = new PDO('sqlite:credentials.sqlite');

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        enrollment_id TEXT UNIQUE NOT NULL,
        course TEXT NOT NULL,
        email TEXT NOT NULL,
        vc_id TEXT UNIQUE NOT NULL,
        issue_date DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function generateVCID() {
    return 'VC-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_student') {
    $fullName = trim($_POST['full_name']);
    $enrollmentId = trim($_POST['enrollment_id']);
    $course = trim($_POST['course']);
    $email = trim($_POST['email']);
    $vcId = generateVCID(); // Generate a new VC ID

    // Basic validation
    if (!empty($fullName) && !empty($enrollmentId) && !empty($course) && !empty($email)) {
        try {
            $stmt = $db->prepare("INSERT INTO students (full_name, enrollment_id, course, email, vc_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$fullName, $enrollmentId, $course, $email, $vcId]);
            
            // Redirect to prevent form resubmission
            header("Location: index.php?status=success&message=" . urlencode("Credential successfully issued to $fullName! VC ID: $vcId"));
            exit();

        } catch (PDOException $e) {
            $errorMessage = "Error issuing credential. Enrollment ID may already exist.";
            header("Location: index.php?status=error&message=" . urlencode($errorMessage));
            exit();
        }
    } else {
        $errorMessage = "All fields are required.";
        header("Location: index.php?status=error&message=" . urlencode($errorMessage));
        exit();
    }
}

// 2. DELETE/REVOKE CREDENTIAL
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?status=success&message=" . urlencode("Credential revoked successfully."));
        exit();
    } catch (PDOException $e) {
        $errorMessage = "Error revoking credential.";
        header("Location: index.php?status=error&message=" . urlencode($errorMessage));
        exit();
    }
}

// 3. READ ALL STUDENTS (for display)
$students = [];
try {
    $result = $db->query("SELECT * FROM students ORDER BY issue_date DESC");
    $students = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error if needed, but continue execution
    $students = []; 
}

// --- HTML START (Admin Console Display) ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentia Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1e40af;
            --primary-light: #dbeafe;
            --accent: #dbeafe;
            --danger: #ef4444;
            --success: #10b981;
            --bg-1: #ffffff;
            --bg-2: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --border: #cbd5e1;
            --shadow: 0 4px 12px rgba(30, 64, 175, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-2);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e3a8a 100%);
            color: var(--bg-1);
            padding: 24px 30px;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header h1 i {
            font-size: 2.5rem;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .header h1 i {
                font-size: 2rem;
            }
        }

        .card {
            background: var(--bg-1);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        input[type="text"], input[type="email"] {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            border-color: var(--primary-dark);
            outline: none;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
        }

        .btn.primary {
            background: var(--primary-dark);
            color: var(--bg-1);
        }

        .btn.primary:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
        }

        .btn.danger {
            background: var(--danger);
            color: var(--bg-1);
        }

        .btn.danger:hover {
            background: #c9302c;
            transform: translateY(-1px);
        }

        .btn.secondary {
            background: var(--muted);
            color: var(--bg-1);
            padding: 4px 8px;
            border-radius: 4px;
        }

        .btn.secondary:hover {
            background: #555;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.success {
            background: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .alert.error {
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 15px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }
        
        .student-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .student-item {
            background: var(--bg-1);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 0.95rem;
            position: relative;
        }
        
        .student-info {
            flex-grow: 1;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr 1.2fr; /* Layout columns */
            gap: 15px;
            align-items: center;
        }

        .student-info span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }
        
        .student-info .vc-id {
            font-weight: 700;
            color: #2563eb; /* Blue for verifiable credential ID */
        }

        .student-info .issue-date {
            font-size: 0.85rem;
            color: var(--muted);
        }
        
        .list-header {
            font-weight: 700;
            color: var(--primary-dark);
            background: var(--accent);
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            margin-bottom: -10px; /* Overlap with first item gap */
        }
        
        .flex-end {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1><i class="fas fa-id-badge"></i> Credentia VC Admin Panel</h1>
            <a href="index.php?action=logout" class="btn danger" title="Logout"><i class="fas fa-power-off"></i></a>
        </div>

        <?php
        // Display Alert Messages
        if (isset($_GET['status']) && isset($_GET['message'])) {
            $status = htmlspecialchars($_GET['status']);
            $message = htmlspecialchars(urldecode($_GET['message']));
            echo "<div class='alert $status'><i class='fas fa-info-circle'></i> $message</div>";
        }
        ?>

        <div class="card">
            <h2>Issue New Student Credential</h2>
            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="add_student">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" id="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="enrollment_id">Enrollment ID</label>
                        <input type="text" name="enrollment_id" id="enrollment_id" required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course/Program</label>
                        <input type="text" name="course" id="course" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn primary">
                        <i class="fas fa-plus-circle"></i> Issue Credential
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Issued Credentials (Verification List)</h2>

            <?php if (empty($students)): ?>
                <p>No credentials have been issued yet.</p>
            <?php else: ?>

                <div class="list-header student-info">
                    <span>Full Name</span>
                    <span>Enrollment ID</span>
                    <span>Course</span>
                    <span>VC ID</span>
                    <span>Issued On</span>
                </div>

                <div class="student-list">
                    <?php foreach ($students as $student): ?>
                        <div class="student-item">
                            <div class="student-info">
                                <span><?php echo htmlspecialchars($student['full_name']); ?></span>
                                <span><?php echo htmlspecialchars($student['enrollment_id']); ?></span>
                                <span><?php echo htmlspecialchars($student['course']); ?></span>
                                <span class="vc-id"><?php echo htmlspecialchars($student['vc_id']); ?></span>
                                <span class="issue-date"><?php echo date('Y-m-d', strtotime($student['issue_date'])); ?></span>
                            </div>
                            
                            <div class="flex-end">
                                <button class="btn secondary" onclick="toggleOptions(this)" title="Options">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="actions" style="display: none;">
                                    <a href="student-card.php?name=<?php echo urlencode($student['full_name']); ?>&enrollment=<?php echo urlencode($student['enrollment_id']); ?>&course=<?php echo urlencode($student['course']); ?>&email=<?php echo urlencode($student['email']); ?>&vc_id=<?php echo urlencode($student['vc_id']); ?>"
                                       target="_blank"
                                       class="btn primary" title="View Digital Credential" style="padding: 6px 10px; font-size: 0.9rem;">
                                        <i class="fas fa-id-card"></i> Card
                                    </a>

                                    <a href="index.php?action=delete&id=<?php echo $student['id']; ?>"
                                       onclick="return confirm('Revoke credential for <?php echo addslashes($student['full_name']); ?>?')"
                                       class="btn danger" title="Revoke Credential" style="padding: 6px 10px; font-size: 0.9rem;">
                                        <i class="fas fa-trash-alt"></i> Revoke
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <p style="text-align: center; color: var(--muted); font-size: 0.8rem; margin-top: 40px;">
            Credentia VC Prototype - Developed by Your Team
        </p>

    </div>

    <script>
        function toggleOptions(btn) {
            const item = btn.closest('.student-item');
            const actions = item.querySelector('.actions');
            if (actions.style.display === 'none') {
                actions.style.display = 'flex';
            } else {
                actions.style.display = 'none';
            }
        }
    </script>
</body>
</html>
