<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Path direktori utama
$rootDir = dirname(__DIR__) . '/dir-agent/upload';

// Path yang saat ini diakses
$currentDir = isset($_GET['dir']) ? rtrim($_GET['dir'], '/') : $rootDir;

//$base_url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER["REQUEST_URI"] . '?') . '/';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER["REQUEST_URI"] . '?') . '/';

// Fungsi untuk menampilkan daftar file dan direktori
function listContents($dir)
{
    if (!is_dir($dir)) {
        return [];
    }
    return array_diff(scandir($dir), array('.', '..'));
}

// Fungsi untuk menampilkan apakah itu file atau direktori
function isDirectory($path)
{
    return is_dir($path);
}

// Fungsi untuk membuat link agar user bisa masuk ke direktori tertentu
function createLink($dir, $item)
{
    return "index.php?dir=" . urlencode($dir . '/' . $item);
}

// Fungsi untuk menghapus file atau folder
function deleteItem($path)
{
    if (is_dir($path)) {
        // Ambil semua isi folder (file dan subfolder)
        $contents = array_diff(scandir($path), array('.', '..'));
        foreach ($contents as $item) {
            $itemPath = $path . '/' . $item;
            // Hapus isi folder secara rekursif
            deleteItem($itemPath);
        }
        // Setelah semua isinya dihapus, hapus folder
        rmdir($path);
    } else {
        // Hapus file
        unlink($path);
    }
}

// Fungsi untuk menghapus folder berdasarkan tanggal secara rekursif di semua subdirektori
function deleteFoldersByDateRecursive($dir, $date)
{
    $contents = listContents($dir);
    $found = false;

    foreach ($contents as $item) {
        $itemPath = $dir . '/' . $item;

        // Jika item adalah direktori, telusuri rekursif
        if (isDirectory($itemPath)) {
            // Jika nama folder sesuai dengan tanggal
            if (strpos($item, $date) === 0) {
                deleteItem($itemPath); // Hapus folder
                $found = true;
            } else {
                // Lakukan rekursif untuk masuk ke subdirektori
                if (deleteFoldersByDateRecursive($itemPath, $date)) {
                    $found = true; // Setel ke true jika ditemukan di subdirektori
                }
            }
        }
    }
    return $found;
}

function displayDirectoryContents($dir, $level = 0)
{
    $contents = array_diff(scandir($dir), array('.', '..'));

    echo '<ul>';
    foreach ($contents as $item) {
        $itemPath = $dir . '/' . $item;
        echo '<li>';

        // Indentasi sesuai dengan level direktori
        echo str_repeat('&nbsp;', $level * 4);

        // Jika item adalah direktori
        if (isDirectory($itemPath)) {
            // Tampilkan link ke direktori dan lanjutkan rekursif
            echo '<strong>' . htmlspecialchars($item) . ' (Directory)</strong>';
            displayDirectoryContents($itemPath, $level + 1); // Rekursif untuk subdirektori
        } else {
            // Tampilkan nama file
            echo htmlspecialchars($item) . ' (File)';
        }

        echo '</li>';
    }
    echo '</ul>';
}

// Proses penghapusan berdasarkan tanggal
$message = ''; // Variabel untuk menyimpan pesan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_by_date'])) {
    if (!empty($_POST['date'])) {
        $date = $_POST['date']; // Format tanggal harus sesuai dengan nama folder (ddmmyyyy)
        $found = deleteFoldersByDateRecursive($currentDir, $date);
        if ($found) {
            $message = "Folder dengan tanggal $date berhasil dihapus.";
        } else {
            $message = "Folder dengan tanggal $date tidak ditemukan.";
        }
    } else {
        $message = "Harap masukkan tanggal.";
    }
}

// Ambil isi direktori saat ini
$contents = listContents($currentDir);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/footers/">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Directory Browser</title>
</head>

