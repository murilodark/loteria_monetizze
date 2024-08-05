<header>
    <img src="assets/monetizze_logo.svg" alt="Logomarca" class="logo">
    <!-- <h1>Loteria</h1> -->
    <?php if ($ClassSessao->validaSessao()) { ?>
        <nav>
            <ul>
                <li><a href="loteria_lista.php">Loterias</a></li>
                <li><a href="loteria_cadastro.php">Cadastro Loterias</a></li>
                <li><a href="user_login.php">Sair</a></li>
            </ul>
        </nav>
    <?php  } else { ?>
        <nav>
            <ul>
                <li><a href="user_cadastro.php">Cadastro</a></li>
                <li><a href="user_login.php">login</a></li>
            </ul>
        </nav>
    <?php  }  ?>
</header>
<div id="loading">Carregando...</div>