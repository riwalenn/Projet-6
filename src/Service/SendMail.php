<?php

namespace App\Service;


use App\Entity\User;

class SendMail
{
    public function sendToken(User $user, $typeMessage)
    {
        if (empty($user)) :
            echo "Le token ou l'email sont manquants !";
            return false;
        endif;
        $email = strip_tags(htmlspecialchars($user->getEmail()));
        $token = strip_tags(htmlspecialchars($user->getToken()));
        $pseudo = strip_tags(htmlspecialchars($user->getUsername()));
        $sujet = "Confirmation de votre inscription sur le site communautaire Snow Tricks";

        if ($typeMessage == "inscription") :
            $message = "Bonjour $pseudo. Pour confirmer votre inscription, veuillez cliquer sur le lien ci-dessous :\n\n" . "https://127.0.0.1:8000/confirmation?token=$token";
        elseif ($typeMessage == "oubli") :
            $message = "Bonjour $pseudo. Pour modifier votre mot de passe, veuillez cliquer sur le lien ci-dessous :\n\n" . "https://127.0.0.1:8000/iForgotMyPassword?token=$token";
        endif;
        $to = $email;
        $email_subject = "$sujet";
        $email_body = "$message";
        $headers = "From: noreply@riwalennbas.com\n";
        $headers .= "Reply-to: $email";
        mail($to, $email_subject, $email_body, $headers);
        return true;
    }
}