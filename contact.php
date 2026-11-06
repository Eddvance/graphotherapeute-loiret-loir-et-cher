<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}


if (isset($_POST["website"]) && $_POST["website"] !== "") {
    http_response_code(200);
    exit;
}

function validationError(string $message): void
{
    http_response_code(400);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "success" => false,
        "message" => $message
    ]);

    exit;
}

foreach (["firstname", "lastname", "email", "phone"] as $field) {
    if (isset($_POST[$field]) && !is_string($_POST[$field])) {
        http_response_code(400);
        validationError("Donnes invalides.");
    }
}

$firstname = trim($_POST["firstname"] ?? "");
$lastname = trim($_POST["lastname"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");

if (
    strlen($firstname) > 50 || strlen($lastname) > 100 || strlen($email) > 254 || strlen($phone) > 20
) {
    http_response_code(400);
    validationError("les donnees saisies sont trop longues.");
}

foreach ([$firstname, $lastname, $email, $phone] as $value) {
    if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
        http_response_code(400);
        validationError("Caracteres non autorises.");
    }
}

if ($firstname == "" || $lastname == "") {
    http_response_code(400);
    validationError("Prénom et nom obligatoires.");
}

if ($email == "" && $phone == "") {
    http_response_code(400);
    validationError("Veuillez renseigner un mail ou un téléphone.");
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    validationError("Adresse mail invalide");
}

$phoneDigits = preg_replace('/[ .-]/', '', $phone);

if ($phone !== "" && !preg_match('/^0[1-9][0-9]{8}$/', $phoneDigits)) {
    http_response_code(400);
    validationError("numero de téléphone invalide.");
}

$to = "contact@graphotherapeute-loiret-loir-et-cher.fr";
$subject = "Nouvelle demande de contact";

$message = "Prénom : " . $firstname . "\n";
$message .= "Nom : " . $lastname . "\n";
$message .= "Email : " . ($email !== "" ?  $email : "Non renseigné") . "\n";
$message .= "Téléphone : " . ($phone !== "" ? $phone : "Non renseigné");

$headers = "From: contact@graphotherapeute-loiret-loir-et-cher.fr\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

if (!mail($to, $subject, $message, $headers)) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "success" => false,
        "message" => "Une erreur est survenue lors de l'envoi"
    ]);

    exit;
}

http_response_code(200);
header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "success" => true,
    "message" => "Votre demande a bien été envoyée"
]);

exit;
