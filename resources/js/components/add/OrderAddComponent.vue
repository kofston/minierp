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
                                <a href="/order"><i class="fas fa-solid fa-backward"></i> Powrót</a>
                                <hr>
                                <h2>{{((this.order_ident!='')?'Edycja zamówienia: '+this.order_ident:'Dodawanie nowego zamówienia')}}</h2>
                                <form class="mt-4 save_ord_form" :action="`/order/save/${id}`" method="POST">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label>Klient</label>
                                            <select name="client" id="client" class="form-control select2" required>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Data zamówienia</label>
                                            <input type="date" name="order_date" class="form-control" id="order_date" v-model="order_date"  placeholder="Wprowadź datę zamówienia" required>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <label>Produkty</label>
                                                    <select name="product" id="product" class="form-control select2" required>
                                                    </select>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label></label>
                                                    <a href="javascript:void(0)" class="button is-success w-100 text-white" id="add_btn">Dodaj</a>
                                                </div>
                                            </div>
                                            <table class="table prod_table">

                                            </table>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Rabat<input id="rabate_input" readonly v-model="client_rabate" class="form-control"><a id="do_rabate" href="javascript:void(0);">ZASTOSUJ</a></label>

                                        </div>
                                        <div class="col-md-12">
                                            <label>Dostawa</label>
                                            <select name="delivery" id="delivery" class="form-control " required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-md-12 mt-4">
                                            <a href="javascript:void(0);" class="button is-success w-100 save_order">Zapisz</a>
                                            <button id="Send_form" class="d-none"></button>
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
import {isNumber} from "lodash";

export default {
    name: "OrderAddComponent",
    data() {
        return {
            id:'',
            client_id:'',
            order_ident:'',
            clientsArr:'',
            productsArr:'',
            products_list:'',
            delivery_id:'',
            order_date:'',
            delivery_options:'',
            client_rabate:'',
        }
    },
    props: {
        query:{
            type: String,
            required:false
        },
        clientsarray:{
            type: String,
            required:false
        },
        productsarray:{
            type: String,
            required:false
        }
    },
    methods:{
        verifyForm(){
            $(".save_order").unbind().click(function (){
                if($(".prod_table tr").length>1)
                    $("#Send_form").click();
                else
                    alert("Nie posiadasz żadnych produktów w zamówieniu!");
            });
            $("#do_rabate").unbind().click(function (){
                let rabate_val = $("#rabate_input").val();
                if(rabate_val>0)
                {
                    $( ".prc_inp" ).each(function( index ) {
                        let prc = $(this).val();
                        if(isNumber(parseFloat($(this).val())))
                        {
                            var new_prc = parseFloat(prc);
                            $(this).val((new_prc - ((new_prc*parseFloat(rabate_val))/100)).toFixed(2));
                        }
                    });
                    alert("Zastosowano rabat!");
                }
                else
                    alert("Klient nie ma zdefiniowanego rabatu!");
            });
        }
    },
    beforeMount() {
        var parseQuery =  JSON.parse(this.$props.query);
        var clientsarray = JSON.parse(this.$props.clientsarray);
        var productsarray = JSON.parse(this.$props.productsarray);
        var prod_list = JSON.parse( ((parseQuery.length>0)?parseQuery[0].products:'[]') );
        var FullProdList = '';
        if(parseQuery.length>0)
        {
            this.id = parseQuery[0].order_id;
            this.order_ident = parseQuery[0].order_ident;
            this.client_id = parseQuery[0].client_id;
            this.delivery_id = parseQuery[0].delivery_id;
            this.order_date = parseQuery[0].order_date;
            this.client_rabate = parseQuery[0].rabate;


            // console.log(prod_list.id.length);
            for(let i=0;i<prod_list.id.length;i++)
            {
                FullProdList +="<tr class='product_in_table'>";
                let hpl = 0;
                for (const pls in prod_list) {
                    console.log(hpl);
                    switch (hpl)
                    {
                        case 0:
                            FullProdList+="<td><input type='hidden' readonly name='PRODUCT[id][]' value='"+prod_list[pls][i]+"' />";
                            break;
                        case 1:
                            FullProdList+="<input class='form-control form-control-sm' type='text' readonly name='PRODUCT[name][]' value='"+prod_list[pls][i]+"' /></td>";
                            break;
                        case 2:
                            FullProdList+="<td><input class='form-control form-control-sm' type='number' step='1' min='1' placeholder='Wpisz ilość' name='PRODUCT[qty][]' value='"+prod_list[pls][i]+"' /></td>";
                            break;
                        case 3:
                            FullProdList+="<td><input class='form-control form-control-sm prc_inp' type='number' step='0.01' min='0.01' placeholder='Wpisz cenę (zł)' name='PRODUCT[price][]' value='"+prod_list[pls][i]+"' /></td><td><a href='javascript:void(0)' class='redtext' onclick='$(this).closest(`tr`).remove();'>✖</a></td>";
                            break;
                    }
                    hpl++;
                }
                FullProdList +='</tr>';
            }

            this.products_list = FullProdList;
        }
        if(!$.isEmptyObject(clientsarray))
        {
            for (const client_cache in clientsarray) {
                this.clientsArr+='<option value="'+clientsarray[client_cache].client_id+'" '+((this.client_id==clientsarray[client_cache].client_id)?'selected':'')+' >'+clientsarray[client_cache].name+'</option>';
            }
        }
        if(!$.isEmptyObject(productsarray))
        {
            for (const product_cache in productsarray) {
                this.productsArr+='<option data-price="'+productsarray[product_cache].price+'" value="'+productsarray[product_cache].product_id+'" >'+productsarray[product_cache].name+' '+((productsarray[product_cache].symbol!='')?productsarray[product_cache].symbol:'')+'</option>';
            }
        }
        this.delivery_options = '<option value="0" '+((this.delivery_id=='0')?'selected':'')+' >Odbiór osobisty</option><option value="1" '+((this.delivery_id=='1')?'selected':'')+' >Kurier</option>';

    },
    mounted() {
        this.verifyForm();
        $("#client").html(this.clientsArr);
        $("#product").html(this.productsArr);
        $("#delivery").append(this.delivery_options);
        $(".prod_table").html("<tr><th>Nazwa</th><th>Ilość</th><th>Cena</th><th>Opcje</th></tr>"+this.products_list);
        $("#add_btn").unbind().click(function (){
            let prod_id = $("#product").val(),prod_text = $("#product").text(),prod_price = $("#product option:selected").data("price");
            $(".prod_table").append("<tr class='product_in_table'><td><input type='hidden' readonly name='PRODUCT[id][]' value='"+prod_id+"' /><input class='form-control form-control-sm' type='text' readonly name='PRODUCT[name][]' value='"+prod_text+"' /></td><td><input class='form-control form-control-sm' type='number' step='1' min='1' placeholder='Wpisz ilość' name='PRODUCT[qty][]' value='1' /></td><td><input class='form-control form-control-sm prc_inp' type='number' step='0.01' min='0.01' placeholder='Wpisz cenę (zł)' name='PRODUCT[price][]' value='"+prod_price+"' /></td><td><a href='javascript:void(0)' class='redtext' onclick='$(this).closest(`tr`).remove();'>✖</a></td></tr>");
        });
        $(document).ready(function() {
            $('.select2').select2();
        });
    }
}
</script>

<style scoped>

</style>
