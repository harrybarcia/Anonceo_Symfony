<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate

{
/**
 * @Assert\NotBlank(message="Veuillez saisir votre ancien mot de passe")
 */

    private $oldPassword;

 /**
  * @Assert\NotBlank(message="Veuillez saisir votre nouveau mot de passe")
  * @Assert\EqualTo(
  * propertyPath="confirmPassword",
  * message="Les mots de passe ne sont pas identiques")
  *
  */

    private $newPassword;

 /**
  * @Assert\NotBlank(message="Veuillez confirmer votre mot de passe")
  */

    private $confirmPassword;

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword) //:self concerne la pté elle meme
    {
    
       return $this->oldPassword = $oldPassword;

    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword)
    {
        return $this->newPassword = $newPassword;

    }

    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword($confirmPassword)
    {
        return $this->confirmPassword = $confirmPassword;

    }
}
