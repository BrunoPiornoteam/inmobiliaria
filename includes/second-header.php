
<div class="second-header">
    <button id="toggle-menu" class="menu-button">
        <i class="fas fa-bars"></i>
    </button>

    <div class="search-container">
        <input type="text" id="search" placeholder="Buscar...">
        <button type="submit"><i class="fas fa-search"></i></button>
    </div>

    <a href="logout.php" class="logout-button">
        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </a>
</div>


<style>
   .dashboard-container {
    position: relative;
}

.second-header {
    display: flex;
    background-color: #333;
    color: #fff;
    position: absolute; 
    top: 0; 
    right: 0; 
    width: 100%;
    z-index: 10;
}

.menu-button {
    background: none;
    border: none;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    margin-right: 10px; 
}

.search-container {
    display: flex;
    align-items: center;
    margin-right: 10px;
    width: 100%;
}

.search-container input {
    padding: 5px;
    border: none;
    border-radius: 5px;
    margin-right: 5px;
}

.search-container button {
    background: none;
    border: none;
    color: #fff;
    cursor: pointer;
    width: auto;
}

.logout-button {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    align-items: center;
}

</style>