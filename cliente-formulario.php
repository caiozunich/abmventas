<?php

include_once "config.php";
include_once "entidades/cliente.php";

$pg = "Edición de cliente";

$cliente = new Cliente();
$cliente->cargarFormulario($_REQUEST);


if($_POST){

    if(isset($_POST["btnGuardar"])){
        if(isset($_GET["id"]) && $_GET["id"] > 0){
              //Actualizo un cliente existente
              $cliente->actualizar();
        } else {
            //Es nuevo
            $cliente->insertar();
        }
        if(isset($_POST["txtTipo"])){
            $domicilio = new Domicilio();
            $domicilio->eliminarPorCliente($cliente->idcliente);
            for($i=0; $i < count($_POST["txtTipo"]); $i++){
                $domicilio->fk_idcliente = $cliente->idcliente; 
                $domicilio->fk_tipo = $_POST["txtTipo"][$i];
                $domicilio->fk_idlocalidad =  $_POST["txtLocalidad"][$i];
                $domicilio->domicilio = $_POST["txtDomicilio"][$i];
                $domicilio->insertar();
            }
        }
    } else if(isset($_POST["btnBorrar"])){
        $domicilio = new Domicilio();
        $domicilio->eliminarPorCliente($cliente->idcliente);
        $cliente->eliminar();
    }
} 

if(isset($_GET["do"]) && $_GET["do"] == "buscarLocalidad" && $_GET["id"] && $_GET["id"] > 0){
    $idProvincia = $_GET["id"];
    $localidad = new Localidad();
    $aLocalidad = $localidad->obtenerPorProvincia($idProvincia);
    echo json_encode($aLocalidad);
    exit;
} else if(isset($_GET["id"]) && $_GET["id"] > 0){
    $cliente->obtenerPorId();
}

 if(isset($_GET["do"]) && $_GET["do"] == "cargarGrilla"){
        $idCliente = $_GET['idCliente'];
        $request = $_REQUEST;

        $entidad = new Domicilio();
        $aDomicilio = $entidad->obtenerFiltrado($idCliente);

        $data = array();

        if (count($aDomicilio) > 0)
            $cont=0;
            for ($i=0; $i < count($aDomicilio); $i++) {
                $row = array();
                $row[] = $aDomicilio[$i]->tipo;
                $row[] = $aDomicilio[$i]->provincia;
                $row[] = $aDomicilio[$i]->localidad;
                $row[] = $aDomicilio[$i]->domicilio;
                $cont++;
                $data[] = $row;
            }

        $json_data = array(
            "draw" => isset($request['draw'])?intval($request['draw']):0,
            "recordsTotal" => count($aDomicilio), //cantidad total de registros sin paginar
            "recordsFiltered" => count($aDomicilio),//cantidad total de registros en la paginacion
            "data" => $data
        );
        echo json_encode($json_data);
        exit;
    }




include_once("header.php"); 
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Cliente</h1>
            <div class="row">
                <div class="col-12 mb-3">
                    <a href="clientes.php" class="btn btn-primary mr-2">Listado</a>
                    <a href="cliente-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
                    <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
                    <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input type="text" required class="form-control" name="txtNombre" id="txtNombre" value="<?php echo $cliente->nombre ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtCuit">CUIT:</label>
                    <input type="text" required class="form-control" name="txtCuit" id="txtCuit" value="<?php echo $cliente->cuit ?>" maxlength="11">
                </div>
                <div class="col-6 form-group">
                    <label for="txtFechaNac">Fecha de nacimiento:</label>
                    <input type="date" class="form-control" name="txtFechaNac" id="txtFechaNac" value="<?php echo $cliente->fecha_nac ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtTelefono">Teléfono:</label>
                    <input type="number" class="form-control" name="txtTelefono" id="txtTelefono" value="<?php echo $cliente->telefono ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtCorreo">Correo:</label>
                    <input type="" class="form-control" name="txtCorreo" id="txtCorreo" required value="<?php echo $cliente->correo ?>">
                </div>
            </div>
        <div class="row">
        <div class="col-12">  
        <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> Domicilios
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalDomicilio">Agregar</button>
                    </div>
                </div>
                <div class="panel-body">
                    <table id="grilla" class="display" style="width:98%">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Provincia</th>
                                <th>Localidad</th>
                                <th>Dirección</th>
                            </tr>
                        </thead>
                    </table> 
                 </div>
            </div>          
        </div>
    </div>


<div class="modal fade" id="modalDomicilio" tabindex="-1" role="dialog" aria-labelledby="modalDomicilioLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDomicilioLabel">Domicilio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="row">
            <div class="col-12 form-group">
                <label for="lstTipo">Tipo:</label>
                <select name="lstTipo" id="lstTipo" class="form-control">
                    <option value="" disabled selected>Seleccionar</option>
                    <option value="1">Personal</option>
                    <option value="2">Laboral</option>
                    <option value="3">Comercial</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="lstProvincia">Provincia:</label>
                <select name="lstProvincia" id="lstProvincia" onchange="fBuscarLocalidad();" class="form-control">
                    <option value="" disabled selected>Seleccionar</option>
                    <?php foreach($aProvincias as $prov): ?>
                        <option value="<?php echo $prov->idprovincia; ?>"><?php echo $prov->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="lstLocalidad">Localidad:</label>
                <select name="lstLocalidad" id="lstLocalidad" class="form-control">
                    <option value="" disabled selected>Seleccionar</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="txtDireccion">Dirección:</label>
                <input type="text" name="" id="txtDireccion" class="form-control">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="fAgregarDomicilio()">Agregar</button>
      </div>
    </div>
  </div>
</div>
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
<script>
$(document).ready( function () {
    var idCliente = '<?php echo isset($cliente) && $cliente->idcliente > 0? $cliente->idcliente : 0 ?>';

   var dataTable = $('#grilla').DataTable({
        "processing": true,
        "serverSide": false,
        "bFilter": false,
        "bInfo": true,
        "bSearchable": false,
        "paging": false,
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "ajax": "cliente-formulario.php?do=cargarGrilla&idCliente=" + idCliente
    });
} );

 

        function limpiarFormulario(){
            $("#lstTipo").val("");
            $("#lstProvincia").val("");
            $("#lstLocalidad").val("");
            $("#txtDireccion").val("");
        }
</script>
<?php include_once("footer.php"); ?>