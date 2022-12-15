<?php.01nbv cxw
header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Invalid Request Method. HTTP method should be PUT',
    ]);
    exit;
endif;

require '../Database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please provide the post ID.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `etudiants` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $matricule = isset($data->matricule) ? $data->matricule : $row['matricule'];
        $nom = isset($data->nom) ? $data->nom : $row['nom'];
        $prenom = isset($data->prenom) ? $data->prenom : $row['prenom'];
        $filiere = isset($data->filiere) ? $data->filiere : $row['filiere'];
        $note = isset($data->note) ? $data->note : $row['note'];
        

        $update_query = "UPDATE `etudiants` SET matricule = :matricule, nom = :nom, prenom = :prenom, filiere = :filiere, 
        note = :note
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':matricule', htmlspecialchars(strip_tags($matricule)), PDO::PARAM_STR);
        $update_stmt->bindValue(':nom', htmlspecialchars(strip_tags($nom)), PDO::PARAM_STR);
        $update_stmt->bindValue(':prenom', htmlspecialchars(strip_tags($prenom)), PDO::PARAM_STR);
        $update_stmt->bindValue(':filiere', htmlspecialchars(strip_tags($filiere)), PDO::PARAM_STR);
        $update_stmt->bindValue(':note', htmlspecialchars(strip_tags($note)), PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Mis à jour avec succès'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Echec :: Quelque chose ne va pas!.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'ID invalide. Aucune donnée trouvée par l\'ID.']);
        exit;
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}