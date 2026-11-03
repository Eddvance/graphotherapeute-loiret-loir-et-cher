<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

if (!empty($_POST["website"])) {
    header("Location: /contact.html?sent=1");
    exit;
}

foreach (["firstname", "lastname", "email", "phone"] as $field) {
    if (isset($_POST[$field]) && !is_string($_POST[$field])) {
        http_response_code(400);
        exit("Donnes invalides.");
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
    exit("les donnees saisies sont trop longues.");
}

foreach ([$firstname, $lastname, $email, $phone] as $value) {
    if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
        http_response_code(400);
        exit("Caracteres non autorises.");
    }
}

if ($firstname == "" || $lastname == "") {
    http_response_code(400);
    exit("Prénom et nom obligatoires.");
}

if ($email == "" && $phone == "") {
    http_response_code(400);
    exit("Veuillez renseigner un mail ou un téléphone.");
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit("Adresse mail invalide");
}

$phoneDigits = str_replace(" ", "", $phone);

if ($phone !== "" && !preg_match('/^0[1-9][0-9]{8}$/', $phoneDigits)) {
    http_response_code(400);
    exit("numero de téléphone invalide.");
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
    header("Location: /contact.html?sent=0");
    exit;
}

header("Location: /contact.html?sent=1");
exit;

