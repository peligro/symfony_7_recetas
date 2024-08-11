<?php 
namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
//composer require symfony/serializer-pack
//composer require symfony/validator
class CategoriaDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'El campo nombre está vacío')]
        #[Assert\Length(
            min: 5,
            minMessage: 'El campo nombre debe tener al menos 5 caracteres',
        )]
        #[Assert\Type('string')]
        public readonly string $nombre,

       
    ) {
    
    }
}