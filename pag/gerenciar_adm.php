<?php
session_start();

// Bloqueia acesso de usuários comuns
if ($_SESSION['role_user'] == 'user') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 50px;
        }

        .dashboard {
            background: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 320px;
        }

        .dashboard h1 {
            text-align: center;
            color: #6a3b12;
            margin-bottom: 30px;
        }

        .dashboard a {
            display: block;
            text-decoration: none;
            background: #8B4513;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }

        .dashboard a:hover {
            background: #6a3b12;
            transform: scale(1.03);
        }
.nav-produto {
  position: fixed;      /* fixa na tela */
  top: 20px;            /* distância do topo */
  left: 20px;           /* distância da esquerda */
  z-index: 1000;        /* fica acima de outros elementos */
}

.nav-produto a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;          /* tamanho do botão */
  height: 50px;
  background-color: #fff;
  border-radius: 50%;   /* botão redondo */
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  transition: transform 0.2s, background 0.2s;
}

.nav-produto a:hover {
  transform: scale(1.1);
  background-color: #f4e4d8; /* hover suave */
}

.nav-produto img.voltar {
  width: 24px;
  height: 24px;
}

        @media (max-width: 400px) {
            .dashboard {
                width: 90%;
                padding: 20px;
            }

            .dashboard a {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <nav class="nav-produto">
        <a href="../index.php"><img class="voltar" src="../img/left-arrow.png" alt="Voltar"></a>
    </nav>

    <div class="dashboard">
        <h1>Painel Admin</h1>
        <a href="gerenciar.php">Gerenciar Produtos</a>
        <a href="pedidos_adm.php">Pedidos Pendentes</a>
        <a href="gerenciar_usuarios.php">Gerenciar Usuários</a>
    </div>

</body>
</html>