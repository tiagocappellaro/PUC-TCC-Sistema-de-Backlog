<nav class="navbar navbar-expand-lg" style="background-color: #1DB954;">
  <form>
  <a class="navbar-brand text-white" href="index.php" style="font-size: 20px;">Backlog</a>
</form>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon" style="color: white;"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <form class="form-inline w-100" id="filtroForm">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" 
             aria-haspopup="true" aria-expanded="false">
            Menu
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="gerencial.php">Gerencial</a>
          </div>
        </li>
      </ul>

      <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <button class="btn btn-success mr-2" type="button" data-toggle="modal" data-target="#novoTicketModal">Novo Ticket</button>
          </li>
          <li class="nav-item">
            <button class="btn btn-info" type="button" id="meusTicketsButton" data-usuario="<?php echo htmlspecialchars($_SESSION['usuario']); ?>">Meus Tickets</button>
          </li>
        </ul>
        <select name="filtro" class="form-control ml-2" id="filtroSelect">
          <option value="id">ID</option>
          <option value="categoria">Categoria</option>
          <option value="descricao">Descrição</option>
        </select>
        <input class="form-control ml-2" type="search" placeholder="Buscar" aria-label="Search" id="campoBusca" name="busca">
      <?php endif; ?>
    </form>
  </div>
</nav>