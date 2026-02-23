<?php

require_once 'models/Blog.php';

class BlogController extends Controller {
    private $blogModel;

    public function __construct() {
        parent::__construct();
        $this->blogModel = new Blog($this->db);
    }

    // Admin List
    public function index() {
        $this->checkAdmin();
        $data['blogs'] = $this->blogModel->getAll();
        $data['page_title'] = "Manage Blogs";
        $this->view('admin/blogs/index', $data);
    }

    // Admin Add Form
    public function add() {
        $this->checkAdmin();
        $data['page_title'] = "Add New Blog";
        $this->view('admin/blogs/add', $data);
    }

    // Admin Store
    public function store() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $slug = $this->blogModel->generateSlug($title);
            $content = $_POST['content'];
            $excerpt = $_POST['excerpt'] ?? substr(strip_tags($content), 0, 150);
            $category = $_POST['category'];
            $author_id = $_SESSION['user_id'];

            // Handle Image Upload
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "assets/uploads/blogs/";
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

            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'category' => $category,
                'image' => $image_path,
                'author_id' => $author_id
            ];

            if ($this->blogModel->create($data)) {
                $_SESSION['success'] = "Blog created successfully!";
            } else {
                $_SESSION['error'] = "Failed to create blog.";
            }
            $this->redirect('?route=blog/index');
        }
    }

    // Admin Edit Form
    public function edit() {
        $this->checkAdmin();
        if (!isset($_GET['id'])) $this->redirect('?route=blog/index');
        $id = $_GET['id'];
        $data['blog'] = $this->blogModel->getById($id);
        $data['page_title'] = "Edit Blog";
        $this->view('admin/blogs/edit', $data);
    }

    // Admin Update
    public function update() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            $category = $_POST['category'];
            $excerpt = $_POST['excerpt'] ?? substr(strip_tags($content), 0, 150);

            $updateData = [
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt,
                'category' => $category
            ];

            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $slug = $this->blogModel->generateSlug($title);
                $target_dir = "assets/uploads/blogs/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $new_filename = $slug . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $updateData['image'] = $target_file;
                }
            }

            if ($this->blogModel->update($id, $updateData)) {
                $_SESSION['success'] = "Blog updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update blog.";
            }
            $this->redirect('?route=blog/index');
        }
    }

    // Admin Delete
    public function delete() {
        $this->checkAdmin();
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if ($this->blogModel->delete($id)) {
                $_SESSION['success'] = "Blog deleted successfully!";
            } else {
                $_SESSION['error'] = "Failed to delete blog.";
            }
        }
        $this->redirect('?route=blog/index');
    }

    // Public API
    public function api_list() {
        $blogs = $this->blogModel->getAll();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $blogs]);
        exit();
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            $this->redirect('?route=auth/login');
        }
    }
}