<body>
    <div class="container-fluid">
        <h1>Directory Browser Payload</h1>
        <div
            class="alert alert-primary"
            role="alert">
            <strong>Perhatian!</strong> Aplikasi ini dibuat hanya untuk kebutuhan menghapus secara recursive file dan folder sesuai dengan format tanggal : ddmmyyyy, setelah digunakan harap dihapus kembali agar tidak terjadi kesalahan penggunaan aplikasi ini.
        </div>
        <div
            class="alert alert-danger"
            role="alert">
            <strong>Penting!</strong> Jangan lupa untuk mengubah <b>$rootDir</b> ke folder root directory anda, untuk menghindari terjadi kesalahan penghapusan.
        </div>

        <!-- Tampilkan pesan -->
        <?php if (!empty($message)): ?>
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert">
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Tampilkan current directory -->
        <p>Direktori Sekarang : <span
                class="badge bg-primary"><?php echo htmlspecialchars($currentDir); ?></span>
        </p>

        <!-- Form untuk memilih tanggal dan menghapus folder berdasarkan tanggal -->
        <form method="POST" action="index.php?dir=<?php echo urlencode($currentDir); ?>">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="inputTanggal" class="col-form-label">Masukkan Tanggal : </label>
                </div>
                <div class="col-auto">
                    <input type="text" id="date" name="date" class="form-control" pattern="\d{8}" placeholder="10102024" aria-describedby="tanggalHelpInline" required>
                </div>
                <div class="col-auto">
                    <span id="tanggalHelpInline" class="form-text">
                        Format ddmmyyyy.
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-sm my-2 btn-danger" name="delete_by_date">Delete Folders by Date</button>
        </form>

        <?php if ($currentDir == $rootDir): ?>
            <p>Anda sudah berada di direktori root.</p>
        <?php else: ?>
            <a href="<?php echo createLink(dirname($currentDir), ''); ?>" class="btn btn-sm btn-link">&larr; Go Back</a>
        <?php endif; ?>

        <!-- Tampilkan daftar folder dan file -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#home">Tampilan Tabel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#menu1">Tampilan Hirarki</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane container active p-0 m-0" id="home">
                <div class="py-4">
                    <form method="POST" action="index.php?dir=<?php echo urlencode($currentDir); ?>">
                        <table border="1" class="table table-striped table-responsive w-100" style="width: 100%;">
                            <tr>
                                <th>Item Name</th>
                                <th>Type</th>
                            </tr>
                            <?php foreach ($contents as $item): ?>
                                <tr>
                                    <td>
                                        <?php if (isDirectory($currentDir . '/' . $item)): ?>
                                            <!-- Link untuk masuk ke dalam subdirektori -->
                                            <a href="<?php echo createLink($currentDir, $item); ?>">
                                                <?php echo htmlspecialchars($item); ?>
                                            </a>
                                        <?php else: ?>
                                            <!-- Hanya tampilkan nama file -->
                                            <?php echo htmlspecialchars($item); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Tampilkan apakah itu file atau direktori -->
                                        <?php echo isDirectory($currentDir . '/' . $item) ? 'Directory' : 'File'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </form>
                </div>
            </div>
            <div class="tab-pane container fade p-0 m-0" id="menu1">
                <div class="py-4">
                    <?php displayDirectoryContents($currentDir); ?>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button
                type="button"
                name="btnRefresh"
                id="btnRefresh"
                class="btn btn-warning" onclick="window.location.assign('<?= $base_url ?>')">
                Refresh
            </button>
        </div>
        <footer class="d-flex flex-wrap justify-content-center align-items-center py-3 border-top">
            <div class="col-md-12 d-flex justify-content-center align-items-center">
                <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                    <svg class="bi" width="30" height="24">
                        <use xlink:href="#bootstrap" />
                    </svg>
                </a>
                <span class="mb-3 mb-md-0 text-body-secondary">Made with &hearts; by Panda Developer.</span>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="./stub.js"></script>
</body>

</html>