<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
// naive storage for demo only
$store = __DIR__ . '/comments.json';
if (!file_exists($store)) { file_put_contents($store, json_encode([])); }

// Load comments
$comments = json_decode(file_get_contents($store), true) ?: [];

// Handle new comment (stored XSS vulnerability)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Intentionally vulnerable: stores raw HTML/JS
    $comments[] = [
        'author' => $_POST['author'] ?? 'anonymous',
        'text'   => $_POST['text'] ?? ''
    ];
    file_put_contents($store, json_encode($comments));
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=Comment+added'); // reflected XSS in msg
    exit;
}

// Reflected XSS from `q` and `msg`
$q   = $_GET['q']   ?? '';
$msg = $_GET['msg'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Vulnerable XSS Demo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Vulnerable XSS Demo</h1>

  <section>
    <h2>Search</h2>
    <form method="get">
      <input type="text" name="q" placeholder="Try anything..." value="<?php echo $q; ?>">
      <button type="submit">Search</button>
    </form>
    <p>Results for: <?php echo $q; ?></p> <!-- reflected XSS sink -->
  </section>

  <?php if ($msg): ?>
    <div class="flash"><?php echo $msg; ?></div> <!-- reflected XSS sink -->
  <?php endif; ?>

  <section>
    <h2>Comments</h2>
    <form method="post">
      <input name="author" placeholder="Name">
      <textarea name="text" placeholder="Your comment"></textarea>
      <button type="submit">Post</button>
    </form>

    <ul>
      <?php foreach ($comments as $c): ?>
        <li>
          <strong><?php echo $c['author']; ?>:</strong>
          <div class="comment-body"><?php echo $c['text']; ?></div> <!-- stored XSS sink -->
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</body>
</html>