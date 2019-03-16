<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CotationRepository")
 */
class Cotation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $valeur;

    /**
     * @ORM\Column(type="float")
     */
    private $cours;

    /**
     * @ORM\Column(type="float")
     */
    private $evolution;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cryptos", inversedBy="cotations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cryptos;

    
   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getCours(): ?float
    {
        return $this->cours;
    }

    public function setCours(float $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function getEvolution(): ?float
    {
        return $this->evolution;
    }

    public function setEvolution(float $evolution): self
    {
        $this->evolution = $evolution;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCryptos(): ?Cryptos
    {
        return $this->cryptos;
    }

    public function setCryptos(?Cryptos $cryptos): self
    {
        $this->cryptos = $cryptos;

        return $this;
    }

   

   

    

}
