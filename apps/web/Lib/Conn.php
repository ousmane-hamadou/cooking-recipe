<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Lib;


/**
 * Cette class définit une structure et les principales fonctions pour travailler avec
 * des requêtes et des réponses dans une connexion HTTP.
 */
final class Conn
{
    private array $params;
    /**
     * Contenu renvoyer au client
     *
     * @var string
     */
    private ?string $respBody = null;

    /**
     * Entetes de reponse.
     *
     * @var array
     */
    private array $respHeader = [];

    /**
     * Gesionnaire associe a un chemin mis en correspondance avec success
     * [$plug, $opts]
     */
    private ?array $handler = null;

    /**
     * Donnees utilisateurs partages entre les plugs sous forme de tableau associatif.
     *
     * $assings destiné à être utilisé pour stocker des valeurs dans la connexion afin que les autres
     * plug-ins de votre pipeline de plug-ins puissent y accéder.
     *
     * @var array
     */
    private array $assigns = [];

    /**
     * Le status indiquant si le pipeline de plug a ete arrete.
     *
     * @var bool;
     */
    private bool $halted = false;

    //-------------------------------------------------------

    private function checkConnState()
    {
        if (\headers_sent()) {
            throw new \RuntimeException('Request already proccessed');
        }
    }

    //-------------------------------------------------------

    public function __construct()
    {
        $this->params = [...$_GET, ...$_POST];
    }

    public function getRespBody()
    {
        return $this->respBody;
    }

    public function getHandler(): ?array
    {
        return $this->handler;
    }

    /**
     * Renvoi le valeur de HTTP_ACCEPT ou une chaine vide.
     *
     * @return sting
     */
    public function acceptHeader(): string
    {
        return $_SERVER['HTTP_ACCEPT'] ?? '';
    }


    /**
     * Renvoi l'hote demandé. Cette valeur peut etre un chaine vide.
     *
     * @return string
     */
    public function host(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    /**
     * Renvoi la methode HTTP associée a la requete.
     *
     * @return string
     */
    public function requestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Renvoi la valeur de l'entete associe a une cle.
     *
     * @param string $key une cle d'entete
     *
     * @return array
     */
    public function requestHeader(string $key): array
    {
        $header = [];

        if (isset($_SERVER[$key])) {
            $header[] = $_SERVER[$key];
        }

        return $header;
    }

    /**
     * Alias au superglobal {@see $_POST}.
     *
     * @return array
     */
    public function bodyParams(): array
    {
        return $_POST;
    }

    /**
     * Alias au superglobal {@see $_GET}.
     *
     * @return array
     */
    public function queryParams(): array
    {
        return $_GET;
    }

    /**
     * Merge au superglobal {@see $_GET} with {@see $_POST}.
     *
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Search key value in {@see $_POST}, {@see $_GET}, otherwise `null`
     */
    public function getParam(string $key): mixed
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        if (isset($_GET[$key])) {
            return $_GET[$key];
        }

        return null;
    }

    public function setRespBody(string $body): Conn
    {
        $this->checkConnState();
        $this->respBody = $body;
        return $this;
    }

    public function setHandler(array $handler): Conn
    {
        $this->handler = $handler;
        return $this;
    }



    /**
     * Un wrapper autour de {@see session_destroy}
     *
     * @return Conn
     */
    public function clearSession(): Conn
    {
        \session_destroy();
        return $this;
    }

    /**
     * Supprime une cle et sa valeur de la session.
     *
     * @param string $key une cle de session
     *
     * @return Conn
     */
    public function deleteSession(string $key): Conn
    {
        unset($_SESSION[$key]);
        return $this;
    }

    /**
     * Attribue plusieurs valeurs aux donnees utilisateurs de la connexion.
     *
     * @param array donnees utilisateurs sous forme de tableau associatif.
     *
     * @return Conn
     */
    public function assigns(array ...$ass): Conn
    {
        $this->assings = [...$this->assings, ...$ass];
        return $this;
    }

    /**
     * Met le type de contenu de reponse.
     *
     * @param string $value type de contenu renvoyer au client
     *
     * @throws \Exception\AlreadySentException
     *
     * @return Conn
     */
    public function putRespContentType(string $value): Conn
    {
        $this->checkConnState();
        $this->respHeader['Content-Type'] = $value;
        return $this;
    }

    /**
     * Ajoute une entete de reponse.
     *
     * @param string $key
     * @param string $value
     *
     * @throws \Exception\AlreadySentException
     *
     * @return Conn
     */
    public function putRespHeader(string $key, string $value): Conn
    {
        $this->checkConnState();
        $this->respHeader[$key] = $value;
        return $this;
    }

    /**
     * Wrapper autour de {@see setcookie}
     *
     * @param string $name 
     * @param mixed $value
     * @param int $maxAge
     * @param array opts$
     *
     * @throws \Exception\AlreadySentException
     *
     * @return Conn
     */
    public function putRespCookie(
        string $name,
        mixed $value,
        int $maxAge,
        array $opts = [],
    ): Conn {
        $this->checkConnState();
        \setcookie($name, $value, ['expires' => $maxAge, ...$opts]);
        return $this;
    }

    public function putSession(string $key, mixed $value): Conn
    {
        $this->checkConnState();
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Set HTTP status code
     */
    public function putStatus(int $code): Conn
    {
        $this->checkConnState();
        \http_response_code($code);
        return $this;
    }

    public function halt(): Conn
    {
        $this->halted = true;
        return $this;
    }

    public function halted()
    {
        return $this->halted;
    }

    public function getAssign(string $key): ?string
    {
        return $this->assigns[$key] ?? null;
    }

    public function getAssigns(): array
    {
        return $this->assigns;
    }

    public function getRespHeaders(): array
    {
        return $this->respHeader;
    }
}
