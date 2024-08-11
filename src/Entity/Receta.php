<?php

namespace App\Entity;

use App\Repository\RecetaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetaRepository::class)]
class Receta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(length: 50)]
    private ?string $tiempo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $detalle = null;

    #[ORM\Column(length: 100)]
    private ?string $foto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\ManyToOne]
    private ?Usuarios $usuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTiempo(): ?string
    {
        return $this->tiempo;
    }

    public function setTiempo(string $tiempo): static
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    public function getDetalle(): ?string
    {
        return $this->detalle;
    }

    public function setDetalle(string $detalle): static
    {
        $this->detalle = $detalle;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUsuario(): ?Usuarios
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuarios $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
