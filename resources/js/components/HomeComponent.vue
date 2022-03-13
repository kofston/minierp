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
