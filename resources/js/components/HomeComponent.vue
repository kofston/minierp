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
            AreaData: [],
            xkey:"year",
            ykeys:'["a"]',
            labels: '["Sprzedano za"]',

        }
    },
    methods:{
      loadingChart(){
          let that = this;
          $.ajax({
              url: "/order/loadingChart",
              method:"GET",
              success: function( xhr ) {
                 that.AreaData = xhr;
              }
          });
        }
    },
    components:{
        AreaChart,
    },
    beforeMount() {
        this.loadingChart();
    }
}
</script>

<style scoped>

</style>
