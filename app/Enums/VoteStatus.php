<?php

namespace App\Enums;

/**
 * Statuts possibles d'un vote.
 *
 * Valeurs (string-backed enum) :
 * - PASSE => 'passé'
 * - EN_COURS => 'en cours'
 * - A_VENIR => 'à venir'
 */
enum VoteStatus: string
{
    case PASSE = 'passé';
    case EN_COURS = 'en cours';
    case A_VENIR = 'à venir';

    /**
     * Retourne tous les codes (valeurs) sous forme de tableau.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn(VoteStatus $s) => $s->value, self::cases());
    }

    /**
     * Retourne une représentation lisible (label) — ici identique à la valeur.
     */
    public function label(): string
    {
        return $this->value;
    }
}
