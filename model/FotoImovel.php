<?php
require_once(__DIR__ . "/../config/conexao.php");

class FotoImovel
{

    private ?int $id_foto;
    private int $id_imovel;
    private string $caminho;
    private bool $destaque;
    private ?int $ordem;

    public function __construct(
        ?int $id_foto = 0,
        int $id_imovel,
        string $caminho,
        bool $destaque,
        ?int $ordem,
    ) {
        $this->id_foto = $id_foto;
        $this->id_imovel = $id_imovel;
        $this->caminho = $caminho;
        $this->destaque = $destaque;
        $this->ordem = $ordem;
    }

    public function __get(string $prop)
    {
        if (!property_exists($this, $prop)) {
            return $this->$prop;
        }
        throw new Exception("Propriedade $prop não existe.");
    }

    public function __set(string $prop, $valor)
    {
        if (property_exists($this, $prop)) {
            switch ($prop) {
                case "id_foto":
                    $this->id_foto = (int)$valor;
                    break;
                case "id_imovel":
                    $this->id_imovel = (int)$valor;
                    break;
                case "destaque":
                    $this->destaque = (bool)$valor;
                    break;
                case "ordem":
                    $this->ordem = (int)$valor;
                    break;
                default:
                    throw new Exception("Propriedade $prop não existe.");
            }
        }
    }

    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public function salvar()
    {
        $pdo = self::getConexao();
        
        // Se for definir como principal, desmarca as outras fotos deste imóvel primeiro
        if ($this->destaque) {
            $sqlReset = "UPDATE fotos_imovel SET destaque = 0 WHERE id_imovel = :id_imovel";
            $stmtReset = $pdo->prepare($sqlReset);
            $stmtReset->execute([':id_imovel' => $this->id_imovel]);
        }

        $sql = "INSERT INTO fotos_imovel (id_imovel, caminho, destaque) 
                VALUES (:id_imovel, :caminho, :destaque)";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id_imovel' => $this->id_imovel,
            ':caminho' => $this->caminho,
            ':destaque' => (int)$this->destaque
        ]);
    }

    public static function buscarPorImovel(int $id_imovel){

        $pdo = self::getConexao();
        $stmt = $pdo->prepare("SELECT * FROM imoveis_fotos WHERE id_imovel = ?");
        $stmt->execute([$id_imovel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }
}

// $fotoImovel = new FotoImovel(
//     id_foto: null,
//     id_imovel: 1,
//     caminho: "c:System 32",
//     destaque: true,
//     ordem: 1,
// );

// echo "<pre>";
// print_r($fotoImovel);

// $fotoImovel->salvar();


