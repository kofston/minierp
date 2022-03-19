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
                            <div class="d-flex mt-2 mb-3 justify-content-end text-light"><a href="/product/add" class="button is-success">Dodaj produkt</a></div>
                            <table class="dTable table">
                                <thead>
                                <tr><th>Lp.</th><th>Nazwa</th><th>Cena/Jednostka</th><th>Utworzono</th><th>Edytowano</th><th>Opcje</th></tr>
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
    name: "ClientComponent",
    data() {
        return {

        }
    },
    methods: {
        syncDataBase(){
            $(document).ready( function () {
                $('.dTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "aLengthMenu": [[15, 25, 50], [15, 25, 50]],
                    "iDisplayLength": 15,
                    "ajax": {
                        "url": "/product/get_list",
                        "type": "POST"
                    },
                    "columnDefs": [{ "targets": [], "orderable": false, }],
                    "drawCallback": function( settings ) {

                    },
                });
            } );
        },
    },
    mounted() {
        this.syncDataBase();
    }

}
</script>

<style scoped>

</style>
