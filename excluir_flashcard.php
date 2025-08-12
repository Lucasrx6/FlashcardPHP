<?php
require_once 'config/banco_dados.php';

if (!isset($_POST['id']) || empty($_POST['id'])) {
    header('Location: index.php');
    exit();
}

$bd = new BancoDados();
$flashcard_id = $_POST['id'];

try {
    if ($bd->excluirFlashcard($flashcard_id)) {
        // Redirecionar com mensagem de sucesso
        header('Location: index.php?msg=excluido');
    } else {
        // Redirecionar com mensagem de erro
        header('Location: index.php?msg=erro_exclusao');
    }
} catch(Exception $e) {
    // Redirecionar com mensagem de erro
    header('Location: index.php?msg=erro_exclusao');
}
exit();
?>