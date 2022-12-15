
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') :
    http_response_code(405);
    echo json_encode([
        'status' => 0,
        'message' => 'Invalid Request Method. HTTP method should be GET',
    ]);
    exit;
endif;

require '../Database.php';
$database = new Database();
$conn = $database->dbConnection();
$post_id = null;

if (isset($_GET['id'])) {
    $post_id = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 'all_posts',
            'min_range' => 1
        ]
    ]);
}

try {

    $sql = "SELECT * FROM etudiants";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    if ($stmt->rowCount() > 0) :

        $data = null;
        if (is_numeric($post_id)) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode([
            'status' => 1,
            'data' => $data,
        ]);

    else :
        echo json_encode([
            'status' => 0,
            'message' => 'Aucun resultat !',
        ]);
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}