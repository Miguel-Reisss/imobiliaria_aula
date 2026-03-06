<?php

require_once(__DIR__ . '/../config/conexao.php');

class Corretor
{

    private int $id;
    private string $creci;
    private string $telefone;
    private string $whatsapp;
    private bool $ativo;


    public function __construct(


        ?int $id = 0,
        string $creci,
        string $telefone,
        string $whatsapp,
        ?bool $ativo = true
    ) {
        $this->id = $id;
        $this->creci = $creci;
        $this->telefone = $telefone;
        $this->whatsapp = $whatsapp;
        $this->ativo = $ativo;
    }


    public function __get(string $prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
        throw new Exception("Propriedade {$prop} não existe.");
    }

    public function __set(string $prop, $valor)
    {
        switch ($prop) {
            case "id":
                $this->id = (int) $valor;
                break;
            case "creci":
                $this->creci = trim($valor);
                break;
            case "telefone":
                $this->telefone = trim($valor);
                break;
            case "whatsapp":
                $this->whatsapp = trim($valor);
                break;
            case "ativo":
                $this->ativo = (bool)$valor;
                break;    
            default:
                throw new Exception("Propriedade {$prop} não permitida.");
        }
    }

    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

   public static function listarImoveis(): array {
        // placeholder implementation – return empty list
        return [];
    }
    public static function listarVisitas(int $idCorretor): array {
        // placeholder implementation – return empty list
        return [];
    }
    public static function confirmarVisita(int $idVisita): bool {
        // placeholder implementation – always false
        return false;
    }
    public static function Desativar(int $idCorretor): void {
        // nothing to return for void method
    }
    public static function Reativar(int $idCorretor): void {
        // nothing to return for void method
    }
}

$corretor = new Corretor(

    creci: 102030,
    telefone: 11999998888,
    whatsapp: 11888889999,
    ativo: false
)

?>
