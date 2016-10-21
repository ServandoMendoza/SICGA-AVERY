<nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <!--            <div class="navbar-header">-->
    <!--                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">-->
    <!--                    <span class="sr-only">Toggle navigation</span>-->
    <!--                    <span class="icon-bar"></span>-->
    <!--                    <span class="icon-bar"></span>-->
    <!--                    <span class="icon-bar"></span>-->
    <!--                </button>-->
    <!--                <a class="navbar-brand" href="#">Brand</a>-->
    <!--            </div>-->

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tiempo Muerto <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."DeadCode/add" ?>">Crear Código</a></li>
                    <li><a href="<?= $this->url('home')."DeadCode/list" ?>">Listar Códigos</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tiempo Muerto Grupos <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."DeadCode/addGroup" ?>">Crear Grupo</a></li>
                    <li><a href="<?= $this->url('home')."DeadCode/listGroup" ?>">Listar Grupos</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Control de Usuarios<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."auth/registration/index" ?>">Crear Usuario</a></li>
                    <li><a href="<?= $this->url('home')."auth/admin/list" ?>">Listar Usuarios</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Productos<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."Products/add" ?>">Crear Producto</a></li>
                    <li><a href="<?= $this->url('home')."Products/list" ?>">Listar Productos</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Máquinas<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."Machine/add" ?>">Crear Máquina</a></li>
                    <li><a href="<?= $this->url('home')."Machine/list" ?>">Listar Máquinas</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Scrap<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."ScrapCode/add" ?>">Crear Código Scrap</a></li>
                    <li><a href="<?= $this->url('home')."ScrapCode/list" ?>">Listar Código Scrap</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Modelo de Producción<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."StandardProduction" ?>">Listar Producción Estándar</a></li>
                    <li><a href="<?= $this->url('home')."StandardProduction/add" ?>">Agregar Producción Estándar</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Areas<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $this->url('home')."Areas/create" ?>">Agregar Area</a></li>
                    <li><a href="<?= $this->url('home')."Areas/list" ?>">Listar Areas</a></li>
                </ul>
            </li>
        </ul>

    </div><!-- /.navbar-collapse -->
</nav><?php
/**
 * Created by PhpStorm.
 * User: servandomac
 * Date: 1/5/15
 * Time: 8:02 PM
 */