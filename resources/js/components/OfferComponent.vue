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
                            <div class="d-flex mt-2 mb-3 justify-content-end text-light"><a href="/offer/add" class="button is-success">Roześlij ofertę</a></div>
                            <table class="dTable table">
                                <thead>
                                <tr><th>Lp.</th><th>Wiadomość</th><th>Klienci</th><th>Utworzono</th><th>Edytowano</th><th>Opcje</th></tr>
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
    name: "OfferComponent",
    data() {
        return {

        }
    },
    methods: {
        syncDataBase(){
            let that = this;
            $(document).ready( function () {
                $('.dTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "aLengthMenu": [[15, 25, 50], [15, 25, 50]],
                    "iDisplayLength": 15,
                    "ajax": {
                        "url": "/offer/get_list",
                        "type": "POST"
                    },
                    "columnDefs": [{ "targets": [], "orderable": false, }],
                    "drawCallback": function( settings ) {
                        that.initDelete();
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
        }
    },
    mounted() {
        this.syncDataBase();
    }
}
</script>

<style scoped>

</style>
