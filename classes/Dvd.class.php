<?php

namespace Mediatheque;

use Override;

namespace Mediatheque;

class Dvd extends Article implements Empruntable
{
    // Polymorphisme par heritage :
    // Dvd ecrit SA version de description(), differente de Livre.

    public function description(): string
    {
        return "DVD « " . $this->getTitre() . " »"
             . ", réalisé par " . $this->getAuteur()
             . " (" . $this->getAnnee() . ")";
    }

    public function getType(): string
    {
        return 'dvd';
    }

    public function emprunter(): void
    {
        if (!$this->isDisponible()) {
            throw new \RuntimeException("Ce DVD est déjà emprunté.");
        }
        $this->setDisponible(false);
    }

    public function rendre(): void
    {
        $this->setDisponible(true);
    }
}

