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
                                <a href="/client"><i class="fas fa-solid fa-backward"></i> Powrót</a>
                                <hr>
                                <h2>{{((this.name!='')?'Edycja klienta: '+this.name:'Dodawanie nowego klienta')}}</h2>
                                <form class="mt-4" :action="`/client/save/${id}`" method="POST">
                                    <div class="form-group mb-2">
                                        <label for="name">Nazwa</label>
                                        <input type="text" name="name" class="form-control" id="name" v-model="this.name" placeholder="Wprowadź nazwę klienta" required>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label for="name">Symbol</label>
                                        <input type="text" name="symbol" class="form-control" id="symbol" v-model="this.symbol"  placeholder="Wygenerowany automatycznie">
                                    </div>
                                    <hr>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label>Ulica</label>
                                            <input type="text" name="street" class="form-control" v-model="this.street" placeholder="Ulica" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nr budynku</label>
                                            <input type="text" name="building" class="form-control" v-model="this.building" placeholder="Nr budynku" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nr lokalu</label>
                                            <input type="text" name="alt" class="form-control" v-model="this.alt" placeholder="Nr lokalu">
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            <label>Kod pocztowy</label>
                                            <input type="text" name="zip" class="form-control" v-model="this.zip" placeholder="Kod pocztowy" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Miasto</label>
                                            <input type="text" name="city" class="form-control" v-model="this.city" placeholder="Miasto" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Państwo</label>
                                            <select name="country" id="countries" class="form-control" required>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-0">
                                        <div class="col-md-6">
                                            <label>E-mail</label>
                                            <input type="email" name="email" class="form-control" v-model="this.email" placeholder="Email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Telefon</label>
                                            <input type="text" name="phone" class="form-control" v-model="this.phone" placeholder="Telefon" required>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-0">
                                        <div class="col-md-6">
                                            <label>Rabat</label>
                                            <input type="number" min="0" max="99" step="1" name="rabate" class="form-control" v-model="this.rabate" placeholder="Rabat (domyślnie 0)" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Notatka</label>
                                            <input type="text" name="note" class="form-control" v-model="this.note" placeholder="Notatka">
                                        </div>
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
    name: "ClientComponent",
    data() {
        return {
            id:'',
            name:'',
            symbol:'',
            street:'',
            building:'',
            alt:'',
            zip:'',
            city:'',
            country:'',
            email:'',
            phone:'',
            rabate:'',
            note:'',
            countriesArr:'',
        }
    },
    props: {
      query:{
          type: String,
          required:false
      }
    },
    methods: {
    },
    beforeMount() {
        var parseQuery =  JSON.parse(this.$props.query);
        if(parseQuery.length>0)
        {
            this.id = parseQuery[0].client_id;
            this.name = parseQuery[0].name;
            this.symbol = parseQuery[0].symbol;
            this.street = parseQuery[0].street;
            this.building = parseQuery[0].building;
            this.alt = parseQuery[0].alt;
            this.zip = parseQuery[0].zip;
            this.city = parseQuery[0].city;
            this.country = parseQuery[0].country;
            this.email = parseQuery[0].email;
            this.phone = parseQuery[0].phone;
            this.rabate = parseQuery[0].rabate;
            this.note = parseQuery[0].note;
        }
        this.countriesArr = '<option value="pl" '+((this.country=='pl')?'selected':'')+'>Polska</option><option value="gb" '+((this.country=='gb')?'selected':'')+'>Wielka Brytania</option><option value="de" '+((this.country=='de')?'selected':'')+'>Niemcy</option><option value="fr" '+((this.country=='fr')?'selected':'')+'>Francja</option>';
    },
    mounted() {
        $("#countries").html(this.countriesArr);
    }

}
</script>

<style scoped>

</style>
