<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Photo Vault</a>
    <div class="d-flex">
      <span class="navbar-text text-white me-3">Hello, <?= session()->get('name') ?></span>
      <a href="/logout" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/upload" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="input-group">
                    <input type="file" class="form-control" name="photo" required>
                    <button type="submit" class="btn btn-primary">Upload Photo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
    <?php if(!empty($photos)): ?>
        <?php foreach($photos as $photo): ?>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <img src="<?= $photo['thumbnail'] ?>" class="card-img-top" style="height:150px; object-fit:cover;">
                    <div class="card-body text-center">
                        <p class="small text-truncate"><?= $photo['original_name'] ?></p>
                        <a href="/download/<?= $photo['id'] ?>" class="btn btn-sm btn-success">Download</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No photos uploaded yet.</p>
    <?php endif; ?>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
