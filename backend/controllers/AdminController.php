<?php

class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            $this->redirect('?route=auth/login');
        }
    }

    public function dashboard() {
        // 1. Total Hospitals
        $stmt = $this->db->query("SELECT COUNT(*) FROM hospitals WHERE type = 'Hospital'");
        $data['total_hospitals'] = $stmt->fetchColumn();

        // 2. Total Clinics
        $stmt = $this->db->query("SELECT COUNT(*) FROM hospitals WHERE type = 'Clinic'");
        $data['total_clinics'] = $stmt->fetchColumn();

        // 3. Active Subscriptions
        $stmt = $this->db->query("SELECT COUNT(*) FROM hospitals WHERE subscription_status = 'active'");
        $data['active_subscriptions'] = $stmt->fetchColumn();

        // 4. Total Doctors
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'doctor'");
        $data['total_doctors'] = $stmt->fetchColumn();

        // 4. Recent Hospitals (Top 5)
        $stmt = $this->db->query("SELECT * FROM hospitals ORDER BY created_at DESC LIMIT 5");
        $data['recent_hospitals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Recent Doctors (Top 5)
        $stmt = $this->db->query("SELECT u.name, u.email, d.specialization FROM users u JOIN doctors_details d ON u.id = d.user_id WHERE u.role = 'doctor' ORDER BY u.created_at DESC LIMIT 5");
        $data['recent_doctors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['page_title'] = "Super Admin Dashboard";
        $this->view('admin/dashboard', $data);
    }


    public function doctors() {
        $filter_type = $_GET['type'] ?? ''; // Hospital or Clinic
        $filter_hospital_id = $_GET['hospital_id'] ?? '';
        
        $sql = "SELECT d.*, u.email, u.name, u.role, h.name as hospital_name, h.type as hospital_type 
                FROM doctors_details d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN doctor_hospital_affiliations dha ON u.id = dha.doctor_id 
                LEFT JOIN hospitals h ON dha.hospital_id = h.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($filter_type)) {
            $sql .= " AND h.type = :type";
            $params[':type'] = $filter_type;
        }

        if (!empty($filter_hospital_id)) {
            $sql .= " AND h.id = :hid";
            $params[':hid'] = $filter_hospital_id;
        }

        $sql .= " ORDER BY u.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data['doctors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Hospitals/Clinics for Filter Dropdown
        $stmt = $this->db->query("SELECT id, name, type FROM hospitals ORDER BY name ASC");
        $data['hospitals_list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['page_title'] = "Doctors List";
        $data['filter_type'] = $filter_type;
        $data['filter_hospital_id'] = $filter_hospital_id;

        $this->view('admin/doctors', $data);
    }

    public function doctor_details() {
        if (!isset($_GET['id'])) $this->redirect('?route=admin/doctors');
        $id = $_GET['id'];
        
        $stmt = $this->db->prepare("SELECT d.*, u.email, u.name, u.role, u.created_at as join_date FROM doctors_details d JOIN users u ON d.user_id = u.id WHERE d.id = :id");
        $stmt->execute([':id' => $id]);
        $data['doctor'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Initialize empty reviews array for now (To be implemented with Reviews table later)
        $data['reviews'] = [];
        
        $data['page_title'] = "Doctor Details";
        $this->view('admin/doctor_details', $data);
    }

    public function add_doctor() {
        // Fetch Hospitals for Dropdown
        $stmt = $this->db->query("SELECT * FROM hospitals ORDER BY name ASC");
        $data['hospitals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data['page_title'] = "Add New Doctor";
        $this->view('admin/add_doctor', $data);
    }

    public function store_doctor() {
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $name = $_POST['name'];
             $phone = $_POST['phone'];
             $password = $_POST['password'];
             $specialization = $_POST['specialization'];
             $phone_details = $_POST['phone_details'] ?? '';
             $address = $_POST['address'];
             $bio = $_POST['biography'];
             $rating = $_POST['rating'] ?? 5.0;
             $hospital_id = $_POST['hospital_id'] ?? null;

             // Create User
             $hashed_password = password_hash($password, PASSWORD_DEFAULT);
             $stmt = $this->db->prepare("INSERT INTO users (name, phone, password, role) VALUES (:name, :phone, :pass, 'doctor')");
             $stmt->execute([':name' => $name, ':phone' => $phone, ':pass' => $hashed_password]);
             $user_id = $this->db->lastInsertId();

             // Create Doctor Details
             $stmt = $this->db->prepare("INSERT INTO doctors_details (user_id, specialization, phone, address, biography, rating, joining_date) VALUES (:uid, :spec, :phone, :addr, :bio, :rating, NOW())");
             $stmt->execute([
                 ':uid' => $user_id, 
                 ':spec' => $specialization,
                 ':phone' => $phone_details ?: $phone,
                 ':addr' => $address,
                 ':bio' => $bio,
                 ':rating' => $rating
             ]);
             
             // Create Hospital Affiliation
             if ($hospital_id) {
                 $stmt = $this->db->prepare("INSERT INTO doctor_hospital_affiliations (doctor_id, hospital_id) VALUES (:did, :hid)");
                 $stmt->execute([':did' => $user_id, ':hid' => $hospital_id]);
             }

             $this->redirect('?route=admin/doctors');
         }
    }

    public function store_receptionist() {
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $name = $_POST['name'];
             $phone = $_POST['phone'];
             $password = $_POST['password'];

             // Create User
             $hashed_password = password_hash($password, PASSWORD_DEFAULT);
             $stmt = $this->db->prepare("INSERT INTO users (name, phone, password, role) VALUES (:name, :phone, :pass, 'receptionist')");
             $stmt->execute([':name' => $name, ':phone' => $phone, ':pass' => $hashed_password]);

             $this->redirect('?route=admin/receptionists');
         }
    }

    public function receptionists() {
        $stmt = $this->db->query("SELECT * FROM users WHERE role = 'receptionist' ORDER BY created_at DESC");
        $data['receptionists'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['page_title'] = "Manage Receptionists";
        $this->view('admin/receptionists', $data);
    }

    // --- Region Management ---
    public function regions() {
        $stmt = $this->db->query("SELECT * FROM regions ORDER BY created_at DESC");
        $data['regions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['page_title'] = "Manage Regions";
        $this->view('admin/regions', $data);
    }

    public function store_region() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            if(!empty($name)){
                $stmt = $this->db->prepare("INSERT INTO regions (name, created_at) VALUES (:name, NOW())");
                $stmt->execute([':name' => $name]);
            }
            $this->redirect('?route=admin/regions');
        }
    }

    public function delete_region() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->db->prepare("DELETE FROM regions WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        $this->redirect('?route=admin/regions');
    }

    // --- Hospital Management ---
    // --- Hospital Management ---
    public function hospitals() {
        // Fetch Only Hospitals
        $stmt = $this->db->query("SELECT h.*, r.name as region_name FROM hospitals h LEFT JOIN regions r ON h.region_id = r.id WHERE h.type = 'Hospital' ORDER BY h.id DESC");
        $data['hospitals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $this->db->query("SELECT * FROM regions ORDER BY name ASC");
        $data['regions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['page_title'] = "Manage Hospitals";
        $this->view('admin/hospitals', $data);
    }

    public function clinics() {
        // Fetch Only Clinics
        $stmt = $this->db->query("SELECT h.*, r.name as region_name FROM hospitals h LEFT JOIN regions r ON h.region_id = r.id WHERE h.type = 'Clinic' ORDER BY h.id DESC");
        $data['clinics'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $this->db->query("SELECT * FROM regions ORDER BY name ASC");
        $data['regions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['page_title'] = "Manage Clinics";
        $this->view('admin/clinics', $data);
    }

    public function store_hospital() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $password = $_POST['password'];
            $region_id = $_POST['region_id'];
            $address = $_POST['address'];
            $contact = $_POST['contact_info'];
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? 'Hospital';
            
            // Slug Generation
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            
            // Image Upload
            $image_path = '';
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                $target_dir = "assets/uploads/hospitals/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $new_filename = $slug . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                }
            }

            // Default Subscription
            $plan_type = 'monthly';
            $status = 'active';
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

            if(!empty($name) && !empty($phone) && !empty($password)){
                try {
                    $this->db->beginTransaction();

                    // 1. Create User
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("INSERT INTO users (name, phone, password, role) VALUES (:name, :phone, :pass, 'hospital_admin')");
                    $stmt->execute([':name' => $name, ':phone' => $phone, ':pass' => $hashed_password]);
                    $user_id = $this->db->lastInsertId();

                    // 2. Create Hospital
                    $stmt = $this->db->prepare("INSERT INTO hospitals (name, slug, type, region_id, address, contact_info, description, image, user_id, subscription_status, plan_type, plan_expires_at) VALUES (:name, :slug, :type, :rid, :addr, :contact, :desc, :img, :uid, :status, :plan, :exp)");
                    $stmt->execute([
                        ':name' => $name,
                        ':slug' => $slug,
                        ':type' => $type,
                        ':rid' => $region_id,
                        ':addr' => $address,
                        ':contact' => $contact,
                        ':desc' => $description,
                        ':img' => $image_path,
                        ':uid' => $user_id,
                        ':status' => $status,
                        ':plan' => $plan_type,
                        ':exp' => $expires_at
                    ]);

                    $this->db->commit();
                    $login_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/?route=auth/login&slug=" . $slug;
                    $_SESSION['success'] = "<b>" . ($type ?? 'Hospital') . " Created Successfully!</b><br>Login URL: <a href='$login_url' target='_blank'>$login_url</a>";
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $_SESSION['error'] = "Error creating account: " . $e->getMessage();
                }
            } else {
                 $_SESSION['error'] = "All fields are required!";
            }
            if ($type == 'Clinic') {
                $this->redirect('?route=admin/clinics');
            } else {
                $this->redirect('?route=admin/hospitals');
            }
        }
    }

    public function delete_hospital() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $this->db->prepare("DELETE FROM hospitals WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
        // Redirect back to referring page or default to hospitals
        if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'clinics') !== false) {
             $this->redirect('?route=admin/clinics');
        } else {
             $this->redirect('?route=admin/hospitals');
        }
    }

    public function update_hospital() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $address = $_POST['address'];
            $contact = $_POST['contact_info'];
            $description = $_POST['description'];
            $region_id = $_POST['region_id'];
            
            // Slug update (optional, usually kept static to avoid breaking links, but let's allow re-generation if name changes drastically or keep same)
            // For now, let's keep slug static or manual update? Let's just update other fields.
            
            $sql = "UPDATE hospitals SET name = :name, address = :addr, contact_info = :contact, description = :desc, region_id = :rid";
            $params = [
                ':name' => $name,
                ':addr' => $address,
                ':contact' => $contact,
                ':desc' => $description,
                ':rid' => $region_id,
                ':id' => $id
            ];

            // Image Upload
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
                $target_dir = "assets/uploads/hospitals/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $new_filename = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $sql .= ", image = :img";
                    $params[':img'] = $target_file;
                }
            }

            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Redirect based on type (fetch type first to be sure?)
            // Or just check referrer.
             if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'clinics') !== false) {
                 $this->redirect('?route=admin/clinics');
            } else {
                 $this->redirect('?route=admin/hospitals');
            }
        }
    }

    public function toggle_status() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            // Get current status
            $stmt = $this->db->prepare("SELECT subscription_status FROM hospitals WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $current = $stmt->fetchColumn();

            $new_status = ($current == 'active') ? 'inactive' : 'active';

            $update = $this->db->prepare("UPDATE hospitals SET subscription_status = :status WHERE id = :id");
            $update->execute([':status' => $new_status, ':id' => $id]);
        }
        
        if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'clinics') !== false) {
             $this->redirect('?route=admin/clinics');
        } else {
             $this->redirect('?route=admin/hospitals');
        }
    }

    // --- Hospital Stats for Super Admin ---
    public function view_hospital_stats() {
        if (!isset($_GET['id'])) $this->redirect('?route=admin/hospitals');
        $hid = $_GET['id'];

        // Get Hospital Info
        $stmt = $this->db->prepare("SELECT * FROM hospitals WHERE id = :id");
        $stmt->execute([':id' => $hid]);
        $data['hospital'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stats
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM doctor_hospital_affiliations WHERE hospital_id = :hid");
        $stmt->execute([':hid' => $hid]);
        $data['total_doctors'] = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM appointments WHERE hospital_id = :hid");
        $stmt->execute([':hid' => $hid]);
        $data['total_appointments'] = $stmt->fetchColumn();

        $data['page_title'] = "Hospital Statistics";
        $this->view('admin/hospital_stats', $data);
    }
}
