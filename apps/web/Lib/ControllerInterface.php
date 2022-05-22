<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Lib;

/**
 * Definie une interface de base niveau pour traiter une requete web
 */
interface ControllerInterface
{
    /**
     * Sert à initialiser les options de notre controllers. Cette methode ne sera appele qu'une
     * seule fois par demande.
     *
     * La valeur renvoyée par sera transmise comme deuxieme argument de `service()` comme second argument.
     *
     * @param mixed $opts valeur d'initialiastion
     *
     * @return mixed
     */
    public function init(mixed $opts): mixed;

    /**
     * A chaque nouvelle requête provenant du serveur Web, cette methode est execute une fois.
     *
     * @param Conn $conn represente la connexion de la requete actuelle
     * @param mixed $opts valeur renvoyee par `init()`
     *
     * @return PlugConn
     */
    public function service(Conn $conn, $opts): mixed;
}
