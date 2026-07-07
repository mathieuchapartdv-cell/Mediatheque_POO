<?php
namespace Mediatheque;

use Override;

// ================================================================
//  CLASSE Article
//  Modele commun a Livre et Dvd.
//  "abstract" : impossible de faire new Article() directement.
//  Elle sert uniquement de base a l'heritage.
// ================================================================

abstract class Article {
    private ?int    $id = null;
    private string  $titre;
    private string  $auteur;
    private string  $annee;
    private bool    $disponible;

    public function __construct(
        string  $titre,
        string  $auteur,
        int     $annee,
        bool    $disponible = true
    )
        {
        // On passe par les setters des le constructeur
        // pour que les validations s'appliquent meme a la creation.
        $this->setTitre($titre);
        $this->setAnnee($annee);
        $this->auteur       = $auteur;
        $this->disponible   =$disponible;    
        }
    // ── Getters ──────────────────────────────────────────────────

    public function getId(): ?int       {return $this->id;}
    public function getTitre(): string  {return $this->titre;}
    public function getAuteur(): string {return $this->auteur;}
    public function getAnnee(): int     {return $this->annee;}
    public function isDisponible(): bool{return $this->disponible;}

    // ── Setters ──────────────────────────────────────────────────
    
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTitre(string $titre): void {
        if (trim($titre) === "") {
            throw new \InvalidArgumentException("Le titre ne peut pas être vide");
        }
        $this->titre = $titre;
    }

    public function setAuteur(string $auteur): void {
        $this->auteur = $auteur;
    }

    public function setAnnee(int $annee): void {
        $max = (int) date("Y");
        if ($annee < 1000 || $annee > $max) {
            throw new \InvalidArgumentException("L'année $annee n'est pas valide.");
        }
        $this->annee = $annee;
    }

    public function setDisponible(bool $disponible) : void {
        $this->disponible = $disponible;
    }

    //  Methodes abstraites 
    // Pas de code ici. Chaque classe fille DOIT les ecrire.

    abstract public function description(): string;
    abstract public function gettype(): string;
    
}


// ================================================================
//  CLASSE ArticleDAO
//  Gere les acces MySQL pour les articles.
//  Herite de DAO : find(), findAll(), create(), update(), delete()
//  sont fournis sans avoir a les reecrire.
//  Il suffit d'implementer hydrate() et dehydrate().
// ================================================================

class ArticleDAO extends DAO {
    protected string $table = "article";

    protected function hydrate(array $row): object {

    // On choisit la classe concrete selon la colonne "type"

    if ($row["type"] === "livre") {
        $article = new Livre(
            $row["titre"],
            $row["auteur"],
            (int) $row["annee"],
            (bool) $row["disponible"]
            );
        } else {
            $article = new Dvd(
                $row["titre"],
                $row["auteur"],
                (int) $row["annee"],
                (bool) $row["disponible"]
            );
        }

        $article->setId((int) $row["id"]);
        return $article;
    }
    #[Override]
    protected function dehydrate(object $entite): array
    {
        return [
            "type"      => $entite->getType(),
            "titre"     => $entite->getTitre(),
            "auteur"    => $entite->getAuteur(),
            "annee"     => $entite->getAnnee(),
            "disponible"=> $entite->isDisponible() ? 1 : 0,
            ];
    }
}

?>