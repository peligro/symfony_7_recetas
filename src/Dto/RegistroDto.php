<?php
namespace App\Dto; 
use Symfony\Component\Validator\Constraints as Assert;
class RegistroDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'El campo nombre está vacío')]
        #[Assert\Type('string')]
        public readonly string $nombre,
        #[Assert\NotBlank(message: 'El campo E-Mail está vacío')]
        #[Assert\Email(message: 'El E-Mail ingresado no tiene un formato válido')]
        public readonly string $correo,
        #[Assert\NotBlank(message: 'El campo password está vacío')]
        public readonly string $password 
    ) {
    
    }
}