<?php
/* 1665806-Programmez-en-oriente-objet-PHP
*TP P2C2
*Dans cet exercice, le code de la salle d'attente (la classe Lobby) existe ainsi que le code d'un joueur (la classe Player). Lorsqu'un joueur s'enregistre dans le Lobby, il devient un *Joueur en Attente. Un joueur en attente possède une propriété range qui est un entier. Le but de cette propriété, est d'accroitre la portée de la recherche d'un adversaire, lorsqu'aucun *ne correspond au niveau du joueur. Le but étant de trouver un adversaire quitte à ce qu'il soit plus faible ou plus fort.

*Votre tâche est de créer une classe QueuingPlayer qui étends la classe Player. Et de lui ajouter la propriété range. */

declare(strict_types=1);

class Lobby
{
    /** @var array<AGPlayer> */
    public array $agPlayers = [];

    public function findOponents(AGPlayer $player): array
    {
        $minLevel = round($player->getRatio() / 100);
        $maxLevel = $minLevel + $player->getRange();

        return array_filter($this->agPlayers, static function (AGPlayer $potentialOponent) use ($minLevel, $maxLevel, $player) {
            $playerLevel = round($potentialOponent->getRatio() / 100);

            return $player !== $potentialOponent && ($minLevel <= $playerLevel) && ($playerLevel <= $maxLevel);
        });
    }

    public function addPlayer(Player $player): void
    {
        $this->agPlayers[] = new AGPlayer($player);
    }

    public function addPlayers(Player ...$players): void
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
    }
}

class Player
{
    public function __construct(protected string $name, protected float $ratio = 400.0)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function probabilityAgainst(self $player): float
    {
        return 1 / (1 + (10 ** (($player->getRatio() - $this->getRatio()) / 400)));
    }

    public function updateRatioAgainst(self $player, int $result): void
    {
        $this->ratio += 32 * ($result - $this->probabilityAgainst($player));
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }
}

class QueuingPlayer extends Player
{
    public function __construct(Player $player, protected int $range = 1)
    {
        parent::__construct($player->getName(), $player->getRatio());
    }

    public function getRange(): int
    {
        return $this->range;
    }

    public function upgradeRange(): void
    {
        $this->range = min($this->range + 1, 40);
    }
}

$greg = new Player('greg', 400);
$jade = new Player('jade', 476);

$lobby = new Lobby();
$lobby->addPlayers($greg, $jade);

var_dump($lobby->findOponents($lobby->agPlayers[0]));

exit(0);
