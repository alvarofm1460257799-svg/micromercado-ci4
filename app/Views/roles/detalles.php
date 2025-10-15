<div id="layoutSidenav_content">
    <main>
    <div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?php echo $titulo; ?></h4>
        </div>
        <div class="card-body">
            <form id="form_permisos" name="form_permisos" method="POST" action="<?php echo base_url() . '/roles/guardaPermisos'; ?>">
                <input type="hidden" name="id_rol" value="<?php echo $id_rol; ?>" />

                <div class="row">
                    <div class="col-md-6">
                        <?php
                        $half = ceil(count($permisos) / 1.7);
                        foreach (array_slice($permisos, 0, $half) as $permiso) { ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="<?php echo $permiso['id']; ?>" name="permisos[]" 
                                <?php if (isset($asignado[$permiso['id']])) { echo 'checked'; } ?> />
                                <label class="form-check-label"><?php echo $permiso['nombre']; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        foreach (array_slice($permisos, $half) as $permiso) { ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="<?php echo $permiso['id']; ?>" name="permisos[]" 
                                <?php if (isset($asignado[$permiso['id']])) { echo 'checked'; } ?> />
                                <label class="form-check-label"><?php echo $permiso['nombre']; ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="mt-3">
                <a href="<?php echo base_url(); ?>/roles" class="btn 
                btn-warning">Regresar</a>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>



    </main>





