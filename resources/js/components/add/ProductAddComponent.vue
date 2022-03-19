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
                            <div class="desc-add">
                                <a href="/product"><i class="fas fa-solid fa-backward"></i> Powrót</a>
                                <hr>
                                <h2>{{((this.name!='')?'Edycja produktu: '+this.name:'Dodawanie nowego produktu')}}</h2>
                                <form class="mt-4" :action="`/product/save/${id}`" method="POST">
                                    <div class="form-group mb-2">
                                        <label for="name">Nazwa</label>
                                        <input type="text" name="name" class="form-control" id="name" v-model="this.name" placeholder="Wprowadź nazwę produktu" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="name">Symbol</label>
                                        <input type="text" name="symbol" class="form-control" id="symbol" v-model="this.symbol" placeholder="Wprowadź symbol">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="name">Jednostka</label>
                                        <input type="text" name="unit" class="form-control" id="unit" v-model="this.unit" placeholder="Wprowadź jednostkę" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="name">Cena (netto)</label>
                                        <input type="number" min="0.01" step="0.01" name="price" class="form-control" id="price" v-model="this.price" placeholder="Wprowadź cenę" required>
                                    </div>
                                    <hr>
                                    <div class="row mb-0">
                                        <div class="col-md-12 mt-4">
                                            <button class="button is-success w-100">Zapisz</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ProductAddComponent",
    data() {
        return {
            id:'',
            name:'',
            symbol:'',
            unit:'',
            price:'',
        }
    },
    props: {
        query:{
            type: String,
            required:false
        }
    },
    methods:{

    },
    beforeMount() {
        var parseQuery =  JSON.parse(this.$props.query);
        if(parseQuery.length>0)
        {
            this.id = parseQuery[0].product_id;
            this.name = parseQuery[0].name;
            this.symbol = parseQuery[0].symbol;
            this.unit = parseQuery[0].unit;
            this.price = parseQuery[0].price;
        }
    },
}
</script>

<style scoped>

</style>
