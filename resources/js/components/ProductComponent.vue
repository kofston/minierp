<template>
    <div>
        <div class="container">
            <div class="row m-0">
                <div class="col-lg-3">
                    <div class="card p-2">
                        <b-menu>
                            <b-menu-list label="MENU">
                                <a href="/home" class="p-0" title="Strona Główna"><b-menu-item icon="table" label="Strona Główna"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list >
                                <a href="/client" class="p-0" title="Klienci"><b-menu-item icon="user" label="Klienci"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list >
                                <a href="/product" class="p-0" title="Produkty"><b-menu-item icon="folder" label="Produkty"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list>
                                <a href="/order" class="p-0" title="Zamówienia"><b-menu-item icon="box" label="Zamówienia"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list>
                                <a href="/delivery" class="p-0" title="Delivery"><b-menu-item icon="cubes" label="Przesyłki"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list>
                                <a href="/helpdesk" class="p-0" title="HelpDesk"><b-menu-item icon="info" label="Help Desk"></b-menu-item></a>
                            </b-menu-list>
                            <b-menu-list>
                                <a href="/offer" class="p-0" title="Oferty"><b-menu-item icon="tag" label="Oferty"></b-menu-item></a>
                            </b-menu-list>
                        </b-menu>
                    </div>
                </div>
                <div class="col-lg-9 p-0">
                    <div class="card p-2">
                        <section>
                            <table class="dTable table">
                                <thead>
                                <tr><th>Lp.</th><th>Nazwa</th><th>Utworzono</th><th>Edytowano</th><th>Opcje</th></tr>
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
