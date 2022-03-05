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
                        <a href="/product" class="p-0" title="Produkty"><b-menu-item icon="box" label="Produkty"></b-menu-item></a>
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
                </b-menu>
            </div>
        </div>
        <div class="col-lg-9 p-0">
            <div class="card p-2">
                <area-chart id="line" :data="AreaData" :xkey="xkey" :ykeys="ykeys" :labels="labels"  line-colors= '[ "#FF6384", "#36A2EB" ]' grid="true" grid-text-weight="bold" resize="true"></area-chart>
            </div>
        </div>
    </div>
</div>
</div>
</template>

<script>
import Raphael from 'raphael/raphael';
global.Raphael = Raphael;
import { AreaChart } from 'vue-morris';
export default {
    name: "HomeComponent",
    data(){
        return{
            donutTestName:'',
            donutTestDate:'',
            barData: [],
            AreaData: [
                { year: '0000-00-00',a: 0 },
            ],
            xkey:"year",
            ykeys:'["a"]',
            labels: '["Brak danych"]',

        }
    },
    components:{
        AreaChart,
    },
}
</script>

<style scoped>

</style>
