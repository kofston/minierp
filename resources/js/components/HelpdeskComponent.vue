<template>
    <div>
        <div class="container">
            <div class="row m-0">
                <div class="col-lg-3">
                    <div class="card p-2">
                        <header-component></header-component>
                    </div>
                </div>
                <div class="col-lg-9 p-0">
                    <div class="card p-2">
                        <section>
                            <table class="dTable table">
                                <thead>
                                <tr><th>Lp.</th><th>Numer zgłoszenia</th><th>Utworzono</th><th>Status</th><th>Opcje</th></tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "HelpdeskComponent",
    data() {
        return {

        }
    },
    methods: {
        syncDataBase(){
            var that = this;
            $(document).ready( function () {
                $('.dTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "aLengthMenu": [[15, 25, 50], [15, 25, 50]],
                    "iDisplayLength": 15,
                    "ajax": {
                        "url": "/helpdesk/get_list",
                        "type": "POST"
                    },
                    "columnDefs": [{ "targets": [], "orderable": false, }],
                    "drawCallback": function( settings ) {
                        that.initDelete();
                        that.discussStatus();
                        $(function () {
                            $('[data-toggle="tooltip"]').tooltip()
                        })
                    },
                });
            } );
        },
        initDelete()
        {
            $( ".delete_row" ).unbind().click(function() {
                if(confirm("Czy chcesz usunąć rekord?")){
                    let id = $(this).data("id"),module=$(this).data('module');
                    $.ajax({
                        url: "/"+module+"/delete/"+id,
                        method:"POST",
                        success: function( xhr ) {
                            $("#"+module+"_"+id).slideUp();
                            alert("Rekord został usunięty");
                        }
                    })
                }
            });
        },
        discussStatus()
        {
            $( ".discuss_change" ).unbind().change(function() {
                let ident = $(this).data("ident"),newStatus = $(this).val();
                $.ajax({
                    url: "/helpdesk/change_status/"+ident+"/"+newStatus,
                    method:"POST",
                    success: function( xhr ) {
                        alert("Status dyskusji został zmieniony, a klient został o tym poinformowany");
                        $(".dTable").DataTable().ajax.reload();
                    }
                });
            });
        }
    },
    mounted() {
        this.syncDataBase();
        this.discussStatus();
    }

}
</script>

<style scoped>

</style>
