<?php

require_once(__DIR__ . '/../config/conexao.php');

class Perfil
{

    private int $id;
    private string $nome;


    public function __construct(


        ?int $id = 0,
        string $nome
    ) {
        $this->id = $id;
        $this->nome = $nome;
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
            case "nome":
                $this->nome = trim($valor);
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida.");
        }
    }


    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public static function listar()
    {
        $pdo = self::getConexao();

        $sql = "SELECT * FROM perfis";

        $stmt = $pdo->query($sql);

        $perfil = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $perfil[] = new Perfil(
                id: (int) $row['id_perfil'],
                nome: $row['nome_perfil']
            );
        }
        return $perfil;
    }


    public static function buscarPorId(int $id)
    {

        $pdo = self::getConexao();

        $sql = "SELECT * FROM perfil WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Perfil(
                id: (int) $row['id'],
                nome: $row['nome']
            );
        }

        if ($stmt->rowCount() === 0) {
            throw new Exception("Perfil com ID {$id} não encontrado.");
        }

        return null;
    }
}


$perfil1 = new Perfil(
    nome: "Atendente");


$perfil2 = new Perfil(
    nome: "corretor2");


try {

    print_r(Perfil::listar());
} catch (Exception $err) {
    echo $err->getMessage();
}
