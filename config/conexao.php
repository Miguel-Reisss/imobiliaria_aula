<?php

    class Conexao {

        private $host = '10.91.45.60';
        private $bd = 'imobiliaria';
        private $user = 'admin';
        private $pass = '123456';

        public function conexao() {
            try {
                $pdo = new PDO("mysql:host={$this->host};dbname={$this->bd}; charset=utf8", $this->user, $this->pass);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;

            } catch (PDOException $err) {
                die("Erro de conexão: " . $err->getMessage());
                return null;
            }
        }




    }



?>