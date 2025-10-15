// Call the dataTables jQuery plugin


$(document).ready(function(){
  $('#id_datatable').DataTable({
    "order": [[1, "asc"]],
    "language":{
      "lengthMenu": "Mostrar _MENU_ registros por pagina",
      "info": "Mostrando pagina _PAGE_ de _PAGES_",
      "infoEmpty": "No hay registros disponibles",
      "infoFiltered": "(filtrada de _MAX_ registros)",
      "loadingRecords": "Cargando...",
      "processing":     "Procesando...",
      "search": "Buscar:",
      "zeroRecords":    "No se encontraron registros coincidentes",
      "paginate": {
        "next":       "Siguiente",
        "previous":   "Anterior"
      },					
    },
    "ordering": false
   // order:[0,'desc']
  });	
});	


