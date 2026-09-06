<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}


$firstname = trim($_POST["firstname"] ?? "");
$lastname = trim($_POST["lastname"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");


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
    http_response_code(500);
    exit("Erreur lors de l'envoi.");
}

echo "Merci, votre demande a bien ete envoyée.";
header("Refresh: 2; url=/");
