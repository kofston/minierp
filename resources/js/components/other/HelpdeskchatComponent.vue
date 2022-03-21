<template>
    <div>
        <div class="container">
            <div class="row m-0">
                <div class="col-lg-12 p-0">
                    <div class="card p-2">
                            <div>
                                <h2 style="font-size:30px;text-align:center;">Twoja dyskusja do Zamówienia: {{this.order_ident}}</h2>
                            </div>
                    </div>
                    <div class="card p-2 mt-4">
                        Moja dyskusja
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "HelpdeskchatComponent",
    data() {
        return {
            order_ident:'',
        }
    },
    props: {
        query:{
            type: String,
            required:false
        }
    },
    beforeMount() {
        var parseQuery =  JSON.parse(this.$props.query);
        if(parseQuery.length>0)
        {
            this.order_ident = parseQuery[0].order_ident;
        }
    },
}
</script>

<style scoped>

</style>
