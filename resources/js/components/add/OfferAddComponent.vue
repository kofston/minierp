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
                                <a href="/offer"><i class="fas fa-solid fa-backward"></i> Powrót</a>
                                <hr>
                                <h2>Dodawanie nowej oferty</h2>
                                <form class="mt-4 save_ord_form" :action="`/offer/save/${id}`" method="POST">
                                    <div class="row mb-0">
                                        <div class="col-lg-10">
                                            <label>Wybierz dostawców</label>
                                            <select name="product" id="client" class="form-control select2" required>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label></label>
                                            <a href="javascript:void(0)" class="button is-success w-100 text-white" id="add_btn">Dodaj</a>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <label>Wprowadź wiadomość</label>
                                            <textarea required class="form-control" name="message" v-model="this.message"></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <table class="table client_table">

                                            </table>
                                        </div>
                                        <div class="col-md-12 mt-4">
                                            <a href="javascript:void(0);" class="button is-success w-100 save_offer">Zapisz</a>
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
export default {
    name: "OfferAddComponent",
    data() {
        return {
            id:'',
            clientsArr:'',
            clients_list:'',
            message:'',
        }
    },
    props:{
        query:{
            type: String,
            required:false
        },
        clientsarray:{
            type: String,
            required:false
        },
    },
    methods:{

    },
    beforeMount() {
        let that = this;
        var parseQuery =  JSON.parse(this.$props.query);
        var clientsarray = JSON.parse(this.$props.clientsarray);
        if(parseQuery.length>0)
        {
            this.id = parseQuery[0].offer_id;
            this.message = parseQuery[0].message;
            let clt_lst = JSON.parse(parseQuery[0].clients);
            $(clt_lst).each(function( index ) {
                that.clients_list += "<tr class='client_in_table'><td><input type='hidden' readonly class='cltid_"+clt_lst[index]+"' name='CLIENT[]' value='"+clt_lst[index]+"' /><input class='form-control form-control-sm' type='text' readonly  value='"+clientsarray[clt_lst[index]].name+"' /></td><td>"+clientsarray[clt_lst[index]].email+"</td><td><a href='javascript:void(0)' class='redtext' onclick='$(this).closest(`tr`).remove();'>✖</a></td></tr>";
            });


        }
        if(!$.isEmptyObject(clientsarray))
        {
            for (const client_cache in clientsarray) {
                if(clientsarray[client_cache].status=="1")
                    this.clientsArr+='<option value="'+clientsarray[client_cache].client_id+'" '+((this.client_id==clientsarray[client_cache].client_id)?'selected':'')+' >'+clientsarray[client_cache].name+'</option>';
            }
        }
    },
    mounted() {
        $(".save_offer").unbind().click(function (){
            if($(".client_table tr").length>1)
                $("#Send_form").click();
            else
                alert("Nie dodałeś dostawców do oferty!");
        });
        var clientsarray = JSON.parse(this.$props.clientsarray);
        $("#client").html(this.clientsArr);
        $(".client_table").html("<tr><th>Nazwa</th><th>Mail</th><th>Opcje</th></tr>"+this.clients_list);
        $("#add_btn").unbind().click(function (){
            let client_id = $("#client").val();
            if($(".cltid_"+client_id).length>0)
                alert("Dostawca zosał już dodany!");
            else
            $(".client_table").append("<tr class='client_in_table'><td><input type='hidden' readonly class='cltid_"+client_id+"' name='CLIENT[]' value='"+client_id+"' /><input class='form-control form-control-sm' type='text' readonly  value='"+clientsarray[client_id].name+"' /></td><td>"+clientsarray[client_id].email+"</td><td><a href='javascript:void(0)' class='redtext' onclick='$(this).closest(`tr`).remove();'>✖</a></td></tr>");
        });
    }
}
</script>

<style scoped>

</style>
