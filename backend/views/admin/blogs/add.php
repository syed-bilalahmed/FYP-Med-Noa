<?php include 'views/layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Blog</h1>
        <a href="?route=blog/index" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="?route=blog/store" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Blog Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Hospital News">Hospital News</option>
                                <option value="Public Health">Public Health</option>
                                <option value="Events">Events</option>
                                <option value="Technology">Technology</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Excerpt (Short description)</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Publish Blog</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
