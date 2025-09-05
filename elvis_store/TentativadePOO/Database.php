<?php

class Database
{
    public function conecta($sql)
    {
        $bd = mysqli_connect("localhost", "root", "Debase33@", "elvis_store") or die("Erro ao conectar ao banco");

        $resp = mysqli_query($bd, $sql) or die("Erro na consulta");

        return $resp;
    }

    public function insert($nome, $sobrenome, $email)
    {

        $sql = "insert into email_list(first_name, last_name, email) values ('$nome', '$sobrenome', '$email')";
        $this->conecta($sql);
    }

    public function exibir()
    {

        $sql = "SELECT first_name, last_name, email FROM email_list ORDER BY id ASC";
        $res = $this->conecta($sql);

        return $res; // <- nunca mais será null
    }
}