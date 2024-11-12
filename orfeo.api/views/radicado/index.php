<h1>Radicacion</h1>

<form id="obtenerRadicado" action="<?php echo URL;?>radicacion/radicar" method="post">
    
    <h1>Esto es el form de radicacion</h1>
    
    <div id="seccion-contactos" >
        
        <div id="datos-generales">
            <h2>Datos Contacto</h2>
            <u1>
                <li><label>Tipo Contacto</label>
                    <select id="tipoContacto">
                        <option value="1">Ciudadano</option>
                        <option value="2">Empresa</option>
                        <option value="3">Entidad</option>
                        <option value="4">Funcionario</option>
                    </select>
                </li>
             </u1>
             <u2>
                 <li><label>id</label><input type="text" name="idCiudadano"/></li>
                 <li><label>Documento</label><input type="text" name="documentoCiudadano"/></li>
             </u2>
             <u3>
                 <li><label>Nombre</label><input type="text" name="nombreCiudadano"/></li>
                 <li><label>Primer Apellido</label><input type="text" type="primerApellidoCiudadano"/></li>
                 <li><label>segundoApellido</label><input type="text" name="segundoApellidoCiudadano"/></li>
             </u3>
             <u4>
                 <li><label>direccion</label><input type="text" name="direccionCiudadano"/></li>
                 <li><label>Codigo Postal</label><input type="text" name="codigoPostalCiudadano"/></li>
                 <li><label>telefono</label><input type="text" name="telefonoCiudadano"/></li>
                 <li><label>mail</label><input type="text" name="mailCiudadano"/></li>
             </u4>
        </div>
        <div id="datos-divipola">
            <h3> Datos Divipola</h3>
            <u1>
                <li><label>Municipio</label><input type="text" name="municipio"/></li>
                 <li><label>Departamento</label><input type="text" name="departamento"/></li>
                <li><label>Municipio</label><input type="text" name="municipio"/></li>
                 <li><label>Departamento</label><input type="text" name="departamento"/></li>
            </u1>
        </div>
        
    </div>
    <div id="seccion-radicacion" >
        <h4>Datos Radicaci&oacute;n</h4>
        <u1>
            <li><label>Asunto</label><textarea name="asunto"></textarea></li>
            <li><label>Fecha</label><input type="date" name="fechaOficio"/></li>
            <li><label>tipo Radicado</label> <select id="tipoRadicado">
                        <option value="1">Ciudadano</option>
                        <option value="2">Empresa</option>
                        <option value="3">Entidad</option>
                        <option value="4">Funcionario</option>
                    </select></li>
            <li><label>Radicado Padre(Opc)</label><input type="text" name="mailCiudadano"/></li>
        </u1>
    </div>
    <div>
        <input type="submit" />
    </div>
    <?php if(isset($this->msg))
    {
        echo $this->msg;   
    }
    ?>
</form>