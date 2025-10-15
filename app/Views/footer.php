<footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
               
             
            </div>
        </div>
    </footer>
</div>
</div>
<script src="<?php echo base_url(); ?>/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>/js/scripts.js"></script>
<script src="<?php echo base_url(); ?>/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>/js/simple-datatables.min.js"></script>
<script src="<?php echo base_url(); ?>/js/datatables-simple-demo.js"></script>
<script src="<?php echo base_url(); ?>/assets/demo/datatables-demo.js"></script>
<script>
    $('#modal-confirma').on('show.bs.modal', function(e){
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
</script>






</body>
</html>  