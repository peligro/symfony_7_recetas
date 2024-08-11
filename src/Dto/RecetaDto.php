<?php 
namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;
class RecetaDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'El campo nombre está vacío')]
        #[Assert\Length(
            min: 5,
            minMessage: 'El campo nombre debe tener al menos 5 caracteres',
        )]
        #[Assert\Type('string')]
        public readonly string $nombre,
        #[Assert\NotBlank(message: 'El campo tiempo está vacío')]
        #[Assert\Length(
            min: 3,
            minMessage: 'El campo tiempo debe tener al menos 3 caracteres',
        )]
        public readonly string $tiempo,
        #[Assert\NotBlank(message: 'El campo detalle está vacío')]
        #[Assert\Length(
            min: 5,
            minMessage: 'El campo detalle debe tener al menos 5 caracteres',
        )]
        #[Assert\Type('string')]
        public readonly string $detalle ,
        #[Assert\Positive(message: 'El campo número debe ser numérico')]
        #[Assert\Type('number')]
        public readonly int $categoria_id 

       
    ) {
    
    }
}