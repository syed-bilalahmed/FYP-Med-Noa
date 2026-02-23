<?php
require_once 'config/Database.php';
require_once 'models/Blog.php';
require_once 'models/BlogCategory.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $blogModel = new Blog($db);
    $catModel = new BlogCategory($db);

    // 1. Categories Mapping
    $categories = [
        'Platform', 'Appointments', 'Mental Health', 'Blood', 'Nutrition'
    ];
    
    $catMap = [];
    foreach ($categories as $catName) {
        $stmt = $db->prepare("SELECT id FROM blog_categories WHERE name = :name");
        $stmt->execute(['name' => $catName]);
        $existingId = $stmt->fetchColumn();

        if (!$existingId) {
            $catModel->create($catName);
            $stmt->execute(['name' => $catName]);
            $existingId = $stmt->fetchColumn();
        }
        $catMap[$catName] = $existingId;
    }

    // 2. Blogs Data
    $blogs = [
        [
            'title' => 'Hospital & Clinic SaaS: Onboarding Made Simple',
            'content' => 'Med Nova helps facilities digitize appointment scheduling, staff workflows, and patient handling—without complicated setup.',
            'category' => 'Platform',
            'image' => 'assets/blog-mri.png',
            'date' => '2026-01-15 10:00:00'
        ],
        [
            'title' => 'Smart Booking: From Search to Appointment',
            'content' => 'Patients can search facilities, select doctors, and submit appointment requests with clear steps and confirmation.',
            'category' => 'Appointments',
            'image' => 'assets/blog-telemedicine.png',
            'date' => '2026-01-18 10:00:00'
        ],
        [
            'title' => 'Separate Psychological Centers Workflow',
            'content' => 'A dedicated module for psychological centers with privacy-aware access, scheduling, and session documentation.',
            'category' => 'Mental Health',
            'image' => 'assets/blog-cardiology.png',
            'date' => '2026-01-20 10:00:00'
        ],
        [
            'title' => 'Blood Donation & Requests Registry',
            'content' => 'Register donors, manage blood stock, and broadcast urgent blood requests across hospitals and clinics.',
            'category' => 'Blood',
            'image' => 'assets/blog-camp.png',
            'date' => '2026-01-22 10:00:00'
        ],
        [
            'title' => 'Diet Planner: Personalized Nutrition Requests',
            'content' => 'Submit your health profile and goals to receive a tailored diet plan aligned with clinical guidance and follow-up.',
            'category' => 'Nutrition',
            'image' => 'assets/blog-dengue.png',
            'date' => '2026-01-10 10:00:00'
        ]
    ];

    $importedCount = 0;
    foreach ($blogs as $blog) {
        $slug = $blogModel->generateSlug($blog['title']);
        
        // Check if blog already exists
        $check = $db->prepare("SELECT id FROM blogs WHERE slug = :slug");
        $check->execute(['slug' => $slug]);
        if ($check->rowCount() > 0) continue; 

        $data = [
            'title' => $blog['title'],
            'slug' => $slug,
            'content' => $blog['content'],
            'excerpt' => substr(strip_tags($blog['content']), 0, 150),
            'category_id' => $catMap[$blog['category']],
            'image' => $blog['image'],
            'author_id' => 1 // Default Super Admin
        ];
        
        $sql = "INSERT INTO blogs (title, slug, content, excerpt, category_id, image, author_id, created_at) 
                VALUES (:title, :slug, :content, :excerpt, :category_id, :image, :author_id, :created_at)";
        $data['created_at'] = $blog['date'];
        $stmt = $db->prepare($sql);
        if ($stmt->execute($data)) {
            $importedCount++;
        }
    }

    echo "<h3>Success!</h3>";
    echo "<p>$importedCount new blogs have been migrated to the database.</p>";
    echo "<p><a href='?route=admin/blogs'>View Blogs in Admin</a></p>";

} catch (Exception $e) {
    echo "<h3>Error!</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
