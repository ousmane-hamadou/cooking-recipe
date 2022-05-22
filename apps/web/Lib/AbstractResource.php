<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Lib;

/**
 * Elle définit les les méthodes nécessaires pour fournir un niveau d'abstraction permettant
 * de travailler facilement des controllers avec le protocole http.
 */
abstract class AbstractResource
{
    /**
     * Affiche une liste de tous les éléments du type de ressource donné
     */
    public function index(Conn $conn, mixed $opts): Conn
    {
        return $conn
            ->setRespBody('Hello from ' . $_SERVER['REQUEST_URI'])
            ->putStatus(200);
    }

    /**
     * Rend un élément individuel par ID
     */
    public function show(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }

    /**
     * Rend un formulaire pour créer un nouvel élément
     */
    public function new(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }

    /**
     *  Récupère un élément individuel par ID et l'affiche dans un formulaire pour modification
     */
    public function edit(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }

    /**
     *  Reçoit les paramètres d'un nouvel élément et l'enregistre dans un magasin de données
     */
    public function create(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }

    /**
     * Reçoit les paramètres d'un élément modifié et enregistre l'élément dans un magasin de données
     */
    public function update(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }

    /**
     * Reçoit un ID pour un élément à supprimer et le supprime d'un magasin de données
     */
    public function delete(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(501);
    }
}