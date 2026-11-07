<?php
require_once 'includes/auth.php';
requireLogin();
$username = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Acacia · Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* ===== Complete Enhanced Styles ===== */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: #333; 
            display: flex;
            min-height: 100vh;
        }
        .hidden { display: none !important; }
        .sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar h2, .sidebar .company {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .sidebar .company i { margin-right: 8px; color: #a5b4fc; }
        .sidebar nav { display: flex; flex-direction: column; }
        .sidebar nav a {
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
        }
        .sidebar nav a:hover, .sidebar nav a.active { background: #34495e; }
        .sidebar nav a i { margin-right: 12px; width: 20px; }
        .main-content {
            margin-left: 300px;
            padding: 30px 20px;
            width: calc(100% - 300px);
            min-height: 100vh;
        }
        .dashboard-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            width: 100%;
            max-width: 1200px;
        }
        #overview { text-align: center; }
        h3 { margin-top: 0; color: #2c3e50; display: flex; align-items: center; gap: 10px; }
        .input-field {
            width: 100%;
            height: 42px;
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
        }
        .input-field:focus { border-color: #007BFF; }
        button { 
            padding: 10px 20px; 
            background: linear-gradient(135deg, #4CAF50, #45a049); 
            color: white; border: none; border-radius: 8px; 
            font-size: 14px; cursor: pointer; margin-right: 8px;
        }
        button.secondary { background: #6c757d; }
        button.danger { background: #dc3545; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; cursor: pointer; user-select: none; }
        .action-btn { cursor: pointer; margin: 0 5px; }
        .action-btn.delete { color: #dc3545; }
        .action-btn.edit { color: #0d6efd; }
        .action-btn.view { color: #198754; }
        .inline-form { 
            margin-top: 15px; padding: 20px; border: 1px solid #ccc; 
            background: #f9f9f9; border-radius: 8px; 
        }
        .stats { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 25px; }
        .stat-card { background: #f8f9fa; padding: 15px 20px; border-radius: 10px; min-width: 120px; }
        .filter-bar { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; min-width: 140px; }
        .filter-group label { font-size: 12px; color: #555; margin-bottom: 4px; }
        .form-row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; }
        .form-group { flex: 1 0 200px; }
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; }
        .modal { background: white; padding: 25px; border-radius: 15px; max-width: 500px; width: 90%; }
        .activity-feed, .deadline-list { list-style: none; padding: 0; text-align: left; }
        .activity-feed li, .deadline-list li { padding: 10px 0; border-bottom: 1px solid #eee; }

        /* Profile Specific */
        .profile-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .profile-card {
            max-width: 600px;
            width: 100%;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .profile-avatar {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .profile-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .profile-info-item:last-child { border-bottom: none; }
        .profile-label {
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-value {
            color: #2c3e50;
        }
        .profile-actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Quick actions */
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .quick-action-btn {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1 0 150px;
        }
        .quick-action-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        .quick-action-btn i { font-size: 24px; color: #667eea; }

        .chart-container { margin: 20px 0; height: 250px; }
    </style>
</head>
<body>

<!-- DASHBOARD -->
<section id="dashboard">
    <div class="sidebar">
        <div class="company"><i class="fas fa-graduation-cap"></i> Acacia</div>
        <nav>
            <a href="#" onclick="showSection('overview')" id="nav-overview"><i class="fas fa-chart-pie"></i> Overview</a>
            <a href="#" onclick="showSection('students')" id="nav-students"><i class="fas fa-user-graduate"></i> Students</a>
            <a href="#" onclick="showSection('instructors')" id="nav-instructors"><i class="fas fa-chalkboard-user"></i> Instructors</a>
            <a href="#" onclick="showSection('courses')" id="nav-courses"><i class="fas fa-layer-group"></i> Courses</a>
            <a href="#" onclick="showSection('subjects')" id="nav-subjects"><i class="fas fa-book-open"></i> Subjects</a>
            <a href="#" onclick="showSection('grades')" id="nav-grades"><i class="fas fa-star"></i> Grades</a>
            <a href="#" onclick="showSection('profile')" id="nav-profile"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="#" onclick="showSection('about')" id="nav-about"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    <div class="main-content">
        <!-- Overview -->
        <div id="overview" class="dashboard-section">
            <h3><i class="fas fa-chart-pie"></i> Dashboard Overview</h3>
            <div class="quick-actions">
                <div class="quick-action-btn" onclick="showSection('students'); setTimeout(() => showAddStudentForm(), 100);">
                    <i class="fas fa-user-plus"></i> Add Student
                </div>
                <div class="quick-action-btn" onclick="showSection('grades'); setTimeout(() => showAddGradeForm(), 100);">
                    <i class="fas fa-plus-circle"></i> Add Grade
                </div>
                <div class="quick-action-btn" onclick="showSection('instructors'); setTimeout(() => showAddInstructorForm(), 100);">
                    <i class="fas fa-chalkboard"></i> Add Instructor
                </div>
            </div>
            <div class="stats" id="statsGrid"></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;">
                <div style="background:#f8f9fa; border-radius:12px; padding:20px;">
                    <h4><i class="far fa-clock"></i> Recent Activities</h4>
                    <ul class="activity-feed" id="activityFeed"></ul>
                </div>
                <div style="background:#f8f9fa; border-radius:12px; padding:20px;">
                    <h4><i class="far fa-calendar-alt"></i> Upcoming Deadlines</h4>
                    <ul class="deadline-list" id="deadlineList"></ul>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <!-- Students -->
        <div id="students" class="dashboard-section hidden">
            <h3><i class="fas fa-user-graduate"></i> Students</h3>
            <div class="filter-bar">
                <div class="filter-group"><label>Search</label><input type="text" id="studentSearch" placeholder="Name/Email" onkeyup="filterStudents()"></div>
                <div class="filter-group"><label>Course</label><select id="filterStudentCourse" onchange="filterStudents()"><option value="">All</option></select></div>
                <div class="filter-group"><label>Year</label><select id="filterStudentYear" onchange="filterStudents()"><option value="">All</option><option>1st</option><option>2nd</option><option>3rd</option><option>4th</option><option>5th</option></select></div>
                <button onclick="showAddStudentForm()"><i class="fas fa-plus"></i> Add</button>
            </div>
            <div id="addStudentForm" class="inline-form hidden">
                <h4 id="studentFormTitle">Add Student</h4>
                <input type="hidden" id="studentId">
                <div class="form-row">
                    <div class="form-group"><label>Name</label><input type="text" id="studentName" class="input-field"></div>
                    <div class="form-group"><label>Course</label><select id="studentCourse" class="input-field"></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Year Level</label><select id="studentYear" class="input-field"><option>1st</option><option>2nd</option><option>3rd</option><option>4th</option><option>5th</option></select></div>
                    <div class="form-group"><label>Email</label><input type="email" id="studentEmail" class="input-field"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Status</label><select id="studentStatus" class="input-field"><option>Active</option><option>Inactive</option><option>Graduated</option></select></div>
                </div>
                <button onclick="saveStudent()">Save</button>
                <button onclick="cancelStudentForm()" class="secondary">Cancel</button>
            </div>
            <div style="overflow-x:auto;"><table id="studentsTable"></table></div>
        </div>

        <!-- Instructors -->
        <div id="instructors" class="dashboard-section hidden">
            <h3><i class="fas fa-chalkboard-user"></i> Instructors</h3>
            <div class="filter-bar">
                <div class="filter-group"><label>Search</label><input type="text" id="instructorSearch" placeholder="Name/Email" onkeyup="filterInstructors()"></div>
                <div class="filter-group"><label>Department</label><select id="filterInstructorDept" onchange="filterInstructors()"><option value="">All</option></select></div>
                <button onclick="showAddInstructorForm()"><i class="fas fa-plus"></i> Add</button>
            </div>
            <div id="addInstructorForm" class="inline-form hidden">
                <h4 id="instructorFormTitle">Add Instructor</h4>
                <input type="hidden" id="instructorId">
                <div class="form-row">
                    <div class="form-group"><label>Name</label><input type="text" id="instructorName" class="input-field"></div>
                    <div class="form-group"><label>Department</label><input type="text" id="instructorDept" class="input-field" placeholder="e.g. CS"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Email</label><input type="email" id="instructorEmail" class="input-field"></div>
                    <div class="form-group"><label>Status</label><select id="instructorStatus" class="input-field"><option>Active</option><option>On Leave</option><option>Inactive</option></select></div>
                </div>
                <button onclick="saveInstructor()">Save</button>
                <button onclick="cancelInstructorForm()" class="secondary">Cancel</button>
            </div>
            <table id="instructorsTable"></table>
        </div>

        <!-- Courses -->
        <div id="courses" class="dashboard-section hidden">
            <h3><i class="fas fa-layer-group"></i> Courses</h3>
            <button onclick="showAddCourseForm()"><i class="fas fa-plus"></i> Add Course</button>
            <div id="addCourseForm" class="inline-form hidden">
                <h4 id="courseFormTitle">Add Course</h4>
                <input type="hidden" id="courseId">
                <div class="form-row">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" id="courseName" class="input-field" placeholder="e.g., BSIT">
                    </div>
                    <div class="form-group">
                        <label>Schedule / Time</label>
                        <input type="text" id="courseSchedule" class="input-field" placeholder="e.g., MWF 9:00-10:30 AM">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="courseDescription" class="input-field" rows="3" placeholder="Course description..."></textarea>
                    </div>
                </div>
                <button onclick="saveCourse()">Save</button>
                <button onclick="cancelCourseForm()" class="secondary">Cancel</button>
            </div>
            <div style="overflow-x:auto;">
                <table id="coursesTable"></table>
            </div>
        </div>

        <!-- Subjects -->
        <div id="subjects" class="dashboard-section hidden">
            <h3><i class="fas fa-book-open"></i> Subjects</h3>
            <button onclick="showAddSubjectForm()"><i class="fas fa-plus"></i> Add Subject</button>
            <div id="addSubjectForm" class="inline-form hidden">
                <h4 id="subjectFormTitle">Add Subject</h4>
                <input type="hidden" id="subjectId">
                <input type="text" id="subjectName" class="input-field" placeholder="Subject name">
                <button onclick="saveSubject()">Save</button>
                <button onclick="cancelSubjectForm()" class="secondary">Cancel</button>
            </div>
            <table id="subjectsTable"></table>
        </div>

        <!-- Grades -->
        <div id="grades" class="dashboard-section hidden">
            <h3><i class="fas fa-star"></i> Grades</h3>
            
            <!-- Student Grade Summary (Auto-computed GPA) -->
            <div style="background:#f0f4ff; border-radius:12px; padding:20px; margin-bottom:25px;">
                <h4 style="margin-top:0; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-calculator"></i> Student Grade Summary (GPA)
                    <span style="font-size:14px; font-weight:normal; color:#666;">Auto‑computed from all grades</span>
                </h4>
                <div style="overflow-x:auto;">
                    <table id="studentSummaryTable">
                        <!-- Populated by JS -->
                    </table>
                </div>
            </div>

            <!-- Grade Filters -->
            <div class="filter-bar">
                <div class="filter-group"><label>Course</label><select id="filterGradeCourse" onchange="applyGradeFilters()"><option value="">All</option></select></div>
                <div class="filter-group"><label>Subject</label><select id="filterGradeSubject" onchange="applyGradeFilters()"><option value="">All</option></select></div>
                <div class="filter-group"><label>Semester</label><select id="filterSemester" onchange="applyGradeFilters()"><option value="">All</option><option>1st</option><option>2nd</option><option>Summer</option></select></div>
                <div class="filter-group"><label>Acad Year</label><input type="text" id="filterAcadYear" placeholder="2025-2026" onkeyup="applyGradeFilters()"></div>
                <div class="filter-group"><label>Sort</label><select id="gradeSortOrder" onchange="applyGradeFilters()"><option value="newest">Newest</option><option value="oldest">Oldest</option></select></div>
                <button onclick="showAddGradeForm()"><i class="fas fa-plus"></i> Add Grade</button>
            </div>

            <!-- Add/Edit Grade Form -->
            <div id="addGradeForm" class="inline-form hidden">
                <h4 id="gradeFormTitle">Add Grade</h4>
                <input type="hidden" id="editGradeId">
                <div class="form-row">
                    <div class="form-group"><label>Student</label><select id="gradeStudent" class="input-field"></select></div>
                    <div class="form-group">
                        <label>Course</label>
                        <select id="gradeCourse" class="input-field" disabled style="background:#e9ecef;">
                            <option value="">Select a student first</option>
                        </select>
                        <input type="hidden" id="gradeCourseHidden">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Subject</label><select id="gradeSubject" class="input-field"></select></div>
                    <div class="form-group"><label>Instructor</label><select id="gradeInstructor" class="input-field"></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Grade</label><select id="gradeValue" class="input-field">
                        <option value="">Select</option>
                        <option>1.00</option><option>1.25</option><option>1.50</option><option>1.75</option><option>2.00</option>
                        <option>2.25</option><option>2.50</option><option>2.75</option><option>3.00</option><option>3.25</option>
                        <option>3.50</option><option>3.75</option><option>4.00</option><option>4.25</option><option>4.50</option>
                        <option>4.75</option><option>5.00</option>
                    </select></div>
                    <div class="form-group"><label>Semester</label><select id="gradeSemester" class="input-field"><option>1st</option><option>2nd</option><option>Summer</option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Academic Year</label><input type="text" id="gradeAcadYear" class="input-field" placeholder="2025-2026"></div>
                </div>
                <button onclick="saveGrade()">Save</button>
                <button onclick="cancelGradeForm()" class="secondary">Cancel</button>
            </div>

            <!-- Detailed Grades Table -->
            <h4 style="margin-top:20px;"><i class="fas fa-list"></i> All Grade Records</h4>
            <table id="gradesTable"></table>
        </div>

        <!-- Profile (Centered Card) -->
        <div id="profile" class="dashboard-section hidden">
            <div class="profile-container">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h2 id="profileDisplayName"><?php echo htmlspecialchars($username); ?></h2>
                    <p style="color:#777; margin-bottom:25px;">Member since 2026</p>
                    
                    <div class="profile-info-item">
                        <span class="profile-label"><i class="fas fa-user"></i> Username</span>
                        <span class="profile-value" id="profileUsername"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="profile-value" id="profileEmailDisplay">user@acacia.com</span>
                        <i class="fas fa-pencil-alt" style="color:#667eea; cursor:pointer;" onclick="editProfileField('email')"></i>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-label"><i class="fas fa-user-tag"></i> Display Name</span>
                        <span class="profile-value" id="profileDisplayNameValue"><?php echo htmlspecialchars($username); ?></span>
                        <i class="fas fa-pencil-alt" style="color:#667eea; cursor:pointer;" onclick="editProfileField('display_name')"></i>
                    </div>
                    
                    <div class="profile-actions">
                        <button onclick="openChangePasswordModal()"><i class="fas fa-key"></i> Change Password</button>
                        <button class="secondary" onclick="saveProfileChanges()"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                    <p style="margin-top:15px; font-size:12px; color:#888;">Last login: Just now</p>
                </div>
            </div>
        </div>

        <!-- About Us -->
        <div id="about" class="dashboard-section hidden">
            <h3>About Acacia</h3>
            <p>Acacia is a comprehensive student information and grade management platform designed for modern educational institutions. Our mission is to simplify academic tracking and empower educators.</p>
            <p><i class="fas fa-map-marker-alt"></i> 123 Education St, Manila</p>
            <p><i class="fas fa-envelope"></i> hello@acacia.edu</p>
        </div>
    </div>
</section>

<!-- Global Modal -->
<div id="globalModal" class="modal-overlay hidden">
    <div class="modal" id="modalContent"></div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal-overlay hidden">
    <div class="modal">
        <h3><i class="fas fa-key"></i> Change Password</h3>
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" id="currentPassword" class="input-field">
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" id="newPassword" class="input-field">
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" id="confirmNewPassword" class="input-field">
        </div>
        <div style="display:flex; gap:10px; margin-top:20px;">
            <button onclick="changePassword()">Update Password</button>
            <button class="secondary" onclick="closePasswordModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    // ========== Global Data & State ==========
    let studentsData = [];
    let instructorsData = [];
    let coursesData = [];
    let subjectsData = [];
    let gradesData = [];
    let currentSort = { students: { col: 'name', asc: true }, instructors: { col: 'name', asc: true } };
    let profileData = {
        display_name: '<?php echo htmlspecialchars($username); ?>',
        email: 'user@acacia.edu'
    };
    const API_BASE = 'api/';

    // ========== Initialization ==========
    document.addEventListener('DOMContentLoaded', () => {
        loadAllData();
        showSection('overview');
    });

    async function loadAllData() {
        await Promise.all([
            fetchStudents(),
            fetchInstructors(),
            fetchCourses(),
            fetchSubjects(),
            fetchGrades()
        ]);
        populateFilters();
        renderStats();
        renderStudents();
        renderInstructors();
        renderCourses();
        renderSubjects();
        renderGrades();
        renderActivitiesDeadlines();
    }

    // ========== API Fetch ==========
    async function fetchStudents() { const res = await fetch(API_BASE + 'students.php'); studentsData = await res.json(); return studentsData; }
    async function fetchInstructors() { const res = await fetch(API_BASE + 'instructors.php'); instructorsData = await res.json(); return instructorsData; }
    async function fetchCourses() { const res = await fetch(API_BASE + 'courses.php'); coursesData = await res.json(); return coursesData; }
    async function fetchSubjects() { const res = await fetch(API_BASE + 'subjects.php'); subjectsData = await res.json(); return subjectsData; }
    async function fetchGrades() { const res = await fetch(API_BASE + 'grades.php'); gradesData = await res.json(); return gradesData; }

    // ========== Section Switching ==========
    window.showSection = function(sectionId) {
        document.querySelectorAll('.dashboard-section').forEach(s => s.classList.add('hidden'));
        document.getElementById(sectionId).classList.remove('hidden');
        document.querySelectorAll('.sidebar nav a').forEach(a => a.classList.remove('active'));
        document.getElementById(`nav-${sectionId}`).classList.add('active');
        
        if (sectionId === 'overview') { renderStats(); renderGradeChart(); }
        if (sectionId === 'students') renderStudents();
        if (sectionId === 'instructors') renderInstructors();
        if (sectionId === 'courses') renderCourses();
        if (sectionId === 'subjects') renderSubjects();
        if (sectionId === 'grades') renderGrades();
        if (sectionId === 'profile') loadProfileData();
    };

    // ========== Stats & Charts ==========
    function renderStats() {
        document.getElementById('statsGrid').innerHTML = `
            <div class="stat-card"><h4>Students</h4><p>${studentsData.length}</p></div>
            <div class="stat-card"><h4>Instructors</h4><p>${instructorsData.length}</p></div>
            <div class="stat-card"><h4>Courses</h4><p>${coursesData.length}</p></div>
            <div class="stat-card"><h4>Subjects</h4><p>${subjectsData.length}</p></div>
            <div class="stat-card"><h4>Grades</h4><p>${gradesData.length}</p></div>`;
    }

    function renderActivitiesDeadlines() {
        document.getElementById('activityFeed').innerHTML = `
            <li><i class="fas fa-circle" style="font-size:8px;color:#4CAF50;"></i> Welcome back, <?php echo htmlspecialchars($username); ?>! <span style="float:right;">Just now</span></li>
            <li><i class="fas fa-circle" style="font-size:8px;color:#4CAF50;"></i> System ready <span style="float:right;">Just now</span></li>`;
        document.getElementById('deadlineList').innerHTML = `
            <li><i class="far fa-calendar-check"></i> Midterm grades due <span style="float:right;">Mar 30, 2026</span></li>
            <li><i class="far fa-calendar-check"></i> Faculty meeting <span style="float:right;">Apr 2, 2026</span></li>`;
    }

    function renderGradeChart() {
        if (!gradesData.length) return;
        const ctx = document.getElementById('gradeChart').getContext('2d');
        if (window.gradeChartInstance) window.gradeChartInstance.destroy();
        const gradeCounts = {};
        gradesData.forEach(g => { const grade = parseFloat(g.grade); if (!isNaN(grade)) gradeCounts[grade.toFixed(2)] = (gradeCounts[grade.toFixed(2)] || 0) + 1; });
        const labels = Object.keys(gradeCounts).sort();
        const data = labels.map(l => gradeCounts[l]);
        window.gradeChartInstance = new Chart(ctx, {
            type: 'bar', data: { labels, datasets: [{ label: 'Number of Grades', data, backgroundColor: '#667eea', borderRadius: 5 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, title: { display: true, text: 'Grade Distribution' } } }
        });
    }

    // ========== Students CRUD ==========
    function renderStudents() {
        let filtered = [...studentsData];
        const search = document.getElementById('studentSearch')?.value.toLowerCase() || '';
        const courseFilter = document.getElementById('filterStudentCourse')?.value;
        const yearFilter = document.getElementById('filterStudentYear')?.value;
        if (search) filtered = filtered.filter(s => (s.name||'').toLowerCase().includes(search) || (s.email||'').toLowerCase().includes(search));
        if (courseFilter) filtered = filtered.filter(s => s.course_name === courseFilter);
        if (yearFilter) filtered = filtered.filter(s => s.year_level === yearFilter);
        filtered.sort((a,b) => {
            let valA = a[currentSort.students.col] || '', valB = b[currentSort.students.col] || '';
            if (typeof valA === 'string') valA = valA.toLowerCase();
            if (typeof valB === 'string') valB = valB.toLowerCase();
            return (valA < valB ? -1 : (valA > valB ? 1 : 0)) * (currentSort.students.asc ? 1 : -1);
        });
        document.getElementById('studentsTable').innerHTML = `
            <tr><th onclick="sortTable('students','name')">Name ${currentSort.students.col==='name'?(currentSort.students.asc?'▲':'▼'):''}</th>
                <th onclick="sortTable('students','course_name')">Course</th><th onclick="sortTable('students','year_level')">Year</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            ${filtered.map(s => `<tr><td>${s.name}</td><td>${s.course_name||''}</td><td>${s.year_level}</td><td>${s.email||''}</td><td>${s.status}</td>
                <td><span class="action-btn view" onclick="viewStudent(${s.id})"><i class="fas fa-eye"></i></span>
                    <span class="action-btn edit" onclick="editStudent(${s.id})"><i class="fas fa-edit"></i></span>
                    <span class="action-btn delete" onclick="deleteStudent(${s.id})"><i class="fas fa-trash"></i></span></td></tr>`).join('')}`;
    }
    window.filterStudents = () => renderStudents();
    window.sortTable = (type, col) => {
        if (type === 'students') {
            if (currentSort.students.col === col) currentSort.students.asc = !currentSort.students.asc;
            else { currentSort.students.col = col; currentSort.students.asc = true; }
            renderStudents();
        } else if (type === 'instructors') {
            if (currentSort.instructors.col === col) currentSort.instructors.asc = !currentSort.instructors.asc;
            else { currentSort.instructors.col = col; currentSort.instructors.asc = true; }
            renderInstructors();
        }
    };
    window.showAddStudentForm = () => {
        document.getElementById('studentId').value = '';
        document.getElementById('studentFormTitle').innerText = 'Add Student';
        document.getElementById('studentName').value = '';
        document.getElementById('studentYear').value = '1st';
        document.getElementById('studentEmail').value = '';
        document.getElementById('studentStatus').value = 'Active';
        const courseSelect = document.getElementById('studentCourse');
        courseSelect.innerHTML = coursesData.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        document.getElementById('addStudentForm').classList.remove('hidden');
    };
    window.cancelStudentForm = () => document.getElementById('addStudentForm').classList.add('hidden');
    window.editStudent = (id) => {
        const s = studentsData.find(s => s.id == id);
        if (!s) return;
        document.getElementById('studentId').value = s.id;
        document.getElementById('studentFormTitle').innerText = 'Edit Student';
        document.getElementById('studentName').value = s.name;
        document.getElementById('studentYear').value = s.year_level;
        document.getElementById('studentEmail').value = s.email || '';
        document.getElementById('studentStatus').value = s.status;
        const courseSelect = document.getElementById('studentCourse');
        courseSelect.innerHTML = coursesData.map(c => `<option value="${c.id}" ${c.id == s.course_id ? 'selected' : ''}>${c.name}</option>`).join('');
        document.getElementById('addStudentForm').classList.remove('hidden');
    };
    window.saveStudent = async () => {
        const id = document.getElementById('studentId').value;
        const data = { name: document.getElementById('studentName').value, course_id: document.getElementById('studentCourse').value, year_level: document.getElementById('studentYear').value, email: document.getElementById('studentEmail').value, status: document.getElementById('studentStatus').value };
        const url = API_BASE + 'students.php' + (id ? '?id=' + id : '');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        if (res.ok) { await fetchStudents(); renderStudents(); renderStats(); cancelStudentForm(); }
    };
    window.deleteStudent = async (id) => {
        if (!confirm('Delete student and all their grades?')) return;
        await fetch(API_BASE + `students.php?id=${id}`, { method: 'DELETE' });
        await fetchStudents(); await fetchGrades(); renderStudents(); renderStats();
        if (!document.getElementById('grades').classList.contains('hidden')) renderGrades();
    };
    window.viewStudent = (id) => {
        const s = studentsData.find(s => s.id == id);
        let html = `<h3>Student Details</h3><p><strong>Name:</strong> ${s.name}</p><p><strong>Course:</strong> ${s.course_name||''}</p><p><strong>Year:</strong> ${s.year_level}</p><p><strong>Email:</strong> ${s.email||''}</p><p><strong>Status:</strong> ${s.status}</p>`;
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('globalModal').classList.remove('hidden');
    };

    // ========== Instructors CRUD ==========
    function renderInstructors() {
        let filtered = [...instructorsData];
        const search = document.getElementById('instructorSearch')?.value.toLowerCase() || '';
        const dept = document.getElementById('filterInstructorDept')?.value;
        if (search) filtered = filtered.filter(i => (i.name||'').toLowerCase().includes(search) || (i.email||'').toLowerCase().includes(search));
        if (dept) filtered = filtered.filter(i => i.department === dept);
        filtered.sort((a,b) => {
            let valA = a[currentSort.instructors.col] || '', valB = b[currentSort.instructors.col] || '';
            return currentSort.instructors.asc ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });
        document.getElementById('instructorsTable').innerHTML = `
            <tr><th onclick="sortTable('instructors','name')">Name ${currentSort.instructors.col==='name'?(currentSort.instructors.asc?'▲':'▼'):''}</th>
                <th onclick="sortTable('instructors','department')">Department</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            ${filtered.map(i => `<tr><td>${i.name}</td><td>${i.department||''}</td><td>${i.email||''}</td><td>${i.status}</td>
                <td><span class="action-btn view" onclick="viewInstructor(${i.id})"><i class="fas fa-eye"></i></span>
                    <span class="action-btn edit" onclick="editInstructor(${i.id})"><i class="fas fa-edit"></i></span>
                    <span class="action-btn delete" onclick="deleteInstructor(${i.id})"><i class="fas fa-trash"></i></span></td></tr>`).join('')}`;
    }
    window.filterInstructors = () => renderInstructors();
    window.showAddInstructorForm = () => {
        document.getElementById('instructorId').value = '';
        document.getElementById('instructorFormTitle').innerText = 'Add Instructor';
        document.getElementById('instructorName').value = '';
        document.getElementById('instructorDept').value = '';
        document.getElementById('instructorEmail').value = '';
        document.getElementById('instructorStatus').value = 'Active';
        document.getElementById('addInstructorForm').classList.remove('hidden');
    };
    window.cancelInstructorForm = () => document.getElementById('addInstructorForm').classList.add('hidden');
    window.editInstructor = (id) => {
        const i = instructorsData.find(i => i.id == id);
        if (!i) return;
        document.getElementById('instructorId').value = i.id;
        document.getElementById('instructorFormTitle').innerText = 'Edit Instructor';
        document.getElementById('instructorName').value = i.name;
        document.getElementById('instructorDept').value = i.department || '';
        document.getElementById('instructorEmail').value = i.email || '';
        document.getElementById('instructorStatus').value = i.status;
        document.getElementById('addInstructorForm').classList.remove('hidden');
    };
    window.saveInstructor = async () => {
        const id = document.getElementById('instructorId').value;
        const data = { name: document.getElementById('instructorName').value, department: document.getElementById('instructorDept').value, email: document.getElementById('instructorEmail').value, status: document.getElementById('instructorStatus').value };
        const url = API_BASE + 'instructors.php' + (id ? '?id=' + id : '');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        if (res.ok) { await fetchInstructors(); renderInstructors(); renderStats(); cancelInstructorForm(); }
    };
    window.deleteInstructor = async (id) => {
        if (!confirm('Delete instructor?')) return;
        await fetch(API_BASE + `instructors.php?id=${id}`, { method: 'DELETE' });
        await fetchInstructors(); renderInstructors(); renderStats();
    };
    window.viewInstructor = (id) => {
        const i = instructorsData.find(i => i.id == id);
        let html = `<h3>Instructor Details</h3><p><strong>Name:</strong> ${i.name}</p><p><strong>Department:</strong> ${i.department||''}</p><p><strong>Email:</strong> ${i.email||''}</p><p><strong>Status:</strong> ${i.status}</p>`;
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('globalModal').classList.remove('hidden');
    };

    // ========== Courses CRUD ==========
    function renderCourses() {
        const html = `
            <tr><th>Course Name</th><th>Schedule</th><th>Description</th><th>Actions</th></tr>
            ${coursesData.map(c => `
                <tr><td>${c.name}</td><td>${c.schedule || '—'}</td><td>${c.description ? c.description.substring(0,50)+(c.description.length>50?'…':'') : '—'}</td>
                <td><span class="action-btn view" onclick="viewCourse(${c.id})"><i class="fas fa-eye"></i></span>
                    <span class="action-btn edit" onclick="editCourse(${c.id})"><i class="fas fa-edit"></i></span>
                    <span class="action-btn delete" onclick="deleteCourse(${c.id})"><i class="fas fa-trash"></i></span></td></tr>`).join('')}`;
        document.getElementById('coursesTable').innerHTML = html;
    }
    window.showAddCourseForm = () => {
        document.getElementById('courseId').value = '';
        document.getElementById('courseFormTitle').innerText = 'Add Course';
        document.getElementById('courseName').value = '';
        document.getElementById('courseSchedule').value = '';
        document.getElementById('courseDescription').value = '';
        document.getElementById('addCourseForm').classList.remove('hidden');
    };
    window.cancelCourseForm = () => document.getElementById('addCourseForm').classList.add('hidden');
    window.editCourse = (id) => {
        const c = coursesData.find(c => c.id == id);
        if (!c) return;
        document.getElementById('courseId').value = c.id;
        document.getElementById('courseFormTitle').innerText = 'Edit Course';
        document.getElementById('courseName').value = c.name;
        document.getElementById('courseSchedule').value = c.schedule || '';
        document.getElementById('courseDescription').value = c.description || '';
        document.getElementById('addCourseForm').classList.remove('hidden');
    };
    window.saveCourse = async () => {
        const id = document.getElementById('courseId').value;
        const data = { name: document.getElementById('courseName').value.trim(), schedule: document.getElementById('courseSchedule').value.trim(), description: document.getElementById('courseDescription').value.trim() };
        if (!data.name) return alert('Course name required');
        const url = API_BASE + 'courses.php' + (id ? '?id=' + id : '');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        if (res.ok) { await fetchCourses(); renderCourses(); renderStats(); cancelCourseForm(); populateFilters(); }
        else alert('Error saving course');
    };
    window.viewCourse = (id) => {
        const c = coursesData.find(c => c.id == id);
        let html = `<h3>${c.name}</h3><p><strong>Schedule:</strong> ${c.schedule || 'Not specified'}</p><p><strong>Description:</strong><br>${c.description || 'No description.'}</p>`;
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('globalModal').classList.remove('hidden');
    };
    window.deleteCourse = async (id) => {
        if (!confirm('Delete course? This may affect students.')) return;
        await fetch(API_BASE + `courses.php?id=${id}`, { method: 'DELETE' });
        await fetchCourses(); await fetchStudents(); renderCourses(); renderStudents(); renderStats(); populateFilters();
    };

    // ========== Subjects CRUD ==========
    function renderSubjects() {
        document.getElementById('subjectsTable').innerHTML = `
            <tr><th>Subject Name</th><th>Actions</th></tr>
            ${subjectsData.map(s => `<tr><td>${s.name}</td>
                <td><span class="action-btn view" onclick="viewSubject(${s.id})"><i class="fas fa-eye"></i></span>
                    <span class="action-btn edit" onclick="editSubject(${s.id})"><i class="fas fa-edit"></i></span>
                    <span class="action-btn delete" onclick="deleteSubject(${s.id})"><i class="fas fa-trash"></i></span></td></tr>`).join('')}`;
    }
    window.showAddSubjectForm = () => {
        document.getElementById('subjectId').value = '';
        document.getElementById('subjectFormTitle').innerText = 'Add Subject';
        document.getElementById('subjectName').value = '';
        document.getElementById('addSubjectForm').classList.remove('hidden');
    };
    window.cancelSubjectForm = () => document.getElementById('addSubjectForm').classList.add('hidden');
    window.editSubject = (id) => {
        const s = subjectsData.find(s => s.id == id);
        if (!s) return;
        document.getElementById('subjectId').value = s.id;
        document.getElementById('subjectFormTitle').innerText = 'Edit Subject';
        document.getElementById('subjectName').value = s.name;
        document.getElementById('addSubjectForm').classList.remove('hidden');
    };
    window.saveSubject = async () => {
        const id = document.getElementById('subjectId').value;
        const name = document.getElementById('subjectName').value.trim();
        if (!name) return alert('Subject name required');
        const url = API_BASE + 'subjects.php' + (id ? '?id=' + id : '');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify({name}) });
        if (res.ok) { await fetchSubjects(); renderSubjects(); renderStats(); cancelSubjectForm(); populateFilters(); }
        else { const err = await res.json(); alert(err.error || 'Error saving subject'); }
    };
    window.deleteSubject = async (id) => {
        if (!confirm('Delete subject?')) return;
        const res = await fetch(API_BASE + `subjects.php?id=${id}`, { method: 'DELETE' });
        if (res.ok) { await fetchSubjects(); renderSubjects(); renderStats(); populateFilters(); }
        else { const err = await res.json(); alert(err.error || 'Cannot delete subject'); }
    };
    window.viewSubject = (id) => {
        const s = subjectsData.find(s => s.id == id);
        let html = `<h3>Subject Details</h3><p><strong>Name:</strong> ${s.name}</p>`;
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('globalModal').classList.remove('hidden');
    };

    // ========== Grades CRUD (with auto GPA and course auto‑sync) ==========
    function renderGrades() {
        renderStudentSummary();
        renderGradeDetails();
    }

    function renderStudentSummary() {
        if (!gradesData.length || !studentsData.length) {
            document.getElementById('studentSummaryTable').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:20px;">No grades available.</td></tr>';
            return;
        }
        const studentGrades = {};
        gradesData.forEach(g => {
            if (!studentGrades[g.student_id]) {
                studentGrades[g.student_id] = { student_name: g.student_name, totalPoints: 0, count: 0 };
            }
            const gradeValue = parseFloat(g.grade);
            if (!isNaN(gradeValue)) {
                studentGrades[g.student_id].totalPoints += gradeValue;
                studentGrades[g.student_id].count++;
            }
        });
        const summary = Object.values(studentGrades).map(s => ({
            ...s,
            average: s.count > 0 ? (s.totalPoints / s.count).toFixed(2) : 'N/A'
        }));
        summary.sort((a, b) => a.student_name.localeCompare(b.student_name));
        const html = `
            <tr><th>Student Name</th><th>Subjects Taken</th><th>Average Grade (GPA)</th><th>Status</th></tr>
            ${summary.map(s => {
                const avg = parseFloat(s.average);
                let status = '';
                if (!isNaN(avg)) {
                    if (avg <= 1.75) status = '<span style="color:#27ae60; font-weight:bold;">President\'s Lister</span>';
                    else if (avg <= 2.00) status = '<span style="color:#2ecc71;">Dean\'s Lister</span>';
                    else if (avg <= 3.00) status = '<span style="color:#f39c12;">Passing</span>';
                    else status = '<span style="color:#e74c3c;">Failing</span>';
                } else status = '—';
                return `<tr><td>${s.student_name}</td><td>${s.count}</td><td><strong>${s.average}</strong></td><td>${status}</td></tr>`;
            }).join('')}`;
        document.getElementById('studentSummaryTable').innerHTML = html;
    }

    function renderGradeDetails() {
        let filtered = [...gradesData];
        const course = document.getElementById('filterGradeCourse')?.value;
        const subject = document.getElementById('filterGradeSubject')?.value;
        const sem = document.getElementById('filterSemester')?.value;
        const ay = document.getElementById('filterAcadYear')?.value.trim();
        const sort = document.getElementById('gradeSortOrder')?.value;
        if (course) filtered = filtered.filter(g => g.course_id == course);
        if (subject) filtered = filtered.filter(g => g.subject_id == subject);
        if (sem) filtered = filtered.filter(g => g.semester === sem);
        if (ay) filtered = filtered.filter(g => g.academic_year.includes(ay));
        if (sort === 'newest') filtered.reverse();
        document.getElementById('gradesTable').innerHTML = `
            <tr><th>Student</th><th>Course</th><th>Subject</th><th>Instructor</th><th>Grade</th><th>Sem</th><th>AY</th><th>Actions</th></tr>
            ${filtered.map(g => `<tr><td>${g.student_name}</td><td>${g.course_name}</td><td>${g.subject_name}</td><td>${g.instructor_name||'—'}</td><td>${g.grade}</td><td>${g.semester}</td><td>${g.academic_year}</td>
                <td><span class="action-btn view" onclick="viewGrade(${g.id})"><i class="fas fa-eye"></i></span>
                    <span class="action-btn edit" onclick="editGrade(${g.id})"><i class="fas fa-edit"></i></span>
                    <span class="action-btn delete" onclick="deleteGrade(${g.id})"><i class="fas fa-trash"></i></span></td></tr>`).join('')}`;
    }

    window.applyGradeFilters = () => renderGrades();

    // Helper to set course field based on selected student
    function updateCourseFromStudent() {
        const studentId = document.getElementById('gradeStudent').value;
        const courseSelect = document.getElementById('gradeCourse');
        const hiddenCourse = document.getElementById('gradeCourseHidden');
        
        if (!studentId) {
            courseSelect.innerHTML = '<option value="">Select a student first</option>';
            hiddenCourse.value = '';
            return;
        }
        
        const student = studentsData.find(s => s.id == studentId);
        if (student && student.course_id) {
            const course = coursesData.find(c => c.id == student.course_id);
            if (course) {
                courseSelect.innerHTML = `<option value="${course.id}">${course.name}</option>`;
                hiddenCourse.value = course.id;
            } else {
                courseSelect.innerHTML = '<option value="">Course not found</option>';
                hiddenCourse.value = '';
            }
        } else {
            courseSelect.innerHTML = '<option value="">No course assigned</option>';
            hiddenCourse.value = '';
        }
    }

    window.showAddGradeForm = () => {
        if (!studentsData.length || !coursesData.length) return alert('Add students and courses first');
        
        const studentSelect = document.getElementById('gradeStudent');
        studentSelect.innerHTML = studentsData.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        document.getElementById('gradeSubject').innerHTML = subjectsData.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        document.getElementById('gradeInstructor').innerHTML = '<option value="">Select</option>' + instructorsData.map(i => `<option value="${i.id}">${i.name}</option>`).join('');
        document.getElementById('gradeValue').value = '';
        document.getElementById('gradeSemester').value = '1st';
        document.getElementById('gradeAcadYear').value = '';
        document.getElementById('editGradeId').value = '';
        document.getElementById('gradeFormTitle').innerText = 'Add Grade';
        
        updateCourseFromStudent();
        studentSelect.onchange = updateCourseFromStudent;
        
        document.getElementById('addGradeForm').classList.remove('hidden');
    };

    window.cancelGradeForm = () => { 
        document.getElementById('addGradeForm').classList.add('hidden'); 
        document.getElementById('editGradeId').value = ''; 
    };

    window.editGrade = (id) => {
        const g = gradesData.find(g => g.id == id);
        if (!g) return;
        
        const studentSelect = document.getElementById('gradeStudent');
        studentSelect.innerHTML = studentsData.map(s => `<option value="${s.id}" ${s.id==g.student_id?'selected':''}>${s.name}</option>`).join('');
        document.getElementById('gradeSubject').innerHTML = subjectsData.map(s => `<option value="${s.id}" ${s.id==g.subject_id?'selected':''}>${s.name}</option>`).join('');
        document.getElementById('gradeInstructor').innerHTML = '<option value="">Select</option>' + instructorsData.map(i => `<option value="${i.id}" ${i.id==g.instructor_id?'selected':''}>${i.name}</option>`).join('');
        document.getElementById('gradeValue').value = g.grade;
        document.getElementById('gradeSemester').value = g.semester;
        document.getElementById('gradeAcadYear').value = g.academic_year;
        document.getElementById('editGradeId').value = g.id;
        document.getElementById('gradeFormTitle').innerText = 'Edit Grade';
        
        const courseSelect = document.getElementById('gradeCourse');
        const hiddenCourse = document.getElementById('gradeCourseHidden');
        const course = coursesData.find(c => c.id == g.course_id);
        if (course) {
            courseSelect.innerHTML = `<option value="${course.id}">${course.name}</option>`;
            hiddenCourse.value = course.id;
        } else {
            courseSelect.innerHTML = '<option value="">Course not found</option>';
            hiddenCourse.value = '';
        }
        
        studentSelect.onchange = updateCourseFromStudent;
        document.getElementById('addGradeForm').classList.remove('hidden');
    };

    window.saveGrade = async () => {
        const id = document.getElementById('editGradeId').value;
        const data = {
            student_id: document.getElementById('gradeStudent').value,
            course_id: document.getElementById('gradeCourseHidden').value,
            subject_id: document.getElementById('gradeSubject').value,
            instructor_id: document.getElementById('gradeInstructor').value || null,
            grade: document.getElementById('gradeValue').value,
            semester: document.getElementById('gradeSemester').value,
            academic_year: document.getElementById('gradeAcadYear').value
        };
        if (!data.grade) return alert('Select a grade');
        if (!data.course_id) return alert('Selected student has no course assigned.');
        
        const url = API_BASE + 'grades.php' + (id ? '?id=' + id : '');
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, { method, headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) });
        if (res.ok) { 
            await fetchGrades(); 
            renderGrades(); 
            renderStats(); 
            cancelGradeForm(); 
        } else {
            alert('Error saving grade');
        }
    };

    window.deleteGrade = async (id) => {
        if (!confirm('Delete grade?')) return;
        await fetch(API_BASE + `grades.php?id=${id}`, { method: 'DELETE' });
        await fetchGrades(); 
        renderGrades(); 
        renderStats();
    };

    window.viewGrade = (id) => {
        const g = gradesData.find(g => g.id == id);
        let html = `<h3>Grade Details</h3><p><strong>Student:</strong> ${g.student_name}</p><p><strong>Course:</strong> ${g.course_name}</p><p><strong>Subject:</strong> ${g.subject_name}</p><p><strong>Instructor:</strong> ${g.instructor_name||'N/A'}</p><p><strong>Grade:</strong> ${g.grade}</p><p><strong>Semester:</strong> ${g.semester}</p><p><strong>Academic Year:</strong> ${g.academic_year}</p>`;
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('globalModal').classList.remove('hidden');
    };

    // ========== Filters Population ==========
    function populateFilters() {
        const courseSelect = document.getElementById('filterStudentCourse');
        if (courseSelect) courseSelect.innerHTML = '<option value="">All</option>' + coursesData.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
        const deptSelect = document.getElementById('filterInstructorDept');
        if (deptSelect) {
            const depts = [...new Set(instructorsData.map(i => i.department).filter(Boolean))];
            deptSelect.innerHTML = '<option value="">All</option>' + depts.map(d => `<option value="${d}">${d}</option>`).join('');
        }
        const gradeCourse = document.getElementById('filterGradeCourse');
        if (gradeCourse) gradeCourse.innerHTML = '<option value="">All</option>' + coursesData.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        const gradeSubject = document.getElementById('filterGradeSubject');
        if (gradeSubject) gradeSubject.innerHTML = '<option value="">All</option>' + subjectsData.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    }

    // ========== Profile Functions ==========
    async function loadProfileData() {
        try {
            const res = await fetch('api/profile.php');
            const data = await res.json();
            profileData = data;
            document.getElementById('profileDisplayName').innerText = data.display_name || data.username;
            document.getElementById('profileDisplayNameValue').innerText = data.display_name || data.username;
            document.getElementById('profileEmailDisplay').innerText = data.email || 'Not set';
            document.getElementById('profileUsername').innerText = data.username;
        } catch(e) {}
    }
    window.editProfileField = (field) => {
        let val = field === 'email' ? profileData.email : profileData.display_name;
        const newVal = prompt(`Enter new ${field}:`, val);
        if (newVal !== null) {
            if (field === 'email') { profileData.email = newVal; document.getElementById('profileEmailDisplay').innerText = newVal; }
            else { profileData.display_name = newVal; document.getElementById('profileDisplayName').innerText = newVal; document.getElementById('profileDisplayNameValue').innerText = newVal; }
        }
    };
    window.saveProfileChanges = async () => {
        const res = await fetch('api/profile.php', { method: 'PUT', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ display_name: profileData.display_name, email: profileData.email }) });
        if (res.ok) { alert('Profile updated'); loadProfileData(); }
    };
    window.openChangePasswordModal = () => document.getElementById('passwordModal').classList.remove('hidden');
    window.closePasswordModal = () => {
        document.getElementById('passwordModal').classList.add('hidden');
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmNewPassword').value = '';
    };
    window.changePassword = async () => {
        const current = document.getElementById('currentPassword').value;
        const newPass = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmNewPassword').value;
        if (newPass !== confirm) return alert('Passwords do not match');
        if (newPass.length < 6) return alert('Password too short');
        const res = await fetch('api/profile.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ current_password: current, new_password: newPass }) });
        if (res.ok) { alert('Password changed'); closePasswordModal(); }
        else { const data = await res.json(); alert(data.error || 'Error'); }
    };

    // Modal close
    document.getElementById('globalModal').addEventListener('click', e => { if (e.target.classList.contains('modal-overlay')) e.target.classList.add('hidden'); });
    document.getElementById('passwordModal').addEventListener('click', e => { if (e.target.classList.contains('modal-overlay')) e.target.classList.add('hidden'); });
</script>
</body>
</html>