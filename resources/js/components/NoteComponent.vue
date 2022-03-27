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
                                <tr><th>Lp.</th><th>Zdarzenie</th><th>Moduł</th><th>Utworzono</th><th>Przez</th></tr>
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
    name: "NoteComponent",
    data() {
        return {

        }
    },
}
</script>

<style scoped>

</style>
