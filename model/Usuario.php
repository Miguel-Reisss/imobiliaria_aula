<?php

require_once(__DIR__ . '/../config/conexao.php');

class Usuario
{

    private int $id;
    private string $nome;
    private string $email;
    private string $senhaHash;
    private int $idPerfil;
    private bool $ativo;

    public function __construct(


        ?int $id = 0,
        string $nome,
        string $email,
        string $senhaHash,
        int $idPerfil,
        ?bool $ativo = true
    ) {

        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash =  password_hash($senhaHash, PASSWORD_DEFAULT);
        $this->idPerfil = $idPerfil;
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
            case "nome":
                $this->nome = trim($valor);
                break;
            case "email":
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email inválido: {$valor}");
                }
                $this->email = $valor;
                break;
            case "senhaHash":
                $this->senhaHash = password_hash($valor, PASSWORD_DEFAULT);
                break;
            case "idPerfil":
                $this->idPerfil = (int) $valor;
                break;
            case "ativo":
                $this->ativo = (bool) $valor;
                break;
            case "perfilNome":
                $this->perfilNome = $valor;
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida.");
        }
    }

    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public function inserir()
    {
        $pdo = self::getConexao();


        $sql = "INSERT INTO `usuarios` (`nome`, `email`, `senha`, `id_perfil`, `ativo`) VALUES (:nome, :email, :senhaHash, :idPerfil, :ativo)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':senhaHash' => $this->senhaHash,
            ':idPerfil' => $this->idPerfil,
            ':ativo' => $this->ativo,
        ]);

        $ultimoId = $pdo->lastInsertId();

        if ($ultimoId <= 0) {
            throw new Exception("Não foi possível inserir o usuário.");
        }

        return $ultimoId;
    }

    public static function listar()
    {
        $pdo = self::getConexao();


        $sql = "SELECT u.id_usuario,
         u.nome,
          u.email,
           u.ativo,
            u.id_perfil,
             p.nome_perfil AS perfil_nivel
                FROM usuarios AS u
                INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
                ORDER BY u.nome";

        $stmt = $pdo->query($sql);

        $usuarios = [];



        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(
                id: $row['id_usuario'],
                nome: $row['nome'],
                email: $row['email'],
                senhaHash: "",
                idPerfil: $row['id_perfil'],
                ativo: (bool)$row['ativo']
            );



            $usuario->perfilNome = $row['perfil_nivel'];

            array_push($usuarios, $usuario);
        }

        return $usuarios;
    }


    public static function listarPorId(int $id)
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario,
         u.nome,
          u.email,
           u.ativo,
            u.id_perfil,
             p.nome_perfil AS perfil_nivel
                FROM usuarios AS u
                INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
                WHERE u.id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("ID não encontrado.");
        }

        $usuario = new Usuario(
            id: $row['id_usuario'],
            nome: $row['nome'],
            email: $row['email'],
            senhaHash: "",
            idPerfil: $row['id_perfil'],
            ativo: (bool)$row['ativo']
        );

        $usuario->perfilNome = $row['perfil_nivel'];

        return $usuario;
    }

    public static function listarPorEmail(string $email)
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario,
         u.nome,
          u.email,
           u.ativo,
            u.id_perfil,
             p.nome_perfil AS perfil_nivel
                FROM usuarios AS u
                INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
                WHERE u.email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Email não encontrado.");
        }

        $usuario = new Usuario(
            id: $row['id_usuario'],
            nome: $row['nome'],
            email: $row['email'],
            senhaHash: "",
            idPerfil: $row['id_perfil'],
            ativo: (bool)$row['ativo']
        );

        $usuario->perfilNome = $row['perfil_nivel'];

        return $usuario;
    }

    public static function listarPorSenha(string $senha)
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario,
                 u.nome,
                  u.email,
                   u.senha, 
                    u.ativo,
                     u.id_perfil,
                      p.nome_perfil AS perfil_nivel
                        FROM usuarios AS u
                        INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil";

        $stmt = $pdo->query($sql);

        // Passa por cada usuário retornado do banco
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // Testa se a senha em texto bate com o hash daquele usuário específico
            if (password_verify($senha, $row['senha'])) {

                // Se bater, monta o objeto e já retorna ele (interrompendo a busca)
                $usuario = new Usuario(
                    id: $row['id_usuario'],
                    nome: $row['nome'],
                    email: $row['email'],
                    senhaHash: "",
                    idPerfil: $row['id_perfil'],
                    ativo: (bool)$row['ativo']
                );

                $usuario->perfilNome = $row['perfil_nivel'];

                return $usuario;
            }
        }

        throw new Exception("Senha não encontrada.");
    }


    public static function excluir(int $id)
    {
        $pdo = self::getConexao();

        $sql = "DELETE FROM `usuarios` WHERE `id_usuario` = :id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([':id' => $id]); 

        if ($stmt->rowCount() === 0) {
            throw new Exception("ID não encontrado para exclusão.");
            return false;
        }
            
        return true;
    }



}


// $usuario1 = new Usuario(
//     nome: "Sergio",
//     email: "sergio@example.com",
//     idPerfil: "14",
//     senhaHash: "1234",
//     ativo: true
// );


Usuario::excluir(14);

// echo "<pre>";

// try {
//     print_r(Usuario::listarPorEmail("sergio@example.com"));
// } catch (Exception $err) {
//     echo $err->getMessage();
// }
