<?php

// Include the paginate() function
function paginate($current_page, $total_records, $limit, $base_url, $max_pages_to_display = 4) {
    $total_pages = ceil($total_records / $limit);
    $pagination = '<div class="pagination">';

    // Link para a página anterior
    if ($current_page > 1) {
        $pagination .= '<a href="' . $base_url . '?page=' . ($current_page - 1) . '">&laquo; Anterior</a>';
    }

    // Link para a primeira página
    if ($current_page > 1) {
        $pagination .= '<a href="' . $base_url . '?page=1">Primeiro</a>';
    }

    // Início e fim dos links de páginas individuais
    $start = max(1, $current_page - floor($max_pages_to_display / 2));
    $end = min($total_pages, $start + $max_pages_to_display - 1);

    // Ajustar o início caso o fim esteja no limite
    $start = max(1, $end - $max_pages_to_display + 1);

    // Mostrar "..." antes dos números das páginas, se necessário
    if ($start > 1) {
        $pagination .= '<span>...</span>';
    }

    // Links para as páginas individuais
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $pagination .= '<span class="active">' . $i . '</span>';
        } else {
            $pagination .= '<a href="' . $base_url . '?page=' . $i . '">' . $i . '</a>';
        }
    }

    // Mostrar "..." após os números das páginas, se necessário
    if ($end < $total_pages) {
        $pagination .= '<span>...</span>';
    }

    // Link para a última página
    if ($current_page < $total_pages) {
        $pagination .= '<a href="' . $base_url . '?page=' . $total_pages . '">Último</a>';
    }

    // Link para a próxima página
    if ($current_page < $total_pages) {
        $pagination .= '<a href="' . $base_url . '?page=' . ($current_page + 1) . '">Próximo &raquo;</a>';
    }

    $pagination .= '</div>';

    return $pagination;
}

// Define database connection constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'database_name');
define('DB_USER', 'root');
define('DB_PASS', '');

// Set the current page number
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Set the number of records to display per page
$limit = 1;

// Calculate the offset for the query based on the current page number and the limit
$offset = ($current_page - 1) * $limit;

// Create a new PDO object for database connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}

// Execute a SELECT query with LIMIT and OFFSET clauses to retrieve the data for the current page
try {
$stmt = $pdo->prepare("SELECT * FROM tbl_fotos_exclusivas LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Define the column header labels manually
$header_labels = array(
    'id_anunciante' => 'ID',
    'nome' => 'Nome',
    'email' => 'Foto Perfil',
    'telefone' => 'Email',
    'login' => 'Login',
    'password' => "Password",
    'telefone' => "Telefone",
    'celular' => "Celular"

);

// Output the results in an HTML table with CSS styling
echo '<table style="border-collapse: collapse; width: 100%; max-width: 800px; margin: auto;">';

// Output the column headers with CSS styling
echo '<tr style="background-color: #dddddd;">';
foreach ($header_labels as $label) {
    echo '<th style="padding: 8px; border: 1px solid #dddddd; text-align: left;">' . htmlspecialchars($label) . '</th>';
}
echo '</tr>';

// Output the data rows with CSS styling
foreach ($result as $row) {
    echo '<tr>';
    foreach ($row as $value) {
        echo '<td style="padding: 8px; border: 1px solid #dddddd;">' . htmlspecialchars($value) . '</td>';
    }
    echo '</tr>';
}

echo '</table>';

// Output the pagination links with CSS styling
$total_records = $pdo->query("SELECT COUNT(*) FROM tbl_anunciantes")->fetchColumn();
$base_url = $_SERVER['PHP_SELF'];
echo paginate($current_page, $total_records, $limit, $base_url);
} catch(PDOException $e) {
die("ERROR: Could not execute query. " . $e->getMessage());
}

// Close the database connection
$pdo = null;

?>
<style>
.pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin: 1em 0;
}

.pagination a,
.pagination span {
    display: inline-block;
    margin: 0 3px;
    padding: 5px 10px;
    border-radius: 3px;
    background-color: #e8e8e8;
    text-decoration: none;
    color: #3a3a3a;
}

.pagination a:hover {
    background-color: #b8b8b8;
}

.pagination a:active {
    background-color: #a8a8a8;
}

.pagination .active {
    background-color: #ff5e5b;
    color: #fff;
}

.pagination a[disabled] {
    background-color: #e8e8e8;
    color: #888;
    pointer-events: none;
}
</style>
